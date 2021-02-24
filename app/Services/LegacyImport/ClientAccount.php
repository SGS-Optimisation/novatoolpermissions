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
            $client = \App\Models\ClientAccount::whereRaw('LOWER(alias) LIKE "%'.$projet['name'].'%"')->first();
            if($client) {
                $client->image = $projet['image'];
                $client->legacy_id = $projet['legacy_id'];
                $client->save();
            } else {
                \App\Models\ClientAccount::firstOrCreate($projet);
            }
        }
    }

}
