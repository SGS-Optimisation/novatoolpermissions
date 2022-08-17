<?php

namespace App\Http\Controllers\PMs\Rules;

use App\Events\Rules\RuleUpdated;
use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\RuleTerm;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use function back;

class RuleTaxonomyController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $rule_id
     * @return JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $client_account_slug, $rule_id)
    {
        logger('updating taxo for rule ' . $rule_id);
        $rule = Rule::find($rule_id);
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $taxonomy_fields = $request->only(['taxonomy'])['taxonomy'];

        $term_id = [];

        foreach ($taxonomy_fields as $taxonomy_name => $terms) {
            //logger('checking taxonomy name '.$taxonomy_name);

            /** @var Taxonomy $taxonomy */
            $taxonomy = $client_account->taxonomies()->where('name', $taxonomy_name)->first();

            if ($taxonomy) {
                //logger('taxonomy id:'.$taxonomy->id);
                if(!is_array($terms)) {
                    $terms = [$terms];
                }

                foreach ($terms as $term_name) {
                    //logger('checking term '.$term_name);
                    $term = $taxonomy->terms()->where('name', $term_name)->first();

                    if ($term) {
                        $term_id[] = $term->id;
                    }
                }
            }
        }

        $rule->terms()->sync($term_id);
        $executed = RateLimiter::attempt(
            'event-rule-update-'.$rule->id,
            $perMinute = 1,
            function() use($rule) {
                broadcast(new RuleUpdated($rule));
            },
            $decaySeconds = 5
        );

        return back(303);
    }
}
