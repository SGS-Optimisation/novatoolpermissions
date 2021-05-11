<?php


namespace App\Services\MySgs\Api\Resolvers;


class ProductionStageResolver
{

    public static function handle($data)
    {
        //\Log::debug('stage resolver received data:'.print_r($data, true));

        // no processing
        return $data;
    }
}
