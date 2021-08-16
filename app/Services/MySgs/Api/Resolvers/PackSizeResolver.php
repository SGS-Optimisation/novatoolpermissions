<?php


namespace App\Services\MySgs\Api\Resolvers;


class PackSizeResolver
{

    public static function handle($data)
    {
        logger('pack size resolver received data:'.print_r($data, true));

        $sections = explode('~', $data[0]);
        if(count($sections) == 1) {
            return $data;
        }

        $last = end($sections);

        $parts = explode(':', $last);
        return trim($parts[0]);

    }

}
