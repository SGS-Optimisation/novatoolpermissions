<?php

namespace App\Console\Commands;

use App\Models\Rule;
use App\Models\Taxonomy;
use Illuminate\Console\Command;

class RandomRuleJobCategorization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rule:jobcat:random';

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
        $jobCat = Taxonomy::where('name', 'Artwork Structure Elements')->first();

        $rules = Rule::whereDoesntHave('terms', function($query) use ($jobCat){
            return $query->where('taxonomy_id', $jobCat->id);
        })->get();

        $this->info('Rules concerned: ' . $rules->count());

        if ($this->confirm('Do you wish to continue?')) {

            $term_ids = $jobCat->terms->pluck('id');

            $rules->each(function(Rule $rule) use ($term_ids){
                $rule->terms()->attach($term_ids->random());
            });
        }
    }
}
