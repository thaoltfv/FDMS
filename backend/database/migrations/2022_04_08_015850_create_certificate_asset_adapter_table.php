<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetAdapterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_asset_adapter', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('certificate_assets')
                ->onDelete('cascade');
            $table->integer('asset_general_id');
            $table->foreign('asset_general_id')
                ->references('id')
                ->on('compare_asset_generals')
                ->onDelete('cascade');
			$table->integer('percent')->nullable();
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
        Schema::dropIfExists('certificate_asset_adapter');
    }
}
