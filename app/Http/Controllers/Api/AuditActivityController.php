<?php


namespace App\Http\Controllers\Api;

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

    public function index(Request $request)
    {

        $audits = Audit::with('user')
            ->orderBy('created_at', 'desc')->get();


        return Jetstream::inertia()->render($request, 'Api/AuditActivity', [
            'audits' => $audits,

        ]);

    }

    public function show(Request $request)
    {

        $audits = Audit::with('user')
            ->orderBy('created_at', 'desc')->get();


        return response()->json($audits);

    }

    public function history(Request $request, $client_account_slug, $id)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();

        $rule = Rule::find($id);
        $all =  $rule->audits()->with('user')->get();

      return Jetstream::inertia()->render($request, 'PM/AuditActivity', [
          'team' => $request->user()->currentTeam,
          'clientAccount' => $client_account,
          'audits' => $all,
          'ruleId'=>$id,

        ]);
    }

}
