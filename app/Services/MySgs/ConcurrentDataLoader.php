<?php


namespace App\Services\MySgs;


use App\Services\MySgs\Api\BaseApi;
use App\Services\MySgs\Api\EloquentHelpers\MysgsApiCaller;
use App\Services\MySgs\Api\JobApi;
use App\Services\MySgs\Api\ProductionApi;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class ConcurrentDataLoader extends DataLoader
{

    public function handle()
    {
        logger('concurrent data loader for job ' . $this->job->job_number);
        /*
        * Essential as this returns the jobVersionId, required for various other endpoints
        */
        $basicInfo = (new MysgsApiCaller($this->job))->handle('JobApi', 'basicInfo');

        if (!$basicInfo->response) {
            static::fail('Job not found');
            return;
        }
        $jobVersionId = $this->job->metadata->basicInfo->jobVersionId;

        $http = BaseApi::buildRequest();

        $headers = [
            'Ocp-Apim-Subscription-Key' => nova_get_setting('subscription_key'),
            'Ocp-Apim-Trace' => 'true',
            'Cache-Control' => 'no-cache',
        ];
        $token = Cache::get('mysgs_token');

        $responses = $http->pool(fn(Pool $pool) => [

            $pool->as('basicDetails')->withHeaders($headers)->withToken($token)
                ->get(JobApi::basicDetailsRoute($jobVersionId)),

            $pool->as('extraDetails')->withHeaders($headers)->withToken($token)
                ->get(JobApi::extraDetailsRoute($jobVersionId)),

            $pool->as('jobContacts')->withHeaders($headers)->withToken($token)
                ->get(JobApi::jobContactsRoute($jobVersionId)),

            $pool->as('jobItems')->withHeaders($headers)->withToken($token)
                ->get(ProductionApi::jobItemsRoute($jobVersionId)),
        ]);

        $basicDetails = Cache::remember(JobApi::basicDetailsRoute($jobVersionId).print_r([], true), 3600,
            function () use ($responses) {
                return BaseApi::parseResponse($responses['basicDetails'], false);
            });

        $extraDetails = Cache::remember(JobApi::extraDetailsRoute($jobVersionId).print_r([], true),
            3600,
            function () use ($responses) {
                return BaseApi::parseResponse($responses['extraDetails'], false);
            });
        $jobContacts = Cache::remember(JobApi::jobContactsRoute($jobVersionId).print_r([], true), 3600,
            function () use ($responses) {
                return BaseApi::parseResponse($responses['jobContacts'], false);
            });
        $jobItems = Cache::remember(ProductionApi::jobItemsRoute($jobVersionId).print_r([], true), 3600,
            function () use ($responses) {
                return BaseApi::parseResponse($responses['jobItems'], false);
            });

        $job_metadata = $this->job->metadata;

        $job_metadata->basicDetails = $basicDetails;
        $job_metadata->extraDetails = $extraDetails;
        $job_metadata->jobContacts = $jobContacts;
        $job_metadata->jobItems = $jobItems;

        $this->job->metadata = $job_metadata;
        $this->job->save();

        static::augmentMeta($basicInfo, $basicDetails, $extraDetails, $jobItems);

    }
}
