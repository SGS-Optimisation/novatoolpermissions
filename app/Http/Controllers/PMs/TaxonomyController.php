<?php

namespace App\Http\Controllers\PMs;

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
        $taxonomy = $client_account->taxonomies()->withTrashed()
            ->firstOrCreate([
                'name' => $request->name,
                'parent_id' => $request->parentTaxonomyId
            ]);

        if ($taxonomy->deleted_at) {
            $taxonomy->restore();
        }

        $client_account->taxonomies()->syncWithoutDetaching($taxonomy);

        Cache::tags(['taxonomy'])->clear();

        return back();
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
        $taxonomy->update(['name' => $request->name]);

        Cache::tags(['taxonomy'])->clear();

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
        $taxonomy = Taxonomy::find($id);
        $taxonomy->delete();

        Cache::tags(['taxonomy'])->clear();

        return back();
    }
}
