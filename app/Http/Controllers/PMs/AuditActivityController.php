<?php


namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\RuleTerm;
use App\Models\Term;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OwenIt\Auditing\Models\Audit;
use Laravel\Jetstream\Jetstream;
//use Inertia\Inertia;

class AuditActivityController extends Controller
{

    /**
     * @param  Request  $request
     * @param $client_account_slug
     * @param $id
     * @return \Inertia\Response
     */
    public function ruleHistory(Request $request, $client_account_slug, $id)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $rule = Rule::withTrashed()->find($id);
        //$all =  $rule->audits()->orderBy('created_at', 'desc')->with('user')->get();
        $rules = $rule->ledgers()->whereIn('event', [
            'attached',
            'detached',
        ])

        ->get();
        //$rule_term = RuleTerm::where("rule_id",$id)->get();
        $terms = [];
        //dd($rules);

        foreach($rules as $key => $rt){
            //dd($rt->getPivotData());
            if($rt->event=='attached'){
                $pivoted = $rt->getPivotData();
                $taxonomy = [];
                foreach($pivoted['properties'] as $piv){
                    $tax = Term::with('taxonomy')->where('id',$piv['term_id'])->first();
                    //dd($tax->taxonomy);
                    logger("test tset".$piv['term_id']);
                    $taxonomy[] = array("taxonomy_name"=>$tax->taxonomy->name,"term_name"=>$tax->name,"term_id"=>$piv['term_id']);
                }

                $pivot = $rt->pivot;

            }

           // $rt_id[] = $rt->id;
           // $led = RuleTerm::find($rt->id)->ledgers()->latest()->first();
           // $properties = $led->properties;
          //  $term =  Term::where("id",$properties['term_id'])->with('taxonomy')->first();
           // $data = array("event",$led->event);
          //  $terms[$led->event][] = array('taxonomy'=>$term->taxonomy->name,"term"=>$term->name);
          //  logger("test tset".$term->name);


        }
        //dd($rt_id);
        dd($taxonomy);
      return Jetstream::inertia()->render($request, 'PM/AuditActivity', [
          'team' => $request->user()->currentTeam,
          'clientAccount' => $client_account,
          'audits' => $all,
          'ruleId'=>$id,

        ]);
    }

}
