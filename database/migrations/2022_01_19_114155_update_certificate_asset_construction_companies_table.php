<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCertificateAssetConstructionCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('certificate_asset_construction_companies');
		Schema::create('certificate_asset_construction_companies', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('certificate_id');
			$table->integer('appraise_id');
            $table->string('name');
            $table->string('address');
            $table->string('phone_number');
            $table->string('manager_name');
            $table->bigInteger('unit_price_m2');
            $table->boolean('is_defaults')->default(true);
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
        Schema::dropIfExists('certificate_asset_construction_companies');
    }
}
