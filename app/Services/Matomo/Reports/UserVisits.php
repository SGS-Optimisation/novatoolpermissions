<?php


namespace App\Services\Matomo\Reports;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use RobBrazier\Piwik\Facades\Piwik;

class UserVisits
{
    public $visitors = [];

    public $visits_list = [];

    public $raw_data;

    /**
     * @param  string  $period
     * @param  null  $date  defaults to previous day
     * @return UserVisits
     */
    public function handle($period = 'day', $date = null, $client = null)
    {
        if (!$date) {
            $date = Carbon::yesterday()->format('Y-m-d');
        }
        $params = [
            'period' => $period,
            'date' => $date,
        ];

        if ($client) {
            if (!is_array($client)) {
                $client = [$client];
            }

            $params['segment'] = implode(',', array_map(function ($item) {
                return 'eventAction=@'.urlencode($item);
            }, $client));
        }

        logger('searching for live visits with params: '.print_r($params, true));


        $this->raw_data = Cache::remember(
            'live-visits-v2-'.print_r($params, true),
            Carbon::now()->addMinutes(nova_get_setting('matomo_cache_duration')),
            function () use ($params) {
                return Piwik::getLive()->getLastVisitsDetails(100000, $params);
            });

        $groupedVisits = collect($this->raw_data)->groupBy('userId');

        //dd($this->raw_data);

        foreach ($groupedVisits as $user => $visits) {

            $this->visitors[$user] = ['visits' => []];

            foreach ($visits as $visit) {

                if (!property_exists($visit, 'actionDetails') || !$visit->actionDetails) {
                    continue;
                }

                $groupedPageViews = collect($visit->actionDetails)->groupBy('idpageview');

                foreach ($groupedPageViews as $pageViewId => $pageViews) {
                    $job_number = $client = $jobteam = $country = $time = $duration = $durationPretty = null;

                    foreach ($pageViews as $pageView) {
                        $job_number = $job_number ?? $pageView->pageTitle ?? null;
                        $client = $client ?? $pageView->eventAction ?? null;
                        $jobteam = $jobteam ?? $pageView->dimension2 ?? null;
                        $country = $visit->country ?? 'Error';
                        $time = $time ?? $pageView->serverTimePretty ?? null;
                        $duration = $duration ?? $pageView->timeSpent ?? null;
                        if($pageView->timeSpent && $pageView->timeSpent > $duration) {
                            $duration = $pageView->timeSpent;
                            $durationPretty = $pageView->timeSpentPretty;
                        }
                    }

                    // TODO: Legacy, to remove
                    if (!$jobteam) {
                        $jobteam = $visit->dimension1 ?? 'Error';
                    }

                    $entry = compact('user', 'job_number', 'client', 'jobteam',
                        'country', 'time', 'duration', 'durationPretty');

                    $this->visits_list[] = $entry;
                    $this->visitors[$user]['visits'][] = $entry;
                }
            }
        }

        return $this;

    }
}
