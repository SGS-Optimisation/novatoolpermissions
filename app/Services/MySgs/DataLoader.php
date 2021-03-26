<?php


namespace App\Services\MySgs;


use App\Models\ClientAccount;
use App\Models\Job;
use App\Services\Job\JobApiHandler;
use Illuminate\Support\Str;

class DataLoader
{

    /**
     * @param Job $job
     */
    public static function handle($job) {
        $basicInfo = (new JobApiHandler($job))->handle('basicInfo');
        $basicDetails = (new JobApiHandler($job))->handle('basicDetails');
        $extraDetails = (new JobApiHandler($job))->handle('extraDetails');

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
