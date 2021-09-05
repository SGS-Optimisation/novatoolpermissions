<?php


namespace App\Operations\Jobs;


use App\Models\FieldMapping;
use App\Models\Job;
use App\Models\Taxonomy;
use App\Operations\BaseOperation;
use App\Services\MySgs\Api\EloquentHelpers\JobFieldsMapper;

class GetStageOperation extends BaseOperation
{
    public function __construct(public Job $job)
    {

    }

    public function handle()
    {
        $mapping = Taxonomy::firstWhere('name', nova_get_setting('stage_taxonomy_name', 'Stage'))
            ->mapping;

        list($value, $raw) = (new JobFieldsMapper($this->job, $mapping))->run();

        $metadata = $this->job->metadata;

        $metadata->stages = $value;

        $this->job->metadata = $metadata;
        $this->job->save();
    }

}
