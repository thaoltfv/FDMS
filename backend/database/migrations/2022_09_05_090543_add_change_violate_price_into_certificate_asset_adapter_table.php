<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeViolatePriceIntoCertificateAssetAdapterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('certificate_asset_adapter', 'change_violate_price')){
            Schema::table('certificate_asset_adapter', function (Blueprint $table) {
				$table->float('change_violate_price')->nullable()->before('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('certificate_asset_adapter', 'change_violate_price')){
            Schema::table('certificate_asset_adapter', function (Blueprint $table) {
				$table->dropColumn('change_violate_price');
            });
        }
    }
}
