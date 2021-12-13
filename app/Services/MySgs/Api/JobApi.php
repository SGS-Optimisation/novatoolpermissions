<?php

namespace App\Services\MySgs\Api;

class JobApi extends BaseApi
{
    public static string $api_name = 'jobapi';

    public static function jobSearch($query, $params = [])
    {
        return static::get('Job/JobSearch/', $query, $params);
    }

    public static function basicInfo($formattedJobNumber, $params = [], $array_mode = false)
    {
        logger('basic info job api');
        return static::get('Job/basicInfo/formattedJobNumber/', $formattedJobNumber, $params, $array_mode);
    }

    public static function basicInfoRoute($formattedJobNumber)
    {
        return static::buildBaseUrl() . 'Job/basicInfo/formattedJobNumber/' . $formattedJobNumber;
    }

    public static function basicDetails($jobVersionId, $params = [])
    {
        logger('basic details job api');
        return static::get('JobVersion/basicDetails/', $jobVersionId, $params);
    }

    public static function basicDetailsRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'JobVersion/basicDetails/' . $jobVersionId;
    }

    public static function extraDetails($jobVersionId, $params = [])
    {
        logger('extra details job api');
        return static::get('JobVersion/extraDetails/', $jobVersionId, $params);
    }

    public static function extraDetailsRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'JobVersion/extraDetails/' . $jobVersionId;
    }

    public static function jobContacts($jobVersionId, $params = [])
    {
        logger('contact job api');
        return static::get('JobContacts/', $jobVersionId, $params);
    }

    public static function jobContactsRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'JobContacts/' . $jobVersionId;
    }

    public static function latestStage($jobVersionId, $params = [])
    {
        logger('latest stage job api');
        return static::get('JobStage/LatestStage/', $jobVersionId, $params);
    }

    public static function latestStageRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'JobStage/LatestStage/' . $jobVersionId;
    }

    public static function jobTeam($jobVersionId, $params = [])
    {
        logger('jobteam job api');
        return static::get('JobTeam/', $jobVersionId, $params);
    }

    public static function jobTeamRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'JobTeam/' . $jobVersionId;
    }
}
