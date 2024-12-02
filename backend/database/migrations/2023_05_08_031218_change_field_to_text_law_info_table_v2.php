<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldToTextLawInfoTableV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumns('apartment_asset_laws', ['content', 'description', 'legal_name_holder', 'origin_of_use', 'duration'])) {
            Schema::table('apartment_asset_laws', function (Blueprint $table) {
                $table->text('content')->change();
                $table->text('description')->change();
                $table->text('legal_name_holder')->change();
                $table->text('origin_of_use')->change();
                $table->text('duration')->change();
            });
        }
        if (Schema::hasColumns('appraise_law', ['content', 'description', 'legal_name_holder', 'origin_of_use', 'duration'])) {
            Schema::table('appraise_law', function (Blueprint $table) {
                $table->text('content')->change();
                $table->text('description')->change();
                $table->text('legal_name_holder')->change();
                $table->text('origin_of_use')->change();
                $table->text('duration')->change();
            });
        }
        if (Schema::hasColumns('certificate_apartment_laws', ['content', 'description', 'legal_name_holder', 'origin_of_use', 'duration'])) {
            Schema::table('certificate_apartment_laws', function (Blueprint $table) {
                $table->text('content')->change();
                $table->text('description')->change();
                $table->text('legal_name_holder')->change();
                $table->text('origin_of_use')->change();
                $table->text('duration')->change();
            });
        }
        if (Schema::hasColumns('certificate_asset_law', ['content', 'description', 'legal_name_holder', 'origin_of_use', 'duration'])) {
            Schema::table('certificate_asset_law', function (Blueprint $table) {
                $table->text('content')->change();
                $table->text('description')->change();
                $table->text('legal_name_holder')->change();
                $table->text('origin_of_use')->change();
                $table->text('duration')->change();
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
        Schema::table('text_law_info_table_v2', function (Blueprint $table) {
            //
        });
    }
}
