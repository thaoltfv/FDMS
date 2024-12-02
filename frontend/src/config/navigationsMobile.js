export let navigationsMobile = [];
navigationsMobile = [
	{
		id: "dashboard",
		type: "item",
		icon: "chart-line",
		title: "Dashboard",
		image: require('@/assets/icons/Dashboard.png'),
		routeName: "dashboard.index",
		exact: true
	},
	{
		id: "pre_certification",
		type: "item",
		icon: "nav_ycsb",
		title: "Yêu cầu sơ bộ",
		image: require('@/assets/icons/yeu-cau-so-bo.png'),
		routeName: "pre_certification.index",
		customImage: true
	},
	{
		id: "price_estimates",
		type: "item",
		icon: "nav_utg",
		title: "Tài sản sơ bộ",
		image: require('@/assets/icons/tai-san-so-bo.png'),
		routeName: "price_estimates.index",
		customImage: true
	},
	{
		id: "certification_brief",
		type: "item",
		icon: "nav_hstd",
		title: "Hồ sơ thẩm định",
		image: require('@/assets/icons/ho-so-tham-dinh.png'),
		routeName: "certification_brief.index",
		customImage: true
	},
	{
		id: "certification_asset",
		type: "item",
		icon: "nav_tstd",
		title: "Tài sản thẩm định",
		image: require('@/assets/icons/tai-san-tham-dinh.png'),
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
		id: "home",
		type: "item",
		icon: "nav_bdg",
		title: "Bản đồ giá",
		image: require('@/assets/icons/ban-do-gia.png'),
		routeName: "home",
		exact: true,
		customImage: true
	},
	{
		id: "quyhoach",
		type: "item",
		icon: "nav_bddc",
		title: "Bản đồ địa chính",
		image: require('@/assets/icons/quy-hoach-dia-chinh.png'),
		routeName: "map_dia_chinh",
		exact: true,
		customImage: true
	},
	{
		id: "warehouse",
		type: "item",
		icon: "nav_kg",
		title: "Kho giá",
		image: require('@/assets/icons/kho-gia.png'),
		routeName: "warehouse.index",
		customImage: true
	},
	{
		id: "menu_certification",
		type: "item",
		icon: "nav_tdhs",
		title: "Tài khoản khách",
		image: require('@/assets/icons/CSM.png'),
		dropdown: [
			{
				id: "menu_pre_certificate",
				title: "Yêu cầu sơ bộ",
				routeName: "menu_pre_certification.index",
				exact: true
			},
			{
				id: "menu_certificate",
				title: "Hồ sơ thẩm định",
				routeName: "menu_certification.index",
				exact: true
			},
		],
		// routeName: "menu_certification.index",
		customImage: true
	},
];