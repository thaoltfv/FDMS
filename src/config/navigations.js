export let navigations = [];
// if (process.env.API_URL === 'https://trial-api.fastvalue.com.vn' || process.env.API_URL === 'https://vavc-api.fastvalue.vn') {
navigations = [
	{
		id: "dashboard",
		type: "item",
		icon: "chart-line",
		title: "Dashboard",
		routeName: "dashboard.index",
		exact: true
	},
	{
		id: "pre_certification",
		type: "item",
		icon: "nav_ycsb",
		title: "Yêu cầu sơ bộ",
		routeName: "pre_certification.index",
		customImage: true
	},
	{
		id: "certification_brief",
		type: "item",
		icon: "nav_hstd",
		title: "Hồ sơ thẩm định",
		routeName: "certification_brief.index",
		customImage: true
	},
	{
		id: "certification_asset",
		type: "item",
		icon: "nav_tstd",
		title: "Tài sản thẩm định",
		dropdown: [
			{
				id: "real_estate",
				title: "Bất động sản",
				routeName: "certification_asset.index",
				exact: true
			},
			{
				id: "personal_property",
				title: "Động sản",
				routeName: "certification_personal_property.index",
				exact: true
			}
		],
		customImage: true
	},
	{
		id: "price_estimates",
		type: "item",
		icon: "nav_ycsb",
		title: "Ước tính giá",
		routeName: "price_estimates.index",
		customImage: true
	},
	{
		id: "home",
		type: "item",
		icon: "nav_bdg",
		title: "Bản đồ giá",
		routeName: "home",
		exact: true,
		customImage: true
	},
	{
		id: "quyhoach",
		type: "item",
		icon: "nav_bddc",
		title: "Bản đồ địa chính",
		routeName: "map_dia_chinh",
		exact: true,
		customImage: true
	},
	{
		id: "warehouse",
		type: "item",
		icon: "nav_kg",
		title: "Kho giá",
		routeName: "warehouse.index",
		customImage: true
	},
	{
		id: "price_estimate",
		type: "item",
		icon: "nav_utg",
		title: "Ước tính giá",
		routeName: "price_estimate.index",
		customImage: true,
		denied: ["nova"]
	},
	{
		id: "log",
		type: "item",
		icon: "nav_lsut",
		title: "Lịch sử ước tính",
		routeName: "log.index",
		customImage: true,
		denied: ["nova"]
	},
	// { id: 'certificate', type: 'item', icon: 'certificate', title: 'Tài sản thẩm định Ver.1', routeName: 'certificate.index' },
	// { id: 'appraisal', type: 'item', icon: 'file-signature', title: 'Hồ sơ thẩm định Ver.1', routeName: 'appraisal.index' },
	{
		id: "appraise",
		type: "item",
		icon: "nav_dltd",
		title: "Dữ liệu thẩm định",
		dropdown: [
			{
				id: "appraise-law",
				title: "Văn bản pháp luật",
				routeName: "appraise-law.index",
				exact: true
			},
			{
				id: "appraise-other",
				title: "Cơ sở thẩm định",
				routeName: "appraise-other.index",
				exact: true
			},
			{
				id: "appraisal-construction",
				title: "Đơn vị xây dựng",
				routeName: "appraisal-construction.index",
				exact: true
			},
			{
				id: "building",
				title: "Quản lý loại công trình",
				routeName: "building.index",
				exact: true
			},
			{
				id: "element",
				title: "Yếu tố so sánh",
				routeName: "element.index",
				exact: true
			},
			{
				id: "dictionary",
				title: "Quản lý dữ liệu khác",
				routeName: "dictionary.index",
				exact: true
			},
			{
				id: "workFlowConfig",
				title: "Thời gian thực hiện",
				routeName: "pre_certificate_config.index",
				exact: true
			}
		],
		customImage: true
	},
	{
		id: "category",
		type: "item",
		icon: "nav_dmql",
		title: "Danh mục hành chính",
		dropdown: [
			{
				id: "province",
				title: "Quản lý Tỉnh/Thành",
				routeName: "province.index",
				exact: true
			},
			{
				id: "district",
				title: "Quản lý Quận/Huyện",
				routeName: "district.index",
				exact: true
			},
			{
				id: "ward",
				title: "Quản lý Phường/Xã",
				routeName: "ward.index",
				exact: true
			},
			{
				id: "street",
				title: "Quản lý Đường phố",
				routeName: "street.index",
				exact: true
			},
			{
				id: "apartment",
				title: "Quản lý Chung cư",
				routeName: "apartment.index",
				exact: true
			}
			// { id: 'unit', title: 'Quản lý đơn giá UBND', routeName: 'unit_price.index', exact: true },
		],
		customImage: true
	},
	{
		id: "manage",
		type: "item",
		icon: "users-cog",
		title: "HRM",
		dropdown: [
			{
				id: "role",
				type: "item",
				title: "Phân quyền",
				routeName: "role.index",
				exact: true
			},
			{
				id: "appraiser-company",
				title: "Thông tin công ty",
				routeName: "appraiser-company.index",
				exact: true
			},
			{
				id: "branch",
				title: "Quản lý Chi nhánh",
				routeName: "branch.index",
				exact: true
			},
			{
				id: "staff",
				type: "item",
				title: "Quản lý Nhân viên",
				routeName: "staff.index",
				exact: true
			}
			// { id: 'appraiser', title: 'Danh sách TDV', routeName: 'appraiser.index', exact: true }
		],
		exact: true,
		customImage: false
	},

	{
		id: "customer",
		type: "item",
		icon: "users",
		title: "Quản lý đối tác",
		routeName: "customer.index",
		customImage: false
	},
	{
		id: "configuration",
		type: "item",
		icon: "cogs",
		title: "Cấu hình",
		dropdown: [
			{
				id: "system_config",
				type: "item",
				title: "Cấu hình hệ thống",
				routeName: "PrincipleConfig.index",
				exact: true
			},
			{
				id: "document_config",
				type: "item",
				title: "Cấu hình tài liệu",
				routeName: "watermark.index",
				exact: true
			},
			{
				id: "principle_config",
				type: "item",
				title: "Quy trình",
				routeName: "PrincipleConfig.index",
				exact: true
			},
			{
				id: "dash_board_config",
				type: "item",
				title: "DashBoard",
				routeName: "DashBoardConfig.index",
				exact: true
			},
			{
				id: "import_street",
				type: "item",
				title: "Import địa danh",
				routeName: "import_street.index",
				exact: true
			}
		],
		customImage: false
	}
];
// } else {
// 	navigations = [
// 		{ id: 'dashboard', type: 'item', icon: 'chart-line', title: 'Dashboard', routeName: 'dashboard.index', exact: true },
// 		{ id: 'certification_brief', type: 'item', icon: 'nav_hstd', title: 'Hồ sơ thẩm định', routeName: 'certification_brief.index', customImage: true },
// 		{
// 			id: 'certification_asset',
// 			type: 'item',
// 			icon: 'nav_tstd',
// 			title: 'Tài sản thẩm định',
// 			dropdown: [
// 				{ id: 'real_estate', title: 'Bất động sản', routeName: 'certification_asset.index', exact: true },
// 				{ id: 'personal_property', title: 'Động sản', routeName: 'certification_personal_property.index', exact: true }
// 			],
// 			customImage: true
// 		},
// 		{ id: 'home', type: 'item', icon: 'nav_bdg', title: 'Bản đồ giá', routeName: 'home', exact: true, customImage: true },
// 		// { id: 'quyhoach', type: 'item', icon: 'nav_bdg', title: 'Bản đồ địa chính', routeName: 'map_dia_chinh', exact: true, customImage: true },
// 		{ id: 'warehouse', type: 'item', icon: 'nav_kg', title: 'Kho giá', routeName: 'warehouse.index', customImage: true },
// 		{ id: 'price_estimate', type: 'item', icon: 'nav_utg', title: 'Ước tính giá', routeName: 'price_estimate.index', customImage: true, denied: ['nova']},
// 		{ id: 'log', type: 'item', icon: 'nav_lsut', title: 'Lịch sử ước tính', routeName: 'log.index', customImage: true, denied: ['nova'] },
// 		// { id: 'certificate', type: 'item', icon: 'certificate', title: 'Tài sản thẩm định Ver.1', routeName: 'certificate.index' },
// 		// { id: 'appraisal', type: 'item', icon: 'file-signature', title: 'Hồ sơ thẩm định Ver.1', routeName: 'appraisal.index' },
// 		{
// 			id: 'appraise',
// 			type: 'item',
// 			icon: 'nav_dltd',
// 			title: 'Dữ liệu thẩm định',
// 			dropdown: [
// 				{ id: 'appraise-law', title: 'Văn bản pháp luật', routeName: 'appraise-law.index', exact: true },
// 				{ id: 'appraise-other', title: 'Cơ sở thẩm định', routeName: 'appraise-other.index', exact: true },
// 				{ id: 'appraisal-construction', title: 'Đơn vị xây dựng', routeName: 'appraisal-construction.index', exact: true },
// 				{ id: 'building', title: 'Quản lý loại công trình', routeName: 'building.index', exact: true },
// 				{ id: 'element', title: 'Yếu tố so sánh', routeName: 'element.index', exact: true }
// 			],
// 			customImage: true
// 		},
// 		{
// 			id: 'category',
// 			type: 'item',
// 			icon: 'nav_dmql',
// 			title: 'Danh mục hành chính',
// 			dropdown: [
// 				{ id: 'province', title: 'Quản lý Tỉnh/Thành', routeName: 'province.index', exact: true },
// 				{ id: 'district', title: 'Quản lý Quận/Huyện', routeName: 'district.index', exact: true },
// 				{ id: 'ward', title: 'Quản lý Phường/Xã', routeName: 'ward.index', exact: true },
// 				{ id: 'street', title: 'Quản lý Đường phố', routeName: 'street.index', exact: true },
// 				{ id: 'apartment', title: 'Quản lý Chung cư', routeName: 'apartment.index', exact: true },
// 				// { id: 'unit', title: 'Quản lý đơn giá UBND', routeName: 'unit_price.index', exact: true },
// 				{ id: 'dictionary', title: 'Quản lý Chung', routeName: 'dictionary.index', exact: true }
// 			],
// 			customImage: true
// 		},
// 		{
// 			id: 'manage',
// 			type: 'item',
// 			icon: 'users-cog',
// 			title: 'HRM',
// 			dropdown: [
// 				{ id: 'role', type: 'item', title: 'Phân quyền', routeName: 'role.index', exact: true },
// 				{ id: 'appraiser-company', title: 'Thông tin công ty', routeName: 'appraiser-company.index', exact: true },
// 				{ id: 'branch', title: 'Quản lý Chi nhánh', routeName: 'branch.index', exact: true },
// 				{ id: 'staff', type: 'item', title: 'Quản lý Nhân viên', routeName: 'staff.index', exact: true }
// 				// { id: 'appraiser', title: 'Danh sách TDV', routeName: 'appraiser.index', exact: true }
// 			],
// 			exact: true,
// 			customImage: false
// 		},

// 		{ id: 'customer', type: 'item', icon: 'users', title: 'Quản lý đối tác', routeName: 'customer.index', customImage: false },
// 		{
// 			id: 'configuration',
// 			type: 'item',
// 			icon: 'cogs',
// 			title: 'Cấu hình',
// 			dropdown: [
// 				{ id: 'system_config', type: 'item', title: 'Cấu hình hệ thống', routeName: 'PrincipleConfig.index', exact: true },
// 				{ id: 'document_config', type: 'item', title: 'Cấu hình tài liệu', routeName: 'watermark.index', exact: true },
// 				{ id: 'principle_config', type: 'item', title: 'Quy trình', routeName: 'PrincipleConfig.index', exact: true },
// 				{ id: 'dash_board_config', type: 'item', title: 'DashBoard', routeName: 'DashBoardConfig.index', exact: true },
// 				{ id: 'import_street', type: 'item', title: 'Import địa danh', routeName: 'import_street.index', exact: true }
// 			],
// 			customImage: false
// 		}
// 	]
// }
