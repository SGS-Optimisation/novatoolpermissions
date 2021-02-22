<?php

namespace App\Services\MySgs\Api;

class JobApi extends BaseApi
{
    public static $api_name = 'jobapi';

    public static function jobSearch($query, $params = [])
    {
        return static::get('Job/JobSearch/', $query, $params);
    }

    public static function basicInfo($formattedJobNumber, $params = [], $array_mode = false)
    {
        return static::get('Job/basicInfo/formattedJobNumber/', $formattedJobNumber, $params, $array_mode);
    }

    public static function basicDetails($jobVersionId, $params = [])
    {
        return static::get('JobVersion/basicDetails/', $jobVersionId, $params);
    }

    public static function extraDetails($jobVersionId, $params = [])
    {
        return static::get('JobVersion/extraDetails/', $jobVersionId, $params);
    }
}
