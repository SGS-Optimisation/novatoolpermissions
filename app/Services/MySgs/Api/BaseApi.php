<?php


namespace App\Services\MySgs\Api;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BaseApi
{

    public static $api_name;

    public static function buildBaseUrl()
    {
        return str_replace('{api_name}', static::$api_name,
            str_replace('{version}', nova_get_setting('api_version'), nova_get_setting('api_base_path'))
        );
    }

    public static function buildRequest()
    {
        AuthApi::appAuth();

        return Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => nova_get_setting('subscription_key'),
            'Ocp-Apim-Trace' => 'true',
            'Cache-Control' => 'no-cache',
        ])->withToken(Cache::get('mysgs_token'));
    }

    /**
     * @param $api_action
     * @param $query
     * @param $params
     * @param bool $array_mode
     * @return mixed
     */
    public static function get($api_action, $query, $params, $array_mode = false)
    {
        $url = static::buildBaseUrl() . $api_action . $query;

        return Cache::remember(
            $url . print_r($params, true),
            3600,
            function () use ($url, $params, $array_mode) {
                $response = static::buildRequest()->get($url, $params);
                return static::parseResponse($response, $array_mode);
            }
        );
    }

    public static function parseResponse($response, $array_mode){
        return !is_array($response->body()) ?
            (json_decode($response->successful() ? $response->body() : "", $array_mode)) :
            $response->body();
    }
}
