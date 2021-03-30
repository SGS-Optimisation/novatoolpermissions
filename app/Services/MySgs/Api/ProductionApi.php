<?php


namespace App\Services\MySgs\Api;


class ProductionApi extends BaseApi
{
    public static $api_name = 'prodapi';

    public static function jobItems($jobVersionId, $params = [])
    {
        logger('job items prod api');
        return static::get('JobItem/list/jobversion/', $jobVersionId, $params);
    }

}
