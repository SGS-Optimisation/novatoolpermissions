<?php


namespace App\Features\Stats;


use App\Features\BaseFeature;
use App\Models\ClientAccount;
use App\Models\Rule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendDateExpressionFactory;

class RuleCreationPerClientAccount extends Trend
{
    public $dataset = [];

    /**
     * RuleCreationPerClientAccount constructor.
     * @param  int  $range
     * @param  string  $count
     */
    public function __construct(
        public ?string $count = self::BY_WEEKS,
        public ?int $range = 24,
        public ?string $column = 'created_at'
    ) {
        parent::__construct();
    }


    public function handle()
    {
        foreach (ClientAccount::withCount('rules')->get() as $client_account) {
            $this->dataset[$client_account->name] = [
                'client_id' => $client_account->id,
                'rules_count' => $client_account->rules_count,
                'created_at' => $client_account->created_at->format('Y-m-d H:i:s'),
                'trend' => $this->processClientAccount($client_account)->trend,
            ];
        }

        return $this->dataset;
    }

    public function processClientAccount($client_account)
    {
        $query = Rule::forClient($client_account);

        $request = new Request();
        $request->merge(['range' => $this->range, 'twelveHourTime' => false, 'timezone' => 'UTC']);

        return $this->{'countBy'.Str::title(Str::plural($this->count))}($request, $query, $this->column);
    }

    public function ranges()
    {
        return [
            5 => __('5 Weeks'),
            10 => __('10 Weeks'),
            15 => __('15 Weeks'),
            24 => __('24 Weeks'),
        ];
    }

    public function name()
    {
        return 'Client Account Rules Per Week';
    }

}
