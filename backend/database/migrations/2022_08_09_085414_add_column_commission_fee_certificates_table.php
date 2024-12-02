<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCommissionFeeCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('certificates', 'commission_fee')) {
			Schema::table('certificates', function (Blueprint $table) {
				$table->float('commission_fee')->unsigned()->default(0)->before('created_at');
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
        if (Schema::hasColumn('certificates', 'commission_fee')){
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropColumn('commission_fee');
            });
        }
    }
}
