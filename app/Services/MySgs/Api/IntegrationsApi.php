<?php


namespace App\Services\MySgs\Api;


class IntegrationsApi extends BaseApi
{
    public static string $api_name = 'integrationsapi';

    public static function dragonflyProject($jobVersionId, $params = [])
    {
        logger('dragonfly project integrations api');
        return static::get('Dragonfly/DragonflyProject/', $jobVersionId, $params);
    }

    public static function dragonflyProjectRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'Dragonfly/DragonflyProject/' . $jobVersionId;
    }

    public static function importedContent($jobVersionId, $params = [])
    {
        logger('imported content integrations api');
        return static::get('ImportedContent/job/2/', $jobVersionId, $params);
    }

    public static function importedContentRoute($jobVersionId)
    {
        return static::buildBaseUrl() . 'ImportedContent/job/2/' . $jobVersionId;
    }

    public static function importedContentV2($jobVersionId, $params = [])
    {
        logger('variable imported content integrations api with params: ' . $jobVersionId . ' + ' . print_r($params, true));
        $ics = $params['importedContentSource'];

        return static::get("ImportedContent/job/$ics/", $jobVersionId, $params);
    }

}
