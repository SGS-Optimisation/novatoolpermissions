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
                            'Afghanistan' => ['aliases' => ['AFG'], 'default' => false],
                            'Albania' => ['aliases' => ['ALB'], 'default' => false],
                            'Algeria' => ['aliases' => ['DZA'], 'default' => false],
                            'American Samoa' => ['aliases' => ['ASM'], 'default' => false],
                            'Andorra' => ['aliases' => ['AND'], 'default' => false],
                            'Angola' => ['aliases' => ['AGO'], 'default' => false],
                            'Anguilla' => ['aliases' => ['AIA'], 'default' => false],
                            'Antarctica' => ['aliases' => ['ATA'], 'default' => false],
                            'Antigua and Barbuda' => ['aliases' => ['ATG'], 'default' => false],
                            'Argentina' => ['aliases' => ['ARG'], 'default' => false],
                            'Armenia' => ['aliases' => ['ARM'], 'default' => false],
                            'Aruba' => ['aliases' => ['ABW'], 'default' => false],
                            'Australia' => ['aliases' => ['AUS'], 'default' => false],
                            'Austria' => ['aliases' => ['AUT'], 'default' => false],
                            'Azerbaijan' => ['aliases' => ['AZE'], 'default' => false],
                            'Bahamas' => ['aliases' => ['BHS'], 'default' => false],
                            'Bahrain' => ['aliases' => ['BHR'], 'default' => false],
                            'Bangladesh' => ['aliases' => ['BGD'], 'default' => false],
                            'Barbados' => ['aliases' => ['BRB'], 'default' => false],
                            'Belarus' => ['aliases' => ['BLR'], 'default' => false],
                            'Belgium' => ['aliases' => ['BEL'], 'default' => false],
                            'Belize' => ['aliases' => ['BLZ'], 'default' => false],
                            'Benin' => ['aliases' => ['BEN'], 'default' => false],
                            'Bermuda' => ['aliases' => ['BMU'], 'default' => false],
                            'Bhutan' => ['aliases' => ['BTN'], 'default' => false],
                            'Bolivia' => ['aliases' => ['BOL'], 'default' => false],
                            'Bosnia and Herzegovina' => ['aliases' => ['BIH'], 'default' => false],
                            'Botswana' => ['aliases' => ['BWA'], 'default' => false],
                            'Brazil' => ['aliases' => ['BRA'], 'default' => false],
                            'British Indian Ocean Territory' => ['aliases' => ['IOT'], 'default' => false],
                            'British Virgin Islands' => ['aliases' => ['VGB'], 'default' => false],
                            'Brunei' => ['aliases' => ['BRN'], 'default' => false],
                            'Bulgaria' => ['aliases' => ['BGR'], 'default' => false],
                            'Burkina Faso' => ['aliases' => ['BFA'], 'default' => false],
                            'Burundi' => ['aliases' => ['BDI'], 'default' => false],
                            'Cambodia' => ['aliases' => ['KHM'], 'default' => false],
                            'Cameroon' => ['aliases' => ['CMR'], 'default' => false],
                            'Canada' => ['aliases' => ['CAN'], 'default' => false],
                            'Cape Verde' => ['aliases' => ['CPV'], 'default' => false],
                            'Caricam' => ['aliases' => ['CCA'], 'default' => false],
                            'Cayman Islands' => ['aliases' => ['CYM'], 'default' => false],
                            'Central African Republic' => ['aliases' => ['CAF'], 'default' => false],
                            'Central Eastern European' => ['aliases' => ['CEE'], 'default' => false],
                            'Chad' => ['aliases' => ['TCD'], 'default' => false],
                            'Chile' => ['aliases' => ['CHL'], 'default' => false],
                            'China' => ['aliases' => ['CHN'], 'default' => false],
                            'Christmas Island' => ['aliases' => ['CXR'], 'default' => false],
                            'Cocos Islands' => ['aliases' => ['CCK'], 'default' => false],
                            'Colombia' => ['aliases' => ['COL'], 'default' => false],
                            'Comoros' => ['aliases' => ['COM'], 'default' => false],
                            'Congo' => ['aliases' => ['COG'], 'default' => false],
                            'Costa Rica' => ['aliases' => ['CRI'], 'default' => false],
                            'Cote Divoire' => ['aliases' => ['CIV'], 'default' => false],
                            'Croatia' => ['aliases' => ['HRV'], 'default' => false],
                            'Cuba' => ['aliases' => ['CUB'], 'default' => false],
                            'Cyprus' => ['aliases' => ['CYP'], 'default' => false],
                            'Czech Republic' => ['aliases' => ['CZE'], 'default' => false],
                            'Democratic Republic of the Congo' => ['aliases' => ['COD'], 'default' => false],
                            'Denmark' => ['aliases' => ['DNK'], 'default' => false],
                            'Djibouti' => ['aliases' => ['DJI'], 'default' => false],
                            'Dominica' => ['aliases' => ['DMA'], 'default' => false],
                            'Dominican Republic' => ['aliases' => ['DOM'], 'default' => false],
                            'East Africa' => ['aliases' => ['CAF'], 'default' => false],
                            'Ecuador' => ['aliases' => ['ECU'], 'default' => false],
                            'Egypt' => ['aliases' => ['EGY'], 'default' => false],
                            'El Salvador' => ['aliases' => ['SLV'], 'default' => false],
                            'Equatorial Guinea' => ['aliases' => ['GNQ'], 'default' => false],
                            'Eritrea' => ['aliases' => ['ERI'], 'default' => false],
                            'Estonia' => ['aliases' => ['EST'], 'default' => false],
                            'Ethiopia' => ['aliases' => ['ETH'], 'default' => false],
                            'Falkland Islands' => ['aliases' => ['FLK'], 'default' => false],
                            'Faroe Islands' => ['aliases' => ['FRO'], 'default' => false],
                            'Fiji' => ['aliases' => ['FJI'], 'default' => false],
                            'Finland' => ['aliases' => ['FIN'], 'default' => false],
                            'France' => ['aliases' => ['FRA'], 'default' => false],
                            'French Polynesia' => ['aliases' => ['PYF'], 'default' => false],
                            'Gabon' => ['aliases' => ['GAB'], 'default' => false],
                            'Gambia' => ['aliases' => ['GMB'], 'default' => false],
                            'Georgia' => ['aliases' => ['GEO'], 'default' => false],
                            'Germany' => ['aliases' => ['DEU'], 'default' => false],
                            'Ghana' => ['aliases' => ['GHA'], 'default' => false],
                            'Gibraltar' => ['aliases' => ['GIB'], 'default' => false],
                            'Greece' => ['aliases' => ['GRC'], 'default' => false],
                            'Greenland' => ['aliases' => ['GRL'], 'default' => false],
                            'Grenada' => ['aliases' => ['GRD'], 'default' => false],
                            'Guam' => ['aliases' => ['GUM'], 'default' => false],
                            'Guatemala' => ['aliases' => ['GTM'], 'default' => false],
                            'Guinea Bissau' => ['aliases' => ['GNB'], 'default' => false],
                            'Guinea' => ['aliases' => ['GIN'], 'default' => false],
                            'Guyana' => ['aliases' => ['GUY'], 'default' => false],
                            'Haiti' => ['aliases' => ['HTI'], 'default' => false],
                            'Honduras' => ['aliases' => ['HND'], 'default' => false],
                            'Hong Kong' => ['aliases' => ['HKG'], 'default' => false],
                            'Hungary' => ['aliases' => ['HUN'], 'default' => false],
                            'Iceland' => ['aliases' => ['ISL'], 'default' => false],
                            'India' => ['aliases' => ['IND'], 'default' => false],
                            'Indonesia' => ['aliases' => ['IDN'], 'default' => false],
                            'Iran' => ['aliases' => ['IRN'], 'default' => false],
                            'Iraq' => ['aliases' => ['IRQ'], 'default' => false],
                            'Ireland' => ['aliases' => ['IRL'], 'default' => false],
                            'Islamic Republic of Iran' => ['aliases' => ['IRN'], 'default' => false],
                            'Israel' => ['aliases' => ['ISR'], 'default' => false],
                            'Italy' => ['aliases' => ['ITA'], 'default' => false],
                            'Ivory Coast' => ['aliases' => ['CIV'], 'default' => false],
                            'Jamaica' => ['aliases' => ['JAM'], 'default' => false],
                            'Japan' => ['aliases' => ['JPN'], 'default' => false],
                            'Jordan' => ['aliases' => ['JOR'], 'default' => false],
                            'Kazakstan' => ['aliases' => ['KAZ'], 'default' => false],
                            'Kenya' => ['aliases' => ['KEN'], 'default' => false],
                            'Kiribati' => ['aliases' => ['KIR'], 'default' => false],
                            'Korea' => ['aliases' => ['KOR'], 'default' => false],
                            'Kosovo' => ['aliases' => ['KOS'], 'default' => false],
                            'Kuwait' => ['aliases' => ['KWT'], 'default' => false],
                            'Kyrgyzstan' => ['aliases' => ['KGZ'], 'default' => false],
                            'Laos' => ['aliases' => ['LAO'], 'default' => false],
                            'Latvia' => ['aliases' => ['LVA'], 'default' => false],
                            'Lebanon' => ['aliases' => ['LBN'], 'default' => false],
                            'Lesotho' => ['aliases' => ['LSO'], 'default' => false],
                            'Liberia' => ['aliases' => ['LBR'], 'default' => false],
                            'Libya' => ['aliases' => ['LBY'], 'default' => false],
                            'Libyan Arab Jamahiriya' => ['aliases' => ['LBY'], 'default' => false],
                            'Liechtenstein' => ['aliases' => ['LIE'], 'default' => false],
                            'Lithuania' => ['aliases' => ['LTU'], 'default' => false],
                            'Luxembourg' => ['aliases' => ['LUX'], 'default' => false],
                            'Macao' => ['aliases' => ['MAC'], 'default' => false],
                            'Macedonia' => ['aliases' => ['MKD'], 'default' => false],
                            'Madagascar' => ['aliases' => ['MDG'], 'default' => false],
                            'Malawi' => ['aliases' => ['MWI'], 'default' => false],
                            'Malaysia' => ['aliases' => ['MYS'], 'default' => false],
                            'Maldives' => ['aliases' => ['MDV'], 'default' => false],
                            'Mali' => ['aliases' => ['MLI'], 'default' => false],
                            'Malta' => ['aliases' => ['MLT'], 'default' => false],
                            'Marshall Islands' => ['aliases' => ['MHL'], 'default' => false],
                            'Mauritania' => ['aliases' => ['MRT'], 'default' => false],
                            'Mauritius' => ['aliases' => ['MUS'], 'default' => false],
                            'Mayotte' => ['aliases' => ['MYT'], 'default' => false],
                            'Mexico' => ['aliases' => ['MEX'], 'default' => false],
                            'Micronesia' => ['aliases' => ['FSM'], 'default' => false],
                            'Middle East' => ['aliases' => ['MEA'], 'default' => false],
                            'Moldova' => ['aliases' => ['MDA'], 'default' => false],
                            'Monaco' => ['aliases' => ['MCO'], 'default' => false],
                            'Mongolia' => ['aliases' => ['MNG'], 'default' => false],
                            'Montenegro' => ['aliases' => ['MNE'], 'default' => false],
                            'Montserrat' => ['aliases' => ['MSR'], 'default' => false],
                            'Morocco' => ['aliases' => ['MAR'], 'default' => false],
                            'Mozambique' => ['aliases' => ['MOZ'], 'default' => false],
                            'Multi' => ['aliases' => ['Mar'], 'default' => false],
                            'Myanmar' => ['aliases' => ['MMR'], 'default' => false],
                            'Namibia' => ['aliases' => ['NAM'], 'default' => false],
                            'Nauru' => ['aliases' => ['NRU'], 'default' => false],
                            'Nepal' => ['aliases' => ['NPL'], 'default' => false],
                            'Netherlands Antilles' => ['aliases' => ['ANT'], 'default' => false],
                            'Netherlands' => ['aliases' => ['NLD'], 'default' => false],
                            'New Caledonia' => ['aliases' => ['NCL'], 'default' => false],
                            'New Zealand' => ['aliases' => ['NZL'], 'default' => false],
                            'Nicaragua' => ['aliases' => ['NIC'], 'default' => false],
                            'Nigeria' => ['aliases' => ['NGA'], 'default' => false],
                            'Niger' => ['aliases' => ['NER'], 'default' => false],
                            'Niue' => ['aliases' => ['NIU'], 'default' => false],
                            'North Korea' => ['aliases' => ['PRK'], 'default' => false],
                            'Northern Mariana Islands' => ['aliases' => ['MNP'], 'default' => false],
                            'Norway' => ['aliases' => ['NOR'], 'default' => false],
                            'Oman' => ['aliases' => ['OMN'], 'default' => false],
                            'Pakistan' => ['aliases' => ['PAK'], 'default' => false],
                            'Palau' => ['aliases' => ['PLW'], 'default' => false],
                            'Palestine' => ['aliases' => ['PSE'], 'default' => false],
                            'Panama' => ['aliases' => ['PAN'], 'default' => false],
                            'Papua New Guinea' => ['aliases' => ['PNG'], 'default' => false],
                            'Paraguay' => ['aliases' => ['PRY'], 'default' => false],
                            'Peru' => ['aliases' => ['PER'], 'default' => false],
                            'Philippines' => ['aliases' => ['PHL'], 'default' => false],
                            'Pitcairn' => ['aliases' => ['PCN'], 'default' => false],
                            'Poland' => ['aliases' => ['POL'], 'default' => false],
                            'Portugal' => ['aliases' => ['PRT'], 'default' => false],
                            'Puerto Rico' => ['aliases' => ['PRI'], 'default' => false],
                            'Qatar' => ['aliases' => ['QAT'], 'default' => false],
                            'Reunion' => ['aliases' => ['REU'], 'default' => false],
                            'Romania' => ['aliases' => ['ROM'], 'default' => false],
                            'Russian Federation' => ['aliases' => ['RUS'], 'default' => false],
                            'Russia' => ['aliases' => ['RUS'], 'default' => false],
                            'Rwanda' => ['aliases' => ['RWA'], 'default' => false],
                            'Saint Helena' => ['aliases' => ['SHN'], 'default' => false],
                            'Saint Kitts and Nevis' => ['aliases' => ['KNA'], 'default' => false],
                            'Saint Lucia' => ['aliases' => ['LCA'], 'default' => false],
                            'Saint Pierre and Miquelon' => ['aliases' => ['SPM'], 'default' => false],
                            'Saint Vincent and the Grenadines' => ['aliases' => ['VCT'], 'default' => false],
                            'Samoa' => ['aliases' => ['WSM'], 'default' => false],
                            'San Marino' => ['aliases' => ['SMR'], 'default' => false],
                            'Sao Tome and Principe' => ['aliases' => ['STP'], 'default' => false],
                            'Saudi Arabia' => ['aliases' => ['SAU'], 'default' => false],
                            'Senegal' => ['aliases' => ['SEN'], 'default' => false],
                            'Serbia and Montenegro' => ['aliases' => ['SCG'], 'default' => false],
                            'Serbia' => ['aliases' => ['SRB'], 'default' => false],
                            'Seychelles' => ['aliases' => ['SYC'], 'default' => false],
                            'Sierra Leone' => ['aliases' => ['SLE'], 'default' => false],
                            'Singapore' => ['aliases' => ['SGP'], 'default' => false],
                            'Slovakia' => ['aliases' => ['SVK'], 'default' => false],
                            'Slovenia' => ['aliases' => ['SVN'], 'default' => false],
                            'Solomon Islands' => ['aliases' => ['SLB'], 'default' => false],
                            'Somalia' => ['aliases' => ['SOM'], 'default' => false],
                            'South Africa' => ['aliases' => ['ZAF'], 'default' => false],
                            'South Korea' => ['aliases' => ['KOR'], 'default' => false],
                            'Spain' => ['aliases' => ['ESP'], 'default' => false],
                            'Sri Lanka' => ['aliases' => ['LKA'], 'default' => false],
                            'Sudan' => ['aliases' => ['SDN'], 'default' => false],
                            'Suriname' => ['aliases' => ['SUR'], 'default' => false],
                            'Svalbard and Jan Mayen' => ['aliases' => ['SJM'], 'default' => false],
                            'Swaziland' => ['aliases' => ['SWZ'], 'default' => false],
                            'Sweden' => ['aliases' => ['SWE'], 'default' => false],
                            'Switzerland' => ['aliases' => ['CHE'], 'default' => false],
                            'Syrian Arab Republic' => ['aliases' => ['SYR'], 'default' => false],
                            'Taiwan' => ['aliases' => ['TWN'], 'default' => false],
                            'Tajikistan' => ['aliases' => ['TJK'], 'default' => false],
                            'Tanzania' => ['aliases' => ['TZA'], 'default' => false],
                            'Thailand' => ['aliases' => ['THA'], 'default' => false],
                            'Togo' => ['aliases' => ['TGO'], 'default' => false],
                            'Tokelau' => ['aliases' => ['TKL'], 'default' => false],
                            'Tonga' => ['aliases' => ['TON'], 'default' => false],
                            'Trinidad and Tobago' => ['aliases' => ['TTO'], 'default' => false],
                            'Tunisia' => ['aliases' => ['TUN'], 'default' => false],
                            'Turkey' => ['aliases' => ['TUR'], 'default' => false],
                            'Turkmenistan' => ['aliases' => ['TKM'], 'default' => false],
                            'Turks and Caicos Islands' => ['aliases' => ['TCA'], 'default' => false],
                            'Tuvalu' => ['aliases' => ['TUV'], 'default' => false],
                            'U.S. Virgin Islands' => ['aliases' => ['VIR'], 'default' => false],
                            'Uganda' => ['aliases' => ['UGA'], 'default' => false],
                            'UK Regulatory' => ['aliases' => ['GBR'], 'default' => false],
                            'Ukraine' => ['aliases' => ['UKR'], 'default' => false],
                            'United Arab Emirates' => ['aliases' => ['ARE'], 'default' => false],
                            'United Kingdom' => ['aliases' => ['GBR'], 'default' => false],
                            'United Republic of Tanzania' => ['aliases' => ['TZA'], 'default' => false],
                            'United States' => ['aliases' => ['USA'], 'default' => false],
                            'Uruguay' => ['aliases' => ['URY'], 'default' => false],
                            'Uzbekistan' => ['aliases' => ['UZB'], 'default' => false],
                            'Vanuatu' => ['aliases' => ['VUT'], 'default' => false],
                            'Vatican' => ['aliases' => ['VAT'], 'default' => false],
                            'Venezuela' => ['aliases' => ['VEN'], 'default' => false],
                            'Vietnam' => ['aliases' => ['VNM'], 'default' => false],
                            'Wallis and Futuna' => ['aliases' => ['WLF'], 'default' => false],
                            'Western Sahara' => ['aliases' => ['ESH'], 'default' => false],
                            'Yemen' => ['aliases' => ['YEM'], 'default' => false],
                            'Zambia' => ['aliases' => ['ZMB'], 'default' => false],
                            'Zimbabwe' => ['aliases' => ['ZWE'], 'default' => false],
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
