<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportInfinityRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:infinity:rules {client} {folder} {--workspace=} {--board=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import rules from Infinity';

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
        (new \App\Features\Infinity\ImportRules(
            client: $this->argument('client'),
            folder: $this->argument('folder'),
            board: $this->option('board'),
            workspace: $this->option('workspace'),
        ))->handle();

        return 0;
    }
}
