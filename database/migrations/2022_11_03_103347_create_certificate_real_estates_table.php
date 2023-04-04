<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateRealEstatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_real_estates', function (Blueprint $table) {
            $table->id();
            $table->integer('real_estate_id');
            $table->integer('asset_type_id')->nullable();
            $table->string('appraise_asset')->nullable();
            $table->decimal('total_area', 19, 2)->unsigned()->nullable();
            $table->bigInteger('total_price')->unsigned()->nullable();
            $table->integer('round_total')->unsigned()->nullable();
            $table->string('coordinates')->nullable();
            $table->integer('front_side')->nullable();
            $table->uuid('created_by')->nullable();
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
        Schema::dropIfExists('certificate_real_estates');
    }
}
