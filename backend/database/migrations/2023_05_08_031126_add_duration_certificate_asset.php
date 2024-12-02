<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationCertificateAsset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificate_asset_law', 'duration')) {
            Schema::table('certificate_asset_law', function (Blueprint $table) {
				$table->text('duration')->nullable()->before('created_at');
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
        if (Schema::hasColumn('certificate_asset_law', 'duration')){
            Schema::table('certificate_asset_law', function (Blueprint $table) {
                $table->dropColumn('duration');
            });
        }
    }
}
