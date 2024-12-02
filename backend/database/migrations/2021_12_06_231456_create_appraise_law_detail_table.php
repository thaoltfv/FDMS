<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraiseLawDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraise_law', function (Blueprint $table) {
            $table->dropColumn('expiry_type');
            $table->dropColumn('expiry_date');
            $table->dropColumn('is_zoning');
            $table->decimal('total_area', 18, 10)->nullable();
        });

        Schema::create('appraise_law_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('appraise_law_id');
            $table->foreign('appraise_law_id')
                ->references('id')
                ->on('appraise_law')
                ->onDelete('cascade');

            $table->integer('land_type_purpose_id')->nullable();
            $table->foreign('land_type_purpose_id')
                ->references('id')
                ->on('dictionaries')
                ->onDelete('cascade');
            $table->decimal('total_area', 18, 10)->nullable();
            $table->boolean('expiry_type')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_zoning')->nullable();
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
        Schema::dropIfExists('appraise_law_details');
    }
}
