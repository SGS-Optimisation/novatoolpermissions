<?php

return [
    'default_cache_duration' => env('MYSGS_API_DEFAULT_CACHE_DURATION', 300),

    'task_status' => [
        [
            "taskStatusId" => 0,
            "taskStatusDescription" => "Not Started"
        ],
        [
            "taskStatusId" => 1,
            "taskStatusDescription" => "In Progress"
        ],
        [
            "taskStatusId" => 2,
            "taskStatusDescription" => "Complete"
        ],
        [
            "taskStatusId" => 3,
            "taskStatusDescription" => "Amends Required"
        ],
        [
            "taskStatusId" => 4,
            "taskStatusDescription" => "On Approval"
        ],
        [
            "taskStatusId" => 5,
            "taskStatusDescription" => "Approved"
        ],
        [
            "taskStatusId" => 6,
            "taskStatusDescription" => "On Hold"
        ],
        [
            "taskStatusId" => 7,
            "taskStatusDescription" => "Abandoned"
        ],
        [
            "taskStatusId" => 254,
            "taskStatusDescription" => "Offsite"
        ],
        [
            "taskStatusId" => 255,
            "taskStatusDescription" => "Paused"
        ]
    ],

    /**
     * data retrieved from mysgs: /prodapi/api/v1.0/TechSpec/PrintProcesses
     */
    'print_processes' => [
        0 => "",
        10 => "Flexo",
        20 => "Gravure",
        30 => "Litho / Offset",
        40 => "Letterpress",
        50 => "Digital",
        60 => "Screen",
        70 => "Dry Offset",
        80 => "Flexo Perfect Highlight",
        90 => "Half Tone Flexo",
        100 => "Offset Gamut",
        110 => "Offset Gamut Hybrid",
        120 => "UV Flexo",
        130 => "Litho Silkscreen",
        140 => "Flexo Postprint",
        150 => "Flexo Preprint",
        160 => "Litho Lam",
        170 => "HQ Post Print",
        180 => "Digital File Only",
        190 => "TBC",
    ],
];
