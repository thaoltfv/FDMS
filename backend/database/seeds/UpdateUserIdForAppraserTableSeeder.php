<?php

use App\Models\Appraiser;
use App\Models\Branch;
use App\Models\Dictionary;
use App\Models\User;
use Illuminate\Database\Seeder;

class UpdateUserIdForAppraserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $position = Dictionary::where(['type' => 'CHUC_VU', 'acronym' => 'CHUYEN-VIEN-THAM-DINH'])->first();
        $mainBranch = Branch::where(['name' => 'Hội Sở'])->first();
        $users = User::all();
        if(isset($users)){
            foreach($users as $user){
                if (!$user->branch_id) {
                    $user->branch_id = $mainBranch->id;
                    $user->save();
                }
                $appraiser = Appraiser::query()->whereNull('user_id')->where('name',$user->name)->first();
                if(isset($appraiser)){
                    Appraiser::query()->where('id',$appraiser->id)->update(['user_id' => $user->id, 'branch_id' => $user->branch_id]);
                }else{
                    $appraiser = Appraiser::query()->where('user_id',$user->id)->first();
                    if(! $appraiser)
                        Appraiser::insert([
                            'name' => $user->name,
                            'appraiser_number' => '',
                            'appraise_position_id' => isset($position) ? $position->id : 160,
                            'branch_id' => $user->branch_id,
                            'user_id' => $user->id,
                            ]);
                }
                $appraiser = Appraiser::query()->where('user_id', $user->id)->whereNull('branch_id')->first();
                if(isset($appraiser)){
                    Appraiser::query()->where('id',$appraiser->id)->update(['branch_id' => $user->branch_id]);
                }
            }
        }
    }
}
