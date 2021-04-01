<?php

namespace Database\Seeders;

use App\Services\LegacyImport\TaxonomyLegacyImport;
use App\Services\Taxonomy\Traits\TaxonomyCreationHelper;
use Illuminate\Database\Seeder;

class TaxonomyJobCategoizationsSeeder extends Seeder
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
                    'Artwork Identification Panel (AIP/Shirtail/Header/Legend)' => [
                        'terms' => [
                            'General information',
                            'Technical information',
                            'Colors'
                        ]
                    ],
                    'Technical Drawing' => [
                        'terms' => [
                            'Specs (swatch color, stroke size/attributes)',
                            'Layer management',
                            'Legend/zones identification',
                            'Diecut elements (eyemarks/coding areas/printfree areas/step and repeat/print direction)'
                        ]
                    ],
                    'Mechanical	Artboards' => [
                        'terms' => [
                            'Layers',
                            'Separation',
                            'Links',
                            'Layout',
                        ]
                    ],
                    'Technical/printer specifications' => [
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
