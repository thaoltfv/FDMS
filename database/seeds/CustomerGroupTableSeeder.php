<?php

use App\Models\CustomerGroupFirst;
use App\Models\CustomerGroupSecond;
use App\Models\CustomerGroupThird;
use App\Models\CustomerGroupFourth;
use App\Models\Dictionary;
use Illuminate\Database\Seeder;


class CustomerGroupTableSeeder extends Seeder
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
            $listCustomerGroup = Dictionary::query()->where('type', '=', 'NHOM_DOI_TAC')->get();
            if (count($listCustomerGroup) > 0) {
                foreach ($listCustomerGroup as $key => $group) {
                    $firstId = null;
                    $secondId = null;
                    $thirdId = null;
                    $fourthId = null;
                    if ($group->name_lv_1) {
                        $check = CustomerGroupFirst::query()->where('name', 'ILIKE', '%' . $group->name_lv_1 . '%')->first();
                        if (isset($check) > 0) {
                            $firstId = $check->id;
                            Dictionary::query()->where('id', '=', $group->id)->update(['first_id' => $firstId]);
                        } else {
                            $insert = array(
                                'name' => $group->name_lv_1
                            );
                            $firstId = CustomerGroupFirst::query()->insertGetId($insert);
                            Dictionary::query()->where('id', '=', $group->id)->update(['first_id' => $firstId]);
                        }
                    }

                    if ($group->name_lv_2) {
                        if ($firstId) {
                            $check2 = CustomerGroupSecond::query()->where('name', 'ILIKE', '%' . $group->name_lv_2 . '%')->where('first_id', '=', $firstId)->first();
                            if (isset($check2) > 0) {
                                $secondId = $check2->id;
                                Dictionary::query()->where('id', '=', $group->id)->update(['second_id' => $secondId]);
                            } else {
                                $insert2 = array(
                                    'name' => $group->name_lv_2,
                                    'first_id' => $firstId
                                );
                                $secondId = CustomerGroupSecond::query()->insertGetId($insert2);
                                Dictionary::query()->where('id', '=', $group->id)->update(['second_id' => $secondId]);
                            }
                        }
                    }

                    if ($group->name_lv_3) {
                        if ($firstId && $secondId) {
                            $check3 = CustomerGroupThird::query()->where('name', 'ILIKE', '%' . $group->name_lv_3 . '%')->where('first_id', '=', $firstId)->where('second_id', '=', $secondId)->first();
                            if (isset($check3) > 0) {
                                $thirdId = $check3->id;
                                Dictionary::query()->where('id', '=', $group->id)->update(['third_id' => $thirdId]);
                            } else {
                                $insert3 = array(
                                    'name' => $group->name_lv_3,
                                    'first_id' => $firstId,
                                    'second_id' => $secondId
                                );
                                $thirdId = CustomerGroupThird::query()->insertGetId($insert3);
                                Dictionary::query()->where('id', '=', $group->id)->update(['third_id' => $thirdId]);
                            }
                        }
                    }

                    if ($group->name_lv_4) {
                        if ($firstId && $secondId && $thirdId) {
                            $check4 = CustomerGroupFourth::query()->where('name', 'ILIKE', '%' . $group->name_lv_4 . '%')->where('first_id', '=', $firstId)->where('second_id', '=', $secondId)->where('third_id', '=', $thirdId)->first();
                            if (isset($check4) > 0) {
                                $fourthId = $check4->id;
                                Dictionary::query()->where('id', '=', $group->id)->update(['fourth_id' => $fourthId]);
                            } else {
                                $insert4 = array(
                                    'name' => $group->name_lv_4,
                                    'first_id' => $firstId,
                                    'second_id' => $secondId,
                                    'third_id' => $thirdId
                                );
                                $fourthId = CustomerGroupFourth::query()->insertGetId($insert3);
                                Dictionary::query()->where('id', '=', $group->id)->update(['fourth_id' => $fourthId]);
                            }
                        }
                    }
                }
            }
        });
    }
}
