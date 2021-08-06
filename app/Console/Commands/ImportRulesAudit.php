<?php

namespace App\Console\Commands;

use App\Models\Rule;
use App\Models\RuleTerm;
use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportRulesAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:rules_audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rules = Rule::get();
        foreach ($rules as $rule){
            $this->createLedger($rule,'App\Models\Rule');
            $rule_terms = RuleTerm::where('rule_id',$rule->id)->get();
            $properties =[];
            foreach($rule_terms as $rule_term){
                $properties[] = ['rule_id'=>$rule->id,'term_id'=>$rule_term->term_id];
            }
            $this->createLedgerPivot($properties,$rule,'App\Models\Rule');

        }
    }
    private function createLedger($data,$recordable_type)
    {
        if ($data) {
            $user_type = 'App\Models\User';
            $user_id = User::first()->id;
            $modified = array_keys($data->toArray());

            $check_exits = DB::table('ledgers')
                ->where(['recordable_type' => $recordable_type,
                    'recordable_id' => $data->id,
                    'event' => 'created'])->count();
            if ($check_exits == 0) {
                $insert_data =  [
                    'user_type' => $user_type,
                    'user_id' => $user_id,
                    'recordable_type' => $recordable_type,
                    'recordable_id' => $data->id,
                    'context' => 4,
                    'event' => 'created',
                    'properties' => $data->toJSon(),
                    'modified' => json_encode($modified),
                    'pivot' => '[]',
                    'extra' => '[]',
                    'signature' => $this->sign($data->toArray()),
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
                DB::table('ledgers')->insert($insert_data);

            }
        }
    }
    private function createLedgerPivot($properties,$parent_data,$recordable_type)
    {
        $data = ["relations"=>"terms","properties"=>$properties];
        if ($data) {
            $user_type = 'App\Models\User';
            $user_id = User::first()->id;
              DB::table('ledgers')->insert(
                    [
                        'user_type' => $user_type,
                        'user_id' => $user_id,
                        'recordable_type' => $recordable_type,
                        'recordable_id' => $parent_data->id,
                        'context' => 4,
                        'event' => 'attached',
                        'properties' => $parent_data->toJSon(),
                        'modified' => '[]',
                        'pivot' => json_encode($data),
                        'extra' => '[]',
                        'signature' => '57c13b16571f85a94841a66257ddd081020ebc851e23cff8aa8acf8d4158a8307dcd207a7e70381407dfa85f0205aa9dca8c34e59293a9da6851f8d1852350f3',
                        'created_at' => '2021-08-06 09:32:39',
                        'updated_at' => '2021-08-06 09:32:39',
                    ]
                );


        }
    }
    public static function sign(array $data): string
    {

        return \hash('sha512', \json_encode($data, JSON_THROW_ON_ERROR | JSON_NUMERIC_CHECK));
    }
}
