<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppraisePropertyDetailsSecondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraise_property_details', function (Blueprint $table) {
			$table->boolean('is_zoning')->default(false);
            $table->string('type_zoning')->default('');
			$table->decimal('planning_area', 18, 2)->default(0);
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
