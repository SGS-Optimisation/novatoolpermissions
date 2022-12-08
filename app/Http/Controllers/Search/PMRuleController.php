<?php

namespace App\Http\Controllers\Search;

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

class PMRuleController
{

    public function showClient(Request $request, $client_slug)
    {
        $clientAccount = ClientAccount::whereSlug($client_slug)->first();

        $rules = $clientAccount->pmRules()->with([
            'accountStructureTerms.taxonomy.mappings',
            'jobCategorizationsTerms',
            'attachments'
        ])
            ->isPublished()->get();

        $data = [
            //'jobNumber' => $jobNumber,
            //'job' => $job,
            'rules' => $rules,
            'clientAccount' => $clientAccount,
            'stages' =>  optional($clientAccount)->stages()
        ];

        return $request->wantsJson() ?
            new JsonResponse($data, 200)
            : Jetstream::inertia()->render($request, 'PMRules', $data);
    }

    public function showJob(Request $request, $jobNumber)
    {
        $rules = null;
        $job = JobRepository::findByJobNumber($jobNumber);

        if (!$job->metadata->processing_mysgs && !$job->metadata->error_mysgs) {
            logger('mysgs data available for '.$jobNumber);
            $rules = RuleFilter::handle($job, RuleFilter::FILTER_MODE_PM);
        }

        $data = [
            'jobNumber' => $jobNumber,
            'job' => $job,
            'rules' => $rules
        ];

        if ($job->clientAccount) {
            $data['stages'] = optional($job->clientAccount)->stages();
            $data['clientAccount'] = $job->clientAccount;
        }

        return $request->wantsJson() ?
            new JsonResponse($data, 200)
            : Jetstream::inertia()->render($request, 'PMRules', $data);
    }
}
