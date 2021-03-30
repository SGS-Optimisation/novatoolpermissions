<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use App\Repositories\JobRepository;
use App\Services\Job\JobApiCaller;
use App\Services\Rule\RuleFilter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Laravel\Jetstream\Jetstream;

class JobController extends Controller
{

    /**
     * Display rules relevant for a job.
     *
     * @param  Request  $request
     * @param  null  $jobNumber
     * @return JsonResponse|\Inertia\Response
     */
    public function show(Request $request, $jobNumber)
    {
        $rules = [];

        //$job = JobApiHandler::handle($jobNumber, 'basicDetails');

        $job = JobRepository::findByJobNumber($jobNumber);

        if (!$job->metadata->processing_mysgs) {
            logger('already loaded mysgs data');
            $rules = RuleFilter::handle($job);
        }



        return $request->wantsJson() ?
            new JsonResponse(['rules' => $rules, 'job' => $job], 200)
            : Jetstream::inertia()->render($request, 'OP/JobRules', [
                'team' => $request->user()->currentTeam,
                'jobNumber' => $jobNumber,
                'job' => $job,
                'rules' => $rules
            ]);
    }

    /**
     * Helper to redirect to show.
     *
     * @param  Request  $request
     * @param  null  $jobNumber
     * @return RedirectResponse
     */
    public function search(Request $request, $jobNumber)
    {
        return \Redirect::route('job.rules', [$jobNumber]);
    }
}
