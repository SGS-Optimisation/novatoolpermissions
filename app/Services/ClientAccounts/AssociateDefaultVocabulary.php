<?php


namespace App\Services\ClientAccounts;


use App\Models\ClientAccount;
use App\Models\Taxonomy;

class AssociateDefaultVocabulary extends BaseClientAccountService
{

    public function handle(){
        //$vocabularies = Taxonomy::whereJsonContains('config', ['default' => true])->get(); // BOOL FAILS IN SQLSRV
        $vocabularies = Taxonomy::all()->where('config.default', true);


        $vocabularies->each(function($vocabulary) {
            $this->clientAccount->taxonomies()->attach($vocabulary);

            $vocabulary->terms
                ->where('config.default', true)
                //->whereJsonContains('config', ['default' => true]) // BOOL FAILS IN SQLSRV
                ->each(function($term){
                    $this->clientAccount->terms()->attach($term);
                });
        });

        \Cache::tags('taxonomy')->forget($this->clientAccount->slug.'-taxonomy-usage-data');
        \Cache::tags('taxonomy')->forget($this->clientAccount->slug.'-rules-data');
    }
}
