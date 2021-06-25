<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddTermsFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'term:create:mass {clientName} {taxonomy}
                            {--auto-aliasing : Automatically create aliases from slashes }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add multiple terms by reading from a file, 1 term per line. E.g term:create:mass UL Brand < myfile.txt';

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
        $this->info('client: '.$this->argument('clientName'));
        $this->info('taxonomy: '.$this->argument('taxonomy'));
        $terms_str = file_get_contents('php://stdin');

        $terms = explode("\n", $terms_str);

        foreach ($terms as $term) {
            $this->info('creating term '.$term);

            if (!empty($term)) {
                $this->call('term:create:single', [
                    'clientName' => $this->argument('clientName'),
                    'taxonomy' => $this->argument('taxonomy'),
                    'term' => $term,
                    '--auto-aliasing' => $this->option('auto-aliasing')
                ]);
            }
        }
    }
}
