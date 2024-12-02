<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnZoningDetailApartmentAppraiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('real_estates', ['planning_info', 'planning_source', 'contact_person', 'contact_phone'])) {
            Schema::table('real_estates', function (Blueprint $table) {
                $table->text('planning_info')->nullable();
                $table->text('planning_source')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_phone')->nullable();
            });
        }
        if (!Schema::hasColumns('certificate_real_estates', ['planning_info', 'planning_source', 'contact_person', 'contact_phone'])) {
            Schema::table('certificate_real_estates', function (Blueprint $table) {
                $table->text('planning_info')->nullable();
                $table->text('planning_source')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_phone')->nullable();
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
        if (Schema::hasColumns('real_estates', ['planning_info', 'planning_source', 'contact_person', 'contact_phone'])) {
            Schema::table('real_estates', function (Blueprint $table) {
                $table->dropColumn('planning_info');
                $table->dropColumn('planning_source');
                $table->dropColumn('contact_person');
                $table->dropColumn('contact_phone');
            });
        }
        if (Schema::hasColumns('certificate_real_estates', ['planning_info', 'planning_source', 'contact_person', 'contact_phone'])) {
            Schema::table('certificate_real_estates', function (Blueprint $table) {
                $table->dropColumn('planning_info');
                $table->dropColumn('planning_source');
                $table->dropColumn('contact_person');
                $table->dropColumn('contact_phone');
            });
        }
    }
}
