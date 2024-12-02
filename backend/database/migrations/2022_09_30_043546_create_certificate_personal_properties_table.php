<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatePersonalPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_personal_properties', function (Blueprint $table) {
            $table->id();
            $table->integer('asset_type_id');
            $table->string('name')->nullable();
            $table->integer('status')->default(1);
            $table->integer('step')->default(1);
            $table->decimal('total_price',19,2)->default(0);
            $table->uuid('created_by');
            $table->integer('personal_property_id');
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
        Schema::dropIfExists('certificate_personal_properties');
    }
}
