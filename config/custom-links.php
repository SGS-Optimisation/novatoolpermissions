<?php

return [
    'links' => [

        /*
         * '{TEXT}' => [
         *      '_icon'   => '{ICON}',
         *      '_url'    => '{URL}',    # Optional if _links is present
         *      '_target' => '{TARGET}',
         *      '_links'  => [           # Optional if _url is present
         *          '{TEXT}' => [
         *              '_url'    => '{URL}',
         *              '_target' => '{TARGET}',
         *          ]
         *          '{TEXT}' => [
         *              '_url'    => '{URL}',
         *              '_target' => '{TARGET}',
         *          ]
         *      ]
         * ]
         */

        'Dagobah' => [
            '_links'  => [
                'Home' => [
                    '_url'    => '/',
                ],
                'Project Manager' => [
                    '_url'    => '/pm',
                    '_target' => '_self'
                ],
            ],
        ],

    ]
];
