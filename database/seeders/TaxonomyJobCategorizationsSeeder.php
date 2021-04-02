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
                    'AIP' => [
                        'config' => [
                            'aliases' => ['Artwork Identification Panel', 'Shirtail', 'Header', 'Legend'],
                        ],
                        'terms' => [
                            'General information',
                            'Technical information',
                            'Colors'
                        ]
                    ],
                    'Tech Drawing' => [
                        'config' => [
                            'aliases' => ['Technical Drawing']
                        ],
                        'terms' => [
                            'Specs (swatch color, stroke size/attributes)',
                            'Layer management',
                            'Legend/zones identification',
                            'Diecut elements (eyemarks/coding areas/printfree areas/step and repeat/print direction)'
                        ]
                    ],
                    'Mechanical' => [
                        'config' => [
                            'aliases' => ['Mechanical_1',],
                        ],
                        'terms' => [
                            'Artboards',
                            'Layers',
                            'Separation',
                            'Links',
                            'Layout',
                        ]
                    ],
                    'Tech/printer specs' => [
                        'config' => [
                            'aliases' => ['Printer specifications', 'Technical specifications'],
                        ],
                        'terms' => [
                            'Ink coverage',
                            'Screen rulings/angles',
                            'Printer/project specific rules',
                            'Print process/sub proces',
                            'Distortion',
                        ],
                    ],
                    'Text' => [
                        'terms' => [
                            'Fonts activation (type/version)',
                            'Technical specs (size/thickness)',
                            'FIC/INCO - regulatory min font heights*',
                            'Marketing texts',
                            'Regulatory texts*',
                        ],
                    ],
                    'Regulatory' => [
                        'config' => [
                            'aliases' => ['Regulatory_1',],
                        ],
                        'terms' => [
                            'Codes',
                            'Barcodes',
                            'Logos and icons',
                            'Regulatory texts*',
                            'FIC/INCO - regulatory min font heights*',
                        ]
                    ],
                    'Visuals' => [
                        'terms' => [
                            'Image libraries',
                            'Do‘s and dont‘s rules',
                            'Image build rules/guidelines',
                            'Printer/project specific requirements',
                        ]
                    ],
                    'Deliverables' => [
                        'terms' => [
                            'Customer specificities',
                            'PDFs types (separated, without dieline, HR/LR)',
                            'Workflows/presets',
                            'Naming conventions',
                        ]
                    ],
                ]
            ],


        ];

        $default_vocab_config = ['default' => true];
        $default_term_config = ['default' => true];

        static::processTaxonomies($taxonomies, $default_vocab_config, $default_term_config);


    }
}
