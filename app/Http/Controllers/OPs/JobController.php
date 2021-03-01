<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use App\Services\Job\JobApiHandler;
use App\Services\Rule\RuleFilter;
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
        $rules = [];
        if($jobNumber){
            $job = JobApiHandler::handle($jobNumber, 'basicDetails');
            if ($job) {
                $rules = RuleFilter::handle($job);
            }
        }
        return Jetstream::inertia()->render($request, 'Dashboard', [
            'team' => $request->user()->currentTeam,
            'jobNumber' => $jobNumber,
            'rules' => $rules
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $jobNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $jobNumber)
    {
        $rules = [];
        if($jobNumber) {
            $job = JobApiHandler::handle($jobNumber, 'basicDetails');
            if ($job) {
                $rules = RuleFilter::handle($job);
            }
        }
        return response()->json($rules);
    }
}
