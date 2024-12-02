<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPP2BuildingPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('building_prices', 'h1')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('h1')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'h2')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('h2')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'h3')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('h3')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'h4')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('h4')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'h5')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('h5')->default(0)->before('created_at');
            });
        }

        if (!Schema::hasColumn('building_prices', 'p1')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('p1')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'p2')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('p2')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'p3')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('p3')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'p4')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('p4')->default(0)->before('created_at');
            });
        }
        if (!Schema::hasColumn('building_prices', 'p5')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->decimal('p5')->default(0)->before('created_at');
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
        if (Schema::hasColumn('building_prices', 'h1')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('h1');
            });
        }
        if (Schema::hasColumn('building_prices', 'h2')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('h2');
            });
        }
        if (Schema::hasColumn('building_prices', 'h3')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('h3');
            });
        }
        if (Schema::hasColumn('building_prices', 'h4')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('h4');
            });
        }
        if (Schema::hasColumn('building_prices', 'h5')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('h5');
            });
        }

        if (Schema::hasColumn('building_prices', 'p1')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('p1');
            });
        }
        if (Schema::hasColumn('building_prices', 'p2')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('p2');
            });
        }
        if (Schema::hasColumn('building_prices', 'p3')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('p3');
            });
        }
        if (Schema::hasColumn('building_prices', 'p4')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('p4');
            });
        }
        if (Schema::hasColumn('building_prices', 'p5')) {
            Schema::table('building_prices', function (Blueprint $table) {
				$table->dropColumn('p5');
            });
        }

    }
}
