<?php


namespace App\Services\MySgs\Resolvers;


class CountryResolver
{

    public static function handle($language)
    {
        if ($language && trim($language) !== "") {
            $tags = explode(',', $language);
            return array_filter($tags, function ($tag) {
                return $tag && $tag !== "";
            });
        }

        return false;
    }

}
