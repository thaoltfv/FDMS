<?php


namespace App\Enum;


class ValueDefault
{
    public const PASSWORD_DEFAULT = 'ThamDinhGiaDongNai';
    public const ROOT_ADMIN_DEFAULT = 'admin@thamdinhgiadongnai.vn';
    public const MIGRATION_STATUS_DEFAULT = 'TSC';
    public const MIGRATION_STATUS_NEW = 'TSS';
    public const RELIABILITY_HIGHT = 1;
    public const RELIABILITY_NORMAL = 2;
    public const RELIABILITY_LOW = 3;
    public const STATUS_SUCCESS = 1;
    public const STATUS_ERROR = 0;

    public const ACTIVE_STATUS = 'active';
    public const INACTIVE_STATUS = 'inactive';

    public const PRICE_VALIDATION_VALUE = 9999;
    public const PRICE_VALIDATION_MESSAGE_UBND = 'Đơn giá UBND thấp nhất là 10,000';
    public const PRICE_VALIDATION_MESSAGE_OTHER = 'Đơn giá thấp nhất là 10,000';

    public const CERTIFICATE_DESCRIPTION = 'Các hồ sơ, tài liệu về tài sản do khách hàng cung cấp là đầy đủ và tin cậy';

    public const MAXIMUM_AVERAGE_RATE = 15;
    public const TOTAL_ADJUST_RATE = 200;
    public const RADIUS_SCAN = 0.5;
    public const CERTIFICATION_ASSET_MIN_RISK = 15;
    public const CERTIFICATION_ASSET_MAX_RISK = 25;

    public const BETWEEN_VALUE = 1000000;
    public const TOTAL_PRICE_PERCENT = 0.05;

    public const ALPHA = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    public const CERTIFICATION_ASSET_COLUMN_LIST = [
        'id' => 'Mã TSTĐ',
        'assetType.description' => 'Loại tài sản',
        'fronSide' => 'Vị trí',
        'appraise_asset' => 'Tên tài sản',
        'appraise_land_sum_area' => 'Tổng DT đất',
        'total_construction_area' => 'Tổng DT xây dựng',
        'total_price' => 'Tổng giá trị',
        'createdBy.name' => 'Người tạo',
        'created_at' => 'Ngày tạo',
        'status_text' => 'Trạng thái',
    ];

    public const CERTIFICATION_BRIEF_COLUMN_LIST = [
        'id' => 'Mã HSTĐ',
        'document_num' => 'Số hợp đồng',
        'certificate_num' => 'Số chứng thư',
        'petitioner_name' => 'Khách hàng',
        'total_price' => 'Tổng giá trị (VNĐ)',
        'appraiserSale' => 'Nhân viên kinh doanh',
        'appraiserConfirm' => 'Chuyên viên thẩm định',
        'appraiser' => 'Thẩm định viên',
        'created_by' => 'Người tạo',
        'created_at' => 'Ngày tạo',
        'status' => 'Trạng thái',
    ];

    public const CERTIFICATION_BRIEF_CUSTOMIZE_COLUMN_LIST = [
        'certificate_id' => 'Mã HSTD',
        'real_estate_id' => 'Mã TSTD',
        'certificate_num' => 'Số chứng thư',
        'certificate_date' => 'Ngày chứng thư',
        'document_num' => 'Số hợp đồng',
        'document_date' => 'Ngày hợp đồng',
        'status_text' => 'Trạng thái hồ sơ',
        'petitioner_name' => 'Tên khách hàng',
        'petitioner_phone' => 'Số điện thoại',
        'petitioner_address' => 'Địa chỉ',
        'customer_name' => 'Họ tên đối tác',
        'customer_phone' => 'SĐT đối tác',
        'customer_address' => 'Địa chỉ đối tác',
        'customer_group_name' => 'Nhóm đối tác',
        'appraise_asset' => 'Tên Tài Sản',
        'gcn' => 'GCN',
        'front_side' => 'Mặt tiền',
        'full_address' => 'Địa chỉ BĐS Thẩm định giá',
        'province' => 'Tỉnh/ Thành phố',
        'district' => 'Quận/ Huyện',
        'ward' => 'Phường/ Xã',
        'street' => 'Đường/ phố',
        'location_type' => 'Địa chỉ thửa đất',
        'appraise_method_used' => 'Phương pháp thẩm định áp dụng',
        'location_description' => 'Mô tả BĐS',
        'purpose_detail' => 'Loại đất',
        'land_price' => 'Giá trị QSD đất',
        'tangible_price' => 'Giá trị CTXD',
        'other_asset_price' => 'Giá trị tài sản khác',
        'total_price' => 'Tổng giá trị định giá',
        'service_fee' => 'Phí thẩm định giá',
        'appraiser_sale_name' => 'Nhân viên kinh doanh',
        'appraiser_perform_name' => 'Chuyên viên thẩm định',
        'appraiser_name' => 'Thẩm định viên',
        'branch_name' => 'Chi nhánh',
    ];
    public const CERTIFICATION_BRIEF_CUSTOMIZE_LAND_DETAIL_COLUMN_LIST = [
        'residential_area' => 'Diện tích đất ở (m2)',
        'residential_unit_price' => 'Đơn giá đất ở (đồng/m2)',
        'residential_price' => 'Giá trị đất ở (đồng)',
        'agricultural_area' => 'Diện tích đất khác (m2)',
        'agricultural_unit_price' => 'Đơn giá đất khác (đồng/m2)',
        'agricultural_price' => 'Giá trị đất khác (đồng)',
        'agricultural_area_2' => 'Diện tích đất khác 2 (m2)',
        'agricultural_unit_price_2' => 'Đơn giá đất khác 2 (đồng/m2)',
        'agricultural_price_2' => 'Giá trị đất khác 2 (đồng)',
    ];
    public const CERTIFICATION_BRIEF_CUSTOMIZE_LAND_DETAIL_ZONING_COLUMN_LIST = [];
    public const CERTIFICATION_BRIEF_CUSTOMIZE_TANGIBLE_DETAIL_COLUMN_LIST = [
        'tangible_type' => 'Loại CTXD',
        'tangible_area' => 'Diện tích sàn',
        'tangible_unit_price' => 'Đơn giá XD',
        'tangible_remain' => 'CLCL (%)',
        'other_tangible_price' => 'Giá trị CTXD khác',
    ];

    public const STATUSES = [
        1 => 'Mới',
        2 => 'Đang thẩm định',
        3 => 'Đang duyệt',
        4 => 'Hoàn thành',
        5 => 'Hủy',
        6 => 'Đang kiểm soát',
    ];
}
