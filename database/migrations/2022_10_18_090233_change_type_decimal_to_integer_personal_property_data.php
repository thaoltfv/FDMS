<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeDecimalToIntegerPersonalPropertyData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumns('other_certificate_brief_prices',['quantity','unit_price','total_price'])){
            Schema::table('other_certificate_brief_prices', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->unsigned()->change();
                    $table->bigInteger('unit_price')->nullable()->unsigned()->change();
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumns('machine_certificate_brief_prices',['quantity','unit_price','total_price'])){
            Schema::table('machine_certificate_brief_prices', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->unsigned()->change();
                    $table->bigInteger('unit_price')->nullable()->unsigned()->change();
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumns('verhicle_certificate_brief_prices',['quantity','unit_price','total_price'])){
            Schema::table('verhicle_certificate_brief_prices', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->unsigned()->change();
                    $table->bigInteger('unit_price')->nullable()->unsigned()->change();
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumns('other_certificate_asset_prices',['quantity','unit_price','total_price'])){
            Schema::table('other_certificate_asset_prices', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->unsigned()->change();
                    $table->bigInteger('unit_price')->nullable()->unsigned()->change();
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumns('machine_certificate_asset_prices',['quantity','unit_price','total_price'])){
            Schema::table('machine_certificate_asset_prices', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->unsigned()->change();
                    $table->bigInteger('unit_price')->nullable()->unsigned()->change();
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumns('verhicle_certificate_asset_prices',['quantity','unit_price','total_price'])){
            Schema::table('verhicle_certificate_asset_prices', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->unsigned()->change();
                    $table->bigInteger('unit_price')->nullable()->unsigned()->change();
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumns('technological_line_certificate_asset_prices',['quantity','unit_price','total_price'])){
            Schema::table('technological_line_certificate_asset_prices', function (Blueprint $table) {
                    $table->integer('quantity')->nullable()->unsigned()->change();
                    $table->bigInteger('unit_price')->nullable()->unsigned()->change();
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumn('personal_properties','total_price')){
            Schema::table('personal_properties', function (Blueprint $table) {
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
            });
        }
        if(Schema::hasColumn('certificate_personal_properties','total_price')){
            Schema::table('certificate_personal_properties', function (Blueprint $table) {
                    $table->bigInteger('total_price')->nullable()->unsigned()->change();
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
    }
}
