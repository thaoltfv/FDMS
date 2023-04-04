<?php

use App\Models\AppraiseOtherInformation;
use Illuminate\Database\Seeder;


class UpdateAppraiseOtherInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            $sheets = AppraiseOtherInformation::query()->where('name', '=', 'Cơ sở giá trị thị trường')->first();
            AppraiseOtherInformation::query()
                ->where('id','=',$sheets->id)
                ->update(
                    array(
                        'is_defaults' => true,
                    )

                )
            ;
            $sheets = AppraiseOtherInformation::query()->where('name','=','Nguyên tắc cung - cầu')->first();
            $sheets::query()
                ->where('id','=',$sheets->id)
                ->update(
                    array(
                        'is_defaults' =>true,
                    )
                );
            $sheets = AppraiseOtherInformation::query()->where('name','=','Quyền sử dụng đất và nhà cửa vật kiến trúc')->first();
            $sheets::query()
                ->where('id','=',$sheets->id)
                ->update(
                    array(
                        'is_defaults' =>true,
                    )
                );
            $sheets = AppraiseOtherInformation::query()->where('name','=','Cách tiếp cận thị trường')->first();
            $sheets::query()
                ->where('id','=',$sheets->id)
                ->update(
                    array(
                        'is_defaults' =>true,
                    )
                );

            $sheets = AppraiseOtherInformation::query()->where('name','=','Phương pháp so sánh')->first();
            $sheets::query()
                ->where('id','=',$sheets->id)
                ->update(
                    array(
                        'is_defaults' =>true,
                    )
                );

            $sheets = AppraiseOtherInformation::query()->where('name','=','Vay vốn ngân hàng ACB')->first();
            $sheets::query()
                ->where('id','=',$sheets->id)
                ->update(
                    array(
                        'is_defaults' =>true,
                    )
                );
        });
    }
}
