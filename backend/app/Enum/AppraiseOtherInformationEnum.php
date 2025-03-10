<?php


namespace App\Enum;


class AppraiseOtherInformationEnum
{
    public const DATA =[
        'thong_nhat_muc_gia_chi_dan' => [
            [
				"id" => 101,
				"name" => "Trung bình",
				"slug" => "trung-binh",
				"description" => "Trung bình",
				"type" => "THONG_NHAT_MUC_GIA_CHI_DAN",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => true,
                "acronym" => array("BDS"),
			],
			[
				"id" => 102,
				"name" => "Thấp nhất",
				"slug" => "thap-nhat",
				"description" => "Thấp nhất",
				"type" => "THONG_NHAT_MUC_GIA_CHI_DAN",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false
			],
			[
				"id" => 103,
				"name" => "Cao nhất",
				"slug" => "cao-nhat",
				"description" => "Cao nhất",
				"type" => "THONG_NHAT_MUC_GIA_CHI_DAN",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false
			],
			[
				"id" => 104,
				"name" => "TSSS 1",
				"slug" => "tsss-1",
				"description" => "TSSS 1",
				"type" => "THONG_NHAT_MUC_GIA_CHI_DAN",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false
			],
			[
				"id" => 105,
				"name" => "TSSS 2",
				"slug" => "tsss-2",
				"description" => "TSSS 2",
				"type" => "THONG_NHAT_MUC_GIA_CHI_DAN",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false
			],
			[
				"id" => 106,
				"name" => "TSSS 3",
				"slug" => "tsss-3",
				"description" => "TSSS 3",
				"type" => "THONG_NHAT_MUC_GIA_CHI_DAN",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false
			]
		],
		'tinh_gia_dat_hon_hop_con_lai' => [
			[
				"id" => 201,
				"name" => "Theo chi phí chuyển MĐSD đất",
				"slug" => "theo-chi-phi-chuyen-mdsd-dat",
				"description" => "Theo chi phí chuyển MĐSD đất",
				"type" => "TINH_GIA_DAT_HON_HOP_CON_LAI",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => true,
                "acronym" => array("BDS"),
			],
			[
				"id" => 202,
				"name" => "Theo tỷ lệ % giá đất cơ sở chính",
				"slug" => "theo-ty-le-gia-dat-co-so-chinh",
				"description" => "Theo tỷ lệ % giá đất cơ sở chính",
				"type" => "TINH_GIA_DAT_HON_HOP_CON_LAI",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false,
                "acronym" => array("BDS"),
			],
			[
				"id" => 302,
				"name" => "Theo phương pháp độc lập",
				"slug" => "theo-phuong-phap-doc-lap",
				"description" => "Theo phương pháp độc lập",
				"type" => "TINH_GIA_DAT_HON_HOP_CON_LAI",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false,
                "acronym" => array("BDS"),
			]
		],
		'tinh_gia_dat_vi_pham_quy_hoach' => [
			[
				"id" => 301,
				"name" => "Theo giá đất QĐ UBND",
				"slug" => "theo-gia-dat-qd-ubnd",
				"description" => "Theo giá đất QĐ UBND",
				"type" => "TINH_GIA_DAT_VI_PHAM_QUY_HOACH",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => true,
                "acronym" => array("BDS"),
			],
			[
				"id" => 302,
				"name" => "Theo tỷ lệ % giá đất thị trường",
				"slug" => "theo-ty-le-gia-dat-thi-truong",
				"description" => "Theo tỷ lệ % giá đất thị trường",
				"type" => "TINH_GIA_DAT_VI_PHAM_QUY_HOACH",
				"created_at" => "2022-02-15T10:22:46.000000Z",
				"updated_at" => "2022-02-15T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false,
                "acronym" => array("BDS"),
			],
			// [
			// 	"id" => 303,
			// 	"name" => "Không tính",
			// 	"slug" => "khong-tinh",
			// 	"description" => "Không tính",
			// 	"type" => "TINH_GIA_DAT_VI_PHAM_QUY_HOACH",
			// 	"created_at" => "2022-02-15T10:22:46.000000Z",
			// 	"updated_at" => "2022-02-15T15:27:53.000000Z",
			// 	"deleted_at" => null,
			// 	"is_defaults" => false,
            //     "acronym" => array("BDS"),
			// ]
        ],
        'xac_dinh_clcl' =>[
			[
				"id" => 401,
				"name" => "Theo PP1",
				"slug" => "tuoi-doi",
				"description" => "Phương pháp tuổi đời",
				"type" => "XAC_DINH_CHAT_LUONG_CON_LAI",
				"created_at" => "2022-12-20T10:22:46.000000Z",
				"updated_at" => "2022-12-20T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false,
                "acronym" => array("BDS"),
            ],
			[
				"id" => 402,
				"name" => "Theo PP2",
				"slug" => "chuyen-gia",
				"description" => "Phương pháp chuyên gia",
				"type" => "XAC_DINH_CHAT_LUONG_CON_LAI",
				"created_at" => "2022-12-20T10:22:46.000000Z",
				"updated_at" => "2022-12-20T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false,
                "acronym" => array("BDS"),
			],
			[
				"id" => 403,
				"name" => "Theo bình quân",
				"slug" => "trung-binh-cong",
				"description" => "Trung bình cộng",
				"type" => "XAC_DINH_CHAT_LUONG_CON_LAI",
				"created_at" => "2022-12-20T10:22:46.000000Z",
				"updated_at" => "2022-12-20T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => true,
                "acronym" => array("BDS"),
            ]
		],
		'xac_dinh_dgxd' =>[
			[
				"id" => 501,
				"name" => "Đơn giá trung bình",
				"slug" => "dg-uoc-tinh",
				"description" => "Đơn giá ước tính",
				"type" => "XAC_DINH_DON_GIA_XAY_DUNG",
				"created_at" => "2022-12-20T10:22:46.000000Z",
				"updated_at" => "2022-12-20T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => false,
                "acronym" => array("BDS"),
            ],
			[
				"id" => 502,
				"name" => "Đơn giá quyết định",
				"slug" => "dg-quyet-dinh",
				"description" => "Đơn giá quyết định",
				"type" => "XAC_DINH_DON_GIA_XAY_DUNG",
				"created_at" => "2022-12-20T10:22:46.000000Z",
				"updated_at" => "2022-12-20T15:27:53.000000Z",
				"deleted_at" => null,
				"is_defaults" => true,
                "acronym" => array("BDS"),
			]
        ]
    ];
}
