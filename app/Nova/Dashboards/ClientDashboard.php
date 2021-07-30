<?php

namespace App\Nova\Dashboards;

use App\Models\ClientAccount;
use App\Nova\Metrics\RulesPerWeek;
use Laravel\Nova\Dashboard;

class ClientDashboard extends Dashboard
{
    public ClientAccount $client_account;

    /**
     * ClientDashboard constructor.
     * @param  ClientAccount  $client_account
     */
    public function __construct(ClientAccount $client_account)
    {
        $this->client_account = $client_account;
        parent::__construct();
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new RulesPerWeek($this->client_account),
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'client-dashboard';
    }
}
