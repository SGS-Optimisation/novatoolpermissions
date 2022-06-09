<?php


namespace App\Services\MySgs\Api\Resolvers;


use Illuminate\Support\Arr;

class ProductionStageResolver
{
    public static $stages = [
        'PA' => [
            '0 - Production Art',
        ],
        'PP' => [
            '0 - Prepress / Assembly',
            '0 - Prepress / Stepping'
        ],
        'PF' => [
            '0 - Preflight'
        ],

        'Hybrid' => [
            '0 - Hybrid PA/PP',
            '01 - Hybrid PA/PP',
        ],

        '3D' => [
            'Create 3D',
            '0 - 3D',
            '01 - 3D',
        ],

        'Retouch' => [
            'Retouch'
        ],

        'Released' => [
            'Printer File Release',
        ]

    ];

    public static function handle($data)
    {
        $detected_stages = [];

        if (count($stages = \Arr::pluck($data, 'jobStageId')) == 0) {
            return $detected_stages;
        }

        $latest_stage_id = max($stages);

        logger('found latest stage id: '.$latest_stage_id);

        $latest_stage = \Arr::where($data, function ($value, $key) use ($latest_stage_id) {
            return $value->jobStageId == $latest_stage_id;
        });

        if (count($latest_stage)) {
            foreach ($latest_stage as $stage) {
                foreach ($stage->jobTasks as $task) {
                    static::checkTaskStage($task, $detected_stages);
                }
            }
        }

        $detected_stages = array_values(array_unique($detected_stages));

        logger('detected stages:'.print_r($detected_stages, true));

        return $detected_stages;
    }

    protected static function checkTaskStage($task, &$detected_stages)
    {
        $stages = json_decode(nova_get_setting('stage_config'), true);
        foreach ($stages as $stage => $names) {
            if (\Str::startsWith($task->productionTaskName, $names)) {
                $detected_stages[] = $stage;
            }
        }
    }
}
