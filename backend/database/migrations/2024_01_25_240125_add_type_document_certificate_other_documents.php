<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addTypeDocumentCertificateOtherDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificate_other_documents', 'type_document')) {
            Schema::table('certificate_other_documents', function (Blueprint $table) {
                $table->text('type_document')->nullable();
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
        if (!Schema::hasColumn('certificate_other_documents', 'type_document')) {
            Schema::table('certificate_other_documents', function (Blueprint $table) {
                $table->dropColumn('type_document');
            });
        }
    }
}
