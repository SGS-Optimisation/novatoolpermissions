<?php


namespace App\Services\MySgs\Api\Resolvers;


class CountryResolver
{

    public static function handle($data)
    {
        \Log::debug('country resolver received data:'.print_r($data, true));

        $accumulator = [];

        foreach ($data as $language) {
            if (trim($language) !== "") {
                $tags = explode(',', $language);
                $accumulator[] = array_filter($tags, function ($tag) {
                    return $tag && $tag !== "";
                });
            }
        }

        return $accumulator;
    }

}
