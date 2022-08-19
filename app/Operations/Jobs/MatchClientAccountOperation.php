<?php


namespace App\Operations\Jobs;


use App\Events\Jobs\ClientAccountNotMatched;
use App\Exceptions\MultipleClientAccountsMatchedException;
use App\Models\ClientAccount;
use App\Models\Job;
use App\Operations\BaseOperation;
use App\Services\MySgs\WarehousedData\Customers\PortfolioGroupNameFinder;
use App\Services\MySgs\WarehousedData\Customers\SimplifiedGroupNameFinder;
use Illuminate\Support\Str;
use function config;
use function logger;

class MatchClientAccountOperation extends BaseOperation
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

        try {
            $customer_name = $this->job->metadata->basicDetails->retailer->customerName;

            if (!$customer_name) {
                logger('no retailer in basic details, checking contacts');
                try {
                    $customer_name = collect($this->job->metadata->jobContacts)
                        ->whereIn('contactType', [10, 30])->first()
                        ->customerName;
                } catch (\Exception $e) {
                    logger('No customer name and no contact type "End User"');
                }
            }

            $jobteam = null;

            logger('searching for client name '.$customer_name);
            if ($this->job->metadata->jobTeam) {
                $jobteam = collect($this->job->metadata->jobTeam)
                    ->where('primaryJobTeam', true)
                    ->first()->teamName;

                logger('searching for team name '.$jobteam);
            }

            if (!$customer_name || trim($customer_name) == '') {
                $customer_name = '[NOT SET]';
                throw new \Exception('Client not set on job');
            }

            /*
             * Naive search from customer name and user provided aliases
             */

            $client = $this->findFromAlias($customer_name, $jobteam);

            /*
             * Let's bust out the warehoused data
             */
            if (!$client) {
                $client = $this->findFromWarehousedData($customer_name, $jobteam);
            }

            if ($client) {
                logger('found client '.$client->name);
                $this->job->client_account_id = $client->id;
                $job_metadata->client = $client->only(['id', 'name', 'slug', 'image']);
                $job_metadata->client_found = true;
            } else {
                throw new \Exception('Client not found');
            }
        } catch (MultipleClientAccountsMatchedException $multipleMatches) {
            logger($multipleMatches->getMessage());
            $job_metadata->client_found = false;
            $job_metadata->client = ['name' => $customer_name ?? '[NOT SET]'];
            $job_metadata->error_reason = $multipleMatches->getMessage();
        } catch (\Exception $e) {
            logger($e->getMessage());
            $job_metadata->client_found = false;
            $job_metadata->client = ['name' => $customer_name ?? '[NOT SET]'];
            logger('couldn’t find match for ' . print_r($customer_name, true));
            event(new ClientAccountNotMatched($customer_name ?? '[NOT SET]'));
        }


        $this->job->metadata = $job_metadata;
        $this->job->save();
    }


    public static function findFromAlias($customer_name, $jobteam = null)
    {
        logger('searching for client account with name '.$customer_name);
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        switch ($driver) {
            case 'sqlsrv':
                $search = 'LOWER("alias") LIKE ?'." ESCAPE '\'";
                break;
            case 'mysql':
            default:
                $search = 'LOWER(alias) LIKE ?';
        }
        $searchData = ['%'.Str::lower(str_replace('[', '\[', $customer_name)).'%'];

        /** @var ClientAccount[] $matches */
        $matches = ClientAccount::where('name', 'LIKE', '%'.$customer_name.'%')
            ->orWhereRaw($search, $searchData)
            ->get();

        if (count($matches) === 1) {
            return $matches[0];
        }

        if (count($matches) > 1) {
            if ($jobteam) {
                foreach ($matches as $match) {
                    if (Str::contains(
                        Str::lower(trim($match->jobteam)),
                        Str::lower(trim($jobteam)))
                    ) {
                        return $match;
                    }
                }
            }
            throw new MultipleClientAccountsMatchedException('Multiple client accounts match. Please add a jobteam to refine the match. Accounts: '
                .implode(', ', $matches->pluck('name')->all())
                .'.  Detected jobteam for this job: '.$jobteam
                .'.  Common end user: '.$customer_name
            );
        }

        return null;
    }

    protected function findFromWarehousedData($customer_name, $jobteam = null)
    {
        $portfolio_name = (new PortfolioGroupNameFinder($customer_name))->handle();

        if ($portfolio_name) {
            $customer = $this->findFromAlias($portfolio_name, $jobteam);

            if ($customer) {
                return $customer;
            }

            return (new SimplifiedGroupNameFinder($customer_name))->handle();
        }

        return false;
    }


}
