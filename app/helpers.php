<?php

/**
 * @param string $filename
 * @param string $delimiter
 * @return array|bool
 */
if (!function_exists('csvToArray')) {
    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }
}

/**
 * Group items from an array together by some criteria or value.
 *
 * @param  $arr array The array to group items from
 * @param  $criteria string|callable The key to group by or a function the returns a key to group by.
 * @return array
 *
 */
if (!function_exists('arrayGroupBy')) {
    function arrayGroupBy($arr, $criteria): array
    {
        return array_reduce($arr, function ($accumulator, $item) use ($criteria) {
            $key = (is_callable($criteria)) ? $criteria($item) : $item[$criteria];
            if (!array_key_exists($key, $accumulator)) {
                $accumulator[$key] = [];
            }

            array_push($accumulator[$key], $item);
            return $accumulator;
        }, []);
    }
}

/**
 * Obfuscate a string to prevent spam-bots from sniffing it.
 *
 * @param string $value
 *
 * @return string
 */
if (!function_exists('obfuscate')) {
    function obfuscate($value)
    {
        $safe = '';

        foreach (str_split($value) as $letter) {
            if (ord($letter) > 128) {
                return $letter;
            }

            // To properly obfuscate the value, we will randomly convert each letter to
            // its entity or hexadecimal representation, keeping a bot from sniffing
            // the randomly obfuscated letters out of the string on the responses.
            switch (rand(1, 3)) {
                case 1:
                    $safe .= '&#' . ord($letter) . ';';
                    break;

                case 2:
                    $safe .= '&#x' . dechex(ord($letter)) . ';';
                    break;

                case 3:
                    $safe .= $letter;
            }
        }

        return $safe;
    }
}


function get_func_argNames($funcName, $className = null)
{
    if ($className) {
        $f = new ReflectionMethod($className, $funcName);
    } else {
        $f = new ReflectionFunction($funcName);
    }
    $result = array();
    foreach ($f->getParameters() as $param) {
        $result[$param->name] = $param->isOptional() ?: 'required';
    }
    if (count($result)) {
        return $result;
    }
}

function get_class_info($c)
{
    $n = get_class($c);
    if ($f = get_class_methods($n)) {
        $mm = array_flip($f);
        foreach ($mm as $m => $a) {
            $mm[$m] = get_func_argNames($m, $n);
        }
        return $mm;
    }
}

function get_defined_functions_info($arr = false)
{
    foreach ($arr as $d => $f) {
        $arr[$d] = array_flip($f);
        foreach ($arr[$d] as $k => $v) {
            $arr[$d][$k] = get_func_argNames($k);//func_get_args($v));
        }
    }
    return $arr;
}

function obj_to_array_recursive(stdClass $obj)
{
    foreach ($obj as &$element) {
        if ($element instanceof stdClass) {
            obj_to_array_recursive($element);
            $element = (array)$element;
        }
    }
    $obj = (array)$obj;
    return $obj;
}

/**
 * @param $array1
 * @param $array2
 * @return bool
 */
function compareTwoArrays($array1, $array2): bool
{
    foreach ($array1 as $key => $element) {
        \Log::debug('KEY: '.$key);
        if (!is_array($element) && !is_array($array2[$key])) {
            \Log::debug('KEY: '.$key.' value1: '.$element.' value2: '.$array2[$key]);
            if ($array2[$key] != $element) {
                return false;
            }
        } else if (is_array($element) && is_array($array2[$key])) {
            if (!compareTwoArrays($element, $array2[$key])) {
                return false;
            }
        } else {
            return false;
        }
    }
    return true;
}
