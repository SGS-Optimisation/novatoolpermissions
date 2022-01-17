<?php


namespace App\Features\Stats;


use App\Features\BaseFeature;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Team;
use App\Services\Rule\GetRulesForTeam;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendDateExpressionFactory;

class RuleCreationPerTeam extends Trend
{
    public $dataset = [];

    /**
     * RuleCreationPerTeam constructor.
     * @param  int  $range
     * @param  string  $view_by
     */
    public function __construct(
        public ?string $view_by = self::BY_WEEKS,
        public ?int $range = 24,
        public ?string $function = 'count',
        public ?bool $cumulative = true,
        public ?string $region = null,
        public ?string $column = 'created_at',
        public null|int|array $client_account_ids = null
    ) {
        parent::__construct();

        if(is_int($client_account_ids)) {
            $this->client_account_ids = [$client_account_ids];
        }
    }


    public function handle()
    {
        $teams = Team::personal(false)
            ->with(['clientAccount'])
            ->when($this->region, function($query)  {
                $query->where('region', $this->region);
            })->when($this->client_account_ids, function($query)  {
                $query->whereIn('client_account_id', $this->client_account_ids);
            })
            ->get();

        foreach($teams as $team) {

            $trend = $this->processTeam($team)->trend;

            if($this->cumulative) {
                $cumul = 0;
                foreach($trend as $item => $value) {
                    $cumul += $value;
                    $trend[$item] = $cumul;
                }
            }

            $this->dataset[$team->name] = [
                'client_id' => $team->clientAccount->id,
                'created_at' => $team->created_at->format('Y-m-d H:i:s'),
                'trend' => $trend,
            ];
        }

        return $this->dataset;
    }

    public function processTeam($team)
    {
        $query = GetRulesForTeam::handle($team, $this->region);

        $request = new Request();
        $request->merge(['range' => $this->range, 'twelveHourTime' => false, 'timezone' => 'UTC']);

        return $this->{$this->function.'By'.Str::title(Str::plural($this->view_by))}($request, $query, 'rules.'.$this->column);
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
