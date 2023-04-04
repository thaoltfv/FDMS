<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineCertificateBriefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_certificate_briefs', function (Blueprint $table) {
            $table->id();
            $table->integer('personal_property_id');
            $table->string('name');
            $table->integer('asset_type_id');
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->integer('step')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_certificate_briefs');
    }
}
