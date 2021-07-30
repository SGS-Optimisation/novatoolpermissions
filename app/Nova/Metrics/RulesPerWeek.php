<?php

namespace App\Nova\Metrics;

use App\Models\ClientAccount;
use App\Models\Rule;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class RulesPerWeek extends Trend
{

    public ClientAccount $client_account;

    /**
     * RulesPerWeek constructor.
     * @param  ClientAccount  $client_account
     */
    public function __construct(ClientAccount $client_account)
    {
        $this->client_account = $client_account;
        parent::__construct();
    }


    public function name()
    {
        return $this->client_account->name . ' Rules Per Week';
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        logger(print_r($request->all(), true));
        return $this->countByWeeks($request, Rule::forClient($this->client_account));
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            5 => __('5 Weeks'),
            10 => __('10 Weeks'),
            15 => __('15 Weeks'),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'rules-per-week';
    }
}
