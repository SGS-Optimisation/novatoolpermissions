<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class ClientAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request, $client_account_id = null)
    {
        \Log::debug('client account: '.$client_account_id);

        $client_account = ClientAccount::find($client_account_id) ?? $request->user()->currentTeam->clientAccount;

        if ($client_account && (!$client_account_id || $client_account_id == 'dashboard')) {
            return redirect(route('dashboard', ['clientAccount' => $client_account->id]));
        }

        if (!$client_account) {
            \Log::debug('displaying user team');
            return Jetstream::inertia()->render($request, 'Dashboard', [
                'team' => $request->user()->currentTeam,
            ]);
        }

        \Log::debug('displaying client account team');
        return Jetstream::inertia()->render($request, 'ClientAccount/Dashboard', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
            'rules' => $client_account->rules ?? [],
        ]);
    }

    public function rules(Request $request, $client_account_id)
    {
        $client_account = ClientAccount::find($client_account_id);

        return Jetstream::inertia()->render($request, 'ClientAccount/Rules', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
            'rules' => $client_account->rules ?? [],
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientAccount  $clientAccount
     * @return \Illuminate\Http\Response
     */
    public function show(ClientAccount $clientAccount)
    {
        //
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
