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

class RuleCreationPerTeam extends Trend
{
    public $dataset = [];

    /**
     * RuleCreationPerTeam constructor.
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
        foreach(Team::personal(false)->with(['clientAccount'])->get()  as $team) {
            $this->dataset[$team->name] = [
                'client_id' => $team->clientAccount->id,
                'created_at' => $team->created_at->format('Y-m-d H:i:s'),
                'trend' => $this->processTeam($team)->trend
            ];
        }

        return $this->dataset;
    }

    public function processTeam($team)
    {
        $query = \App\Services\Rule\GetRulesByTeam::handle($team);

        $request = new Request();
        $request->merge(['range' => $this->range, 'twelveHourTime' => false, 'timezone' => 'UTC']);

        return $this->{'countBy'.Str::title(Str::plural($this->count))}($request, $query, 'rules.'.$this->column);
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
