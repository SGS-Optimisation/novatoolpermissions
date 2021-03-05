<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\Jetstream;

class ClientAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $client_account_slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function index(Request $request, $client_account_slug)
    {
        \Log::debug('client account: ' . $client_account_slug);

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ClientAccount $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function show(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ClientAccount $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ClientAccount $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientAccount $clientAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ClientAccount $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientAccount $clientAccount)
    {
        //
    }
}
