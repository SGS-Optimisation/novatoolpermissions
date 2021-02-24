<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class JobController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $jobNumber
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function index(Request $request, $jobNumber = null)
    {
        return Jetstream::inertia()->render($request, 'Dashboard', [
            'team' => $request->user()->currentTeam,
            'jobNumber' => $jobNumber
        ]);
    }
}
