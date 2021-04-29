<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Projet;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ClientAccountLegacyImport extends BaseService
{
    public $client_name;

    /**
     * ClientAccountLegacyImport constructor.
     * @param $client_name
     */
    public function __construct($client_name = null)
    {
        $this->client_name = $client_name;
    }


    public function handle()
    {
        $projects = Projet::select(['Name', 'Logo'])
            ->where('SoftDeleted', false)
            ->when($this->client_name, function($query) {
                return $query->whereIn(
                    'Name',
                    array_map('trim', explode(',', $this->client_name))
                );
            })
            ->get();

        logger('found ' . count($projects) . ' matching');

        $projects->each(function ($item) {
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
