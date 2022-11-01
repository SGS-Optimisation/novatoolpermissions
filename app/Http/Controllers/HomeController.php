<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function prodSearch(Request $request)
    {
        $invitations = TeamInvitation::with('team.clientAccount')
            ->where('email', $request->user()->email)
            ->get();

        return Jetstream::inertia()->render($request, 'ProdSearch', [
            'team' => optional($request->user())->currentTeam,
            'invitations' => $invitations,
        ]);
    }

    public function pmSearch(Request $request)
    {
        $invitations = TeamInvitation::with('team.clientAccount')
            ->where('email', $request->user()->email)
            ->get();

        $suggestions = [];

        $client_accounts = array_values(ClientAccount::orderBy('name')->pluck('name', 'slug')
            ->map(function ($name, $slug) {
                return [
                    'label' => $name,
                    'value' => $slug,
                    'search' => $slug . ' ' . $name,
                    'type' => 'ca',
                ];
            })->toArray());

        $suggestions[] = [
            'label' => 'Client Account',
            'code' => 'ca',
            'items' => $client_accounts,
        ];

        $client_accounts = ClientAccount::with(['pm_searchable_account_structure_child_taxonomies'])->get();

        foreach ($client_accounts as $client_account) {

            foreach ($client_account->pm_searchable_account_structure_child_taxonomies as $taxonomy) {

                $client_terms = array_values($taxonomy->terms()->whereHas('client_accounts', function ($query) use ($client_account) {
                        $query->where('id', $client_account->id);
                    })
                    ->orderBy('name')
                    ->pluck('name', 'id')->map(function($term, $id) use($taxonomy, $client_account) {
                        return [
                            'client' => $client_account->name,
                            'label' => $term,
                            'value' => $term,
                            'slug' => $client_account->slug,
                            'type' => 'bu',
                            'taxonomy' => $taxonomy->name,
                            'search' => $client_account->slug . ' ' . $client_account->name . ' ' . $term . ' ' . $taxonomy->name
                        ];
                    })->toArray());

                $suggestions[] = [

                    'label' => $client_account->name .' - '. $taxonomy->name,
                    'code' => 'bu',
                    'taxonomy' => $taxonomy->name,
                    'items' => $client_terms,
                ];
            }
        }


        return Jetstream::inertia()->render($request, 'PMSearch', [
            'team' => optional($request->user())->currentTeam,
            'invitations' => $invitations,
            'suggestions' => $suggestions,
        ]);
    }
}
