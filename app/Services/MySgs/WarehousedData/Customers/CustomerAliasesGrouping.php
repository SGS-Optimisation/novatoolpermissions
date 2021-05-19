<?php


namespace App\Services\MySgs\WarehousedData\Customers;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomerAliasesGrouping
{
    const CUSTOMER_ALIASES_SEARCH_BY_SIMPLIFIED_NAME = 1;
    const CUSTOMER_ALIASES_SEARCH_BY_PORTFOLIO_NAME = 2;

    /**
     * @return Collection
     */
    public static function bySimplifiedName()
    {
        return \Cache::tags(['warehoused_data', 'customers'])
            ->rememberForever('warehoused_data_customers_by_simplified', function () {
                $all_customers = \DB::connection('mysgs_warehoused_data')->table('customers')->get();

                return $all_customers->groupBy('SimplifiedGroupName');
            });

    }

    /**
     * @return Collection
     */
    public static function byPortfolioName()
    {
        return \Cache::tags(['warehoused_data', 'customers'])
            ->rememberForever('warehoused_data_customers_by_portfolio', function () {
                $all_customers = \DB::connection('mysgs_warehoused_data')->table('customers')->get();

                return $all_customers->groupBy('PortfolioGroupName');
            });

    }

    /**
     * @param $str
     * @param  null  $search_mode
     * @return array
     */
    public static function search($str, $search_mode = null)
    {
        $search_mode = ($search_mode ?? static::CUSTOMER_ALIASES_SEARCH_BY_SIMPLIFIED_NAME);

        $search_function = $search_mode == static::CUSTOMER_ALIASES_SEARCH_BY_SIMPLIFIED_NAME ? 'bySimplifiedName' : 'byPortfolioName';

        $matches = static::$search_function()->filter(function ($value, $key) use ($str) {
            return Str::contains(Str::lower($key), Str::lower($str));
        });

        $group_aliases = [];

        foreach ($matches as $match => $data) {
            $group_data = [];

            foreach ($data as $datum) {
                $group_data[] = $datum->CustomerName;
            }
            $group_aliases[$match] = array_values(collect($group_data)->unique()->toArray());
        }

        return $group_aliases;
    }
}
