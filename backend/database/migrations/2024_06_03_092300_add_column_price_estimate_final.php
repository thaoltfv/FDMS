<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addColumnPriceEstimateFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('price_estimate_finals', 'note')) {
            Schema::table('price_estimate_finals', function (Blueprint $table) {
                $table->text('note')->nullable();
            });
        }
        if (!Schema::hasColumn('price_estimate_finals', 'difference_amplitude')) {
            Schema::table('price_estimate_finals', function (Blueprint $table) {
                $table->float('difference_amplitude')->nullable();
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
        if (Schema::hasColumn('price_estimate_finals', 'note')) {
            Schema::table('price_estimate_finals', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (Schema::hasColumn('price_estimate_finals', 'difference_amplitude')) {
            Schema::table('price_estimate_finals', function (Blueprint $table) {
                $table->dropColumn('difference_amplitude');
            });
        }
    }
}
