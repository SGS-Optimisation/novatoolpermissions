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
            $table->primary(['client_account_id', 'taxonomy_id']);
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
            $table->dropPrimary(['client_account_id', 'taxonomy_id']);
        });
    }
};
