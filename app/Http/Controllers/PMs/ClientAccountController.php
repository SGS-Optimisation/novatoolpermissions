<?php

namespace App\Http\Controllers\PMs;

use App\Features\Stats\JobCreationPerClientAccount;
use App\Features\Stats\RuleCreationPerTeam;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClientAccountRequest;
use App\Http\Requests\UpdateClientAccountRequest;
use App\Models\ClientAccount;
use App\Models\Taxonomy;
use App\Models\User;
use App\Operations\ClientAccounts\AssociateDefaultTermsToAttachedTaxonomy;
use App\Services\ClientAccounts\MakeTeam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;

class ClientAccountController extends Controller
{

    public function getById($id)
    {
        $client_account = ClientAccount::find($id);

        return redirect(route('library.client-account.dashboard', [$client_account->slug]));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function show(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::withCount([
            'rules',
            'flagged_rules',
            'published_rules',
            'omnipresent_rules',
            'taxonomies',
            'root_taxonomies',
            'terms',
            'teams',
        ])
            ->whereSlug($client_account_slug)->first();//?? $request->user()->currentTeam->clientAccount;

        $teams = $client_account->teams;

        foreach ($teams as $team) {
            $team->teamMembers = collect($team->allUsers())->map(function ($user) {
                return collect($user->toArray())->only(['id', 'name', 'email', 'membership'])->all();
            });
        }

        $rule_stats = (new RuleCreationPerTeam(
            view_by: $request->get('rules_view_by', 'week'),
            range: $request->get('rules_range', 5),
            function: $request->get('rules_function', 'count'),
            cumulative: $request->get('rules_cumulative', 1),
            region: $request->get('rules_region', ''),
            column: $request->get('rules_column', 'created_at'),
            client_account_ids: $client_account->id
        ))->handle();

        $job_stats = (new JobCreationPerClientAccount(
            view_by: $request->get('jobs_view_by', 'week'),
            range: $request->get('jobs_range', 15),
            function: $request->get('jobs_function', 'count'),
            cumulative: $request->get('jobs_cumulative', 0),
            column: $request->get('jobs_column', 'created_at'),
            client_account_ids: $client_account->id
        ))->handle();

        return Jetstream::inertia()->render($request, 'ClientAccount/Dashboard', [
            'teams' => $client_account->teams,
            'clientAccount' => $client_account,
            'rulesCount' => (int) $client_account->rules_count,
            'omnipresentRulesCount' => (int) $client_account->omnipresent_rules_count,
            'flaggedRulesCount' => (int) $client_account->flagged_rules_count,
            'publishedRulesCount' => (int) $client_account->published_rules_count,
            'taxonomiesCount' => (int) $client_account->taxonomies_count - $client_account->root_taxonomies_count,
            'termsCount' => (int) $client_account->terms_count,

            'rule_stats' => [
                'stats' => $rule_stats,
                'view_by' => Str::title($request->get('rules_view_by', 'week')),
                'range' => (int) $request->get('rules_range', 5),
                'column' => $request->get('rules_column', 'created_at'),
                'level' => 'team',
                'region' => $request->get('rules_region', ''),
                'cumulative' => (int) $request->get('rules_cumulative', 1),
                'mode' => 'account-specific',
            ],

            'job_stats' => [
                'stats' => $job_stats,
                'view_by' => Str::title($request->get('jobs_view_by', 'week')),
                'range' => (int) $request->get('jobs_range', 15),
                'column' => $request->get('jobs_column', 'created_at'),
                'level' => 'client',
                'cumulative' => (int) $request->get('jobs_cumulative', 0),
                'mode' => 'account-specific',
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        return Jetstream::inertia()->render($request, 'ClientAccount/Create', [
            'team' => $request->user()->currentTeam,
            'client' => (new ClientAccount(['name' => ''])),
            'accountStructure' => Taxonomy::accountStructure()->with('mappings')->get(),
            'users'=> $users = User::select(['name', 'id', 'profile_photo_path'])->orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateClientAccountRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CreateClientAccountRequest $request)
    {
        \Log::debug('creating client account');

        $client_data = $request->only(['name', 'slug', 'alias']);

        if ($request->hasFile('image')) {
            $image_path = Storage::putFile('logos', $request->file('image'));
            $client_data['image'] = Storage::url($image_path);
        }

        $client_account = ClientAccount::create($client_data);

        (new MakeTeam($client_account))->handle( (int) $request->get('owner_id'));

        if ($request->has('taxonomy')) {
            logger('taxonomies to associate: ' . print_r($request->get('taxonomy'), true));

            $client_account->taxonomies()->attach($request->get('taxonomy'));

            (new AssociateDefaultTermsToAttachedTaxonomy($client_account))->handle();
        }

        return redirect(route('library.client-account.dashboard', [$client_account->slug]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function edit(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        return Jetstream::inertia()->render($request, 'ClientAccount/Update', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateClientAccountRequest  $request
     * @param $client_account_slug
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(UpdateClientAccountRequest $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $image_path = $client_account->image;

        if ($request->hasFile('image')) {
            $image_path = Storage::putFile('logos', $request->file('image'));
            $image_path = Storage::url($image_path);
        }

        $client_account->update(array_merge(
            $request->only(['name', 'slug', 'alias', 'jobteam']),
            ['image' => $image_path]
        ));

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : redirect(route('library.client-account.edit', [$client_account->slug]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientAccount $clientAccount)
    {
        //
    }
}
