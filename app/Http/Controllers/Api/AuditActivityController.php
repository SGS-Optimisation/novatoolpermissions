<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use Laravel\Jetstream\Jetstream;
//use Inertia\Inertia;

class AuditActivityController extends Controller
{

    public function index(Request $request)
    {

        $audits = Audit::with('user')
            ->orderBy('created_at', 'desc')->get();

//dd($audits);
        return Jetstream::inertia()->render($request, 'PM/AuditActivity', [
            'audits' => $audits,

        ]);

    }


}
