<?php


namespace App\Features\Stats;


use App\Features\BaseFeature;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendDateExpressionFactory;

class RuleCreationPerRegion extends Trend
{
    public $dataset = [];

    /**
     * RuleCreationPerRegion constructor.
     * @param  int  $range
     * @param  string  $view_by
     */
    public function __construct(
        public ?string $view_by = self::BY_WEEKS,
        public ?int $range = 24,
        public ?string $function = 'count',
        public ?bool $cumulative = true,
        public ?string $region = null,
        public ?string $column = 'created_at'
    ) {
        parent::__construct();
    }


    public function handle()
    {
        $regions = Team::whereNotNull('region')->get()->pluck('region')->unique();

        foreach($regions as $region) {

            $trend = $this->processRegion($region)->trend;

            if($this->cumulative) {
                $cumul = 0;
                foreach($trend as $item => $value) {
                    $cumul += $value;
                    $trend[$item] = $cumul;
                }
            }


            $this->dataset[$region] = [
                'client_id' => $region,
                'created_at' => Team::firstWhere('region', $region)->created_at->format('Y-m-d H:i:s'),
                'trend' => $trend,
            ];
        }

        return $this->dataset;
    }

    public function processRegion($region)
    {
        $query = \App\Services\Rule\GetRulesForRegion::handle($region);

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
