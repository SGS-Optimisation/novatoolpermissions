<?php


namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Models\Rule;
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
        $all =  $rule->audits()->orderBy('created_at', 'desc')->with('user')->get();

      return Jetstream::inertia()->render($request, 'PM/AuditActivity', [
          'team' => $request->user()->currentTeam,
          'clientAccount' => $client_account,
          'audits' => $all,
          'ruleId'=>$id,

        ]);
    }

}
