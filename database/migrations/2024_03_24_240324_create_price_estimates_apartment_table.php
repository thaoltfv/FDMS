<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceEstimatesApartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('price_estimate_apartment_properties')) {
            Schema::create('price_estimate_apartment_properties', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_id');
                $table->integer('block_id');
                $table->integer('floor_id');
                $table->decimal('area', 8, 2);
                $table->string('apartment_name');
                $table->integer('bedroom_num')->nullable();
                $table->integer('wc_num')->nullable();
                $table->integer('handover_year')->nullable();
                $table->integer('direction_id')->nullable();
                $table->integer('furniture_quality_id')->nullable();
                $table->string('description')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->timestamp('deleted_at')->nullable();
            });
        }
        if (!Schema::hasTable('price_estimate_apartment_finals')) {
            Schema::create('price_estimate_apartment_finals', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_final_id');
                $table->string('name');
                $table->decimal('total_area', 8, 2);
                $table->double('unit_price');
                $table->double('total_price');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasColumn('apartment_assets', 'price_estimate_id')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->integer('price_estimate_id')->nullable();
            });
        }

        if (!Schema::hasColumn('price_estimates', 'project_id')) {
            Schema::table('price_estimates', function (Blueprint $table) {
                $table->integer('project_id')->nullable();
            });
        }
        if (!Schema::hasColumn('price_estimates', 'apartment_asset_id')) {
            Schema::table('price_estimates', function (Blueprint $table) {
                $table->integer('apartment_asset_id')->nullable();
            });
        }
        if (!Schema::hasColumn('price_estimate_finals', 'total_price')) {
            Schema::table('price_estimate_finals', function (Blueprint $table) {
                $table->double('total_price')->nullable();
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
        Schema::dropIfExists('price_estimate_apartment_properties');
        Schema::dropIfExists('price_estimate_apartment_finals');

        if (Schema::hasColumn('apartment_assets', 'price_estimate_id')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->dropColumn('price_estimate_id');
            });
        }

        if (Schema::hasColumn('price_estimates', 'project_id')) {
            Schema::table('price_estimates', function (Blueprint $table) {
                $table->dropColumn('project_id');
            });
        }
        if (Schema::hasColumn('price_estimates', 'apartment_asset_id')) {
            Schema::table('price_estimates', function (Blueprint $table) {
                $table->dropColumn('apartment_asset_id');
            });
        }
        if (Schema::hasColumn('price_estimate_finals', 'total_price')) {
            Schema::table('price_estimate_finals', function (Blueprint $table) {
                $table->dropColumn('total_price');
            });
        }
    }
}
