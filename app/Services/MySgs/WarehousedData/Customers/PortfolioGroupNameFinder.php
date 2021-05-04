<?php


namespace App\Services\MySgs\WarehousedData\Customers;


class PortfolioGroupNameFinder
{

    public $provided_name;

    /**
     * SimplifiedGroupNameMatcher constructor.
     * @param $provided_name
     */
    public function __construct($provided_name)
    {
        $this->provided_name = $provided_name;
    }

    public function handle()
    {
        logger('porfolio finder running on ' . $this->provided_name);
        $match = \DB::connection('mysgs_warehoused_data')->table('customers')
            ->where('CustomerName', $this->provided_name)->first();

        if($match) {
            return $match->PortfolioGroupName;
        }

        return false;


    }


}
