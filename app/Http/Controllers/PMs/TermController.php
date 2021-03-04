<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTermRequest;
use App\Models\ClientAccount;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\Jetstream;

class TermController extends Controller
{

    /**
     * Update a term.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateTermRequest $request)
    {

        $taxonomy = Taxonomy::find($request->taxonomyId);
        $client_account = ClientAccount::find($request->clientAccountId);

        /** @var Term $term */
        $term = $taxonomy->terms()->withTrashed()->firstOrCreate(['name' => $request->name]);

        if($term->deleted_at) {
            $term->restore();
        }

        $client_account->terms()->syncWithoutDetaching($term);

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        return back();
    }

    /**
     * Update a term.
     *
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $client_account = ClientAccount::find($request->clientAccountId);
        $term = Term::find($id);

        $term->update(['name' => $request->name]);

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        return back();
    }

    /**
     * Delete a term.
     *
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $term = Term::find($id);
        $client_account = ClientAccount::find($request->clientAccountId);

        $term->delete();

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        return back();
    }
}
