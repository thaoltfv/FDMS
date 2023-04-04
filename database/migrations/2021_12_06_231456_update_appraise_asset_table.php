<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraiseAssetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraises', function (Blueprint $table) {
            $table->dropColumn('ticket_num');
            $table->dropColumn('document_num');
            $table->dropColumn('document_date');
            $table->dropColumn('certificate_num');
            $table->dropColumn('certificate_date');
            $table->dropColumn('petitioner_name');
            $table->dropColumn('petitioner_phone');
            $table->dropColumn('petitioner_address');
            $table->dropColumn('appraise_date');
            $table->dropColumn('appraiser_id');
            $table->dropColumn('appraiser_confirm_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
