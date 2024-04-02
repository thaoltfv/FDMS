<?php


namespace App\Enum;


class ErrorMessage
{
    public const LOGIN_ERROR = "Email hoặc mật khẩu không chính xác";
    public const CREATE_ASSET_ERROR = "Dữ liệu đầu của bạn chưa chính xác, vui lòng liên hệ để hỗ trợ.";
    public const UPLOAD_IMAGE_ERROR = "Lưu trữ hình ảnh không thành công, vui lòng liên hệ để hỗ trợ.";
    public const UPLOAD_NOT_SUPPORT_FILE = "Tập tin không được hỗ trợ, vui lòng chỉ lưu trữ hình ảnh.";
    public const SYSTEM_ERROR = "Hệ thống xảy ra lỗi, vui lòng liên hệ để được hỗ trợ.";
    public const CHANGE_PASSWORD_ERROR = "Xác nhận mật khẩu không chính xác.";
    public const DUPLICATE_PROVINCE = "Tỉnh/Thành đã tồn tại.";
    public const DUPLICATE_DISTRICT = "Quận/Huyện đã tồn tại.";
    public const DUPLICATE_WARD = "Xã/Phường đã tồn tại.";
    public const PERMISSION_ERROR = "Người dùng không có quyền sử dụng chức năng này, vui lòng liên hệ để hỗ trợ.";

    public const CERTIFICATE_NOTEXISTS = 'Không tồn tại hồ sơ thẩm định - HSTD ';
    public const PRE_CERTIFICATE_NOTEXISTS = 'Không tồn tại yêu cầu sơ bộ - YCSB ';
    public const PRE_CERTIFICATE_HAVE_CERTIFICATE = 'YCSB đã được chuyển sang trạng thái chính thức. Vui lòng kiểm tra lại.';
    public const CERTIFICATE_APPRAISERTEAM = 'Vui lòng chọn đầy đủ thông tin tổ thẩm định.';
    public const CERTIFICATE_APPROVE_CHECK_APPRAISE = 'Bạn không thể thực hiện do chưa có thông tin tài sản thẩm định. ';
    public const CERTIFICATE_CHOOSE_APPRAISE = 'HSTD không ở trạng thái Đang thẩm định.';
    public const CERTIFICATE_CHECK_STATUS_FOR_UPDATE = 'Bạn không có quyền chuyển trạng thái phiếu có trạng thái ';
    public const CERTIFICATE_CHECK_UPDATE = 'Bạn không có quyền chỉnh sửa HSTD ';
    public const PRE_CERTIFICATE_CHECK_STATUS_FOR_UPDATE = 'Bạn không có quyền chuyển trạng thái YCSB có trạng thái ';
    public const PRE_CERTIFICATE_CHECK_UPDATE = 'Bạn không có quyền chỉnh sửa YCSB ';
    public const PAYMENT_CHECK_UPDATE = 'Bạn không có quyền chỉnh sửa thông tin thanh toán';
    public const PRE_CERTIFICATE_CHECK_UPDATE_TO_OFFICAL = 'Bạn không có quyền sang trạng thái chính thức ';
    public const CERTIFICATE_CHECK_ADD = 'Bạn không có quyền tạo mới HSTD ';
    public const CERTIFICATE_CHECK_VIEW = 'Bạn không có quyền xem thông tin HSTD ';
    public const PRE_CERTIFICATE_CHECK_VIEW = 'Bạn không có quyền xem thông tin YCSB ';
    public const CERTIFICATE_CHECK_EXPORT = 'Bạn không có quyền xuất danh sách HSTD ';
    public const PRE_CERTIFICATE_CHECK_EXPORT = 'Bạn không có quyền xuất danh sách YCSB';
    public const PRE_CERTIFICATE_CHECK_EXPORT_2 = 'Bạn không có quyền với tài liệu sơ bộ';

    public const APPRAISE_AUTOMATIC_ASSET = 'Xin lỗi! Khu vực hiện tại chưa đủ dữ liệu để so sánh. Vui lòng chọn TSSS trên bản đồ.';
    public const APPRAISE_NOTEXISTS = 'Không tồn tại tài sản thẩm định - TSTD ';
    public const APPRAISE_NOTEXISTS_TANGIBLE = 'Không tồn tại công trình xây dựng';

    public const APPRAISE_CHECK_TUNNING = 'Vui lòng nhập thông tin hẻm.';
    public const APPRAISE_CHECK_MULTIPLE_MDSDD = 'Chỉ được phép lưu một mục đích sử dụng đất Chính. Vui lòng xem lại.';
    public const APPRAISE_CHECK_MDSDD = 'Bạn chưa chọn mục đích sử dụng Chính. Vui lòng xem lại.';
    public const APPRAISE_CHECK_UBND_PRICE = 'Vui lòng nhập đầy đủ đơn giá nhà nước.';
    public const APPRAISE_CHECK_MAIN_EREA =  'Vui lòng nhập diện tích phù hợp quy hoạch.';
    public const APPRAISE_CHECK_ASSET_NUMBER =  'Số lượng tài sản so sánh không thể lớn hơn 3. Vui lòng xem lại.';
    public const APPRAISE_CHECK_STATUS_FOR_UPDATE =  'Bạn không được chỉnh sửa phiếu có trạng thái ';
    public const APPRAISE_CHECK_VIEW =  'Bạn không có quyền xem TSTD ';
    public const APPRAISE_CHECK_UPDATE =  'Bạn không có quyền chỉnh sửa TSTD ';
    public const APPRAISE_CHECK_ADD = 'Bạn không có quyền tạo mới TSTD ';
    public const APPRAISE_CHECK_EXPORT = 'Bạn không có xuất danh sách TSTD ';
    public const PERSONAL_CHECK_EXPORT = 'Bạn không có quyền xuất danh sách Động sản ';
    public const COMPAREASSET_NOTEXISTS = 'Không tồn tại tài sản so sánh - TSSS ';
    public const LOG_ACTIVITY_NOT_FOUND = 'Không tìm thấy lịch sử được ghi lại';

    public const PE_CHECK_ADD = 'Bạn không có quyền tạo mới TSSB ';
    public const PE_CHECK_UPDATE = 'Bạn không có quyền chỉnh sửa TSSB ';
    public const PE_CHECK_VIEW = 'Bạn không có quyền xem TSSB ';
    public const PE_CHECK_STATUS_FOR_UPDATE =  'Bạn không được chỉnh sửa phiếu có trạng thái';
    public const PE_CHECK_MULTIPLE_MDSDD = 'Chỉ được phép lưu một mục đích sử dụng đất Chính. Vui lòng xem lại.';
    public const PE_CHECK_TUNNING = 'Vui lòng nhập thông tin hẻm.';
    public const PE_CHECK_MDSDD = 'Bạn chưa chọn mục đích sử dụng Chính. Vui lòng xem lại.';
    public const PE_CHECK_MAIN_EREA =  'Vui lòng nhập diện tích phù hợp quy hoạch.';
    public const PE_CHECK_ASSET_NUMBER =  'Số lượng tài sản so sánh không thể lớn hơn 3. Vui lòng xem lại.';
    public const PE_APPRAISE_EXIT =  'TSSB đã được chuyển sang HSTĐ. Vui lòng kiểm tra lại.';
    public const APPRAISE_PE_EXIT =  'Đã có HSTĐ được tạo từ UGT này. Vui lòng kiểm tra lại.';
    public const PE_CHECK_EXIT =  'Không tồn tại tài sản sơ bộ - TSSB';
}
