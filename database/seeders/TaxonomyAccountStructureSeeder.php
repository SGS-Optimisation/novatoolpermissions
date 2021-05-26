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
                            'ANY' => ['aliases' => [], 'default' => true,],
                            'Australia-AUS' => ['aliases' => ['AU', 'AU - NZ'], 'default' => false],
                            'Austria-AUT' => ['aliases' => ['AT'], 'default' => false],
                            'Azerbaijan-AZE' => ['aliases' => ['AZ'], 'default' => false],
                            'Belgium-BEL' => ['aliases' => ['BE'], 'default' => false],
                            'Belize-BLZ' => ['aliases' => ['BZ'], 'default' => false],
                            'Bosnia and Herzegovina-BIH' => ['aliases' => ['BA'], 'default' => false],
                            'Brazil-BRA' => ['aliases' => ['BR'], 'default' => false],
                            'Bulgaria-BGR' => ['aliases' => ['BG'], 'default' => false],
                            'Canada-CAN' => ['aliases' => ['CA'], 'default' => false],
                            'Croatia-HRV' => ['aliases' => ['HR'], 'default' => false],
                            'Czech Republic-CZE' => ['aliases' => ['CZ'], 'default' => false],
                            'Estonia-EST' => ['aliases' => ['EE'], 'default' => false],
                            'Finland-FIN' => ['aliases' => ['FI'], 'default' => false],
                            'France-FRA' => ['aliases' => ['FR'], 'default' => false],
                            'Germany-DEU' => ['aliases' => ['DE'], 'default' => false],
                            'Hong Kong-HKG' => ['aliases' => ['HK'], 'default' => false],
                            'Hungary-HUN' => ['aliases' => ['HU'], 'default' => false],
                            'Indonesia-IDN' => ['aliases' => ['ID'], 'default' => false],
                            'Ireland-IRL' => ['aliases' => ['IE'], 'default' => false],
                            'Israel-ISR' => ['aliases' => ['IL'], 'default' => false],
                            'Italy-ITA' => ['aliases' => ['IT'], 'default' => false],
                            'Japan-JPN' => ['aliases' => ['JP'], 'default' => false],
                            'Kazakstan-KAZ' => ['aliases' => ['KZ'], 'default' => false],
                            'Latvia-LVA' => ['aliases' => ['LV'], 'default' => false],
                            'Lebanon-LBN' => ['aliases' => ['LB'], 'default' => false],
                            'Lithuania-LTU' => ['aliases' => ['LT'], 'default' => false],
                            'Malaysia-MYS' => ['aliases' => ['MY'], 'default' => false],
                            'Mexico-MEX' => ['aliases' => ['MX'], 'default' => false],
                            'Netherlands-NLD' => ['aliases' => ['NL'], 'default' => false],
                            'New Zealand-NZL' => ['aliases' => ['AU - NZ', 'NZ'], 'default' => false],
                            'Norway-NOR' => ['aliases' => ['NO'], 'default' => false],
                            'Palestine-PSE' => ['aliases' => ['PS'], 'default' => false],
                            'Peru-PER' => ['aliases' => ['PE'], 'default' => false],
                            'Philippines-PHL' => ['aliases' => ['PH'], 'default' => false],
                            'Poland-POL' => ['aliases' => ['PL'], 'default' => false],
                            'Portugal-PRT' => ['aliases' => ['PT'], 'default' => false],
                            'Romania-ROM' => ['aliases' => ['RO'], 'default' => false],
                            'Russia-RUS' => ['aliases' => ['RU'], 'default' => false],
                            'Saudi Arabia-SAU' => ['aliases' => ['SA'], 'default' => false],
                            'Serbia-SRB' => ['aliases' => ['RS'], 'default' => false],
                            'Singapore-SGP' => ['aliases' => ['SG'], 'default' => false],
                            'Slovakia-SVK' => ['aliases' => ['SK'], 'default' => false],
                            'Slovenia-SVN' => ['aliases' => ['SL', 'SI'], 'default' => false],
                            'South Africa-ZAF' => ['aliases' => ['ZA'], 'default' => false],
                            'South Korea-KOR' => ['aliases' => ['KR'], 'default' => false],
                            'Spain-ESP' => ['aliases' => ['ES'], 'default' => false],
                            'Sweden-SWE' => ['aliases' => ['SE'], 'default' => false],
                            'Switzerland-CHE' => ['aliases' => ['CH'], 'default' => false],
                            'Taiwan-TWN' => ['aliases' => ['TW'], 'default' => false],
                            'Thailand-THA' => ['aliases' => ['TH'], 'default' => false],
                            'Turkey-TUR' => ['aliases' => ['TR'], 'default' => false],
                            'Ukraine-UKR' => ['aliases' => ['UA'], 'default' => false],
                            'United Kingdom-GBR' => ['aliases' => ['GB', 'UK'], 'default' => false],
                            'United States-USA' => ['aliases' => ['USA'], 'default' => false],
                            'Venezuela-VEN' => ['aliases' => ['VE'], 'default' => false],
                            'Vietnam-VNM' => ['aliases' => ['VN'], 'default' => false],
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
