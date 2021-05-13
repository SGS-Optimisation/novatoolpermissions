<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Projet;
use App\Services\BaseService;
use Carbon\Carbon;
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
            $image_path = 'logos/'.Carbon::now()->format('Y-m-d').'/'.$item->Name;

            \Storage::put($image_path, \Storage::disk('local')->get('public/' . $item->Logo));

                $projet = [
                    'name' => $item->Name,
                    'slug' => Str::slug($item->Name),
                    'legacy_id' => $item->_id
                ];

                $ca = \App\Models\ClientAccount::firstOrcreate($projet);

                $ca->update([
                    'image' => \Storage::url($image_path),
                ]);


            });
    }

}
