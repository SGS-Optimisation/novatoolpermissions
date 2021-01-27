<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TaxonomySeeder extends Seeder
{
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
                    'Legend' => [
                        'terms' => [
                            'General information',
                            'Technical information',
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
                    'Mechanical' => [
                        'terms' => [
                            'Layout',
                        ]
                    ],
                    'Regulatory' => [
                        'terms' => [
                            'Codes',
                            'Barcodes',
                            'Pictos and icons',
                            'EU Instructions',
                            'Generic Content',
                        ]
                    ],
                    /*'Header' => [
                        'terms' => [
                            'Bouty Code',
                            'Color sequence',
                            'Fogra',
                            'Manufacturing site',
                            'Production Site',
                            'Reference MKT',
                            'Site Component Code',
                        ]
                    ],*/
                ]
            ],

            'Account Structure' => [
                'Brands' => [
                    'terms' => [
                        'ANY'
                    ]
                ],
                'Market' => [
                    'terms' => [
                        'ANY'
                    ]
                ],
                'Production Site' => [
                    'terms' => [
                        'ANY'
                    ]
                ],
                'Category/BU' => [
                    'terms' => [
                        'ANY'
                    ]
                ],
                'Pack Type' => [
                    'terms' => [
                        'ANY',
                        'Back Label',
                        'Front Label',
                        'Shipper'
                    ]
                ],
            ]
        ];

        $default_config = ['default' => true];

        static::processTaxonomies($taxonomies, $default_config);
    }

    protected static function processTaxonomies($list, $config, $parent = null)
    {
        if (Arr::isAssoc($list)) {
            foreach ($list as $name => $items) {
                $taxonomy = static::buildTaxonomy($name, $config, $parent);

                if (Arr::has($items, 'children')) {
                    static::processTaxonomies($items['children'], $config, $taxonomy);
                }

                if (Arr::has($items, 'terms')) {
                    foreach ($items['terms'] as $term) {
                        static::buildTerm($term, $taxonomy);
                    }
                }

            }
        } else {
            foreach ($list as $name) {
                static::buildTaxonomy($name, $config, $parent);
            }
        }

    }

    /**
     * @param $name
     * @param $config
     * @param  Taxonomy|null  $parent
     * @return Taxonomy|\Illuminate\Database\Eloquent\Model
     */
    protected static function buildTaxonomy($name, $config, $parent = null)
    {
        $taxonomy_data = [
            'name' => $name,
            'config' => $config
        ];

        if($parent) {
            $taxonomy_data['parent_id'] = $parent->id;
        }

        return Taxonomy::firstOrCreate($taxonomy_data);
    }


    /**
     * @param $name
     * @param  Taxonomy  $taxonomy
     */
    protected static function buildTerm($name, $taxonomy)
    {
        $taxonomy->terms()->create(['name' => $name]);
    }
}
