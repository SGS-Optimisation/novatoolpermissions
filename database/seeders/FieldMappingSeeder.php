<?php

namespace Database\Seeders;

use App\Models\FieldMapping;
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
        FieldMapping::insert([
            [
                'api_name' => 'JobApi',
                'api_version' => '1.0',
                'api_action' => 'basicDetails',
                'field_path' => 'packageType.name',
                'taxonomy_id' => '25'
            ],
            [
                'api_name' => 'JobApi',
                'api_version' => '1.0',
                'api_action' => 'extraDetails',
                'field_path' => '0.extraDetailsFields.*[fieldName=Production_Factory_Site].itemValue',
                'taxonomy_id' => '5'
            ]
        ]);
    }
}
