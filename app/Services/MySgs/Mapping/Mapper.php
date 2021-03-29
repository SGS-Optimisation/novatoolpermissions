<?php


namespace App\Services\MySgs\Mapping;


use App\Models\FieldMapping;
use App\Models\Job;
use Illuminate\Support\Str;

class Mapper
{

    /**
     * @param  Job  $job
     * @param  FieldMapping  $mapping
     * @return array|\ArrayAccess|mixed|void
     */
    public static function getMetaValue(Job $job, FieldMapping $mapping)
    {

        /*
         * Build the appropriate Api object
         */
        $apiClass = 'App\Services\MySgs\Api\\'.$mapping->api_name;
        $api = new $apiClass;
        $function = $mapping->api_action;

        /*
         * Check which parameter is expected from the api function
         */
        $signature = get_func_argNames($function, $api);

        if (array_keys($signature)[0] == 'jobVersionId') {
            if (!is_array($job)) {
                $job = json_decode($job);
            }
            $param = $job->metadata->basicInfo->jobVersionId;
        } else {
            $param = $job->job_number;
        }

        if (isset($job->metadata->{$mapping->api_action})) {
            $response = $job->metadata->{$mapping->api_action};
        } else {
            $response = $api::$function($param);
        }

        return static::parseMapping($mapping, $response);
    }


    protected static function parseMapping($mapping, $data)
    {
        $field_path_sections = explode('.', $mapping->field_path);

        $dataToResolve = static::parseFieldPathSection($field_path_sections, $data);

        // send the response to resolver to make some changes according to your need to tag
        if ($mapping->resolver_name !== null) {
            $resolverClass = 'App\Services\Resolvers\\'.$mapping->resolver_name;
            $resolver = new $resolverClass;
            $dataToResolve = $resolver::handle($dataToResolve);
        }

        return $dataToResolve;
    }


    protected static function parseFieldPathSection($field_path_sections, $data)
    {
        $section = array_shift($field_path_sections);

        if ($section === null) {
            return $data;
        }

        if (is_int($section) || $section === "0") {
            if (!isset($data[$section])) {
                return null;
            }
            return static::parseFieldPathSection($field_path_sections, $data[$section]);
        } elseif (Str::startsWith($section, '*')) {

            $section_recursive = ltrim($section, '*');

            if (Str::startsWith($section_recursive, '[') && Str::endsWith($section_recursive, ']')) {

                $matcher = array_map(
                    'trim',
                    explode('=', trim($section_recursive, "[]"))
                );

                $field = $matcher[0];
                $value = $matcher[1];

                foreach ($data as $item) {
                    if (isset($item->$field) && $item->$field === $value) {
                        return static::parseFieldPathSection($field_path_sections, $item);
                    }
                }
            } else {
                /*
                 * TODO this should do some recursion, but on what?
                 */
                return static::parseFieldPathSection($field_path_sections, $data);
            }
        } else {
            return static::parseFieldPathSection($field_path_sections, $data->$section);
        }
    }
}
