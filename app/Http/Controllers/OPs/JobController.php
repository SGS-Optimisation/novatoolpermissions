<?php

namespace App\Http\Controllers\OPs;

use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Models\Job;
use App\Repositories\JobRepository;
use App\Services\Jobs\RuleFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $rules = null;
        $job = JobRepository::findByJobNumber($jobNumber);

        if (!$job->metadata->processing_mysgs && !$job->metadata->error_mysgs) {
            logger('mysgs data available for '.$jobNumber);
            $rules = RuleFilter::handle($job);
        }

        $data = [
            'jobNumber' => $jobNumber,
            'job' => $job,
            'rules' => $rules
        ];

        if ($job->clientAccount) {
            $data['stages'] = optional($job->clientAccount)->stages();
        }

        return $request->wantsJson() ?
            new JsonResponse($data, 200)
            : Jetstream::inertia()->render($request, 'OP/JobRules', $data);
    }

    public function showWithClientAccount(Request $request, $client_slug, $jobNumber)
    {
        logger("forcing job number $jobNumber with client $client_slug");
        Cache::forget('rules-job-'.$jobNumber);
        $clientAccount = ClientAccount::whereSlug($client_slug)->first();

        $job = Job::whereJobNumber($jobNumber)->first();
        $job->client_account_id = $clientAccount->id;
        $job->load('clientAccount');

        $rules = RuleFilter::handle($job, $clientAccount->id);

        $data = [
            'jobNumber' => $jobNumber,
            'job' => $job,
            'rules' => $rules,
            'forcedAccount' => $client_slug,
            'stages' =>  optional($clientAccount)->stages()
        ];

        return $request->wantsJson() ?
            new JsonResponse($data, 200)
            : Jetstream::inertia()->render($request, 'OP/JobRules', $data);
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
