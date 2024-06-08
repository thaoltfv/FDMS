<?php

use Illuminate\Database\Seeder;
use App\Models\PreCertificateConfig;

class UpdateCertificateWorkflowConfigs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $record = PreCertificateConfig::where('name', 'workflowHSTD')->first();

        if ($record) {

            // Prepare new data
            $newData =
                [
                    "appraiser" => [
                        "created_by" => "Người tạo",
                        "appraiser_id" => "Thẩm định viên",
                        "administrative_id" => "Hành chính viên",
                        "appraiser_sale_id" => "Nhân viên kinh doanh",
                        "business_manager_id" => "Quản lý nghiệp vụ",
                        "appraiser_confirm_id" => "Ký thay GĐ",
                        "appraiser_control_id" => "Kiểm soát viên",
                        "appraiser_manager_id" => "Giám đốc",
                        "appraiser_perform_id" => "Chuyên viên thẩm định"
                    ],
                    "principle" => [
                        [
                            "id" => 1,
                            "css" => [
                                "color" => "info"
                            ],
                            "key" => 1,
                            "edit" => [
                                "form" => true,
                                "info" => false,
                                "asset" => true,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "isExportDocument" => true,
                            "slug" => "tiep_nhan_ho_so",
                            "print" => false,
                            "status" => 1,
                            "require" => [
                                "appraiser" => false,
                                "check_legal" => false,
                                "check_price" => false,
                                "check_version" => false,
                                "appraise_item_list" => false
                            ],
                            "isActive" => 1,
                            "isCancel" => true,
                            "re_assign" => "appraiser_sale_id",
                            "sub_status" => 1,
                            "description" => "Tiếp nhận hồ sơ",
                            "put_require" => [
                                "appraiser_sale_id"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                12
                            ],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 12,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Phân hồ sơ",
                                    "btnDescription" => "Chuyển tiếp"
                                ]
                            ]
                        ],
                        [
                            "id" => 12,
                            "css" => [
                                "color" => "info"
                            ],
                            "key" => 12,
                            "edit" => [
                                "form" => false,
                                "info" => true,
                                "asset" => true,
                                "payments" => true,
                                "appraiser" => true,
                                "other_document" => true,
                                "export_document" => true,
                                "appraise_item_list" => true
                            ],
                            "isExportDocument" => true,
                            "slug" => "phan_ho_so",
                            "print" => false,
                            "status" => 10,
                            "require" => [
                                "appraiser" => false,
                                "check_legal" => false,
                                "check_price" => false,
                                "check_version" => false,
                                "appraise_item_list" => false
                            ],
                            "isActive" => 1,
                            "isCancel" => true,
                            "re_assign" => "business_manager_id",
                            "sub_status" => 1,
                            "description" => "Phân hồ sơ",
                            "put_require" => [
                                "business_manager_id",
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                5
                            ],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 1,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 5,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Thẩm định",
                                    "btnDescription" => "Chuyển tiếp"
                                ]
                            ]
                        ],
                        [
                            "id" => 2,
                            "css" => [
                                "color" => "info"
                            ],
                            "key" => 2,
                            "edit" => [
                                "form" => true,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "isExportDocument" => true,
                            "slug" => "phan_bo_hs",
                            "print" => false,
                            "status" => 1,
                            "require" => [
                                "appraiser" => false,
                                "check_legal" => false,
                                "check_price" => false,
                                "check_version" => false,
                                "appraise_item_list" => false
                            ],
                            "isActive" => 0,
                            "isCancel" => true,
                            "sub_status" => 2,
                            "description" => "Phân hồ sơ",
                            "put_require" => [
                                "appraiser_sale_id",
                                "created_by"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                1,
                                3
                            ],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 1,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 3,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Gửi định giá sơ bộ"
                                ]
                            ]
                        ],
                        [
                            "id" => 3,
                            "css" => [
                                "color" => "primary"
                            ],
                            "key" => 3,
                            "edit" => [
                                "form" => false,
                                "info" => true,
                                "asset" => true,
                                "payments" => true,
                                "appraiser" => true,
                                "other_document" => true,
                                "export_document" => true,
                                "appraise_item_list" => true
                            ],
                            "isExportDocument" => true,
                            "slug" => "dinh_gia_so_bo",
                            "print" => true,
                            "status" => 2,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => false,
                                "check_price" => false,
                                "check_version" => false,
                                "appraise_item_list" => false
                            ],
                            "isActive" => 0,
                            "isCancel" => true,
                            "sub_status" => 1,
                            "description" => "Định giá sơ bộ",
                            "put_require" => [
                                "appraiser_perform_id"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                2,
                                4
                            ],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 2,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 4,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Gửi duyệt giá sơ bộ"
                                ]
                            ]
                        ],
                        [
                            "id" => 4,
                            "css" => [
                                "color" => "primary"
                            ],
                            "key" => 4,
                            "edit" => [
                                "form" => false,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => true,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "isExportDocument" => true,
                            "slug" => "duyet_gia_so_bo",
                            "print" => true,
                            "status" => 2,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => false,
                                "check_price" => false,
                                "check_version" => true,
                                "appraise_item_list" => true
                            ],
                            "isActive" => 0,
                            "isCancel" => true,
                            "sub_status" => 2,
                            "description" => "Duyệt giá sơ bộ",
                            "put_require" => [
                                "appraiser_perform_id"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                3,
                                5
                            ],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 3,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 5,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Gửi thẩm định"
                                ]
                            ]
                        ],
                        [
                            "id" => 5,
                            "css" => [
                                "color" => "primary"
                            ],
                            "key" => 5,
                            "edit" => [
                                "form" => false,
                                "info" => true,
                                "asset" => true,
                                "payments" => true,
                                "appraiser" => true,
                                "other_document" => true,
                                "export_document" => true,
                                "appraise_item_list" => true
                            ],
                            "isExportDocument" => true,
                            "slug" => "tham_dinh",
                            "print" => true,
                            "status" => 2,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => true,
                                "check_price" => true,
                                "check_version" => true,
                                "appraise_item_list" => false
                            ],
                            "isActive" => 1,
                            "isCancel" => false,
                            "re_assign" => "appraiser_perform_id",
                            "sub_status" => 3,
                            "description" => "Thẩm định",
                            "put_require" => [
                                "appraiser_perform_id"
                            ],
                            "process_time" => 2880,
                            "put_draggable" => [
                                1,
                                6
                            ],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 12,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 6,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Duyệt giá",
                                    "btnDescription" => "Chuyển tiếp"
                                ]
                            ]
                        ],
                        [
                            "id" => 6,
                            "css" => [
                                "color" => "warning"
                            ],
                            "key" => 6,
                            "edit" => [
                                "form" => false,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "isExportDocument" => true,
                            "slug" => "duyet_gia",
                            "print" => true,
                            "status" => 3,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => true,
                                "check_price" => true,
                                "check_version" => true,
                                "appraise_item_list" => true
                            ],
                            "isActive" => 1,
                            "isCancel" => false,
                            "re_assign" => "appraiser_id",
                            "sub_status" => 1,
                            "description" => "Duyệt giá",
                            "put_require" => [
                                "appraiser_id"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                5,
                                7
                            ],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 5,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 7,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Duyệt phát hành",
                                    "btnDescription" => "Chuyển tiếp"
                                ]
                            ]
                        ],
                        [
                            "id" => 7,
                            "css" => [
                                "color" => "warning"
                            ],
                            "key" => 7,
                            "edit" => [
                                "form" => false,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "documentNum" => true,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "slug" => "duyet_phat_hanh",
                            "print" => true,
                            "status" => 7,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => true,
                                "check_price" => true,
                                "check_version" => true,
                                "appraise_item_list" => true
                            ],
                            "isActive" => 1,
                            "isCancel" => false,
                            "re_assign" => "appraiser_control_id",
                            "sub_status" => 1,
                            "description" => "Duyệt phát hành",
                            "put_require" => [
                                "appraiser_control_id"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                6,
                                10
                            ],
                            "isExportDocument" => true,
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 6,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 10,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "In hồ sơ",
                                    "btnDescription" => "Chuyển tiếp"
                                ]
                            ]
                        ],
                        [
                            "id" => 10,
                            "css" => [
                                "color" => "warning"
                            ],
                            "key" => 10,
                            "edit" => [
                                "form" => false,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "slug" => "in_ho_so",
                            "print" => true,
                            "status" => 8,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => true,
                                "check_price" => true,
                                "check_version" => true,
                                "appraise_item_list" => true
                            ],
                            "isActive" => 1,
                            "isCancel" => false,
                            "re_assign" => "administrative_id",
                            "sub_status" => 1,
                            "description" => "In hồ sơ",
                            "put_require" => [
                                "administrative_id"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                7,
                                11
                            ],
                            "isExportDocument" => true,
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 7,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 11,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Bàn giao khách hàng",
                                    "btnDescription" => "Chuyển tiếp"
                                ]
                            ]
                        ],
                        [
                            "id" => 11,
                            "css" => [
                                "color" => "warning"
                            ],
                            "key" => 11,
                            "edit" => [
                                "form" => false,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "slug" => "ban_giao_khach_hang",
                            "print" => true,
                            "status" => 9,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => true,
                                "check_price" => true,
                                "check_version" => true,
                                "appraise_item_list" => true
                            ],
                            "isActive" => 1,
                            "isCancel" => false,
                            "re_assign" => "appraiser_sale_id",
                            "sub_status" => 1,
                            "description" => "Bàn giao khách hàng",
                            "put_require" => [
                                "appraiser_sale_id"
                            ],
                            "process_time" => 1440,
                            "put_draggable" => [
                                10,
                                8
                            ],
                            "isExportDocument" => true,
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => [
                                [
                                    "id" => 10,
                                    "css" => "btn-white",
                                    "img" => "ic_cancel-1.svg",
                                    "description" => "Từ chối"
                                ],
                                [
                                    "id" => 8,
                                    "css" => "btn-white btn-orange",
                                    "img" => "ic_done.svg",
                                    "description" => "Hoàn thành",
                                    "btnDescription" => "Chuyển tiếp"
                                ]
                            ]
                        ],
                        [
                            "id" => 8,
                            "css" => [
                                "color" => "success"
                            ],
                            "key" => 8,
                            "edit" => [
                                "form" => false,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "slug" => "hoan_thanh",
                            "print" => true,
                            "status" => 4,
                            "require" => [
                                "appraiser" => true,
                                "check_legal" => true,
                                "check_price" => true,
                                "check_version" => true,
                                "appraise_item_list" => true
                            ],
                            "isActive" => 1,
                            "isCancel" => false,
                            "sub_status" => 1,
                            "description" => "Hoàn thành",
                            "put_require" => [],
                            "process_time" => null,
                            "put_draggable" => [
                                6
                            ],
                            "isExportDocument" => true,
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => []
                        ],
                        [
                            "id" => 9,
                            "css" => [
                                "color" => "secondary"
                            ],
                            "key" => 9,
                            "edit" => [
                                "form" => true,
                                "info" => false,
                                "asset" => false,
                                "payments" => true,
                                "appraiser" => false,
                                "other_document" => false,
                                "export_document" => true,
                                "appraise_item_list" => false
                            ],
                            "isExportDocument" => true,
                            "slug" => "huy",
                            "print" => false,
                            "status" => 5,
                            "require" => [
                                "appraiser" => false,
                                "check_legal" => false,
                                "check_price" => false,
                                "appraise_item_list" => false
                            ],
                            "isActive" => 1,
                            "isCancel" => false,
                            "sub_status" => 1,
                            "description" => "Hủy",
                            "put_require" => [],
                            "process_time" => null,
                            "put_draggable" => [],
                            "put_require_roles" => [
                                "ROOT_ADMIN",
                                "SUB_ADMIN",
                                "ADMIN"
                            ],
                            "target_description" => []
                        ],

                    ]
                ];

            $oldData = $record->config;

            // Update config with new data
            $jsonData = json_encode($newData);

            if ($jsonData === false) {
                // The data could not be converted to a JSON string
                throw new \Exception("Invalid JSON data");
            } else {
                // Add a new record
                $newRecord = PreCertificateConfig::create([
                    'name' => "workflowHSTD",
                    'config' => $jsonData,
                    'old_config' => $oldData,
                    // Add any other fields required for a new record
                ]);

                if ($newRecord) {
                    // If the new record was created successfully, delete the old record
                    $record->delete();
                } else {
                    // Handle the case where the new record could not be created
                    // For example, throw an exception or log an error message
                    throw new \Exception("Could not create new record");
                }
            }
        }
    }
}
