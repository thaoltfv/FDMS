<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNoteInCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'note')) {
            Schema::table('certificates', function (Blueprint $table) {
				$table->text('note')->unsigned()->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('certificates', 'duration_time')) {
            Schema::table('certificates', function (Blueprint $table) {
				$table->integer('duration_time')->unsigned()->nullable()->before('created_at');
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
        if (Schema::hasColumn('certificates', 'note')){
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (Schema::hasColumn('certificates', 'duration_time')){
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('duration_time');
            });
        }
    }
}
