<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateApartmentComparisonFactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_apartment_comparison_factors', function (Blueprint $table) {
            $table->id();
            $table->integer('apartment_asset_id');
            $table->integer('asset_general_id');
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('apartment_title')->nullable();
            $table->string('asset_title')->nullable();
            $table->integer('adjust_percent')->nullable();
            $table->integer('position')->default(0);
            $table->string('description')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('certificate_apartment_comparison_factors');
    }
}
