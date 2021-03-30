<?php


namespace App\Services\MySgs\Api;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AuthApi extends BaseApi
{

    public static $api_name = 'authapi';

    public static function appAuth()
    {
        if (
            (!Cache::has('mysgs_token') && !Cache::has('mysgs_token_expiry'))
            || Carbon::createFromTimeString(Cache::get('mysgs_token_expiry'))->lessThan(Carbon::today())
        ) {

            $url = static::buildBaseUrl().'ApplicationAuthorisation/authenticate';

            $response = Http::post($url, [
                "appId" => nova_get_setting('api_app_id'),
                "apiKey" => nova_get_setting('api_app_key'),
            ]);

            $tokens = static::parseResponse($response, true);

            Cache::put('mysgs_token_expiry', $tokens['validTo']);
            Cache::put('mysgs_token', $tokens['token']);
        }
    }

}
