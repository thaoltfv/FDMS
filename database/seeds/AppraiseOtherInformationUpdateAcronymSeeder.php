<?php

use App\Models\AppraiseOtherInformation;
use Illuminate\Database\Seeder;

class AppraiseOtherInformationUpdateAcronymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appraiseOtherInfomation = AppraiseOtherInformation::query()->whereNull('dictionary_acronym')->get('id');
        if(isset($appraiseOtherInfomation) && count($appraiseOtherInfomation) > 0){
            foreach($appraiseOtherInfomation as $item){
                $id = $item->id;
                AppraiseOtherInformation::query()->where('id', $id)->update([
                    'dictionary_acronym' => [
                        "BDS","DS","KHAC"
                    ]
                ]);
            }
        }
        $appraiseOtherInfomation = AppraiseOtherInformation::query()->whereNull('status')->get('id');
        if(isset($appraiseOtherInfomation) && count($appraiseOtherInfomation) > 0){
            foreach($appraiseOtherInfomation as $item){
                $id = $item->id;
                AppraiseOtherInformation::query()->where('id', $id)->update([
                    'status' => true
                ]);
            }
        }
    }
}
