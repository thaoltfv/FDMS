<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status');
            $table->integer('ticket_num');
            $table->string('document_num');
            $table->date('document_date');
            $table->string('certificate_num')->nullable();
            $table->date('certificate_date')->nullable();
            $table->string('petitioner_name')->nullable();
            $table->string('petitioner_phone')->nullable();
            $table->string('petitioner_address')->nullable();

            $table->integer('asset_type_id');
            $table->foreign('asset_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

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

            $table->integer('topographic_id')->nullable();
            $table->foreign('topographic_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->string('land_no');
            $table->string('doc_no');
            $table->string('land_no_old')->nullable();
            $table->string('doc_no_old')->nullable();
            $table->string('coordinates');
            $table->string('appraise_asset');

            $table->integer('appraiser_id');
            $table->foreign('appraiser_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraiser_manager_id')->nullable();
            $table->foreign('appraiser_manager_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->integer('appraiser_confirm_id')->nullable();
            $table->foreign('appraiser_confirm_id')
                ->references('id')
                ->on('appraisers')
                ->onDelete('cascade');

            $table->date('appraise_date');

            $table->integer('appraise_purpose_id');
            $table->foreign('appraise_purpose_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->integer('appraise_basis_property_id');
            $table->foreign('appraise_basis_property_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->text('document_description');

            $table->integer('appraise_approach_id');
            $table->foreign('appraise_approach_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');

            $table->integer('appraise_method_used_id');
            $table->foreign('appraise_method_used_id')
                ->references('id')
                ->on('appraise_other_information')
                ->onDelete('cascade');


            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('appraise_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->string('coordinates')->nullable();

            $table->integer('legal_id')->nullable();
            $table->foreign('legal_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->decimal('appraise_land_sum_area', 18, 2)->nullable();
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

            $table->bigInteger('appraise_value_sum_area')->nullable();
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

            $table->string('land_no');
            $table->string('doc_no');
            $table->text('description');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('appraise_property_turning_time', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('appraise_property_id');
            $table->foreign('appraise_property_id')
                ->references('id')
                ->on('appraise_properties')
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

        Schema::create('appraise_property_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('appraise_property_id');
            $table->foreign('appraise_property_id')
                ->references('id')
                ->on('appraise_properties')
                ->onDelete('cascade');

            $table->integer('land_type_purpose_id')->nullable();
            $table->foreign('land_type_purpose_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->decimal('total_area', 18, 2)->nullable();
            $table->float('estimation_value')->nullable();

            $table->integer('position_type_id')->nullable();
            $table->foreign('position_type_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');

            $table->double('k_rate')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('appraise_tangible_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
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
            $table->decimal('remaining_quality', 18, 2)->nullable();
            $table->decimal('total_construction_base', 18, 2)->nullable();
            $table->decimal('total_construction_area', 18, 2)->nullable();
            $table->integer('start_using_year')->nullable();
            $table->integer('plot_num')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('appraise_other_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->integer('other_type_asset_id')->nullable();
            $table->foreign('other_type_asset_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('appraise_pics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');
            $table->string('link')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('appraise_law', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->string('date')->nullable();
            $table->text('description')->nullable();
            $table->string('legal_name_holder')->nullable();
            $table->string('certifying_agency')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('origin_of_use')->nullable();
            $table->boolean('is_zoning')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('appraise_legal_documents_on_valuation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('appraise_legal_documents_on_land', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });
        Schema::create('appraise_legal_documents_on_construction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        Schema::create('appraise_legal_documents_on_local', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('appraises')
                ->onDelete('cascade');

            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law_documents')
                ->onDelete('cascade');

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
        Schema::dropIfExists('appraise_law');
        Schema::dropIfExists('appraise_pics');
        Schema::dropIfExists('appraise_other_assets');
        Schema::dropIfExists('appraise_tangible_assets');
        Schema::dropIfExists('appraise_property_details');
        Schema::dropIfExists('appraise_property_turning_time');
        Schema::dropIfExists('appraise_properties');
        Schema::dropIfExists('appraise_legal_documents_on_land');
        Schema::dropIfExists('appraise_legal_documents_on_valuation');
        Schema::dropIfExists('appraise_legal_documents_on_construction');
        Schema::dropIfExists('appraise_legal_documents_on_local');
        Schema::dropIfExists('appraises');

    }
}
