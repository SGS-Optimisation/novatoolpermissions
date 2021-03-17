<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClientAccountRequest;
use App\Models\ClientAccount;
use App\Services\ClientAccounts\MakeTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Jetstream\Jetstream;

class ClientAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function show(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::withCount('rules')
                ->whereSlug($client_account_slug)->first()
            ?? $request->user()->currentTeam->clientAccount;

        return Jetstream::inertia()->render($request, 'ClientAccount/Dashboard', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
            'rulesCount' => $client_account->rules_count,
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
     * @param  CreateClientAccountRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(CreateClientAccountRequest $request)
    {
        \Log::debug('creating client account');
        \Log::debug(print_r($request->all(), true));

        $image_path = null;

        if ($request->hasFile('image')) {

            $image_path = Storage::disk('public')->putFile('logos', $request->file('image'));
        }

        $clientAccount = ClientAccount::create(array_merge(
            $request->only(['name', 'slug', 'alias']),
            ['image' => $image_path]
        ));

        return redirect(route('pm.client-account.dashboard', [$clientAccount->slug]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientAccount $clientAccount)
    {
        //
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
