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
        Schema::table('client_account_taxonomy', function (Blueprint $table) {
            $table->boolean('use_for_pm_search')->default(false);
            $table->json('metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_account_taxonomy', function (Blueprint $table) {
            $table->dropColumn(['use_for_pm_search', 'metadata']);
        });
    }
};
