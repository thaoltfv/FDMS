<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdAppraisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraisers', 'user_id')) {
            Schema::table('appraisers', function (Blueprint $table) {
				$table->uuid('user_id')->nullable()->before('created_at');
            });
        }
        if (!Schema::hasColumn('appraisers', 'branch_id')) {
            Schema::table('appraisers', function (Blueprint $table) {
				$table->integer('branch_id')->nullable()->before('created_at');
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
        if (Schema::hasColumn('appraisers', 'user_id')) {
            Schema::table('appraisers', function (Blueprint $table) {
				$table->dropColumn('user_id');
            });
        }
        if (Schema::hasColumn('appraisers', 'branch_id')) {
            Schema::table('appraisers', function (Blueprint $table) {
				$table->dropColumn('branch_id');
            });
        }
    }
}
