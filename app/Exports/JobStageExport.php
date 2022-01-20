<?php

namespace App\Exports;

use App\Models\Job;
use App\Services\MySgs\Api\Resolvers\ProductionStageResolver;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JobStageExport implements FromQuery, WithMapping, WithHeadings
{
    public function query()
    {
        return Job::whereBetween('created_at', [
            Carbon::now()->subDay()->startOfDay(),
            Carbon::now()->subDay()->endOfDay()
        ]);
    }

    /**
     * @param  Job  $job
     * @return array
     */
    public function map($job): array
    {
        $stage = 'Error loading stage';

        if (isset($job->metadata->jobItems)) {
            $stage = implode(', ', ProductionStageResolver::handle($job->metadata->jobItems));
        }

        $client = 'Not found';
        if ($job->client_account_id) {
            $client = $job->clientAccount->name;
        }

        return [
            $job->job_number,
            $client,
            $stage,
            $job->created_at->format('Y-m-d H:i')
        ];
    }

    public function headings(): array
    {
        return [
            'Job#',
            'Client',
            'Stage',
            'Datetime'
        ];
    }
}
