<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnologicalLineCertificateAssetPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technological_line_certificate_asset_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('technology_asset_id');
            $table->decimal('quantity');
            $table->string('unit');
            $table->decimal('unit_price',19,2);
            $table->decimal('remaining_quality');
            $table->decimal('total_price', 19, 2);
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
        Schema::dropIfExists('technological_line_certificate_asset_prices');
    }
}
