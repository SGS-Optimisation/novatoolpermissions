<?php


namespace App\Services\MySgs;

use App\Models\ClientAccount;
use App\Models\Job;
use App\Services\Job\JobApiCaller;
use Illuminate\Support\Str;

class DataLoader
{

    /**
     * @param Job $job
     */
    public static function handle($job) {
        $basicInfo = (new JobApiCaller($job))->handle('JobApi', 'basicInfo');
        $basicDetails = (new JobApiCaller($job))->handle('JobApi', 'basicDetails');
        $extraDetails = (new JobApiCaller($job))->handle('JobApi', 'extraDetails');
        $jobItems = (new JobApiCaller($job))->handle('ProductionApi', 'jobItems');

        $job->designation = $basicInfo->response->jobDescription;
        $job_metadata = $job->metadata;
        $job_metadata->processing_mysgs = false;

        try {
            $customer_name = $job->metadata->basicDetails->retailer->customerName;

            $client = ClientAccount::where('name', 'LIKE', '%'.$customer_name.'%')
                ->orWhereRaw('LOWER(alias) LIKE "%'.Str::lower($job->metadata->basicDetails->retailer->customerName).'%"')
                ->first();

            if($client) {
                $job_metadata->client = $client->only(['id', 'name', 'slug', 'image']);
                $job_metadata->client_found = true;
            } else {
                $job_metadata->client_found = false;
                $job_metadata->client = ['name' => $customer_name];
            }

        } catch (\Exception $e) {
            \Log::error('could not deduce client');
            $job_metadata->client = ['name' => $customer_name];
            $job_metadata->client_found = false;
        }

        $job->metadata = $job_metadata;
        $job->save();
    }
}
