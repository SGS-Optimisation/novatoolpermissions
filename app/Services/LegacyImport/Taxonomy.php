<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Projet;
use App\Models\ClientAccount;
use App\Services\BaseService;
use App\Services\Taxonomy\Traits\TaxonomyCreationHelper;

class Taxonomy extends BaseService
{

    use TaxonomyCreationHelper;

    public function handle()
    {

        Projet::select(['Designations', 'Categorizations'])->get()->each(function ($projet) {
            $client_account = ClientAccount::whereLegacyId($projet->_id)->first();

            collect($projet->Designations)->each(function ($designation) use ($client_account) {
                static::createAccountStructureTaxonomy($designation, $client_account);
            });

            collect($projet->Categorizations)->each(function ($categorizations) use ($client_account) {
                static::createJobCategorizationTaxonomy($categorizations, $client_account);
            });
        });

    }

}
