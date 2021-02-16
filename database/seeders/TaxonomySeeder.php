<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Services\Taxonomy\LegacyImport;
use App\Services\Taxonomy\Traits\TaxonomyHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TaxonomySeeder extends Seeder
{

    use TaxonomyHelper;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

//        if(app()->environment() === 'production') {
            /*
             * importing legacy data from mongo dump
             */
            LegacyImport::handle();
//        } else {
//            $taxonomies = [
//                'Job Categorizations' => [
//                    'children' => [
//                        'Legend' => [
//                            'terms' => [
//                                'General information',
//                                'Technical information',
//                            ]
//                        ],
//                        'Technical Drawing' => [
//                            'terms' => [
//                                'Specs (swatch color, stroke size/attributes)',
//                                'Layer management',
//                                'Legend/zones identification',
//                                'Diecut elements (eyemarks/coding areas/printfree areas/step and repeat/print direction)'
//                            ]
//                        ],
//                        'Mechanical' => [
//                            'terms' => [
//                                'Layout',
//                            ]
//                        ],
//                        'Regulatory' => [
//                            'terms' => [
//                                'Codes',
//                                'Barcodes',
//                                'Pictos and icons',
//                                'EU Instructions',
//                                'Generic Content',
//                            ]
//                        ],
//                    ]
//                ],
//
//                'Account Structure' => [
//                    'children' => [
//                        'Brands' => [
//                            'terms' => [
//                                'ANY'
//                            ]
//                        ],
//                        'Market' => [
//                            'terms' => [
//                                'ANY'
//                            ]
//                        ],
//                        'Production Site' => [
//                            'terms' => [
//                                'ANY'
//                            ]
//                        ],
//                        'Category/BU' => [
//                            'terms' => [
//                                'ANY'
//                            ]
//                        ],
//                        'Pack Type' => [
//                            'terms' => [
//                                'ANY',
//                                'Back Label',
//                                'Front Label',
//                                'Shipper'
//                            ]
//                        ],
//                    ],
//                ]
//            ];
//
//            $default_vocab_config = ['default' => true];
//            $default_term_config = ['default' => true];
//
//            static::processTaxonomies($taxonomies, $default_vocab_config, $default_term_config);
//        }

    }
}
