<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->boolean('is_op')->index('rules_rule_is_op_index')
                ->default(false)->after('client_account_id');

            $table->boolean('is_pm')->index('rules_rule_is_pm_index')
                ->default(false)->after('client_account_id');
        });

        Artisan::call('migrate:rules:opify');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropIndex('rules_rule_is_op_index');
            $table->dropIndex('rules_rule_is_pm_index');
            $table->dropColumn(['is_op', 'is_pm']);
        });
    }
};
