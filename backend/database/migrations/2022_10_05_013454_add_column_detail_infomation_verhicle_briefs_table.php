<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDetailInfomationVerhicleBriefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'transport_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->integer('transport_id')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'manufacturer_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->integer('manufacturer_id')->nullable();
            });
        }
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'vehicle_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->integer('vehicle_id')->nullable();
            });
        }
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'model')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->string('model')->nullable();
            });
        }
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'manufacturer_country_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->integer('manufacturer_country_id')->nullable();
            });
        }
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'fuel_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->integer('fuel_id')->nullable();
            });
        }
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'manufacturer_year')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->string('manufacturer_year')->nullable();
            });
        }
        if (!Schema::hasColumn('verhicle_certificate_briefs', 'using_year')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->string('using_year')->nullable();
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
        if (Schema::hasColumn('verhicle_certificate_briefs', 'transport_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('transport_id');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_briefs', 'manufacturer_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('manufacturer_id');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_briefs', 'vehicle_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('vehicle_id');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_briefs', 'model')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('model');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_briefs', 'manufacturer_country_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('manufacturer_country_id');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_briefs', 'fuel_id')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('fuel_id');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_briefs', 'manufacturer_year')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('manufacturer_year');
            });
        }
        if (Schema::hasColumn('verhicle_certificate_briefs', 'using_year')) {
            Schema::table('verhicle_certificate_briefs', function (Blueprint $table) {
                $table->dropColumn('using_year');
            });
        }
    }
}
