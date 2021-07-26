<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\RuleTerm;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $rule = Rule::find($rule_id);
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $taxonomy_fields = $request->only(['taxonomy'])['taxonomy'];
       // $deleted = DB::delete('delete from rule_term where rule_id='.$rule_id);
       // RuleTerm::where('rule_id',$rule_id)->get();
        $deleted = RuleTerm::where("rule_id",$rule_id)->get();
        foreach($deleted as $del){
         // RuleTerm::find($del->id)->delete();
       }
      //  logger('deleted'.$deleted);
       // exit;
        $rule->terms()->detach();
       // exit;
        foreach ($taxonomy_fields as $taxonomy_name => $terms) {
            logger('checking taxonomy name '.$taxonomy_name);

            /** @var Taxonomy $taxonomy */
            $taxonomy = $client_account->taxonomies()->where('name', $taxonomy_name)->first();

            if ($taxonomy) {
                logger('taxonomy id:'.$taxonomy->id);

                $taxonomy_terms_to_sync = [];

                foreach ($terms as $term_name) {
                    logger('checking term '.$term_name);

                    //$possible_terms = $client_account->terms()->where('name', $term_name)->get();
                    $term = $taxonomy->terms()->where('name', $term_name)->first();

                    if ($term) {
                      //  $user = RuleTerm::updateOrCreate(
                       //     ['rule_id' => $rule_id, 'term_id'=>$term->id],
                        //    ['rule_id' => $rule_id, 'term_id'=>$term->id]
                       // );
                      //  RuleTerm::create(["rule_id"=>$rule_id,'term_id'=>$term->id]);
                        $term_id[] = $term->id;

                    }

                }



            }

        }
        if($term_id){
            $rule->terms()->attach($term_id);
        }

        //logger($taxonomy_fields['taxonomy']);


        Cache::tags(['rules'])->clear();

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-taxonomy-updated');
    }
}
