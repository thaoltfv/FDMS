<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnViolatePriceAppraiseAdapterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('appraise_adapter', 'change_violet_price')) {
            Schema::table('appraise_adapter', function (Blueprint $table) {
				$table->dropColumn('change_violet_price');
                if(!Schema::hasColumn('appraise_adapter', 'change_violate_price')){
                    Schema::table('appraise_adapter', function (Blueprint $table) {
                        $table->float('change_violate_price')->nullable()->before('created_at');
                    });
                }
            });
        }elseif(!Schema::hasColumn('appraise_adapter', 'change_violate_price')){
            Schema::table('appraise_adapter', function (Blueprint $table) {
				$table->float('change_violate_price')->nullable()->before('created_at');
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
        if(!Schema::hasColumn('appraise_adapter', 'change_violate_price')){
            Schema::table('appraise_adapter', function (Blueprint $table) {
				$table->dropColumn('change_violate_price');
            });
        }
    }
}
