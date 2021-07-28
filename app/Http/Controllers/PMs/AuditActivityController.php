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
            'created',
            'updated'
        ])->with('user')
        ->get();
        $data=[];
        foreach($rules as $key => $rt){
            $tax_name =[];
            $pivoted = $rt->getPivotData();
            if($pivoted && $pivoted['properties']!=""){
                if($rt->event=='attached' ) {
                    $pivoted_old = $rules[($key)-1]->getPivotData();
                    if($pivoted_old['properties']) {
                        foreach ($pivoted_old['properties'] as $piv) {

                            $tax_old = Term::with('taxonomy')->where('id', $piv['term_id'])->first();
                            $tax_name[ $tax_old->taxonomy->name]['old'][] = $tax_old->name;
                             }
                    }
                        foreach ($pivoted['properties'] as $piv) {
                            logger("test tset" . $piv['term_id']);
                            $tax = Term::with('taxonomy')->where('id', $piv['term_id'])->first();
                            $tax_name[ $tax->taxonomy->name]['new'][] = $tax->name;

                        }

                }
            }
            if($rt->event=='attached'){
                 $data[] = array("user_name"=>$rt->user->name,"created_at"=>$rt->created_at,"tax_names"=>$tax_name,"ip_address"=>$rt->ip_address);
            }else{
                $data[] = array("user_name"=>$rt->user->name,"created_at"=>$rt->created_at,"tax_names"=>"","ip_address"=>$rt->ip_address,"audit"=>$rt);

            }
        }
//dd($data);
      return Jetstream::inertia()->render($request, 'PM/AuditActivity', [
          'team' => $request->user()->currentTeam,
          'clientAccount' => $client_account,
          'audits' => $data,
          'ruleId'=>$id,

        ]);
    }

}
