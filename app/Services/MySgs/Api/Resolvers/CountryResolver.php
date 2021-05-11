<?php


namespace App\Services\MySgs\Api\Resolvers;


class CountryResolver
{

    public static function handle($data)
    {
        //\Log::debug('country resolver received data:'.print_r($data, true));

        $accumulator = collect();

        foreach ($data as $language) {
            if (trim($language) !== "") {
                $tags = array_map('trim', explode(',', $language));

                $accumulator = $accumulator->merge(collect($tags));
            }
        }
        
        return $accumulator->toArray();
    }

}
