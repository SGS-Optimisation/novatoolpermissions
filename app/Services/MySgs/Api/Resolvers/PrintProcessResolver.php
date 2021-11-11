<?php


namespace App\Services\MySgs\Api\Resolvers;


class PrintProcessResolver
{

    public static function handle($data)
    {
        $accumulator = [];

        foreach ($data as $process_id) {
            $accumulator[] = config('mysgs.print_processes.'.$process_id);
        }

        return $accumulator;
    }
}
