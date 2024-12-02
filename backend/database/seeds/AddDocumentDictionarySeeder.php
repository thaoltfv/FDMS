<?php

use App\Models\DocumentDictionary;
use Illuminate\Database\Seeder;

class AddDocumentDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentDictionary::query()->updateOrCreate(['type' => 'DOCUMENT', 'slug' => 'module'], ['description' => 'Mẫu báo cáo','value' => 'NOVA']);
        DocumentDictionary::query()->updateOrCreate(['type' => 'BAO_CAO', 'slug' => 'document_number_prefix'], ['description' => 'TIỀN TỐ BÁO CÁO','value' => null]);
        DocumentDictionary::query()->updateOrCreate(['type' => 'BAO_CAO', 'slug' => 'document_number_suffix'], ['description' => 'HẬU TỐ BÁO CÁO','value' => '/BC-TĐG']);
        DocumentDictionary::query()->updateOrCreate(['type' => 'BAO_CAO', 'slug' => 'custome_retrictions','value' => 'TEXT 1'], ['description' => 'NHỮNG ĐIỀU KHOẢN LOẠI TRỪ VÀ HẠN CHẾ']);
        DocumentDictionary::query()->updateOrCreate(['type' => 'CHUNG_THU', 'slug' => 'certificatte_number_prefix'], ['description' => 'TIỀN TỐ CHỨNG THƯ','value' => null]);
        DocumentDictionary::query()->updateOrCreate(['type' => 'CHUNG_THU', 'slug' => 'certificatte_number_suffix'], ['description' => 'HẬU TỐ CHỨNG THƯ','value' => '/CT-TĐG']);
        DocumentDictionary::query()->updateOrCreate(['type' => 'CHUNG_THU', 'slug' => 'custome_retrictions','value' => 'TEXT 2'], ['description' => 'NHỮNG ĐIỀU KHOẢN LOẠI TRỪ VÀ HẠN CHẾ']);
        DocumentDictionary::query()->updateOrCreate(['type' => 'DOCUMENT', 'slug' => 'contract_code_prefix'], ['description' => 'TIỀN TỐ SỐ HỢP ĐỒNG BÁO CÁO','value' => null]);
        DocumentDictionary::query()->updateOrCreate(['type' => 'DOCUMENT', 'slug' => 'contract_code_suffix'], ['description' => 'HẬU TỐ SỐ HỢP ĐỒNG BÁO CÁO','value' => '/HĐTĐG-HCM']);
        DocumentDictionary::query()->updateOrCreate(['type' => 'DOCUMENT', 'slug' => 'print_watermask'], ['description' => 'IN WATERMASK TRONG FILE','value' => 'print']);
    }
}
