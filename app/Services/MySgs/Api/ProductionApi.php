<?php


namespace App\Services\MySgs\Api;


class ProductionApi extends BaseApi
{
    public static string $api_name = 'prodapi';

    public static function jobItems($jobVersionId, $params = [])
    {
        logger('job items prod api');
        return static::get('JobItem/list/jobversion/', $jobVersionId, $params);
    }

    public static function jobItemsRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'JobItem/list/jobversion/' . $jobVersionId;
    }

}
