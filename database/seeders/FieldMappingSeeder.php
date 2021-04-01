<?php

namespace Database\Seeders;

use App\Models\FieldMapping;
use App\Models\Taxonomy;
use Illuminate\Database\Seeder;

class FieldMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bootstrap = [
            [
                'names' => [
                    'Component',
                    'Components',
                    'Pack Type',
                    'PACKAGING TYPE',
                    'Type de pack',
                    'TYPE OF PACK',
                    'Pack'
                ],
                //'api_action' => 'basicDetails',
                //'field_path' => 'packageType.name',
                'api_action' => 'extraDetails',
                'field_path' => '0.extraDetailsFields.*[fieldName=Packaging_Component_Type].itemValue'
            ],

            [
                'names' => ['Production Site', 'PRODUCTION SITE/FACTORY', 'FACTORY'],
                'api_action' => 'extraDetails',
                'field_path' => '0.extraDetailsFields.*[fieldName=Production_Factory_Site].itemValue',
            ],

            [
                'names' => ['Market', 'REGION/MARKET', 'Region'],
                'api_action' => 'extraDetails',
                'field_path' => '0.extraDetailsFields.*[fieldName=Market].itemValue',
            ],


            [
                'names' => ['BRAND', 'Brand', 'Brands', 'Brand (Sub)', 'Brand - Sub',],
                'api_action' => 'basicDetails',
                'field_path' => 'brand',
            ],

        ];

        foreach ($bootstrap as $mappingSet) {
            foreach ($mappingSet['names'] as $name) {

                $taxonomy = Taxonomy::where(['name' => $name])->first();

                if ($taxonomy)
                    FieldMapping::firstOrCreate([
                        'api_name' => 'JobApi',
                        'api_version' => '1.0',
                        'api_action' => $mappingSet['api_action'],
                        'field_path' => $mappingSet['field_path'],
                        'taxonomy_id' => $taxonomy->id,
                    ]);
            }
        }
    }
}
