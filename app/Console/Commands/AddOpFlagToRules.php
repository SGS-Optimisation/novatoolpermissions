<?php

namespace App\Console\Commands;

use App\Models\Rule;
use Illuminate\Console\Command;

class AddOpFlagToRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rules:opify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all rules Search rules';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Rule::withTrashed()
            ->where('is_op', false)->where('is_pm', false)
            ->update(['is_op' => true]);
    }
}
