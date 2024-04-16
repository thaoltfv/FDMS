<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class alterColumnPriceEstimateFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_estimate_finals', function (Blueprint $table) {
            $table->string('petitioner_name')->nullable()->change();
            $table->date('request_date')->nullable()->change();
            $table->integer('appraise_purpose_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_estimate_finals', function (Blueprint $table) {
            $table->string('petitioner_name')->nullable(false)->change();
            $table->date('request_date')->nullable(false)->change();
            $table->integer('appraise_purpose_id')->nullable(false)->change();
        });
    }
}
