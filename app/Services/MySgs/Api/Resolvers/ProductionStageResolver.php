<?php


namespace App\Services\MySgs\Api\Resolvers;


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
        ]
    ];

    public static function handle($data)
    {
        $detected_stages = [];

        $latest_stage_id = max(\Arr::pluck($data, 'jobStageId'));

        logger('found latest stage id: '.$latest_stage_id);

        $latest_stage = \Arr::where($data, function ($value, $key) use ($latest_stage_id) {
            return $value->jobStageId == $latest_stage_id;
        });

        if(count($latest_stage)) {
            $latest_stage = \Arr::first($latest_stage);
        }

        if ($latest_stage) {
            foreach ($latest_stage->jobTasks as $task) {
                static::checkTaskStage($task, $detected_stages);
            }
        }

        // no processing
        return $detected_stages;
    }

    protected static function checkTaskStage($task, &$detected_stages)
    {
        foreach(static::$stages as $stage => $names) {
            if (\Str::startsWith($task->productionTaskName, $names)) {
                $detected_stages[] = $stage;
            }
        }
    }
}
