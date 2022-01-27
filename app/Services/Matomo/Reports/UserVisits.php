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

        logger('searching for live visits with params: ' . print_r($params, true));


        $this->raw_data = Cache::remember('live-visits-v2-'.print_r($params, true), Carbon::now()->addMinutes(15),
            function () use ($params) {
                return Piwik::getLive()->getLastVisitsDetails(100000, $params);
            });

        $groupedVisits = collect($this->raw_data)->groupBy('userId');

        foreach ($groupedVisits as $user => $visits) {

            $this->visitors[$user] = ['visits' => []];

            foreach ($visits as $visit) {
                $job_number = 'Not Found';
                $client = 'Not Found';
                $time = $visit->lastActionDateTime ?? 'Error';
                $duration = $visit->visitDurationPretty ?? 'Error';
                $jobteam = $visit->dimension1 ?? 'Error';

                if (isset($visit->actionDetails[0])
                    && $visit->actionDetails[0]->type === 'action') {
                    $job_number = $visit->actionDetails[0]->pageTitle;

                    if (isset($visit->actionDetails[1])) {
                        $client = $visit->actionDetails[1]->eventAction ?? 'Not Found';
                    }

                } elseif (isset($visit->actionDetails[0])
                    && $visit->actionDetails[0]->type === 'event') {
                    $job_number = $visit->actionDetails[0]->eventName;
                    $client = $visit->actionDetails[0]->eventAction;
                }

                $entry = compact('user', 'job_number', 'client', 'jobteam', 'time', 'duration');

                $this->visits_list[] = $entry;
                $this->visitors[$user]['visits'][] = $entry;
            }
        }

        return $this;

    }
}
