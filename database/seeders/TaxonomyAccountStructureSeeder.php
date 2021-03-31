<?php

namespace Database\Seeders;

use App\Services\LegacyImport\Taxonomy;
use App\Services\Taxonomy\Traits\TaxonomyCreationHelper;
use Illuminate\Database\Seeder;

class TaxonomyAccountStructureSeeder extends Seeder
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
            'Account Structure' => [
                'children' => [
                    'Brand' => [
                        'meta' => [
                            'aliases' => ['BRAND', 'Brand', 'Brand (Sub)', 'Brand - Sub'],
                        ],
                        'terms' => [
                            'ANY'
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'basicDetails',
                                'field_path' => 'brand',
                            ],
                        ]
                    ],
                    'Category/Business Unit' => [
                        'terms' => [
                            'ANY'
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'extraDetails',
                                'field_path' => '0.extraDetailsFields.*[fieldName=02_Category|fieldName=Category].itemValue'
                            ]
                        ]
                    ],
                    'Pack Type' => [
                        'meta' => [
                            'aliases' => [
                                'PACKAGING TYPE',
                                'Type de pack',
                                'TYPE OF PACK',
                                'Pack',
                                'Component',
                                'Components'
                            ]
                        ],
                        'terms' => [
                            'ANY',
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'extraDetails',
                                'field_path' => '0.extraDetailsFields.*[fieldName=Packaging_Component_Type|fieldName=10_PackagingFormat].itemValue'
                            ],
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'basicDetails',
                                'field_path' => 'packageType.name'
                            ]
                        ]
                    ],
                    'Region/Market' => [
                        'meta' => [
                            'aliases' => ['Market', 'REGION/MARKET', 'Region'],
                        ],
                        'terms' => [
                            'ANY',
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'extraDetails',
                                'field_path' => '0.extraDetailsFields.*[fieldName=Market].itemValue',
                            ]
                        ]
                    ],
                    'Printer' => [
                        'terms' => [
                            'ANY'
                        ],
                        'mapping' => [
                            // TODO: find MYSGS API for this
                        ]
                    ],
                    'Production site/Factory' => [
                        'meta' => [
                            'aliases' => ['Production Site', 'PRODUCTION SITE/FACTORY', 'FACTORY'],
                        ],
                        'terms' => [
                            'ANY'
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'extraDetails',
                                'field_path' => '0.extraDetailsFields.*[fieldName=Production_Factory_Site].itemValue',
                            ]
                        ]
                    ],
                    'Weight' => [
                        'terms' => [
                            'ANY'
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'basicDetails',
                                'field_path' => 'weight',
                            ]
                        ]
                    ],
                    'Variety' => [
                        'terms' => [
                            'ANY'
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'basicDetails',
                                'field_path' => 'variety',
                            ]
                        ]
                    ],
                    'Production Stage' => [
                        'terms' => [
                            'ANY'
                        ],
                        'mapping' => [
                            [
                                'api_name' => 'ProductionApi',
                                'api_version' => '1.0',
                                'api_action' => 'jobItems',
                                'field_path' => '*.jobItemDescription',
                                'resolver_name' => 'ProductionStageResolver',
                            ]
                        ]
                    ],
                ],
            ],
        ];

        $default_vocab_config = ['default' => true];
        $default_term_config = ['default' => true];

        static::processTaxonomies($taxonomies, $default_vocab_config, $default_term_config);

        (new Taxonomy)->handle();
    }
}
