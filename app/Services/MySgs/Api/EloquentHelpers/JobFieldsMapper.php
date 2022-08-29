<?php


namespace App\Services\MySgs\Api\EloquentHelpers;


use App\Models\FieldMapping;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Str;

class JobFieldsMapper
{

    public $accumulator = [];

    /**
     * @var Job
     */
    public $job;

    /**
     * @var FieldMapping
     */
    public $mapping;

    /**
     * Mapper constructor.
     * @param  Job  $job
     */
    public function __construct(Job $job, FieldMapping $mapping = null)
    {
        $this->job = $job;
        $this->mapping = $mapping;
    }


    /**
     * @param  Job  $job
     * @param  FieldMapping  $mapping
     * @return array|\ArrayAccess|mixed|void
     */
    public function getMetaValue(FieldMapping $mapping = null)
    {
        $this->accumulator = [];
        $job = $this->job;
        if (!$mapping) {
            $mapping = $this->mapping;
        }

        /*
         * Check if data from API is already stored and still fresh
         */
        if (isset($job->metadata->{$mapping->title})
            && $job->metadata->{$mapping->title} != []
            && $job->metadata->{$mapping->title} != ''
            && $job->updated_at->isAfter(Carbon::now()->subHours(1))
        ) {
            //logger('using stored data for mapping ' . $mapping->id);
            $data = $job->metadata->{$mapping->title};
        } else {
            logger('no stored data for mapping '.$mapping->id);

            if ($job->updated_at->isBefore(Carbon::now()->subMinutes(5))) {
                $api_data = (new MysgsApiCaller($job))->handle($mapping);
                $data = $api_data->response;
            } else {
                $data = [];
            }
        }

        return static::parseMapping($mapping, $data);
    }

    public function run()
    {
        try {
            $value = $this->getMetaValue();
        } catch (\Exception $e) {
            \Log::error('error running mapping '.$this->mapping->id);
            \Log::error($e->getMessage());
            \Log::error(join("\n", $e->getTrace()));
            $value = null;
        }

        return [$value, $this->accumulator];
    }


    protected function parseMapping($mapping, $data)
    {
        $field_path_sections = explode('.', $mapping->field_path);

        static::parseFieldPathSection($field_path_sections, $data);

        $dataToResolve = $this->accumulator;

        // send the response to resolver to make some changes according to your need to tag
        if ($mapping->resolver_name !== null) {
            $resolverClass = 'App\Services\MySgs\Api\Resolvers\\'.$mapping->resolver_name;
            $resolver = new $resolverClass;
            $dataToResolve = $resolver::handle($this->accumulator);
        } else {
            if (count($this->accumulator) == 0) {
                $dataToResolve = '';
            } elseif (count($this->accumulator) == 1) {
                $dataToResolve = explode('#', $this->accumulator[0]);
            }
        }

        return $dataToResolve;
    }


    protected function parseFieldPathSection($field_path_sections, $data)
    {
        $section = array_shift($field_path_sections);
        if ($section === null || $section === '@') {
            $this->accumulator[] = $data;
        }

        if (is_int($section) || $section === "0") {
            if (is_array($data) && isset($data[$section])) {
                static::parseFieldPathSection($field_path_sections, $data[$section]);
            } elseif (is_object($data) && isset($data->$section)) {
                static::parseFieldPathSection($field_path_sections, $data->$section);
            }
        } elseif (Str::startsWith($section, '*')) {

            $section_recursive = ltrim($section, '*');

            /*
             * Case of *[key1=val1|key2=val2]
             */
            if (Str::startsWith($section_recursive, '[') && Str::endsWith($section_recursive, ']')) {

                $attrs_matcher = array_map(
                    'trim',
                    explode('|', trim($section_recursive, "[]"))
                );

                /*
                 * Process each key value pair
                 */
                foreach ($attrs_matcher as $attr_matcher) {
                    $matcher = explode('=', $attr_matcher);

                    $field = $matcher[0];
                    $value = $matcher[1];

                    foreach ($data as $item) {
                        if (isset($item->$field) && $item->$field === $value) {
                            static::parseFieldPathSection($field_path_sections, $item);
                        }
                    }
                }

            } else {
                foreach ($data as $item) {
                    static::parseFieldPathSection($field_path_sections, $item);
                }

            }
        } elseif (isset($data->$section)) {
            static::parseFieldPathSection($field_path_sections, $data->$section);
        }
    }
}
