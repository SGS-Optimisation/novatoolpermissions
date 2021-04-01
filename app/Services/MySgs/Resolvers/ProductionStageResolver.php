<?php


namespace App\Services\MySgs\Resolvers;


class ProductionStageResolver
{

    public static function handle($data)
    {
        \Log::debug('resolver received data:' . print_r($data, true));

        return implode(', ', $data);
    }
}
