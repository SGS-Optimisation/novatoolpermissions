<?php

namespace App\Http\Controllers\PMs\Rules;

use App\Events\Rules\Deleted;
use App\Events\Rules\Updated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRuleRequest;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Term;
use App\Repositories\RuleRepository;
use App\Services\ClientAccounts\BuildTaxonomyLists;
use App\Services\LegacyImport\ExtractImages;
use App\Services\Taxonomy\Traits\TaxonomyBuilder;
use App\States\Rules\DraftState;
use App\States\Rules\PublishedState;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\Jetstream;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Trix\PendingAttachment;
use function auth;
use function back;
use function config;
use function event;
use function logger;
use function optional;
use function redirect;

class RuleController extends Controller
{

    use TaxonomyBuilder;

    /**
     * TODO: Fix attachments not created in db
     *
     * @param Request $request
     * @param Rule $rule
     */
    protected function parseContent($request, $rule)
    {
        if ($request->ContentDraftId) {
            $callbacks[] = function () use ($request, $rule) {
                PendingAttachment::persistDraft(
                    $request->ContentDraftId,
                    (new Trix('Content'))->withFiles(config('filesystems.default'), 'rules/'),
                    $rule
                );
            };
        }

        if ($request->state && $rule->state != $request->state) {
            logger('transitioning rule ' . $rule->id . ' to ' . $request->state);
            $rule->state->transitionTo($request->state, $request->user());
        }
    }


    protected static function buildStates(Rule $rule)
    {
        $transitionable_states = $rule->state->transitionableStates();
        $currentState = $rule->state->getMorphClass();
        $shown_states = [];

        foreach ($transitionable_states as $state) {
            $transitionClass = $rule->state->config()->resolveTransitionClass($currentState, $state);

            if (!$transitionClass
                || (new $transitionClass($rule, auth()->user()))->canTransition()
            ) {
                $shown_states[] = $state;
            }
        }

        return $shown_states;
    }

    /**
     * @param Request $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function index(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->with(['teams', 'users'])->first();

        $search_term = $request->query('term');

        $ruleRepo = new RuleRepository($client_account);

        return Jetstream::inertia()->render($request, 'ClientAccount/ListRules', [
            'team' => $request->user()->currentTeam,
            'allTeams' => $client_account->teams->pluck('name', 'id')->sort()->values()->all(),
            'users' => $client_account->users->pluck('name', 'id')
                ->merge($client_account->teamOwners->pluck('name', 'id'))
                ->sort()->values()->all(),
            'clientAccount' => $client_account,
            'rules' => $ruleRepo->all($search_term),
            'states' => (new Rule)->getStatesFor('state'),
            'search' => optional($search_term)->name,
            'rootTaxonomies' => $client_account->root_taxonomies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function create(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        $this->authorize('create', [Rule::class, $client_account]);

        /** @var Rule $rule */
        $rule = new Rule(['content' => '']);
        $rule->state = DraftState::class;

        return Jetstream::inertia()->render($request, 'ClientAccount/CreateRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => $rule,
            'states' => $rule->getStatesFor('state'),
            'allowedStates' => static::buildStates($rule),
        ],
            $this->buildTaxonomyLists($client_account_slug)
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function store(CreateRuleRequest $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $rule_fields = $request->only(['name', 'flagged', 'metadata']);
        $rule_fields['content'] = (new ExtractImages($request->get('content')))->handle()->updated_content;

        $rule = $client_account->rules()->create($rule_fields);
        $this->parseContent($request, $rule);

        logger('rule added: ' . $rule->id);
        event(new Updated($rule));

        $request->session()->flash('success', 'Rule successfully created!');

        return $request->wantsJson()
            ? new JsonResponse(['id' => $rule->id], 200)
            : redirect(route('pm.client-account.rules.edit', [$client_account_slug, $rule->id]))
                ->with('success', 'Rule successfully created!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $client_account_slug
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit(Request $request, $client_account_slug, $id)
    {
        $rule = Rule::withTrashed()->with(['terms', 'attachments'])->find($id);

        $this->authorize('update', $rule);

        $rule->content = str_replace(
            [
                '\n',
                '\r\n',
                //'<div>&nbsp;</div>',
                //'<p></p>',
                //'<p><br></p>',
                //'</span>',
            ],
            ['', '', /*'', '', '','</span><br>'*/],
            strip_tags(
                $rule->content,
                [
                    'p', 'img', 'a', 'span', 'br',
                    'i', 'strong', 'b', 'u', 's', 'em',
                    'ul', 'ol', 'li',
                    'blockquote', 'pre',
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                    'table', 'thead', 'tbody', 'th', 'tr', 'td',
                ]
            )
        );


        return Jetstream::inertia()->render($request, 'ClientAccount/EditRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => $rule,
            'contributors' => $rule->users,
            //'states' => $rule->getStatesFor('state')
            'states' => $rule->state->transitionableStates(),
            'allowedStates' => static::buildStates($rule),
        ],
            $this->buildTaxonomyLists($client_account_slug)
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, $client_account_slug, $id)
    {
        $rule_fields = $request->only(['name', 'flagged', 'metadata']);
        $rule_fields['content'] = (new ExtractImages($request->get('content')))->handle()->updated_content;

        $rule = Rule::find($id);
        $rule->update($rule_fields);
        $this->parseContent($request, $rule);

        event(new Updated($rule));

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-updated');
    }

    public function massPublish(Request $request, $client_account_slug)
    {
        $rule_ids = $request->get('rule_ids');
        $rules = Rule::whereIn('id', $rule_ids)->get();

        foreach($rules as $rule) {
            $rule->state->transitionTo(PublishedState::class, $request->user());
            event(new Updated($rule));
        }

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $client_account_slug
     * @param int $id
     * @return JsonResponse|RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $client_account_slug, $id)
    {
        $rule = Rule::find($id);
        $rule->delete();

        event(new Deleted($rule));

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-deleted');

    }

    /**
     * Restore deleted resource
     *
     * @param Request $request
     * @param $client_account_slug
     * @param int $id
     * @return JsonResponse|RedirectResponse
     * @throws \Exception
     */
    public function restore(Request $request, $client_account_slug, $id)
    {
        $rule = Rule::withTrashed()->find($id);
        $rule->restore();

        event(new Updated($rule));

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-restored');

    }
}
