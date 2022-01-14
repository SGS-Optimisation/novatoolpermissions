<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class AddJobsClientAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:fix-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $num_jobs = Job::where('metadata->client_found', true)
            ->whereNull('client_account_id')
            ->count();

        if ($num_jobs) {
            $this->info("Number of jobs to process: ".$num_jobs);
            $chunk_size = 50;
            $num_chunks = ceil($num_jobs / $chunk_size);
            $bar = $this->output->createProgressBar($num_chunks);

            $bar->start();

            Job::where('metadata->client_found', true)
                ->whereNull('client_account_id')
                ->chunk($chunk_size, function ($chunk) use ($bar) {
                    foreach ($chunk as $job) {
                        if ($job->metadata->client->id) {
                            $job->update(['client_account_id' => $job->metadata->client->id]);
                        }
                    }

                    $bar->advance();
                });
        }


        return 0;
    }
}
