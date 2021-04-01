<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Projet;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ClientAccountLegacyImport extends BaseService
{

    public function handle()
    {
        Projet::select(['Name', 'Logo'])
            ->where('SoftDeleted', false)
            ->get()
            ->each(function ($item) {
                $projet = [
                    'name' => $item->Name,
                    'slug' => Str::slug($item->Name),
                    'image' => $item->Logo,
                    'legacy_id' => $item->_id
                ];

                \App\Models\ClientAccount::firstOrcreate($projet);
            });
    }

}
