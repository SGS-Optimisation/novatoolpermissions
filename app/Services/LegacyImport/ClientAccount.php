<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Projet;
use App\Services\BaseService;

class ClientAccount extends BaseService
{

    public function handle(){
        $projets = Projet::select(['Name', 'Logo', 'Designations', 'Categorizations'])->get()->map(function($item){
            return [
                'name' => $item->Name,
                'image' => $item->Logo,
                'legacy_id' => $item->_id
            ];
        })->toArray();

        foreach($projets as $projet){
            \App\Models\ClientAccount::create($projet);
        }
    }

}
