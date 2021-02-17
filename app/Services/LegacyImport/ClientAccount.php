<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Projet;
use App\Services\BaseService;

class ClientAccount extends BaseService
{

    public function handle(){
        \App\Models\ClientAccount::insert(
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
