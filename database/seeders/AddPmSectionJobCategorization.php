<?php

namespace Database\Seeders;

use App\Services\Taxonomy\Traits\TaxonomyCreationHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddPmSectionJobCategorization extends Seeder
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
                    'PM Section Elements' => [
                        'terms' => [
                            'Client Portal and External Links' => [
                                'aliases' => []
                            ],
                            'Sourcing & Research' => [
                                'aliases' => []
                            ],
                            'Financials' => [
                                'aliases' => [],
                            ],
                            'Account Management' => [
                                'aliases' => [],
                            ],
                            'MySGS' => [
                                'aliases' => [],
                            ],
                            'Known Issues' => [
                                'aliases' => []
                            ],
                            'Task Templates' => [
                                'aliases' => []
                            ],
                            'Client Specific Requirements' => [
                                'aliases' => []
                            ],
                            'Job Folder & File Management' => [
                                'aliases' => []
                            ],
                            'Automation Requirements' => [
                                'aliases' => []
                            ],
                            'Shipping Requirements' => [
                                'aliases' => []
                            ],
                        ]
                    ]
                ]
            ],


        ];

        $default_vocab_config = ['default' => true, 'multiple' => false];
        $default_term_config = ['default' => true];

        static::processTaxonomies($taxonomies, $default_vocab_config, $default_term_config);


    }
}
