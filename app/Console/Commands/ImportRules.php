<?php

namespace App\Console\Commands;

use App\Legacy\Models\Projet;
use App\Services\LegacyImport\RuleLegacyImport;
use Illuminate\Console\Command;

class ImportRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:rules {name?}';

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
        if (!($name = $this->argument('name'))) {
            $project_names = Projet::pluck('Name')->sort()->all();
            $project_names = array_combine($project_names, $project_names);

            $name = $this->menu('Select Client Account', $project_names)
                ->setWidth(80)
                ->open();
        }

        $importer = (new RuleLegacyImport($name))->handle();

        $this->info(count($importer->imported_rules).' rules imported');

        if ($importer->num_problems) {
            $this->warn(count($importer->imported_rules).' rules has issues');

        }
        $this->info('Download report: '.asset('storage/'.$importer->problem_file_name));

        return 0;
    }
}
