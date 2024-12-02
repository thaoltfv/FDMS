<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnologicalLineCertificateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technological_line_certificate_assets', function (Blueprint $table) {
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
        Schema::dropIfExists('technological_line_certificate_assets');
    }
}
