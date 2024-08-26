<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class updateDataTypeCharacterVarying extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Lấy tất cả các cột có kiểu dữ liệu character varying(255)
        $columns = DB::select("
            SELECT table_name, column_name 
            FROM information_schema.columns 
            WHERE data_type = 'character varying' 
            AND character_maximum_length = 255
        ");

        // Tạo các câu lệnh ALTER TABLE để chuyển đổi các cột này thành kiểu text
        foreach ($columns as $column) {
            $table = $column->table_name;
            $col = $column->column_name;
            DB::statement("ALTER TABLE {$table} ALTER COLUMN {$col} TYPE text");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
