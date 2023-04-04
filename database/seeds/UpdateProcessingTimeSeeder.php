<?php

use App\Models\CertificateDictionary;
use Illuminate\Database\Seeder;

class UpdateProcessingTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = 'PROCESSING_TIME';
        $acronyms =[
            0 => 'MOI',
            1 => 'DANG-THAM-DINH',
            2 => 'DANG-DUYET',
            3 => 'HOAN-THANH'
        ];
        $value = 1440;
        DB::transaction(function () use($type,$acronyms,$value) {
        foreach($acronyms as $acronym){
            if(CertificateDictionary::where('acronym' , $acronym)->exists()){
                CertificateDictionary::query()->where(['type' => $type , 'acronym' => $acronym])->update([
                    'description' => $value,
                    ] );
            }else{
                CertificateDictionary::insert([
                    'type' => $type,
                    'acronym' => $acronym,
                    'description' => $value,
                    'useful_year' => 0
                    ] );
            }
            }
        });
    }
}
