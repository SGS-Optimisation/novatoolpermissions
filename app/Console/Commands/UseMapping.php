<?php

namespace App\Console\Commands;

use App\Models\FieldMapping;
use App\Models\Job;
use App\Services\MySgs\Api\EloquentHelpers\JobFieldsMapper;
use Illuminate\Console\Command;

class UseMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fieldmap {jobid} {fieldmappingid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the value from using a field mapping on a job';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job = Job::find($this->argument('jobid'));
        $mapping = FieldMapping::find($this->argument('fieldmappingid'));

        list($value, $raw) = (new JobFieldsMapper($job, $mapping))->run();

        $this->info('mapped value: ' . $value);
        $this->info('raw value: ' . print_r($raw, true));

        return 0;
    }
}
