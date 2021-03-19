<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function history(Request $request)
    {
      $url =  $request->server("HTTP_REFERER");
      $url_array = explode('/', $url);
  if(array_key_exists(6, $url_array)){
      $id =  $url_array[6] ;
      $rule = Rule::find($id);
  }

      $all =  $rule->audits()->with('user')->get();

      return Jetstream::inertia()->render($request, 'Api/AuditActivity', [
            'audits' => $all,

        ]);
    }

}
