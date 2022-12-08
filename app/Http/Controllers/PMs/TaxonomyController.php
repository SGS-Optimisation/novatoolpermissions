<?php

namespace App\Http\Controllers\PMs;

use App\Events\ClientAccounts\TaxonomyUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaxonomyRequest;
use App\Http\Requests\CreateTermRequest;
use App\Models\ClientAccount;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaxonomyController extends Controller
{
    /**
     * Update a term.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateTaxonomyRequest $request)
    {
        $client_account = ClientAccount::find($request->clientAccountId);

        /** @var Term $term */
        $taxonomy = Taxonomy::withTrashed()
            ->firstOrCreate([
                'name' => $request->name,
                'parent_id' => $request->parentTaxonomyId
            ]);

        if ($taxonomy->deleted_at) {
            $taxonomy->restore();
        }

        $client_account->taxonomies()->syncWithoutDetaching($taxonomy);

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        return back(303);
    }

    /**
     * Update a taxonomy.
     *
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $taxonomy = Taxonomy::find($id);
        $client_account = ClientAccount::find($request->clientAccountId);

        $taxonomy->update(['name' => $request->name]);
        logger("taxonomy $id updated to {$request->name}");

        $client_account->taxonomies()
            ->updateExistingPivot($taxonomy, ['use_for_pm_search' => $request->use_for_pm_search]);

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        broadcast(new TaxonomyUpdated($client_account));

        return back(303);
    }

    /**
     * Delete a taxonomy.
     * The taxonomy is not actually deleted, but rather detached from the client account
     *
     * @param  Request  $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $client_account = ClientAccount::find($request->clientAccountId);
        $taxonomy = Taxonomy::find($id);
        logger('detaching taxonomy ' . $taxonomy->name . ' from client ' . $client_account->name);

        $client_account->taxonomies()->detach($id);

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        return back(303);
    }
}
