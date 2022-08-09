<?php

namespace App\Http\Controllers\PMs;

use App\Events\ClientAccounts\TermsUpdated;
use App\Events\Rules\RuleUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTermRequest;
use App\Models\ClientAccount;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\Term\CreateTermService;
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

        if ($request->get('multiple')) {
            logger('creating multiple terms');
            $term_lines = preg_split('/\r\n|[\r\n]/', $request->get('names'));
        } else {
            logger('creating single term');
            $term_lines = [$request->name];
        }

        $terms = collect();
        foreach ($term_lines as $term_line) {
            /** @var Term $term */
            list($term, $restore) = (new CreateTermService($client_account, $taxonomy, $term_line, true))->handle(false, false);

            $terms->add($term);
        }

        $client_account->terms()->syncWithoutDetaching($terms->pluck('id'));

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        broadcast(new TermsUpdated($client_account));

        return back(303) ;
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

        $config = $term->config;
        $config['aliases'] = $request->get('aliases', []);
        $term->config = $config;

        $term->update(['name' => $request->name, 'config' => $config]);

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        return back(303);
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
        $client_account = ClientAccount::find($request->clientAccountId);
        $client_account->terms()->detach($id);

        $term = Term::find($id);

        if ($term->client_accounts()->count() == 0 && $term->rules()->count() == 0) {
            $term->delete();
        }

        Cache::tags(['taxonomy'])->forget($client_account->slug.'-taxonomy-usage-data');
        Cache::tags(['taxonomy'])->forget($client_account->slug.'-rules-data');

        return back(303);
    }
}
