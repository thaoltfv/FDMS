<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateAssetPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_asset_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appraise_id');
            $table->foreign('appraise_id')
                ->references('id')
                ->on('certificate_assets')
                ->onDelete('cascade');
			$table->string('slug');
			$table->float('value')->default(0);
			$table->string('description')->nullable();
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
        Schema::dropIfExists('certificate_asset_prices');
    }
}
