<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDetailTypeCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'document_type')) {
            Schema::table('certificates', function (Blueprint $table) {
				$table->json('document_type')->nullable()->before('created_at');
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
        if (Schema::hasColumn('certificates', 'document_type')) {
            Schema::table('certificates', function (Blueprint $table) {
				$table->dropColumn('document_type');
            });
        }
    }
}
