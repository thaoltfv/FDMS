<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalAccountAppraiserCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('appraiser_companies', 'total_account')) {
            Schema::table('appraiser_companies', function (Blueprint $table) {
				$table->integer('total_account')->nullable()->default(0)->before('created_at');
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
        if (Schema::hasColumn('appraiser_companies', 'total_account')){
            Schema::table('appraiser_companies', function (Blueprint $table) {
                $table->dropColumn('total_account');
            });
        }
    }
}
