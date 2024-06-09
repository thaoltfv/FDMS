<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnCheckShinhanCertificate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'document_alter_by_bank')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->integer('document_alter_by_bank')->default(0);
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
        if (Schema::hasColumn('certificates', 'document_alter_by_bank')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('document_alter_by_bank');
            });
        }
    }
}
