<?php


namespace App\Services\ClientAccounts;


use App\Legacy\Models\Projet;
use App\Models\ClientAccount;

class LegacyImport
{

    public static function handle(){
        ClientAccount::insert(
            Projet::select(['Name', 'Logo', 'Designations', 'Categorizations'])->get()->map(function($item){
                return [
                    'name' => $item->Name,
                    'image' => $item->Logo,
                    'legacy_id' => $item->_id
                ];
            })->toArray()
        );
    }

}
