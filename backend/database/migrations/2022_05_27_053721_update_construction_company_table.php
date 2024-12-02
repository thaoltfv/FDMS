<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConstructionCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('construction_company', function (Blueprint $table) {
			$table->string('name')->nullable()->after('construction_company_id');
            $table->string('address')->nullable()->after('name');
            $table->string('phone_number')->nullable()->after('address');;
            $table->string('manager_name')->nullable()->after('phone_number');
            $table->bigInteger('unit_price_m2')->nullable()->after('manager_name');
            $table->boolean('is_defaults')->nullable()->default(true)->after('unit_price_m2');
		});
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
