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

    /**
     * @param $jobVersionId
     * @param  array  $params
     * @return mixed
     * @deprecated
     */
    public static function techSpec($jobVersionId, $params = [])
    {
        logger('tech spec prod api');
        return static::get('TechSpec/barcode/', $jobVersionId, $params);
    }

    /**
     * @param $jobVersionId
     * @return string
     * @deprecated
     */
    public static function techSpecRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'TechSpec/barcode/' . $jobVersionId;
    }

    public static function techSpecBarcode($jobVersionId, $params = [])
    {
        logger('tech spec barcode prod api');
        return static::get('TechSpec/barcode/', $jobVersionId, $params);
    }

    public static function techSpecBarcodeRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'TechSpec/barcode/' . $jobVersionId;
    }

    public static function techSpecColour($jobVersionId, $params = [])
    {
        logger('tech spec colour prod api');
        return static::get('TechSpec/colour/', $jobVersionId, $params);
    }

    public static function techSpecColourRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'TechSpec/colour/' . $jobVersionId;
    }

    public static function techSpecPrintProcess($jobVersionId, $params = [])
    {
        logger('tech spec print process prod api');
        return static::get('TechSpec/PrintProcess/', $jobVersionId, $params);
    }

    public static function techSpecPrintProcessRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'TechSpec/PrintProcess/' . $jobVersionId;
    }

    public static function techSpecSimple($jobVersionId, $params = [])
    {
        logger('tech spec simple prod api');
        return static::get('TechSpec/Simple/', $jobVersionId, $params);
    }

    public static function techSpecSimpleRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'TechSpec/Simple/' . $jobVersionId;
    }
}
