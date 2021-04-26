<?php

namespace Database\Seeders;

use App\Services\LegacyImport\TaxonomyLegacyImport;
use App\Services\Taxonomy\Traits\TaxonomyCreationHelper;
use Illuminate\Database\Seeder;

class TaxonomyJobCategorizationsSeeder extends Seeder
{

    use TaxonomyCreationHelper;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxonomies = [

            'Job Categorizations' => [
                'children' => [
                    'Artwork Structure Elements' => [
                        'terms' => [
                            'AIP' => [
                                'aliases' => ['Artwork Identification Panel', 'Shirtail', 'Header', 'Legend']
                            ],
                            'Tech Drawing' => [
                                'aliases' => ['Technical Drawing']
                            ],
                            'Mechanical' => [
                                'aliases' => ['Mechanical_1',],
                            ],
                            'Tech/printer specs' => [
                                'aliases' => ['Printer specifications', 'Technical specifications'],
                            ],
                            'Text' => [
                                'aliases' => []
                            ],
                            'Regulatory' => [
                                'aliases' => ['Regulatory_1',],
                            ],
                            'Visuals' => [
                                'aliases' => []
                            ],
                            'Deliverables' => [
                                'aliases' => []
                            ],
                        ]
                    ]
                ]
            ],


        ];

        $default_vocab_config = ['default' => true];
        $default_term_config = ['default' => true];

        static::processTaxonomies($taxonomies, $default_vocab_config, $default_term_config);


    }
}
