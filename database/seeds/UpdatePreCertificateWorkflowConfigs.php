<?php

use Illuminate\Database\Seeder;
use App\Models\PreCertificateConfig;

class UpdatePreCertificateWorkflowConfigs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $record = PreCertificateConfig::where('name', 'workflow')->first();
        // Prepare new data
        $newData = [
            "appraiser" => [
                "created_by" => "Người tạo",
                "appraiser_sale_id" => "Nhân viên kinh doanh",
                "business_manager_id" => "Quản lý nghiệp vụ",
                "appraiser_perform_id" => "Chuyên viên thẩm định"
            ],
            "filterOTS" => [
                [
                    "text" => "Chưa chuyển",
                    "value" => 0
                ],
                [
                    "text" => "Đã chuyển chính thức",
                    "value" => 1
                ]
            ],
            "principle" => [
                [
                    "id" => 1,
                    "css" => [
                        "color" => "info"
                    ],
                    "key" => 1,
                    "edit" => [
                        "export_document" => true,
                        "form" => true,
                        "info" => true,
                        "payments" => true,
                        "appraiser" => true,
                        "file_result" => true,
                        "file_appendix" => true
                    ],
                    "isExportDocument" => true,
                    "slug" => "yeu_cau_so_bo",
                    "print" => false,
                    "status" => 1,
                    "require" => [],
                    "isActive" => 1,
                    "isCancel" => true,
                    "expire_in" => 1200,
                    "re_assign" => "appraiser_sale_id",
                    "sub_status" => 1,
                    "description" => "Yêu cầu sơ bộ",
                    "put_require" => [
                        "appraiser_sale_id",
                    ],
                    "process_time" => 1520,
                    "put_draggable" => [
                        8,
                        7
                    ],
                    "put_require_roles" => [
                        "ROOT_ADMIN"
                    ],
                    "target_description" => [
                        [
                            "id" => 7,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Hủy"
                        ],
                        [
                            "id" => 8,
                            "css" => "btn-white btn-orange",
                            "img" => "ic_done.svg",
                            "description" => "Phân hồ sơ",
                            "btnDescription" => "Chuyển tiếp"
                        ]
                    ]
                ],
                [
                    "id" => 8,
                    "css" => [
                        "color" => "info"
                    ],
                    "key" => 8,
                    "edit" => [
                        "export_document" => true,
                        "form" => false,
                        "info" => true,
                        "payments" => true,
                        "appraiser" => true,
                        "file_result" => true,
                        "file_appendix" => true
                    ],
                    "isExportDocument" => true,
                    "slug" => "phan_ho_so",
                    "print" => false,
                    "status" => 8,
                    "require" => [],
                    "isActive" => 1,
                    "isCancel" => true,
                    "expire_in" => 1200,
                    "re_assign" => "business_manager_id",
                    "sub_status" => 1,
                    "description" => "Phân hồ sơ",
                    "put_require" => [
                        "business_manager_id"
                    ],
                    "process_time" => 1520,
                    "put_draggable" => [
                        2,
                        7
                    ],
                    "put_require_roles" => [
                        "ROOT_ADMIN"
                    ],
                    "target_description" => [
                        [
                            "id" => 7,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Hủy"
                        ],
                        [
                            "id" => 1,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Từ chối"
                        ],
                        [
                            "id" => 2,
                            "css" => "btn-white btn-orange",
                            "img" => "ic_done.svg",
                            "description" => "Định giá sơ bộ",
                            "btnDescription" => "Chuyển tiếp"
                        ]
                    ]
                ],
                [
                    "id" => 2,
                    "css" => [
                        "color" => "primary"
                    ],
                    "key" => 2,
                    "edit" => [
                        "export_document" => true,
                        "form" => false,
                        "info" => true,
                        "payments" => true,
                        "appraiser" => true,
                        "file_result" => true,
                        "file_appendix" => true
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
                    "isActive" => 1,
                    "isCancel" => true,
                    "expire_in" => 1200,
                    "re_assign" => "appraiser_perform_id",
                    "sub_status" => 2,
                    "description" => "Định giá sơ bộ",
                    "put_require" => [
                        "appraiser_perform_id"
                    ],
                    "process_time" => 1440,
                    "put_draggable" => [
                        1,
                        3,
                        7
                    ],
                    "put_require_roles" => [
                        "ROOT_ADMIN"
                    ],
                    "target_description" => [
                        [
                            "id" => 7,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Hủy"
                        ],
                        [
                            "id" => 8,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Từ chối"
                        ],
                        [
                            "id" => 3,
                            "css" => "btn-white btn-orange",
                            "img" => "ic_done.svg",
                            "description" => "Duyệt giá sơ bộ",
                            "btnDescription" => "Chuyển tiếp"
                        ]
                    ]
                ],
                [
                    "id" => 3,
                    "css" => [
                        "color" => "control"
                    ],
                    "key" => 3,
                    "edit" => [
                        "export_document" => true,
                        "form" => false,
                        "info" => false,
                        "payments" => true,
                        "appraiser" => false,
                        "file_result" => false,
                        "file_appendix" => false
                    ],
                    "isExportDocument" => true,
                    "slug" => "duyet_gia_so_bo",
                    "print" => true,
                    "status" => 3,
                    "require" => [
                        "appraiser" => true,
                        "check_legal" => false,
                        "check_price" => false,
                        "check_version" => false,
                        "appraise_item_list" => false
                    ],
                    "isActive" => 1,
                    "isCancel" => true,
                    "expire_in" => 1200,
                    "re_assign" => "business_manager_id",
                    "sub_status" => 3,
                    "description" => "Duyệt giá sơ bộ",
                    "put_require" => [
                        "business_manager_id"
                    ],
                    "process_time" => 1440,
                    "put_draggable" => [
                        2,
                        4,
                        7
                    ],
                    "put_require_roles" => [
                        "ROOT_ADMIN"
                    ],
                    "target_description" => [
                        [
                            "id" => 7,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Hủy"
                        ],
                        [
                            "id" => 2,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Từ chối"
                        ],
                        [
                            "id" => 5,
                            "css" => "btn-white btn-orange",
                            "img" => "ic_done.svg",
                            "description" => "Phát hành KQSB",
                            "btnDescription" => "Chuyển tiếp"
                        ]
                    ]
                ],

                [
                    "id" => 5,
                    "css" => [
                        "color" => "warning"
                    ],
                    "key" => 5,
                    "edit" => [
                        "export_document" => true,
                        "form" => false,
                        "info" => false,
                        "payments" => true,
                        "appraiser" => false,
                        "file_result" => false,
                        "file_appendix" => false
                    ],
                    "isExportDocument" => true,
                    "slug" => "in_ho_so",
                    "print" => true,
                    "status" => 5,
                    "require" => [
                        "appraiser" => true,
                        "check_legal" => false,
                        "check_price" => false,
                        "check_version" => false,
                        "appraise_item_list" => false
                    ],
                    "isActive" => 1,
                    "isCancel" => true,
                    "expire_in" => 1200,
                    "re_assign" => "appraiser_sale_id",
                    "sub_status" => 5,
                    "description" => "Phát hành KQSB",
                    "put_require" => [
                        "appraiser_sale_id"
                    ],
                    "process_time" => 1440,
                    "put_draggable" => [
                        4,
                        6,
                        7
                    ],
                    "put_require_roles" => [
                        "ROOT_ADMIN"
                    ],
                    "target_description" => [
                        [
                            "id" => 7,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Hủy"
                        ],
                        [
                            "id" => 3,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Từ chối"
                        ],
                        [
                            "id" => 6,
                            "css" => "btn-white btn-orange",
                            "img" => "ic_done.svg",
                            "description" => "Hoàn thành",
                            "btnDescription" => "Chuyển tiếp"
                        ]
                    ]
                ],
                [
                    "id" => 6,
                    "css" => [
                        "color" => "success"
                    ],
                    "key" => 6,
                    "edit" => [
                        "export_document" => true,
                        "form" => false,
                        "info" => false,
                        "payments" => true,
                        "appraiser" => false,
                        "file_result" => false,
                        "file_appendix" => false
                    ],
                    "isExportDocument" => true,
                    "slug" => "hoan_thanh",
                    "print" => true,
                    "status" => 6,
                    "require" => [
                        "appraiser" => true,
                        "check_legal" => false,
                        "check_price" => false,
                        "check_version" => false,
                        "appraise_item_list" => false
                    ],
                    "isActive" => 1,
                    "isCancel" => true,
                    "expire_in" => null,
                    "sub_status" => 6,
                    "description" => "Hoàn thành",
                    "put_require" => [
                        "appraiser_sale_id"
                    ],
                    "process_time" => null,
                    "put_draggable" => [
                        5,
                        7
                    ],
                    "put_require_roles" => [
                        "ROOT_ADMIN"
                    ],
                    "target_description" => [
                        [
                            "id" => 7,
                            "css" => "btn-white",
                            "img" => "ic_cancel-1.svg",
                            "description" => "Hủy"
                        ],
                        [
                            "id" => 8,
                            "css" => "btn-white btn-orange",
                            "img" => "ic_done.svg",
                            "code" => "chuyen_chinh_thuc",
                            "description" => "Chuyển chính thức",
                            "btnDescription" => "Chuyển chính thức"
                        ]
                    ]
                ],
                [
                    "id" => 7,
                    "css" => [
                        "color" => "secondary"
                    ],
                    "key" => 7,
                    "edit" => [
                        "export_document" => true,
                        "form" => false,
                        "info" => false,
                        "payments" => true,
                        "appraiser" => false,
                        "file_result" => false,
                        "file_appendix" => false
                    ],
                    "isExportDocument" => true,
                    "slug" => "huy",
                    "print" => true,
                    "status" => 7,
                    "require" => [
                        "appraiser" => true,
                        "check_legal" => false,
                        "check_price" => false,
                        "check_version" => false,
                        "appraise_item_list" => false
                    ],
                    "isActive" => 1,
                    "isCancel" => false,
                    "expire_in" => null,
                    "sub_status" => 7,
                    "description" => "Hủy",
                    "put_require" => [
                        "business_manager_id"
                    ],
                    "process_time" => null,
                    "put_draggable" => [
                        1
                    ],
                    "put_require_roles" => [
                        "ROOT_ADMIN"
                    ],
                    "target_description" => [
                        [
                            "id" => 1,
                            "css" => "btn-white btn-orange",
                            "img" => "ic_done.svg",
                            "description" => "Khôi phục"
                        ]
                    ]
                ],

            ],
            "filterStatus" => [
                [
                    "text" => "Yêu cầu sơ bộ",
                    "value" => 1
                ],
                [
                    "text" => "Định giá sơ bộ",
                    "value" => 2
                ],
                [
                    "text" => "Duyệt giá sơ bộ",
                    "value" => 3
                ],
                [
                    "text" => "Phát hành KQSB",
                    "value" => 5
                ],
                [
                    "text" => "Hoàn thành",
                    "value" => 6
                ],
                [
                    "text" => "Hủy",
                    "value" => 7
                ],

                [
                    "text" => "Phân hồ sơ",
                    "value" => 8
                ]
            ],
            "permissionAllowEdit" => [
                "form" => [
                    "appraiser_sale_id",
                    "business_manager_id"
                ],
                "info" => [
                    "appraiser_sale_id",
                    "appraiser_perform_id",
                    "business_manager_id"
                ],
                "payments" => [
                    "appraiser_perform_id",
                    "business_manager_id"
                ],
                "appraiser" => [
                    "appraiser_sale_id",
                    "business_manager_id"
                ],
                "file_result" => [
                    "appraiser_perform_id",
                    "business_manager_id"
                ],
                "file_appendix" => [
                    "appraiser_sale_id",
                    "appraiser_perform_id",
                    "business_manager_id"
                ]
            ]
        ];
        if ($record) {

            $oldData = $record->config;

            // Update config with new data
            $jsonData = json_encode($newData);

            if ($jsonData === false) {
                // The data could not be converted to a JSON string
                throw new \Exception("Invalid JSON data");
            } else {
                // Add a new record
                $newRecord = PreCertificateConfig::create([
                    'name' => "workflow",
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
        } else {
            // Tạo mới
            // Update config with new data
            $jsonData = json_encode($newData);

            if ($jsonData === false) {
                // The data could not be converted to a JSON string
                throw new \Exception("Invalid JSON data");
            } else {
                // Add a new record
                $newRecord = PreCertificateConfig::create([
                    'name' => "workflow",
                    'config' => $jsonData,
                    'old_config' => null,
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
