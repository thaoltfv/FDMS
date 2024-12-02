<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatusExpiredAtCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'status_expired_at')) {
            Schema::table('certificates', function (Blueprint $table) {
				$table->dateTime('status_expired_at')->nullable()->before('created_at');
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
        if (Schema::hasColumn('certificates', 'status_expired_at')) {
            Schema::table('certificates', function (Blueprint $table) {
				$table->dropColumn('status_expired_at');
            });
        }
    }
}
