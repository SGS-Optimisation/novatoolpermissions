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

        $content_ledgers = $rule->ledgers()->whereIn('event', [
            'created',
            'updated',
        ])->with('user')
            ->get();

        $data = [];
        $content_changes = [];
        foreach ($content_ledgers as $index => $ledger) {
            foreach ($ledger->properties as $key => $value) {
                // dd($key2);
                if (in_array($key, $ledger->modified)
                    && $ledger->event == 'updated'
                    && ($key == "content" || $key == "state" || $key == "name" || $key == "metadata")) {
                    $content_changes[$index][$key] = [
                        "new" => $value,
                        "old" => isset($content_changes[$index - 1][$key]) ? $content_changes[$index - 1][$key]['new'] : ""
                    ];
                } else {
                    $content_changes[$index][$key] = ["new" => $value, "old" => ""];
                }
            }
            $data[] = [
                "user_name" => ($ledger->user) ? $ledger->user->name : "",
                "created_at" => $ledger->created_at,
                "tax_names" => "",
                "ip_address" => $ledger->ip_address,
                "audit" => $content_changes[$index],
                //"r_id" => $ledger->id
            ];


        }

        $relations_ledgers = $rule->ledgers()->whereIn('event', [
            'attached',
            'detached',
            'synced'
        ])->with('user')
            ->get();

        foreach ($relations_ledgers as $index => $ledger) {
            $tax_name = [];
            $pivoted = $ledger->getPivotData();
            if ($pivoted && $pivoted['properties'] != "") {
                if ($index != 0) {
                    $pivoted_old = $relations_ledgers[($index) - 1]->getPivotData();

                    if ($pivoted_old['properties']) {
                        foreach ($pivoted_old['properties'] as $piv) {
                            if (isset($piv['term_id'])) {
                                $tax_old = Term::with('taxonomy')->where('id', $piv['term_id'])->first();
                                $tax_name[$tax_old->taxonomy->name]['old'][] = $tax_old->name;
                            }
                        }
                    }
                }
                foreach ($pivoted['properties'] as $piv) {
                    if (isset($piv['term_id'])) {
                        $tax = Term::with('taxonomy')->where('id', $piv['term_id'])->first();
                        $tax_name[$tax->taxonomy->name]['new'][] = $tax->name;
                    }
                }

            }

            if ($tax_name) {
                $data[] = [
                    "user_name" => ($ledger->user) ? $ledger->user->name : "",
                    "created_at" => $ledger->created_at,
                    "tax_names" => $tax_name,
                    "ip_address" => $ledger->ip_address,
                    //"r_id" => $ledger->id
                ];
            }
        }
        return Jetstream::inertia()->render($request, 'PM/AuditActivity', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => $client_account,
            'audits' => $data,
            'ruleId' => $id,

        ]);
    }

}
