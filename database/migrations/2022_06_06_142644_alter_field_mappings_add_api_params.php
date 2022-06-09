<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFieldMappingsAddApiParams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_mappings', function (Blueprint $table) {
            $table->json('api_params')->nullable()->after('api_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_mappings', function (Blueprint $table) {
            $table->dropColumn('api_params');
        });
    }
}
