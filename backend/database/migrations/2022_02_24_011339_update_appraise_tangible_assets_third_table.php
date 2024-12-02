<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseTangibleAssetsThirdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraise_tangible_assets', function (Blueprint $table) {
            $table->string('contruction_description')->nullable();
		});
		Schema::table('certificate_asset_tangible_assets', function (Blueprint $table) {
            $table->string('contruction_description')->nullable();
		});
		$deafault = "'+ Móng, cột: \n+ Dầm, sàn BTCT chịu lực: \n+ Tường xây: \n+ Mái BTCT: \n+ Nền lát: \n+ Cửa đi, cửa sổ: \n+ Khu vệ sinh: \n+ Khu bếp: \n+ Cầu thang:'";
		\DB::statement('UPDATE appraise_tangible_assets SET contruction_description = '.$deafault);
		\DB::statement('UPDATE certificate_asset_tangible_assets SET contruction_description = '.$deafault);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appraise_tangible_assets', function (Blueprint $table) {
            $table->dropColumn('contruction_description');
		});
		Schema::table('certificate_asset_tangible_assets', function (Blueprint $table) {
            $table->dropColumn('contruction_description');
		});
    }
}
