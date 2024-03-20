<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnDocumentFileCertificatesAsset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraise_law', 'document_file')) {
            Schema::table('appraise_law', function (Blueprint $table) {
                $table->jsonb('document_file')->nullable();
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
        if (Schema::hasColumn('appraise_law', 'document_file')) {
            Schema::table('appraise_law', function (Blueprint $table) {
                $table->dropColumn('document_file');
            });
        }
    }
}
