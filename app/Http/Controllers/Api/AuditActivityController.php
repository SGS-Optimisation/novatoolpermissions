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
       // dd("queryString3");
        //$queryString = $request->getQueryString();
        $queryString2 = http_build_query($request->query());
        $queryString3 = http_build_query($request->all());
       // dd($queryString);
       // dd($queryString2);
        //dd($queryString3);
       //dd($request) ;

        $rule = Rule::find($request->query(0));

        $all =  $rule->audits()->with('user')->get();

        //$last = $article->audits()->latest()

// Get Audit by id
        //$audit = $article->audits()->find(4);

        return Jetstream::inertia()->render($request, 'Api/AuditActivity', [
            'audits' => $all,

        ]);
    }

}
