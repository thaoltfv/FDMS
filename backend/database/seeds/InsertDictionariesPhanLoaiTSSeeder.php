<?php

use App\Models\Dictionary;
use Illuminate\Database\Seeder;

class InsertDictionariesPhanLoaiTSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Dictionary::firstOrCreate(['type' => 'NHOM_TAI_SAN', 'acronym' => 'BDS'], ['description' => 'BẤT ĐỘNG SẢN',]);
        Dictionary::firstOrCreate(['type' => 'NHOM_TAI_SAN', 'acronym' => 'DS'], ['description' => 'ĐỘNG SẢN',]);
        Dictionary::firstOrCreate(['type' => 'NHOM_TAI_SAN', 'acronym' => 'KHAC'], ['description' => 'KHÁC',]);

        Dictionary::updateOrCreate(['type' => 'LOAI_TAI_SAN', 'description' => 'GIÁ TRỊ DOANH NGHIỆP', 'acronym' => 'GTDN'], ['dictionary_acronym' => 'KHAC']);
        Dictionary::updateOrCreate(['type' => 'LOAI_TAI_SAN', 'description' => 'TÀI SẢN KHÁC', 'acronym' => 'TSK'], ['dictionary_acronym' => 'KHAC']);
        Dictionary::updateOrCreate(['type' => 'LOAI_TAI_SAN', 'description' => 'MÁY MÓC - THIẾT BỊ', 'acronym' => 'MMTB'],['dictionary_acronym' => 'DS']);
        Dictionary::updateOrCreate(['type' => 'LOAI_TAI_SAN', 'description' => 'PHƯƠNG TIỆN VẬN TẢI', 'acronym' => 'PTVT'],['dictionary_acronym' => 'DS']);
        Dictionary::updateOrCreate(['type' => 'LOAI_TAI_SAN', 'description' => 'DÂY CHUYỀN CÔNG NGHỆ', 'acronym' => 'DCCN'],['dictionary_acronym' => 'KHAC']);

        Dictionary::where(['type' => 'LOAI_TAI_SAN','description' => 'ĐẤT TRỐNG'])->update(['acronym' => 'DT' , 'dictionary_acronym' => 'BDS']);
        Dictionary::where(['type' => 'LOAI_TAI_SAN','description' => 'CHUNG CƯ'])->update(['acronym' => 'CC' , 'dictionary_acronym' => 'BDS']);
        Dictionary::where(['type' => 'LOAI_TAI_SAN','description' => 'ĐẤT CÓ NHÀ'])->update(['acronym' => 'DCN' , 'dictionary_acronym' => 'BDS']);

        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN', 'acronym' => 'DUONG_BO'], ['description' => 'ĐƯỜNG BỘ']);
        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN', 'acronym' => 'DUONG_THUY'], ['description' => 'ĐƯỜNG THỦY']);
        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN', 'acronym' => 'DUONG_SAT'], ['description' => 'ĐƯỜNG SẮT']);
        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN', 'acronym' => 'DUONG_HANG_KHONG'], ['description' => 'ĐƯỜNG HÀNG KHÔNG']);

        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN_CHI_TIET', 'description' => 'XE MÔ TÔ', 'dictionary_acronym' => 'DUONG_BO']);
        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN_CHI_TIET', 'description' => 'XE Ô TÔ', 'dictionary_acronym' => 'DUONG_BO']);
        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN_CHI_TIET', 'description' => 'XE ĐẦU KÉO', 'dictionary_acronym' => 'DUONG_BO']);
        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN_CHI_TIET', 'description' => 'XE RƠ MOÓC', 'dictionary_acronym' => 'DUONG_BO']);
        Dictionary::updateOrCreate(['type' => 'LOAI_PHUONG_TIEN_CHI_TIET', 'description' => 'XE KHÁC', 'dictionary_acronym' => 'DUONG_BO']);

        Dictionary::updateOrCreate(['type' => 'LOAI_NHIEN_LIEU', 'description' => 'XĂNG']);
        Dictionary::updateOrCreate(['type' => 'LOAI_NHIEN_LIEU', 'description' => 'DẦU']);
        Dictionary::updateOrCreate(['type' => 'LOAI_NHIEN_LIEU', 'description' => 'ĐIỆN']);
        Dictionary::updateOrCreate(['type' => 'LOAI_NHIEN_LIEU', 'description' => 'KHÁC']);

        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'ABARTH'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'ALFA ROMEO'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'ASTON MARTIN'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'AUDI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'BENTLEY'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'BMW'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'BUGATTI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'CADILLAC'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'CATERHAM'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'CITROEN'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'CHEVROLET'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'CHRYSLER'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'DACIA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'FERRARI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'FIAT'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'FORD'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'HONDA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'HYUNDAI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'INFINITI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'JAGUAR'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'JEEP'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'KIA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'LAMBORGHINI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'LAND ROVER'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'LEXUS'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'LOTUS'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MASERATI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MAZDA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MCLAREN'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MERCEDES BENZ'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MG'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MINI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MITSUBISHI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'MORGAN'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'NISSAN'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'NOBLE'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'PAGANI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'PEUGEOT'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'PORSCHE'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'RADICAL'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'RENAULT'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'ROLLS ROYCE'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SAAB'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SEAT'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SKODA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SMART'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SSANGYONG'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SUBARU'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SUZUKI'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'TESLA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'TOYOTA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'VAUXHALL'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'VINFAST'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'VOLKSWAGEN'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'VOLVO'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'ZENOS'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'YAMAHA'],['dictionary_acronym' => 'PTVT']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'KHÁC'],['dictionary_acronym' => 'PTVT']);

        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'TOSHIBA'],['dictionary_acronym' => 'MMTB']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'ITACHI'],['dictionary_acronym' => 'MMTB']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'FUJI'],['dictionary_acronym' => 'MMTB']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'KAMSTU'],['dictionary_acronym' => 'MMTB']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'DAIKIN'],['dictionary_acronym' => 'MMTB']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'SCHNEIDER'],['dictionary_acronym' => 'MMTB']);
        Dictionary::updateOrCreate(['type' => 'NHA_SAN_XUAT', 'description' => 'HONEYWELL'],['dictionary_acronym' => 'MMTB']);

        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'MỸ']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'NGA']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'NHẬT BẢN']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'PAKISTAN']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'PHÁP']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'PHẦN LAN']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'PHILIPPINES']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'TÂY BAN NHA']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'THÁI LAN']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'THỔ NHĨ KỲ']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'THỤY ĐIỂN']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'TRUNG QUỐC']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'UZBEKISTAN']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'VENEZUELA']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'VIỆT NAM']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'Ý']);
        Dictionary::updateOrCreate(['type' => 'NOI_SAN_XUAT', 'description' => 'KHÁC']);
    }
}
