<?php

namespace App\Console\Commands;

use App\Models\ClientAccount;
use App\Repositories\RuleRepository;
use App\Services\Taxonomy\Traits\TaxonomyBuilder;
use Illuminate\Console\Command;

class WarmUpCache extends Command
{
    use TaxonomyBuilder;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warmup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm-up cache for client rules and configuration';

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
        $bar = $this->output->createProgressBar(ClientAccount::count());

        $bar->start();

        ClientAccount::chunk(5, function($chunk) use ($bar){
            foreach($chunk as $client_account) {
                $ruleRepo = new RuleRepository($client_account);
                $ruleRepo->all();

                $this->buildTaxonomyWithUsage($client_account);

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Cache is hot');
    }
}
