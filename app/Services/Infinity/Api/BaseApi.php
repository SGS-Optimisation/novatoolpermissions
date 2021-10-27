<?php


namespace App\Services\Infinity\Api;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class BaseApi
{

    /**
     * BaseApi constructor.
     */
    public function __construct(public ?int $workspace = null, public ?string $board = null)
    {
        if (!$this->workspace) {
            $this->workspace = config('services.infinity.workspace');
        }
        if (!$this->board) {
            $this->board = config('services.infinity.board');
        }
    }

    /*public function withWorkspace($workspace)
    {
        $this->workspace = $workspace;
        return $this;
    }

    public function withBoard($board)
    {
        $this->board = $board;
        return $this;
    }*/

    protected function buildBaseUrl($path, $method = 'get')
    {
        $path = trim($path, '/');

        return "https://app.startinfinity.com/api/v2/workspaces/{$this->workspace}/boards/{$this->board}/{$path}";
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'with')) {
            $property = Str::camel(Str::replace('with', '', $name));
            $this->$property = $arguments[0];
            return $this;
        }

        return $this->callApi($name, $arguments);
    }


    public function callApi($name, $arguments)
    {
        $url = $this->buildBaseUrl($name.'/'.(count($arguments) ? implode('/', $arguments) : ''));

        $method = $arguments['method'] ?? 'get';

        return Cache::remember($url, 3600, function () use ($url, $method) {
            $response = Http::withToken(config('services.infinity.token'))->$method($url);
            return static::parseResponse($response);
        });
    }

    public static function parseResponse($response)
    {
        return !is_array($response->body()) ?
            (json_decode($response->successful() ? $response->body() : "")) :
            $response->body();
    }
}
