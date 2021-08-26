<?php

namespace App\Http\Controllers\PMs;

use App\Features\Stats\RuleCreationPerTeam;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClientAccountRequest;
use App\Http\Requests\UpdateClientAccountRequest;
use App\Models\ClientAccount;
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

        return redirect(route('pm.client-account.dashboard', [$client_account->slug]));
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


        $teamMembers = collect($client_account->team->allUsers())->map(function ($user) {
            return collect($user->toArray())->only(['id', 'name', 'email', 'membership'])->all();
        });

        $stats = (new RuleCreationPerTeam(
            view_by: $request->get('view_by', 'week'),
            range: $request->get('range', 5),
            function: $request->get('function', 'count'),
            cumulative: $request->get('cumulative', 1),
            region: $request->get('region', ''),
            column: $request->get('column', 'created_at'),
            client_account_id: $client_account->id
        ))->handle();

        return Jetstream::inertia()->render($request, 'ClientAccount/Dashboard', [
            'team' => $client_account->team,
            'teams' => $client_account->teams,
            'teamMembers' => $teamMembers,
            'clientAccount' => $client_account,
            'rulesCount' => (int) $client_account->rules_count,
            'omnipresentRulesCount' => (int) $client_account->omnipresent_rules_count,
            'flaggedRulesCount' => (int) $client_account->flagged_rules_count,
            'publishedRulesCount' => (int) $client_account->published_rules_count,
            'taxonomiesCount' => (int) $client_account->taxonomies_count - $client_account->root_taxonomies_count,
            'termsCount' => (int) $client_account->terms_count,

            'stats' => $stats,
            'view_by' => Str::title($request->get('view_by', 'week')),
            'range' => (int) $request->get('range', 5),
            'column' => $request->get('column', 'created_at'),
            'level' => 'team',
            'region' => $request->get('region', ''),
            'cumulative' => (int) $request->get('cumulative', 1),
            'mode' => 'account-specific',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return Jetstream::inertia()->render($request, 'ClientAccount/Create', [
            'team' => $request->user()->currentTeam,
            'client' => (new ClientAccount(['name' => ''])),
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
        \Log::debug(print_r($request->all(), true));

        $image_path = null;

        if ($request->hasFile('image')) {
            $image_path = Storage::putFile('logos', $request->file('image'));
        }

        $client_account = ClientAccount::create(array_merge(
            $request->only(['name', 'slug', 'alias']),
            ['image' => Storage::url($image_path)]
        ));

        return redirect(route('pm.client-account.dashboard', [$client_account->slug]));
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
        }

        $client_account->update(array_merge(
            $request->only(['name', 'slug', 'alias']),
            ['image' => Storage::url($image_path)]
        ));

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : redirect(route('pm.client-account.edit', [$client_account->slug]));
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
