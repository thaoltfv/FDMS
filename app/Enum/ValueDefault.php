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

    public const ALPHA = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
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
}

