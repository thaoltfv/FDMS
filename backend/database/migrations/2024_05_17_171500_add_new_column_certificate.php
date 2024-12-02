<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addNewColumnCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'phone_contact')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->text('phone_contact')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'name_contact')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->text('name_contact')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'survey_location')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->text('survey_location')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'survey_time')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->timestamp('survey_time')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'issue_place_card')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->text('issue_place_card')->nullable();
            });
        }
        if (!Schema::hasColumn('certificates', 'issue_date_card')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->date('issue_date_card')->nullable();
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
        if (Schema::hasColumn('certificates', 'phone_contact')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('phone_contact');
            });
        }
        if (Schema::hasColumn('certificates', 'name_contact')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('name_contact');
            });
        }
        if (Schema::hasColumn('certificates', 'survey_location')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('survey_location');
            });
        }
        if (Schema::hasColumn('certificates', 'survey_time')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('survey_time');
            });
        }
        if (Schema::hasColumn('certificates', 'issue_place_card')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('issue_place_card');
            });
        }
        if (Schema::hasColumn('certificates', 'issue_date_card')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('issue_date_card');
            });
        }
    }
}
