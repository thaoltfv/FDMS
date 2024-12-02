<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApartmentNameToApartmentSpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('apartment_specifications', 'apartment_name')) {
            Schema::table('apartment_specifications', function (Blueprint $table) {
                $table->string('apartment_name')->nullable();
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
        if (Schema::hasColumn('apartment_specifications', 'apartment_name')) {
            Schema::table('apartment_specifications', function (Blueprint $table) {
                $table->dropColumn('apartment_name');
            });
        }
    }
}
