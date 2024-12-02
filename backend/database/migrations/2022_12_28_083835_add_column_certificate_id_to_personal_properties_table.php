<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCertificateIdToPersonalPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('personal_properties', 'certificate_id')) {
            Schema::table('personal_properties', function (Blueprint $table) {
                $table->integer('certificate_id')->unsigned()->nullable();
            });
        }
        if (!Schema::hasColumn('apartment_assets', 'certificate_id')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->integer('certificate_id')->unsigned()->nullable();
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
        if (Schema::hasColumn('personal_properties', 'certificate_id')) {
            Schema::table('personal_properties', function (Blueprint $table) {
                $table->dropColumn('certificate_id');
            });
        }
        if (Schema::hasColumn('apartment_assets', 'certificate_id')) {
            Schema::table('apartment_assets', function (Blueprint $table) {
                $table->dropColumn('certificate_id');
            });
        }
    }
}
