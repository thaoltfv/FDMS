<?php

use App\Models\Dictionary;
use Illuminate\Database\Seeder;

class ProjectRankDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = 'HANG_CHUNG_CU';
        $datas = [
            'binh-dan' => 'BÌNH DÂN',
            'trung-cap' => 'TRUNG CẤP',
            'cao-cap' => 'CAO CẤP',
            'hang-sang'=> 'HẠNG SANG'
        ];

        DB::transaction(function () use ($type, $datas) {
            $paramKeys = array_keys($datas);
            foreach ($paramKeys as $key) {
                Dictionary::query()->updateOrCreate(['type' => $type, 'acronym' => $key],[
                    'type' => $type,
                    'acronym' => $key,
                    'description' => $datas[$key],
                    'useful_year' => 0,
                    'status' => 1,
                ]);
            }
        });

        //// tien ich
        if(Dictionary::query()->where('type','TIEN_ICH_CO_BAN')->whereNull('acronym')->exists()){
            Dictionary::query()->where('type','TIEN_ICH_CO_BAN')->whereNull('acronym')->update(['status'=>0]);
        }
        $type = 'TIEN_ICH_CO_BAN';
        $datas = [
            'ho_boi' => 'HỒ BƠI',
            'cong_vien' => 'CÔNG VIÊN',
            'benh_vien' => 'BỆNH VIÊN',
            'truong_hoc'=> 'TRƯỜNG HỌC',
            'phong_gym' => 'PHÒNG GYM',
            'trung_tam_thuong_mai'=> 'TRUNG TÂM THƯƠNG MẠI',
        ];

        DB::transaction(function () use ($type, $datas) {
            $paramKeys = array_keys($datas);
            foreach ($paramKeys as $key) {
                Dictionary::query()->updateOrCreate(['type' => $type, 'acronym' => $key],[
                    'type' => $type,
                    'acronym' => $key,
                    'description' => $datas[$key],
                    'useful_year' => 0,
                    'status' => 1,
                ]);
            }
        });
    }
}
