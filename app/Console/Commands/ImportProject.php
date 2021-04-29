<?php

namespace App\Console\Commands;

use App\Legacy\Models\Projet;
use App\Services\LegacyImport\ClientAccountLegacyImport;
use Illuminate\Console\Command;

class ImportProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a legacy client account from MongoDB';

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
    {;
        $project_names = Projet::pluck('Name')->sort()->all();
        $project_names = array_combine($project_names, $project_names);

        $selection = $this->menu('Select Client Account', $project_names)
            ->setWidth(80)
            ->open();

        (new ClientAccountLegacyImport($selection))->handle();
        $this->info('Import finished: ' . $selection);
        return 0;
    }
}
