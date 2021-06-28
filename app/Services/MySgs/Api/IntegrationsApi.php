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

}
