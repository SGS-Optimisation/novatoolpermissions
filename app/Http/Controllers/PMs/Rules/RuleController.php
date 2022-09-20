<?php

namespace App\Http\Controllers\PMs\Rules;

use App\Events\Rules\Deleted;
use App\Events\Rules\RuleUpdated;
use App\Features\Rules\AssignAndNotifyRuleReviewersFeature;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRuleRequest;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Team;
use App\Models\Term;
use App\Operations\Rules\GetDefaultRuleReviewersOperation;
use App\Operations\Rules\GetOrderedStatesOperation;
use App\Operations\Rules\GetRuleReviewersOperation;
use App\Repositories\RuleRepository;
use App\Services\ClientAccounts\BuildTaxonomyLists;
use App\Services\LegacyImport\ExtractImages;
use App\Services\Taxonomy\Traits\TaxonomyBuilder;
use App\States\Rules\DraftState;
use App\States\Rules\PublishedState;
use App\States\Rules\ReviewingState;
use App\States\Rules\RuleState;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
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
     * @param  Request  $request
     * @param  Rule  $rule
     */
    protected static function extractImages($request, $rule)
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
    }

    /**
     * @param  Request  $request
     * @param $rule
     */
    protected static function processTransitions($request, $rule)
    {
        if ($request->state && $rule->state != $request->state) {
            logger('transitioning rule '.$rule->id.' to '.$request->state);

            $transitionParameters = [$request->user()];

            /** @var RuleState $stateClass */
            $stateClass = 'App\States\Rules\\'.$request->state.'State';
            if ((new $stateClass($rule))->requiresAssignee) {
                $transitionParameters[] = $request->get('assignees', []);
            }

            $rule->state->transitionTo($request->state, ...$transitionParameters);
        } elseif ($rule->state->requiresAssignee) {
            (new AssignAndNotifyRuleReviewersFeature($rule, $request->user(), $request->get('assignees', [])))->handle();
        }
    }

    /**
     * @param  ClientAccount  $clientAccount
     */
    public static function getPublishers($clientAccount)
    {
        $publishers = [];
        $teams = $clientAccount->teams;

        /** @var Team $team */
        foreach ($teams as $team) {
            foreach ($team->allUsers() as $user) {
                if ($team->userHasPermission($user, 'publishRules')) {
                    //$publishers[$user->id] = $user->name;
                    //$publishers[] = $user->name;
                    $publishers[] = ['value' => $user->id, 'label' => $user->name];
                }
            }
        }

        return $publishers;
    }

    public static function getDefaultPublishers($rule)
    {
        $users = (new GetDefaultRuleReviewersOperation($rule))->handle();
        $default_publishers = [];

        foreach($users as $user){
            $default_publishers[] = [
                'value' => $user->id,
                'label' => $user->name,
                'suggestion_level' => $user->suggestion_level
            ];
        }

        return $default_publishers;
    }

    /**
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function index(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->with(['teams', 'users'])->first();

        $search_term = $request->query('term');

        //$ruleRepo = new RuleRepository($client_account);

        return Jetstream::inertia()->render($request, 'ClientAccount/ListRules', [
            'team' => $request->user()->currentTeam,
            'allTeams' => $client_account->teams->pluck('name', 'id')->sort()->values()->all(),
            'users' => $client_account->users->pluck('name', 'id')
                ->merge($client_account->teamOwners->pluck('name', 'id'))
                ->sort()->values()->all(),
            'clientAccount' => $client_account,
            //'rules' => $ruleRepo->all($search_term),
            'states' => (new Rule)->getStatesFor('state'),
            'stateModels' => (new GetOrderedStatesOperation(new Rule))->handle(),
            'search' => optional($search_term)->name,
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

        /** @var Rule $rule */
        $rule = new Rule(['content' => '']);
        $rule->state = DraftState::class;

        return Jetstream::inertia()->render($request, 'ClientAccount/CreateRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => $rule,
            //'states' => $rule->getStatesFor('state'),
            //'allowedStates' => $allowedStates,
            'stateModels' => (new GetOrderedStatesOperation($rule))->handle(),
            'publishers' => static::getPublishers($client_account),
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

        $rule_fields = $request->only(['name', 'flagged', 'metadata']);
        $rule_fields['content'] = (new ExtractImages($request->get('content')))->handle()->updated_content;

        $rule = $client_account->rules()->create($rule_fields);
        static::extractImages($request, $rule);
        static::processTransitions($request, $rule);

        logger('rule added: '.$rule->id);
        event(new RuleUpdated($rule));

        $request->session()->flash('success', 'Rule successfully created!');

        return redirect(route('library.client-account.rules.edit', [$client_account_slug, $rule->id]))
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
        /** @var Rule $rule */
        $rule = Rule::withTrashed()->with(['terms', 'attachments'])->find($id);

        $this->authorize('update', $rule);

        $rule->content = str_replace(
            [
                '\n',
                '\r\n',
                '<br/>',
                //'<div>&nbsp;</div>',
                //'<p></p>',
                //'<p><br></p>',
                //'</span>',
            ],
            ['', '', '<br>', /*'', '', '','</span><br>'*/],
            strip_tags(
                preg_replace('/\>\s+\</m', '><', $rule->content),
                [
                    'p', 'img', 'a', 'span', 'br',
                    'i', 'strong', 'b', 'u', 's', 'em',
                    'ul', 'ol', 'li',
                    'blockquote', 'pre',
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                    'table', 'thead', 'tbody', 'th', 'tr', 'td',
                    'iframe', 'div'
                ]
            )
        );

        return Jetstream::inertia()->render($request, 'ClientAccount/EditRule', array_merge([
            'team' => $request->user()->currentTeam,
            'rule' => $rule,
            'contributors' => $rule->users,
            //'states' => $rule->getStatesFor('state')
            //'states' => $rule->state->transitionableStates(),
            //'allowedStates' => static::buildStates($rule),
            'stateModels' => (new GetOrderedStatesOperation($rule))->handle(),
            'publishers' => static::getPublishers($rule->clientAccount),
            'defaultPublishers' => static::getDefaultPublishers($rule),
            'initialAssignees' => collect((new \App\Operations\Rules\GetRuleReviewersOperation($rule))->handle())
                ->map(function ($user) {
                    return ['value' => $user->id, 'label' => $user->name];
                })
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
        $rule_fields = $request->only(['name', 'flagged', 'metadata']);
        $rule_fields['content'] = (new ExtractImages($request->get('content')))->handle()->updated_content;

        $rule = Rule::find($id);
        $rule->update($rule_fields);
        static::extractImages($request, $rule);
        static::processTransitions($request, $rule);

        //event(new RuleUpdated($rule));

        //RuleUpdated::dispatch($rule);

        $executed = RateLimiter::attempt(
            'event-rule-update-'.$rule->id,
            $perMinute = 1,
            function() use($rule) {
                broadcast(new RuleUpdated($rule));
            },
            $decaySeconds = 5
        );

        return back(303);
    }

    public function massPublish(Request $request, $client_account_slug)
    {
        $rule_ids = $request->get('rule_ids');
        $rules = Rule::whereIn('id', $rule_ids)->get();

        foreach ($rules as $rule) {
            $rule->state->transitionTo(PublishedState::class, $request->user());
        }

        broadcast(new RuleUpdated($rules->first()))->toOthers();

        return back(303)->with('status', 'rule-updated');

    }

    public function massUnpublish(Request $request, $client_account_slug)
    {
        $rule_ids = $request->get('rule_ids');
        $rules = Rule::whereIn('id', $rule_ids)->get();

        $targetClass = $request->get('status', 'Draft') . 'State';

        foreach ($rules as $rule) {
            $rule->state->transitionTo('App\States\Rules\\' . $targetClass, $request->user());
        }

        broadcast(new RuleUpdated($rules->first()))->toOthers();

        return back(303)->with('status', 'rule-updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @param  int  $id
     * @return JsonResponse|RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $client_account_slug, $id)
    {
        $rule = Rule::find($id);
        $rule->delete();

        broadcast(new Deleted($rule))->toOthers();

        return redirect(route('library.client-account.rules.index', [$client_account_slug]), 303);

    }

    /**
     * Restore deleted resource
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @param  int  $id
     * @return JsonResponse|RedirectResponse
     * @throws \Exception
     */
    public function restore(Request $request, $client_account_slug, $id)
    {
        $rule = Rule::withTrashed()->find($id);
        $rule->restore();

        event(new RuleUpdated($rule));

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-restored');

    }
}
