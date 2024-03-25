<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('price_estimates')) {
            Schema::create('price_estimates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('status');
                $table->integer('asset_type_id');
                $table->integer('province_id')->nullable();
                $table->integer(
                    'district_id'
                )->nullable();
                $table->integer('step')->nullable();
                $table->integer('ward_id')->nullable();
                $table->integer('street_id')->nullable();
                $table->integer('distance_id')->nullable();
                $table->string('land_no')->nullable();
                $table->string('doc_no')->nullable();
                $table->string('coordinates');
                $table->string('address_number')->nullable();
                $table->string('appraise_asset');
                $table->string('full_address')->nullable();
                $table->string('full_address_street')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->uuid('created_by')->nullable();
                $table->integer('filter_year')->default(1)->nullable();
                $table->softDeletes();
                $table->integer('appraise_id')->nullable();
            });
        }

        if (!Schema::hasTable(
            'price_estimate_properties'
        )) {
            Schema::create('price_estimate_properties', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_id');
                $table->decimal('front_side', 8, 2)->nullable();
                $table->decimal('appraise_land_sum_area', 8, 2)->nullable();
                $table->decimal('main_road_length', 8, 2)->nullable();
                $table->integer('material_id')->nullable();
                $table->text('description')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable(
            'price_estimate_property_details'
        )) {
            Schema::create('price_estimate_property_details', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_property_id');
                $table->integer('land_type_purpose_id')->nullable();
                $table->decimal('total_area', 8, 2)->nullable();
                $table->boolean('is_transfer_facility')->nullable();
                $table->decimal('planning_area', 8, 2)->default(0);
                $table->decimal('main_area', 8, 2)->nullable();
                $table->string('type_zoning')->default('');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable(
            'price_estimate_property_turning_time'
        )) {
            Schema::create('price_estimate_property_turning_time', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_property_id');
                $table->string('turning')->nullable();
                $table->decimal('main_road_length', 8, 2)->nullable();
                $table->integer('material_id')->nullable();
                $table->decimal('main_road_distance', 8, 2)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable(
            'price_estimate_versions'
        )) {
            Schema::create('price_estimate_versions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_id')->nullable();
                $table->string('version');
                $table->integer('status');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable(
            'price_estimate_has_assets'
        )) {
            Schema::create('price_estimate_has_assets', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('asset_general_id');
                $table->integer('asset_property_detail_id')->nullable();
                $table->integer('price_estimate_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
                $table->integer('version')->nullable();
                $table->bigInteger('asset_price')->nullable();
                $table->bigInteger('appraise_price')->nullable();
            });
        }
        if (!Schema::hasTable(
            'price_estimate_pics'
        )) {
            Schema::create('price_estimate_pics', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_id');
                $table->string('link')->nullable();
                $table->string('description')->nullable();
                $table->integer('type_id')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable('price_estimate_finals')) {
            Schema::create('price_estimate_finals', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_id');
                $table->string('petitioner_name');
                $table->date('request_date');
                $table->integer('appraise_purpose_id');
                $table->integer('asset_type_id');
                $table->string('appraise_asset');
                $table->string('full_address');
                $table->string('coordinates');
                $table->string('description');
                $table->string('img_map')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
                $table->uuid('created_by')->nullable();
                $table->string('full_address_street')->nullable();
            });
        }
        if (!Schema::hasTable('price_estimate_final_lands')) {
            Schema::create('price_estimate_final_lands', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_final_id');
                $table->integer('land_type_purpose_id')->nullable();
                $table->decimal('total_area', 8, 2)->default(0);
                $table->decimal('planning_area', 8, 2)->default(0);
                $table->decimal('main_area', 8, 2)->default(0);
                $table->double('unit_price', 8, 2)->default(0);
                $table->double('total_price', 8, 2)->default(0);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable('price_estimate_final_tangible_assets')) {
            Schema::create('price_estimate_final_tangible_assets', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price_estimate_final_id');
                $table->integer('building_type_id');
                $table->decimal('remaining_quality', 8, 2)->nullable();
                $table->decimal('total_construction_area', 8, 2)->default(0);
                $table->double('unit_price', 8, 2)->default(0);
                $table->double('total_price', 8, 2)->default(0);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('appraises', 'price_estimate_id')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->integer('price_estimate_id')->nullable();
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
        Schema::dropIfExists('price_estimates');
        Schema::dropIfExists('price_estimate_property_details');
        Schema::dropIfExists('price_estimate_properties');
        Schema::dropIfExists('price_estimate_property_turning_time');
        Schema::dropIfExists('price_estimate_versions');
        Schema::dropIfExists('price_estimate_has_assets');
        Schema::dropIfExists('price_estimate_finals');
        Schema::dropIfExists('price_estimate_final_lands');
        Schema::dropIfExists('price_estimate_final_tangible_assets');
        Schema::dropIfExists('price_estimate_pics');

        if (Schema::hasColumn('appraises', 'price_estimate_id')) {
            Schema::table('appraises', function (Blueprint $table) {
                $table->dropColumn('price_estimate_id');
            });
        }
    }
}
