<?php

namespace App\Console\Commands;

use App\Services\Term\CreateTermService;
use Illuminate\Console\Command;

class AddTerm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'term:create:single {clientName} {taxonomy} {term}
                            {--auto-aliasing :  Automatically create aliases from slashes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a term';

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
        try {
            list($term, $restored) = (new CreateTermService(
                $this->argument('clientName'),
                $this->argument('taxonomy'),
                $this->argument('term'),
                $this->option('auto-aliasing')
            ))->handle();

            $this->info('term '.$term->name.($restored ? ' restored' : ' created').' with id '.$term->id);

        } catch (\TypeError $te) {
            $this->error('client '.$this->argument('clientName').' or taxonomy '.$this->argument('taxonomy').' not found');
        } catch (\Exception $e) {
            logger($e->getTraceAsString());
            $this->error('failed creating term '.$this->argument('term'));
        }

        return 0;
    }
}
