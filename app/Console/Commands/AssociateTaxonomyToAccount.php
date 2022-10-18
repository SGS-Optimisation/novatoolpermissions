<?php

namespace App\Console\Commands;

use App\Models\ClientAccount;
use App\Models\Taxonomy;
use Illuminate\Console\Command;

class AssociateTaxonomyToAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:add:taxonomy {accountId?} {taxonomyId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a taxonomy to a client account, adding the default terms';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($accountId = !$this->argument('accountId')) {

            $accountId = $this->menu(
                'Select Client Account',
                ClientAccount::orderBy('name')->pluck('name', 'id')->toArray()
            )
                ->setWidth(80)
                ->open();
        }

        $clientAccount = ClientAccount::find($accountId);

        if ($taxonomyId = !$this->argument('taxonomyId')) {

            $taxonomyId = $this->menu(
                'Select Taxonomy',
                Taxonomy::children()->orderBy('name')->pluck('name', 'id')->toArray()
            )
                ->setWidth(80)
                ->open();
        }

        $taxonomy = Taxonomy::find($taxonomyId);

        $this->info($taxonomy->name.' selected to be added to '.$clientAccount->name);


        $clientAccount->taxonomies()->attach($taxonomy);

        $taxonomy->terms
            ->where('config.default', true)
            //->whereJsonContains('config', ['default' => true]) // BOOL FAILS IN SQLSRV
            ->each(function ($term) use ($clientAccount) {
                $clientAccount->terms()->attach($term);
            });

        $this->info(('done'));

        \Cache::tags('taxonomy')->forget($clientAccount->slug.'-taxonomy-usage-data');
        \Cache::tags('taxonomy')->forget($clientAccount->slug.'-rules-data');

        $this->info(('cache cleared'));

        return 0;
    }
}
