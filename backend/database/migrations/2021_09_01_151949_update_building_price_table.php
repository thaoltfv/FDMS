<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBuildingPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_prices', function (Blueprint $table) {

            $table->dropColumn('vendor_name');
            $table->date('effect_from')->unsigned()->nullable()->change();
            $table->date('effect_to')->unsigned()->nullable()->change();
            $table->integer('building_category')->unsigned()->nullable()->change();
            $table->integer('level')->nullable();
            $table->integer('rate')->nullable();
            $table->integer('structure')->nullable();
            $table->integer('crane')->nullable();
            $table->integer('aperture')->nullable();
            $table->integer('factory_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
