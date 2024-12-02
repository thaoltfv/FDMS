<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompareAssetGeneralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compare_asset_generals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('input_source');
            $table->integer('asset_type_id')->nullable();
            $table->foreign('asset_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');
            $table->integer('status')->nullable();

            $table->integer('province_id')->nullable();
            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');

            $table->integer('district_id')->nullable();
            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('cascade');

            $table->integer('ward_id')->nullable();
            $table->foreign('ward_id')
                ->references('id')
                ->on('wards')
                ->onDelete('cascade');

            $table->integer('street_id')->nullable();
            $table->foreign('street_id')
                ->references('id')
                ->on('streets')
                ->onDelete('cascade');

            $table->integer('distance_id')->nullable();
            $table->foreign('distance_id')
                ->references('id')
                ->on('distances')
                ->onDelete('cascade');

            $table->text('full_address')->nullable();
            $table->string('land_no')->nullable();
            $table->string('doc_no')->nullable();
            $table->string('coordinates');

            $table->integer('source_id')->nullable();
            $table->foreign('source_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->string('public_date')->nullable();
            $table->text('contact_person')->nullable();
            $table->string('contact_phone')->nullable();

            $table->integer('transaction_type_id')->nullable();
            $table->foreign('transaction_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('topographic')->nullable();
            $table->foreign('topographic')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->string('max_value_description')->nullable();
            $table->bigInteger('average_land_unit_price')->nullable();
            $table->decimal('total_area', 18, 2)->nullable();
            $table->bigInteger('total_area_amount')->nullable();
            $table->bigInteger('total_land_unit_price')->nullable();
            $table->decimal('total_construction_area', 18, 2);
            $table->bigInteger('total_construction_amount')->nullable();
            $table->bigInteger('total_amount')->nullable();
            $table->bigInteger('total_raw_amount')->nullable();
            $table->bigInteger('total_estimate_amount')->nullable();
            $table->bigInteger('total_order_amount')->nullable();
            $table->bigInteger('adjust_amount')->nullable();
            $table->float('adjust_percent')->nullable();
            $table->bigInteger('convert_fee_total')->nullable();
            $table->uuid('created_by');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_general_id');
            $table->foreign('asset_general_id')
                ->references('id')
                ->on('compare_asset_generals')
                ->onDelete('cascade');

            $table->string('coordinates')->nullable();

            $table->integer('legal_id')->nullable();
            $table->foreign('legal_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->decimal('asset_general_land_sum_area', 18, 2)->nullable();
            $table->decimal('front_side', 18, 2)->nullable();
            $table->decimal('individual_road', 18, 2)->nullable();

            $table->integer('zoning_id')->nullable();
            $table->foreign('zoning_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('land_type_id')->nullable();
            $table->foreign('land_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->bigInteger('asset_general_value_sum_area')->nullable();
            $table->decimal('front_side_width', 18, 2)->nullable();
            $table->decimal('insight_width', 18, 2)->nullable();
            $table->string('size_description')->nullable();
            $table->decimal('main_road_length', 18, 2)->nullable();

            $table->integer('material_id')->nullable();
            $table->foreign('material_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('land_shape_id')->nullable();
            $table->foreign('land_shape_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('business_id')->nullable();
            $table->foreign('business_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('electric_water_id')->nullable();
            $table->foreign('electric_water_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('social_security_id')->nullable();
            $table->foreign('social_security_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('feng_shui_id')->nullable();
            $table->foreign('feng_shui_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('paymen_method_id')->nullable();
            $table->foreign('paymen_method_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('condition_id')->nullable();
            $table->foreign('condition_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->text('description');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_property_doc', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('compare_property_id');
            $table->foreign('compare_property_id')
                ->references('id')
                ->on('compare_properties')
                ->onDelete('cascade');
            $table->integer('doc_num')->nullable();
            $table->integer('plot_num')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_property_turning_time', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('compare_property_id');
            $table->foreign('compare_property_id')
                ->references('id')
                ->on('compare_properties')
                ->onDelete('cascade');

            $table->decimal('main_road_length', 18, 2)->nullable();
            $table->boolean('is_near_main_road')->nullable();
            $table->boolean('is_alley_with_connection')->nullable();

            $table->integer('material_id')->nullable();
            $table->foreign('material_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');
            $table->string('turning')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_property_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('compare_property_id');
            $table->foreign('compare_property_id')
                ->references('id')
                ->on('compare_properties')
                ->onDelete('cascade');

            $table->integer('land_type_purpose')->nullable();
            $table->foreign('land_type_purpose')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->decimal('total_area',18,2)->nullable();
            $table->float('estimation_value')->nullable();

            $table->integer('position_type_id')->nullable();
            $table->foreign('position_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->bigInteger('circular_unit_price')->nullable();
            $table->bigInteger('convert_fee')->nullable();
            $table->double('k_rate')->nullable();
            $table->bigInteger('price_land')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_tangible_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_general_id');
            $table->foreign('asset_general_id')
                ->references('id')
                ->on('compare_asset_generals')
                ->onDelete('cascade');

            $table->integer('building_type_id')->nullable();
            $table->foreign('building_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('building_category_id')->nullable();
            $table->foreign('building_category_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->integer('floor')->nullable();
            $table->boolean('gpxd')->nullable();
            $table->bigInteger('unit_price_m2')->nullable();
            $table->decimal('remaining_quality',18,2)->nullable();
            $table->decimal('total_construction_base',18,2)->nullable();
            $table->decimal('estimation_value', 18, 2)->nullable();
            $table->decimal('total_construction_area', 18, 2)->nullable();
            $table->integer('start_using_year')->nullable();
            $table->integer('plot_num')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_other_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_general_id');
            $table->foreign('asset_general_id')
                ->references('id')
                ->on('compare_asset_generals')
                ->onDelete('cascade');

            $table->integer('other_type_asset_id')->nullable();
            $table->foreign('other_type_asset_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');
            $table->bigInteger('total_amount')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('compare_general_pics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_general_id');
            $table->foreign('asset_general_id')
                ->references('id')
                ->on('compare_asset_generals')
                ->onDelete('cascade');
            $table->string('link')->nullable();
            $table->string('picture_type')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('compare_property_pics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('compare_property_id');
            $table->foreign('compare_property_id')
                ->references('id')
                ->on('compare_properties')
                ->onDelete('cascade');
            $table->string('link')->nullable();
            $table->string('picture_type')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('compare_tangible_pics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('compare_tangible_id');
            $table->foreign('compare_tangible_id')
                ->references('id')
                ->on('compare_tangible_assets')
                ->onDelete('cascade');
            $table->string('link')->nullable();
            $table->string('picture_type')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('compare_other_pics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_other_id');
            $table->foreign('asset_other_id')
                ->references('id')
                ->on('compare_other_assets')
                ->onDelete('cascade');
            $table->string('picture_type')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_specification_has_basic_utilities');
        Schema::dropIfExists('room_furniture_details');
        Schema::dropIfExists('room_details');
        Schema::dropIfExists('block_specifications');
        Schema::dropIfExists('block_lists');
        Schema::dropIfExists('compare_general_pics');
        Schema::dropIfExists('compare_property_pics');
        Schema::dropIfExists('compare_tangible_pics');
        Schema::dropIfExists('compare_other_pics');
        Schema::dropIfExists('compare_property_details');
        Schema::dropIfExists('compare_property_turning_time');
        Schema::dropIfExists('compare_property_doc');
        Schema::dropIfExists('compare_tangible_assets');
        Schema::dropIfExists('compare_other_assets');
        Schema::dropIfExists('compare_properties');
        Schema::dropIfExists('compare_asset_generals');

    }
}
