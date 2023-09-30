<?php


namespace App\Enum;


class EstimateAssetDefault
{
    public const URBAN_ASSET = array(432,433);
    public const ASSET_TYPE_APARTMENT = 'CHUNG CƯ';
    public const DEFAULT_UBAN_LAND_TYPE = 'ĐẤT Ở TẠI ĐÔ THỊ';
    public const DEFAULT_COUNTRY_LAND_TYPE = 'ĐẤT Ở TẠI NÔNG THÔN';
    public const ESTIMATE_APARTMENT_TYPE = 'CHUNG_CU';
    public const ESTIMATE_LAND_TYPE = 'DAT';
    public const ESTIMATE_APARTMENT_LIMIT = 100;
    public const APARTMENT_ASSET_TYPE = [39];
    public const LAND_ASSET_TYPE = [37,38];
    public const TRANSACTION_TYPE = [51,52];

    public const GROUP_LAND_TYPE = [61,62,63,65];

    public const COMPARATION_FACTORS = ['muc_dich_chinh','khoang_cach', 'phap_ly', 'quy_mo', 'chieu_rong_mat_tien', 'chieu_sau_khu_dat', 'hinh_dang_dat', 'ket_cau_duong', 'do_rong_duong', 'dieu_kien_ha_tang', 'kinh_doanh', 'an_ninh_moi_truong_song', 'phong_thuy', 'giao_thong', 'quy_hoach', 'dieu_kien_thanh_toan'];
    public const COMPARATION_FACTORS_V2 = ['muc_dich_chinh','khoang_cach', 'phap_ly', 'quy_mo', 'chieu_rong_mat_tien', 'chieu_sau_khu_dat', 'hinh_dang_dat', 'ket_cau_duong', 'do_rong_duong'];
    public const COMPARATION_FACTORS_APARTMENT = ['phap_ly', 'loai_can', 'dien_tich', 'tang', 'so_phong_wc', 'so_phong_ngu'];

    public const DICTIONARY_LAND_TYPE = 'LOAI_DAT_CHI_TIET';

    public const STEP_1_1 = '[Đất][Trong Quy Hoạch]';
    public const STEP_1_2 = '[Đất][Phù Hợp Quy Hoạch][Mặt tiền][Tất cả MĐSD][Chung đường]';
    public const STEP_1_3 = '[Đất][Phù Hợp Quy Hoạch][Mặt tiền][Tất cả MĐSD][Đất Nông Nghiệp][Chung đường]';
    public const STEP_1_4 = '[Đất][Phù Hợp Quy Hoạch][Mặt tiền][Từng MĐSD][Chung đường]';
    public const STEP_1_5 = '[Đất][Phù Hợp Quy Hoạch][Mặt tiền][Từng MĐSD][Đất Nông Nghiệp][Chung đường]';
    public const STEP_1_6 = '[Đất][Phù Hợp Quy Hoạch][Trong hẻm][Tất cả MĐSD][Cùng bề rộng hẻm]';
    public const STEP_1_7 = '[Đất][Phù Hợp Quy Hoạch][Trong hẻm][Tất cả MĐSD][Đất Nông Nghiệp][Cùng bề rộng hẻm]';
    public const STEP_1_8 = '[Đất][Phù Hợp Quy Hoạch][Trong hẻm][Tất cả MĐSD][Khác bề rộng hẻm]';
    public const STEP_1_9 = '[Đất][Phù Hợp Quy Hoạch][Trong hẻm][Tất cả MĐSD][Đất Nông Nghiệp][Khác bề rộng hẻm]';
    public const STEP_2_1 = '[Chung cư][Cùng chung cư][Cùng số phòng]';
    public const STEP_2_2 = '[Chung cư][Cùng chung cư][Khác số phòng]';
    public const STEP_2_3 = '[Chung cư][Khác chung cư][Cùng số phòng]';
    public const STEP_2_4 = '[Chung cư][Khác chung cư][Khác số phòng]';

    public const ERROR_MESSAGE = 'Xin lỗi ! Khu vực hiện tại chưa đủ dữ liệu để ước tính. Vui lòng điền đơn giá và cập nhật để có kết quả sơ bộ';

    public const ERROR_MESSAGE_UNRECOGNIZED = 'Hiện tại không có đơn giá của loại đất [1] trong khu vực. Vui lòng điền đơn giá và cập nhật để có kết quả sơ bộ';

    public const ERROR_MESSAGE_RECOGNIZED = 'Hiện tại không thể tìm thấy giá đất theo UBND của loại đất [1] trong khu vực. Vui lòng điền đơn giá và cập nhật để có kết quả sơ bộ';

    public const ERROR_MESSAGE_COMBO = 'Hiện không có dữ liệu của Phần đất phù hợp quy hoạch [1] và Phần đất vi phạm quy hoạch [2] trong khu vực. Vui lòng điền đơn giá và cập nhật để có kết quả sơ bộ';
}
