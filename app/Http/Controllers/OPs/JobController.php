<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use App\Repositories\JobRepository;
use App\Services\Jobs\RuleFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $job = JobRepository::findByJobNumber($jobNumber);

        if (!$job->metadata->processing_mysgs && !$job->metadata->error_mysgs) {
            logger('mysgs data available for '.$jobNumber);
            $rules = RuleFilter::handle($job);
        }

        return $request->wantsJson() ?
            new JsonResponse([
                'rules' => $rules,
                'job' => $job,
                'processing_mysgs' => $job->metadata->processing_mysgs,
                'error_mysgs' => $job->metadata->error_mysgs,
            ], 200)
            : Jetstream::inertia()->render($request, 'OP/JobRules', [
                'team' => $request->user()->currentTeam,
                'jobNumber' => $jobNumber,
                'job' => $job,
                'rules' => $rules
            ]);
    }

    public function status(Request $request, $jobNumber)
    {
        $job = JobRepository::findByJobNumber($jobNumber);

        return new JsonResponse([
            'processing_mysgs' => $job->metadata->processing_mysgs,
            'error_mysgs' => $job->metadata->error_mysgs,
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
