<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Projet;
use App\Services\BaseService;

class ClientAccount extends BaseService
{

    public function handle(){
        Projet::select(['Name', 'Logo'])->get()->each(function($item){
            $projet = [
                'name' => $item->Name,
                'image' => $item->Logo,
                'legacy_id' => $item->_id
            ];

            \App\Models\ClientAccount::create($projet);
        });

        //        foreach($projets as $projet){
        //            $client = \App\Models\ClientAccount::whereRaw('LOWER(alias) LIKE "%'.$projet['name'].'%"')->first();
        //            if($client) {
        //                $client->image = $projet['image'];
        //                $client->legacy_id = $projet['legacy_id'];
        //                $client->save();
        //            } else {
        //                \App\Models\ClientAccount::create($projet);
        //            }
        //        }
    }

}
