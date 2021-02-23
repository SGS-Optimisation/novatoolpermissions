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
     * @param  Request  $request
     * @param  null  $client_account_slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function index(Request $request, $client_account_slug = null)
    {
        \Log::debug('client account: '.$client_account_slug);

        $client_account = ClientAccount::whereSlug($client_account_slug)->first() ?? $request->user()->currentTeam->clientAccount;

        if ($client_account && (!$client_account_slug || $client_account_slug == 'dashboard')) {
            return redirect(route('dashboard', ['clientAccount' => $client_account->slug]));
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
            'rulesCount' => $client_account->rules()->count(),
        ]);
    }


    /**
     * @param  Request  $request
     * @param $client_account_slug
     * @return \Inertia\Response
     */
    public function rules(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $rules = Cache::remember('rules-' . $client_account_slug, 3600, function() use ($client_account){
            $rules = $client_account->rules->each(function($rule){
                $rule->content = str_replace('<img', '<img loading="lazy"', $rule->content);
            });

            return $rules;
        });

        return Jetstream::inertia()->render($request, 'ClientAccount/ListRules', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
            'rules' => $rules //()->orderBy('updated_at', 'DESC')->paginate(50) ?? [],
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
