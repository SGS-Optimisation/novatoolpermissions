<?php


namespace App\Services\ClientAccounts;


use App\Models\ClientAccount;
use App\Models\Taxonomy;

class AssociateDefaultVocabulary
{
    public ClientAccount $clientAccount;


    public function __construct(ClientAccount $clientAccount)
    {
        $this->clientAccount = $clientAccount;
    }


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
