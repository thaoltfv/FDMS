<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNullableColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'image')) {
			Schema::table('users', function (Blueprint $table) {
				$table->string('image')->unsigned()->nullable()->change();
			});
		}
        if (Schema::hasColumn('users', 'appraisers_number')) {
			Schema::table('users', function (Blueprint $table) {
				$table->string('appraisers_number')->unsigned()->nullable()->change();
			});
		}
        if (Schema::hasColumn('users', 'mailing_address')) {
			Schema::table('users', function (Blueprint $table) {
				$table->string('mailing_address')->unsigned()->nullable()->change();
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
        //
    }
}
