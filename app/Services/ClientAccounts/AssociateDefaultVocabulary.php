<?php


namespace App\Services\ClientAccounts;


use App\Models\ClientAccount;
use App\Models\Taxonomy;

class AssociateDefaultVocabulary extends BaseClientAccountService
{

    public function handle(){
        $vocabularies = Taxonomy::whereJsonContains('config', ['default' => true])->get();


        $vocabularies->each(function($vocabulary) {
            $this->clientAccount->taxonomies()->attach($vocabulary);

            $vocabulary->terms()
                ->whereJsonContains('config', ['default' => true])
                ->get()
                ->each(function($term){
                    $this->clientAccount->terms()->attach($term);
                });
        });
    }
}
