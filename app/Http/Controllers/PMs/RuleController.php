<?php

namespace App\Http\Controllers\PMs;

use App\Events\Rules\Updated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRuleRequest;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Term;
use App\Repositories\RuleRepository;
use App\Services\ClientAccounts\BuildTaxonomyLists;
use App\Services\Taxonomy\Traits\TaxonomyBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\Jetstream;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Trix\PendingAttachment;

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
                    (new Trix('Content'))->withFiles(),
                    $rule
                );
            };
        }

        if ($request->state && $rule->state != $request->state) {
            logger('transitioning rule ' . $rule->id . ' to ' . $request->state);
            $rule->state->transitionTo($request->state);
        }
    }

    /**
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function index(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $term = $request->query('term');

        $ruleRepo = new RuleRepository($client_account);

        \Log::debug('rules index');

        return Jetstream::inertia()->render($request, 'ClientAccount/ListRules', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
            'rules' => $ruleRepo->all($term),
            'states' => (new Rule)->getStatesFor('state'),
            'search' => optional($term)->name,
            'rootTaxonomies' => $client_account->root_taxonomies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function create(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        $this->authorize('create', [Rule::class, $client_account]);

        $rule = new Rule(['content' => '']);

        return Jetstream::inertia()->render($request, 'ClientAccount/CreateRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => $rule,
            'states' => $rule->getStatesFor('state')
        ],
            $this->buildTaxonomyLists($client_account_slug)
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function store(CreateRuleRequest $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $rule_fields = $request->only(['name', 'content', 'flagged', 'metadata']);
        $rule = $client_account->rules()->create($rule_fields);
        $this->parseContent($request, $rule);

        logger('rule added: '.$rule->id);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit(Request $request, $client_account_slug, $id)
    {
        $rule = Rule::find($id);

        $this->authorize('update', $rule);

        $rule->content = str_replace(
            [
                '\n',
                '\r\n',
                '<div>&nbsp;</div>',
                '<p></p>',
                '<p><br></p>',
                '<ul>',
                '</ul>',
                '<li>',
                '</li>',
                '</span>'
            ],
            ['', '', '', '', '', '', '', '', '', '</span><br/>'],
            $rule->content
        );

        return Jetstream::inertia()->render($request, 'ClientAccount/EditRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => $rule,
            //'states' => $rule->getStatesFor('state')
            'states' => $rule->state->transitionableStates(),
        ],
            $this->buildTaxonomyLists($client_account_slug)
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, $client_account_slug, $id)
    {
        $rule_fields = $request->only(['name', 'content', 'flagged', 'metadata']);

        $rule = Rule::find($id);
        $rule->update($rule_fields);
        $this->parseContent($request, $rule);

        event(new Updated($rule));

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
