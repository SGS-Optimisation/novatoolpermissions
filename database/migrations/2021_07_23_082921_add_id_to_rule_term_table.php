<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdToRuleTermTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('rule_term', 'id')) {
            Schema::table('rule_term', function (Blueprint $table) {
                //
                $table->increments('id');
            });
        }

        Artisan::call('import:rules_audit');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rule_term', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
}
