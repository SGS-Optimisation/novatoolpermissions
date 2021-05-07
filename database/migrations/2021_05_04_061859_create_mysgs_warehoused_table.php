<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMysgsWarehousedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('app.env') !== 'production') {
            Schema::connection('mysgs_warehoused_data')->create('customers', function (Blueprint $table) {
                $table->id('CustomerId');
                $table->string('CustomerName');
                $table->unsignedInteger('PortfolioGroupId')->nullable();
                $table->string('PortfolioGroupName')->nullable();
                $table->unsignedInteger('SimplifiedGroupId')->nullable();
                $table->string('SimplifiedGroupName')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('app.env') !== 'production') {
            Schema::connection('mysgs_warehoused_data')->dropIfExists('customers');
        }
    }
}
