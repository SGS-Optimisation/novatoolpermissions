<?php


namespace App\Operations\ClientAccounts;


use App\Models\ClientAccount;
use App\Operations\BaseOperation;

class AssociateDefaultTermsToAttachedTaxonomy extends BaseOperation
{

    /**
     * AssociateDefaultTermsToAttachedTaxonomy constructor.
     */
    public function __construct(public ClientAccount|int $clientAccount)
    {
        if(is_int($clientAccount)) {
            /** @var ClientAccount $clientAccount */
            $clientAccount = ClientAccount::with('taxonomies.terms')->find($clientAccount);
            $this->clientAccount = $clientAccount;
        }
    }

    public function handle()
    {
        foreach($this->clientAccount->taxonomies as $taxonomy) {

            $taxonomy->terms
                ->where('config.default', true)
                ->each(function($term){
                    $this->clientAccount->terms()->attach($term);
                });
        }

        \Cache::tags('taxonomy')->forget($this->clientAccount->slug.'-taxonomy-usage-data');
        \Cache::tags('taxonomy')->forget($this->clientAccount->slug.'-rules-data');
    }

}
