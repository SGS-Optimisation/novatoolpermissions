<?php

namespace Database\Seeders;

use App\Services\LegacyImport\TaxonomyLegacyImport;
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
                        'config' => [
                            'aliases' => ['BRAND', 'Brand', 'Brand (Sub)', 'Brand - Sub', 'BRANDS', 'Brands'],
                        ],
                        'terms' => [
                            'ANY' => ['aliases' => []],
                            'FENISTIL' => [
                                'aliases' => ['Fenistil New Design', 'Fenistil Old design'],
                                'default' => false,
                            ],
                            'Otrivin' => [
                                'aliases' => ['Otrivin 2013', 'Otrivin 2017'],
                                'default' => false,
                            ],
                            'Voltaren' => [
                                'aliases' => ['Voltaren New Design', 'Voltaren Old design'],
                                'default' => false,
                            ]
                        ],
                        'mappings' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'basicDetails',
                                'field_path' => 'brand',
                            ],
                        ]
                    ],
                    'BU' => [
                        'config' => [
                            'aliases' => ['Category/Business Unit', 'Business Unit', 'Category'],
                        ],
                        'terms' => [
                            'ANY'
                        ],
                        'mappings' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'extraDetails',
                                'field_path' => '0.extraDetailsFields.*[fieldName=02_Category|fieldName=Category].itemValue'
                            ]
                        ]
                    ],
                    'Pack Type' => [
                        'config' => [
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
                            'ANY' => ['aliases' => []],
                            'Bag' => ['aliases' => []],
                            'Blister' => ['aliases' => ['FOIL']],
                            'Can/Bottle/Tube/Tin (Direct Print)' => ['aliases' => ['CAN', 'TUB']],
                            'Carton/Box' => ['aliases' => ['Carton/Box', 'CTN']],
                            'Display (Point of Sale)' => ['aliases' => []],
                            'Distortion/Shrink' => ['aliases' => []],
                            'Envelope' => ['aliases' => []],
                            'Hood' => ['aliases' => []],
                            'Label' => ['aliases' => ['LAB']],
                            'Leaflet' => ['aliases' => ['PIL']],
                            'Lid' => ['aliases' => []],
                            'Not Applicable' => ['aliases' => []],
                            'Poster' => ['aliases' => []],
                            'Sachet/Pouch' => ['aliases' => ['SAC']],
                            'Seal' => ['aliases' => []],
                            'Shipper' => ['aliases' => []],
                            'Sticker' => ['aliases' => []],
                            'Tag' => ['aliases' => []],
                            'TBC' => ['aliases' => []],
                            'Tray' => ['aliases' => []],
                            'Tub' => ['aliases' => []],
                            'Wrap/Overwrap (non-distorted)' => ['aliases' => []],
                        ],
                        'mappings' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'basicDetails',
                                'field_path' => 'packageType.name'
                            ],
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'extraDetails',
                                'field_path' => '0.extraDetailsFields.*[fieldName=Packaging_Component_Type|fieldName=10_PackagingFormat].itemValue'
                            ],
                        ]
                    ],
                    'Region/Market' => [
                        'config' => [
                            'aliases' => ['Market', 'REGION/MARKET', 'Region'],
                        ],
                        'terms' => [
                            'ANY',
                        ],
                        'mappings' => [
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
                        'mappings' => [
                            // TODO: find MYSGS API for this
                        ]
                    ],
                    'Factory' => [
                        'config' => [
                            'aliases' => ['Production Site', 'PRODUCTION SITE/FACTORY', 'FACTORY'],
                        ],
                        'terms' => [
                            'ANY'
                        ],
                        'mappings' => [
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
                        'mappings' => [
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
                        'mappings' => [
                            [
                                'api_name' => 'JobApi',
                                'api_version' => '1.0',
                                'api_action' => 'basicDetails',
                                'field_path' => 'variety',
                            ]
                        ]
                    ],
                    /*'Stage' => [
                        'config' => [
                            'aliases' => ['Production Stage'],
                        ],
                        'terms' => [
                            'ANY'
                        ],
                        'mappings' => [
                            [
                                'api_name' => 'ProductionApi',
                                'api_version' => '1.0',
                                'api_action' => 'jobItems',
                                'field_path' => '*.jobItemDescription',
                                'resolver_name' => 'ProductionStageResolver',
                            ]
                        ]
                    ],*/
                ],
            ],
        ];

        $default_vocab_config = ['default' => true];
        $default_term_config = ['default' => true];

        static::processTaxonomies($taxonomies, $default_vocab_config, $default_term_config);

    }
}
