<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeployNova extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Bảng apartment_asset_adapter đổi cột percent từ integer -> double
        if (Schema::hasColumn('apartment_asset_adapter', 'percent')) {
            Schema::table('apartment_asset_adapter', function (Blueprint $table) {
				$table->double('percent')->change();
            });
        }
        // Bảng apartment_asset_adapter thêm cột change_negotiated_price loại double
        if (!Schema::hasColumn('apartment_asset_adapter', 'change_negotiated_price')) {
            Schema::table('apartment_asset_adapter', function (Blueprint $table) {
				$table->double('change_negotiated_price')->nullable();
            });
        }
        // Bảng apartment_asset_comparison_factors thêm cột adjust_coefficient dạng double default 100
        if (!Schema::hasColumn('apartment_asset_comparison_factors', 'adjust_coefficient')) {
            Schema::table('apartment_asset_comparison_factors', function (Blueprint $table) {
				$table->double('adjust_coefficient')->nullable()->default(100);
            });
        }
        // Bảng apartment_asset_properties thêm cột loai_can_ho_id dạng integer
        if (!Schema::hasColumn('apartment_asset_properties', 'loai_can_ho_id')) {
            Schema::table('apartment_asset_properties', function (Blueprint $table) {
				$table->integer('loai_can_ho_id')->nullable();
            });
        }
        // Bảng appraise_adapter thêm cột change_negotiated_price dạng double
        if (!Schema::hasColumn('appraise_adapter', 'change_negotiated_price')) {
            Schema::table('appraise_adapter', function (Blueprint $table) {
				$table->double('change_negotiated_price')->nullable();
            });
        }
        // Bảng appraise_comparison_factor thêm cột adjust_coefficient dạng double default 100
        if (!Schema::hasColumn('appraise_comparison_factor', 'adjust_coefficient')) {
            Schema::table('appraise_comparison_factor', function (Blueprint $table) {
				$table->double('adjust_coefficient')->nullable()->default(100);
            });
        }
        // Bảng certificate_apartment_adapters đổi cột percent từ integer -> double
        if (Schema::hasColumn('certificate_apartment_adapters', 'percent')) {
            Schema::table('certificate_apartment_adapters', function (Blueprint $table) {
				$table->double('percent')->change();
            });
        }
        // Bảng certificate_apartment_adapters thêm cột change_negotiated_price loại double
        if (!Schema::hasColumn('certificate_apartment_adapters', 'change_negotiated_price')) {
            Schema::table('certificate_apartment_adapters', function (Blueprint $table) {
				$table->double('change_negotiated_price')->nullable();
            });
        }
        // Bảng certificate_apartment_comparison_factors thêm cột adjust_coefficient dạng double default 100
        if (!Schema::hasColumn('certificate_apartment_comparison_factors', 'adjust_coefficient')) {
            Schema::table('certificate_apartment_comparison_factors', function (Blueprint $table) {
				$table->double('adjust_coefficient')->nullable()->default(100);
            });
        }
        // Bảng certificate_apartment_properties thêm cột loai_can_ho_id dạng integer
        if (!Schema::hasColumn('certificate_apartment_properties', 'loai_can_ho_id')) {
            Schema::table('certificate_apartment_properties', function (Blueprint $table) {
				$table->integer('loai_can_ho_id')->nullable();
            });
        }
        // Bảng certificate_asset_adapter đổi cột percent từ integer -> double
        if (Schema::hasColumn('certificate_asset_adapter', 'percent')) {
            Schema::table('certificate_asset_adapter', function (Blueprint $table) {
				$table->double('percent')->change();
            });
        }
        // Bảng certificate_asset_adapter thêm cột change_negotiated_price loại double
        if (!Schema::hasColumn('certificate_asset_adapter', 'change_negotiated_price')) {
            Schema::table('certificate_asset_adapter', function (Blueprint $table) {
				$table->double('change_negotiated_price')->nullable();
            });
        }
        // Bảng certificate_asset_comparison_factor thêm cột adjust_coefficient dạng double default 100
        if (!Schema::hasColumn('certificate_asset_comparison_factor', 'adjust_coefficient')) {
            Schema::table('certificate_asset_comparison_factor', function (Blueprint $table) {
				$table->double('adjust_coefficient')->nullable()->default(100);
            });
        }
        // Bảng room_details thêm cột loai_can_ho_id dạng integer
        if (!Schema::hasColumn('room_details', 'loai_can_ho_id')) {
            Schema::table('room_details', function (Blueprint $table) {
				$table->integer('loai_can_ho_id')->nullable();
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
        // Bảng apartment_asset_adapter đổi cột percent từ integer -> double (reverse)
        if (Schema::hasColumn('apartment_asset_adapter', 'percent')) {
            Schema::table('apartment_asset_adapter', function (Blueprint $table) {
				$table->integer('percent')->change();
            });
        }
        // Bảng apartment_asset_adapter thêm cột change_negotiated_price loại double (reverse)
        if (Schema::hasColumn('apartment_asset_adapter', 'change_negotiated_price')) {
            Schema::table('apartment_asset_adapter', function (Blueprint $table) {
				$table->dropColumn('change_negotiated_price');
            });
        }
        // Bảng apartment_asset_comparison_factors thêm cột adjust_coefficient dạng double default 100 (reverse)
        if (Schema::hasColumn('apartment_asset_comparison_factors', 'adjust_coefficient')) {
            Schema::table('apartment_asset_comparison_factors', function (Blueprint $table) {
				$table->dropColumn('adjust_coefficient');
            });
        }
        // Bảng apartment_asset_properties thêm cột loai_can_ho_id dạng integer (reverse)
        if (Schema::hasColumn('apartment_asset_properties', 'loai_can_ho_id')) {
            Schema::table('apartment_asset_properties', function (Blueprint $table) {
				$table->dropColumn('loai_can_ho_id');
            });
        }
        // Bảng appraise_adapter thêm cột change_negotiated_price dạng double (reverse)
        if (Schema::hasColumn('appraise_adapter', 'change_negotiated_price')) {
            Schema::table('appraise_adapter', function (Blueprint $table) {
				$table->dropColumn('change_negotiated_price');
            });
        }
        // Bảng appraise_comparison_factor thêm cột adjust_coefficient dạng double default 100 (reverse)
        if (Schema::hasColumn('appraise_comparison_factor', 'adjust_coefficient')) {
            Schema::table('appraise_comparison_factor', function (Blueprint $table) {
				$table->dropColumn('adjust_coefficient');
            });
        }
        // Bảng certificate_apartment_adapters đổi cột percent từ integer -> double (reverse)
        if (Schema::hasColumn('certificate_apartment_adapters', 'percent')) {
            Schema::table('certificate_apartment_adapters', function (Blueprint $table) {
				$table->integer('percent')->change();
            });
        }
        // Bảng certificate_apartment_adapters thêm cột change_negotiated_price loại double (reverse)
        if (Schema::hasColumn('certificate_apartment_adapters', 'change_negotiated_price')) {
            Schema::table('certificate_apartment_adapters', function (Blueprint $table) {
				$table->dropColumn('change_negotiated_price');
            });
        }
        // Bảng certificate_apartment_comparison_factors thêm cột adjust_coefficient dạng double default 100 (reverse)
        if (!Schema::hasColumn('certificate_apartment_comparison_factors', 'adjust_coefficient')) {
            Schema::table('certificate_apartment_comparison_factors', function (Blueprint $table) {
				$table->dropColumn('adjust_coefficient');
            });
        }
        // Bảng certificate_apartment_properties thêm cột loai_can_ho_id dạng integer (reverse)
        if (Schema::hasColumn('certificate_apartment_properties', 'loai_can_ho_id')) {
            Schema::table('certificate_apartment_properties', function (Blueprint $table) {
				$table->dropColumn('loai_can_ho_id');
            });
        }
        // Bảng certificate_asset_adapter đổi cột percent từ integer -> double (reverse)
        if (Schema::hasColumn('certificate_asset_adapter', 'percent')) {
            Schema::table('certificate_asset_adapter', function (Blueprint $table) {
				$table->integer('percent')->change();
            });
        }
        // Bảng certificate_asset_adapter thêm cột change_negotiated_price loại double (reverse)
        if (Schema::hasColumn('certificate_asset_adapter', 'change_negotiated_price')) {
            Schema::table('certificate_asset_adapter', function (Blueprint $table) {
				$table->dropColumn('change_negotiated_price');
            });
        }
        // Bảng certificate_asset_comparison_factor thêm cột adjust_coefficient dạng double default 100 (reverse)
        if (Schema::hasColumn('certificate_asset_comparison_factor', 'adjust_coefficient')) {
            Schema::table('certificate_asset_comparison_factor', function (Blueprint $table) {
				$table->dropColumn('adjust_coefficient');
            });
        }
        // Bảng room_details thêm cột loai_can_ho_id dạng integer (reverse)
        if (Schema::hasColumn('room_details', 'loai_can_ho_id')) {
            Schema::table('room_details', function (Blueprint $table) {
				$table->dropColumn('loai_can_ho_id');
            });
        }

    }
}
