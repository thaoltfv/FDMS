<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignkeyApartmentsComparisonGeneral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('compare_asset_generals', function (Blueprint $table) {
                $table->dropForeign('compare_asset_generals_apartment_id_foreign');
            });
        } catch (Exception $ex) {
        }
        try {
            Schema::table('block_lists', function (Blueprint $table) {
                $table->dropForeign('block_lists_apartment_id_foreign');
            });
        } catch (Exception $ex) {
        }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
