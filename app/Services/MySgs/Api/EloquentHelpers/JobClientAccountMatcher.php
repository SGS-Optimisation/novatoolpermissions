<?php


namespace App\Services\MySgs\Api\EloquentHelpers;


use App\Models\ClientAccount;
use App\Models\Job;
use App\Services\MySgs\WarehousedData\Customers\PortfolioGroupNameFinder;
use App\Services\MySgs\WarehousedData\Customers\SimplifiedGroupNameFinder;
use Illuminate\Support\Str;

class JobClientAccountMatcher
{
    /** @var Job */
    public $job;

    /**
     * JobClientAccountMatcher constructor.
     * @param  Job  $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function handle()
    {
        logger('JobClientAccountMatcher invoked');
        $job_metadata = $this->job->metadata;

        // TODO : not match client accounts when customer is empty from API
        try {
            $customer_name = $this->job->metadata->basicDetails->retailer->customerName;
            logger('searching for client name '.$customer_name);

            if (!$customer_name || trim($customer_name) == '') {
                $customer_name = ['[NOT SET]'];
                throw new \Exception('Client not set on job');
            }

            /*
             * Naive search from customer name and user provided aliases
             */

            $client = $this->findFromAlias($customer_name);

            /*
             * Let's bust out the warehoused data
             */
            if (!$client) {
                $client = $this->findFromWarehousedData($customer_name);
            }

            if ($client) {
                logger('found client '.$client->name);
                $job_metadata->client = $client->only(['id', 'name', 'slug', 'image']);
                $job_metadata->client_found = true;
            } else {
                throw new \Exception('Client not found');
            }
        } catch (\Exception $e) {
            logger($e->getMessage());
            $job_metadata->client_found = false;
            $job_metadata->client = ['name' => $customer_name];
        }


        $this->job->metadata = $job_metadata;
        $this->job->save();
    }


    public static function findFromAlias($customer_name)
    {
        logger('searching to client account with name '.$customer_name);
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        switch ($driver) {
            case 'sqlsrv':
                $search = 'LOWER("alias") LIKE ?';
                break;
            case 'mysql':
            default:
                $search = 'LOWER(alias) LIKE ?';
        }
        $searchData = ['%'.Str::lower($customer_name).'%'];

        return ClientAccount::where('name', 'LIKE', '%'.$customer_name.'%')
            ->orWhereRaw($search, $searchData)
            ->first();

    }

    protected function findFromWarehousedData($customer_name)
    {
        $portfolio_name = (new PortfolioGroupNameFinder($customer_name))->handle();

        if ($portfolio_name) {
            $customer = $this->findFromAlias($portfolio_name);

            if ($customer) {
                return $customer;
            }

            return (new SimplifiedGroupNameFinder($customer_name))->handle();
        }

        return false;
    }


}
