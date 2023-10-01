import Resource from '@/components/Resource'
import { AuthGuard, LoginGuard, ResolveGuard } from '@/router/guards'

import { PERMISSIONS } from '@/enum/permissions.enum'

export function page (path) {
	return () =>
        import(/* webpackChunkName: "[request]" */ `@/pages/${path}`)
}

export const routes = [
	// Login
	{
		path: '/login',
		name: 'login',
		component: page('Login.vue'),
		meta: { layout: 'auth' },
		beforeEnter: ResolveGuard([LoginGuard])
	},
	{
		path: '/ping',
		name: 'ping',
		component: page('Ping.vue'),
		meta: { layout: 'auth' }
	},
	{
		path: '/verify',
		name: 'verify',
		component: page('Verify.vue'),
		meta: { layout: 'auth' },
		beforeEnter: ResolveGuard([LoginGuard])
	},

	// Profile
	{
		path: '/profile',
		component: Resource,
		children: [{
			path: '',
			name: 'profile.index',
			component: page('profile/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Thông tin cá nhân',
				permissions: [PERMISSIONS.VIEW_USER],
				breadcrumbs: [
					{ title: 'Thông tin cá nhân', name: 'profile.index' }
				]
			}
		},
		{
			path: '/profile/edit',
			name: 'profile.edit',
			component: page('profile/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa thông tin cá nhân',
				permissions: [PERMISSIONS.EDIT_USER],
				breadcrumbs: []
			}
		},
		{
			path: '/profile/change-password',
			name: 'profile.password',
			component: page('profile/ChangePassword.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Thay đổi mật khẩu',
				permissions: [PERMISSIONS.EDIT_USER],
				breadcrumbs: []
			}
		}
		]
	},
	// Configuration
	{
		path: '/configuration',
		component: Resource,
		children: [{
			path: '',
			name: 'configuration.index',
			component: page('configuration/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Cấu hình',
				permissions: [PERMISSIONS.VIEW_PROPERTIES],
				breadcrumbs: [
					{ title: 'Cấu hình', name: 'configuration.index' }
				]
			}
		}]
	},
	{
		path: '/configuration/update-certificate-brief-sub-status',
		component: Resource,
		children: [{
			path: '',
			name: 'UpdateCertificateBriefSubStatus.index',
			component: page('configuration/UpdateCertificateBriefSubStatus.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Cập nhật sub-status',
				permissions: [PERMISSIONS.VIEW_PROPERTIES],
				breadcrumbs: [
					{ title: 'Cập nhật sub-status', name: 'UpdateCertificateBriefSubStatus.index' }
				]
			}
		}]
	},

	{
		path: '/configuration/dashboard-config',
		component: Resource,
		children: [{
			path: '',
			name: 'DashBoardConfig.index',
			component: page('configuration/DashBoardConfig.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Cấu hình Dashboard',
				permissions: [PERMISSIONS.VIEW_PROPERTIES],
				breadcrumbs: [
					{ title: 'Cấu hình Dashboard', name: 'DashBoardConfig.index' }
				]
			}
		}]
	},
	{
		path: '/configuration/principle-config',
		component: Resource,
		children: [{
			path: '',
			name: 'PrincipleConfig.index',
			component: page('configuration/PrincipleConfig.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Cấu hình quy trình',
				permissions: [PERMISSIONS.VIEW_PROPERTIES],
				breadcrumbs: [
					{ title: 'Cấu hình quy trình', name: 'PrincipleConfig.index' }
				]
			}
		}]
	},
	{
		path: '/configuration',
		component: Resource,
		children: [{
			path: '',
			name: 'configuration.index',
			component: page('configuration/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Cấu hình',
				permissions: [PERMISSIONS.VIEW_PROPERTIES],
				breadcrumbs: [
					{ title: 'Cấu hình', name: 'configuration.index' }
				]
			}
		}]
	},
	{
		path: '/log-estimate',
		component: Resource,
		children: [{
			path: '',
			name: 'log.index',
			component: page('log_estimate/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Lịch sử ước tính',
				permissions: [PERMISSIONS.VIEW_PRICE],
				breadcrumbs: [
					{ title: 'Lịch sử ước tính', name: 'log.index' }
				]
			}
		},
		{
			path: 'detail',
			name: 'log.detail',
			component: page('log_estimate/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Lịch sử ước tính',
				permissions: [PERMISSIONS.VIEW_PRICE],
				breadcrumbs: [
					{ title: 'Lịch sử ước tính', name: 'log.index' },
					{ title: 'Chi tiết ước tính', name: 'log.detail' }
				]
			}
		}
		]
	},
	{
		path: '/log-all',
		component: Resource,
		children: [{
			path: '',
			name: 'logAll.index',
			component: page('log_estimate_all/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Lịch sử ước tính',
				permissions: [PERMISSIONS.VIEW_PRICE],
				breadcrumbs: [
					{ title: 'Lịch sử ước tính', name: 'logAll.index' }
				]
			}
		}]
	},
	// import
	{
		path: '/import_street',
		component: Resource,
		children: [{
			path: '',
			name: 'import_street.index',
			component: page('import_street/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Thêm dữ liệu',
				permissions: [PERMISSIONS.VIEW_PROPERTIES],
				breadcrumbs: [
					{ title: 'Thêm dữ liệu', name: 'import_street.index' }
				]
			}
		}]
	},
	// Home
	{
		path: '/',
		component: Resource,
		children: [{
			path: '',
			name: 'home',
			component: page('map/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Bản đồ',
				permissions: [PERMISSIONS.VIEW_MAP],
				breadcrumbs: [
					//   { title: 'Bất động sản' }
				]
			}
		}]
	},
	// Map địa chính
	// {
	// 	path: '/map_dia_chinh',
	// 	component: Resource,
	// 	children: [{
	// 		path: '',
	// 		name: 'map_dia_chinh',
	// 		component: page('map/MapDiaChinh.vue'),
	// 		beforeEnter: ResolveGuard([AuthGuard]),
	// 		meta: {
	// 			title: 'Bản đồ địa chính',
	// 			permissions: [PERMISSIONS.VIEW_MAP],
	// 			breadcrumbs: [
	// 				//   { title: 'Bất động sản' }
	// 			]
	// 		}
	// 	}]
	// },
	// warehouse
	{
		path: '/property',
		component: Resource,
		children: [{
			path: '',
			name: 'warehouse.index',
			component: page('warehouse/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Kho giá',
				permissions: [PERMISSIONS.VIEW_PRICE],
				breadcrumbs: [
					{ title: 'Kho giá', name: 'warehouse.index' }
				]
			}
		},
		{
			path: '/property/create',
			name: 'warehouse.create',
			component: page('warehouse/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tạo mới tài sản so sánh',
				permissions: [PERMISSIONS.ADD_PRICE],
				breadcrumbs: [
					{ title: 'Kho giá', name: 'warehouse.index' },
					{ title: 'Tạo mới tài sản so sánh', name: 'warehouse.create' }
				]
			}
		},
		{
			path: '/property/detail',
			name: 'warehouse.detail',
			component: page('warehouse/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết tài sản so sánh',
				permissions: [PERMISSIONS.VIEW_PRICE],
				breadcrumbs: [
					{ title: 'Kho giá', name: 'warehouse.index' },
					{ title: 'Chi tiết tài sản so sánh', name: 'warehouse.detail' }
				]
			}
		},
		{
			path: '/property/edit',
			name: 'warehouse.edit',
			component: page('warehouse/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản so sánh',
				permissions: [PERMISSIONS.EDIT_PRICE],
				breadcrumbs: [
					{ title: 'Kho giá', name: 'warehouse.index' },
					{ title: 'Chỉnh sửa tài sản so sánh', name: 'warehouse.edit' }
				]
			}
		}
		]
	},
	// report
	// {
	// 	path: '/report/certification_brief',
	// 	component: Resource,
	// 	children: [{
	// 		path: '',
	// 		name: 'report_certification_brief.index',
	// 		component: page('report/CertificationBrief/Index.vue'),
	// 		beforeEnter: ResolveGuard([AuthGuard]),
	// 		meta: {
	// 			title: 'Báo cáo',
	// 			permissions: [PERMISSIONS.VIEW_DASHBOARD],
	// 			breadcrumbs: [
	// 				{ title: 'Báo cáo', name: 'report_certification_brief.index' }
	// 			]
	// 		}
	// 	}
	// {
	//     path: '/property/create',
	//     name: 'warehouse.create',
	//     component: page('warehouse/Create.vue'),
	//     beforeEnter: ResolveGuard([AuthGuard]),
	//     meta: {
	//         title: 'Tạo mới tài sản so sánh',
	//         permissions: [PERMISSIONS.ADD_PRICE],
	//         breadcrumbs: [
	//             { title: 'Kho giá', name: 'warehouse.index' },
	//             { title: 'Tạo mới tài sản so sánh', name: 'warehouse.create' }
	//         ]
	//     }
	// },
	// {
	//     path: '/property/detail',
	//     name: 'warehouse.detail',
	//     component: page('warehouse/Detail.vue'),
	//     beforeEnter: ResolveGuard([AuthGuard]),
	//     meta: {
	//         title: 'Chi tiết tài sản so sánh',
	//         permissions: [PERMISSIONS.VIEW_PRICE],
	//         breadcrumbs: [
	//             { title: 'Kho giá', name: 'warehouse.index' },
	//             { title: 'Chi tiết tài sản so sánh', name: 'warehouse.detail' }
	//         ]
	//     }
	// },
	// {
	//     path: '/property/edit',
	//     name: 'warehouse.edit',
	//     component: page('warehouse/Edit.vue'),
	//     beforeEnter: ResolveGuard([AuthGuard]),
	//     meta: {
	//         title: 'Chỉnh sửa tài sản so sánh',
	//         permissions: [PERMISSIONS.EDIT_PRICE],
	//         breadcrumbs: [
	//             { title: 'Kho giá', name: 'warehouse.index' },
	//             { title: 'Chỉnh sửa tài sản so sánh', name: 'warehouse.edit' }
	//         ]
	//     }
	// }
	// 	]
	// },

	// customer
	{
		path: '/customer',
		component: Resource,
		children: [{
			path: '',
			name: 'customer.index',
			component: page('customer/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Khách hàng',
				permissions: [PERMISSIONS.VIEW_CUSTOMER],
				breadcrumbs: [
					{ title: 'Khách hàng', name: 'customer.index' }
				]
			}
		},
		{
			path: '/customer/create',
			name: 'customer.create',
			component: page('customer/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tạo mới khách hàng',
				permissions: [PERMISSIONS.ADD_CUSTOMER],
				breadcrumbs: [
					{ title: 'Khách hàng', name: 'customer.index' },
					{ title: 'Tạo mới khách hàng', name: 'customer.create' }
				]
			}
		},
		{
			path: '/customer/detail',
			name: 'customer.detail',
			component: page('customer/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết khách hàng',
				permissions: [PERMISSIONS.VIEW_CUSTOMER],
				breadcrumbs: [
					{ title: 'Khách hàng', name: 'customer.index' },
					{ title: 'Chi tiết khách hàng', name: 'customer.detail' }
				]
			}
		},
		{
			path: '/customer/edit',
			name: 'customer.edit',
			component: page('customer/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa khách hàng',
				permissions: [PERMISSIONS.EDIT_CUSTOMER],
				breadcrumbs: [
					{ title: 'Khách hàng', name: 'customer.index' },
					{ title: 'Chỉnh sửa khách hàng', name: 'customer.edit' }
				]
			}
		}
		]
	},
	// price-estimate
	{
		path: '/price_estimate',
		name: 'price_estimate.index',
		component: page('price_estimate/Index.vue'),
		beforeEnter: ResolveGuard([AuthGuard]),
		meta: {
			permissions: [PERMISSIONS.VIEW_PRICE]
		}
	},
	{
		path: '/price_estimate/get_log',
		name: 'price_estimate.log',
		component: page('price_estimate/GetLog.vue'),
		beforeEnter: ResolveGuard([AuthGuard]),
		meta: {
			permissions: [PERMISSIONS.VIEW_PRICE]
		}
	},

	// staff
	{
		path: '/manage',
		component: Resource,
		children: [{
			path: 'staff',
			name: 'staff.index',
			component: page('staff/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Nhân viên',
				permissions: [PERMISSIONS.ADD_ROLE], // Force need role permision
				breadcrumbs: [
					{ title: 'Nhân viên', name: 'staff.index' }
				]
			}
		},
		{
			path: 'staff/create',
			name: 'staff.create',
			component: page('staff/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Nhân viên',
				permissions: [PERMISSIONS.ADD_ROLE], // Force need role permision
				breadcrumbs: [
					{ title: 'Nhân viên', name: 'staff.index' },
					{ title: 'Tạo nhân viên', name: 'staff.create' }
				]
			}
		},
		{
			path: 'staff/edit',
			name: 'staff.edit',
			component: page('staff/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa',
				permissions: [PERMISSIONS.EDIT_ROLE], // Force need role permision
				breadcrumbs: [
					{ title: 'Nhân viên', name: 'staff.index' },
					{ title: 'Chỉnh sửa', name: 'staff.edit' }
				]
			}
		},
			// role
		{
			path: 'role',
			name: 'role.index',
			component: page('role/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Phân quyền',
				permissions: [PERMISSIONS.VIEW_ROLE],
				breadcrumbs: [
					{ title: 'Phân quyền', name: 'role.index' }
				]
			}
		},
		{
			path: 'role/create',
			name: 'role.create',
			component: page('role/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tạo phân quyền',
				permissions: [PERMISSIONS.ADD_ROLE],
				breadcrumbs: [
					{ title: 'Phân quyền', name: 'role.index' },
					{ title: 'Tạo phân quyền', name: 'role.create' }
				]
			}
		},
		{
			path: 'role/edit',
			name: 'role.edit',
			component: page('role/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa phân quyền',
				permissions: [PERMISSIONS.EDIT_ROLE],
				breadcrumbs: [
					{ title: 'Phân quyền', name: 'role.index' },
					{ title: 'Chỉnh sửa phân quyền', name: 'role.edit' }
				]
			}
		}
		]
	},

	// Category
	{
		path: '/category',
		component: Resource,
		children: [
			// unit price
			{
				path: 'unit-price',
				name: 'unit_price.index',
				component: page('category/unit_price/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Đơn giá UBND',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Đơn giá UBND', name: 'unit_price.index' }
					]
				}
			},
			{
				path: 'unit-price/detail',
				name: 'unit_price.detail',
				component: page('category/unit_price/Detail.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Đơn giá UBND chi tiết',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Đơn giá UBND', name: 'unit_price.index' },
						{ title: 'Đơn giá UBND chi tiết', name: 'unit_price.detail' }
					]
				}
			},
			// street
			{
				path: 'street',
				name: 'street.index',
				component: page('category/street/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Đường phố',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Đường phố', name: 'street.index' }
					]
				}
			},
			{
				path: 'street/create',
				name: 'street.create',
				component: page('category/street/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Thêm đường phố',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Đường phố', name: 'street.index' },
						{ title: 'Thêm đường phố', name: 'street.create' }
					]
				}
			},
			{
				path: 'street/edit',
				name: 'street.edit',
				component: page('category/street/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa đường phố',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Đường phố', name: 'street.index' },
						{ title: 'Chỉnh sửa đường phố', name: 'street.edit' }
					]
				}
			},
			{
				path: 'province',
				name: 'province.index',
				component: page('category/province/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Tỉnh/Thành',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Tỉnh/Thành', name: 'province.index' }
					]
				}
			},
			{
				path: 'province/create',
				name: 'province.create',
				component: page('category/province/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Tạo Tỉnh/Thành',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Tỉnh/Thành', name: 'province.index' },
						{ title: 'Tạo Tỉnh/Thành', name: 'province.create' }
					]
				}
			},
			{
				path: 'province/edit',
				name: 'province.edit',
				component: page('category/province/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa Tỉnh/Thành',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Tỉnh/Thành', name: 'province.index' },
						{ title: 'Chỉnh sửa Tỉnh/Thành', name: 'province.edit' }
					]
				}
			},
			// district
			{
				path: 'district',
				name: 'district.index',
				component: page('category/district/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Quận/Huyện',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Quận/Huyện', name: 'district.index' }
					]
				}
			},
			{
				path: 'district/create',
				name: 'district.create',
				component: page('category/district/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Tạo Quận/Huyện',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Quận/Huyện', name: 'district.index' },
						{ title: 'Tạo Quận/Huyện', name: 'district.create' }
					]
				}
			},
			{
				path: 'district/edit',
				name: 'district.edit',
				component: page('category/district/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa Quận/Huyện',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Quận/Huyện', name: 'district.index' },
						{ title: 'Chỉnh sửa Quận/Huyện', name: 'district.edit' }
					]
				}
			},
			// ward
			{
				path: 'ward',
				name: 'ward.index',
				component: page('category/ward/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Phường/Xã',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Phường/Xã', name: 'ward.index' }
					]
				}
			},
			{
				path: 'ward/create',
				name: 'ward.create',
				component: page('category/ward/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Tạo Phường/Xã',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Phường/Xã', name: 'ward.index' },
						{ title: 'Tạo Phường/Xã', name: 'ward.create' }
					]
				}
			},
			{
				path: 'ward/edit',
				name: 'ward.edit',
				component: page('category/ward/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa Phường/Xã',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Phường/Xã', name: 'ward.index' },
						{ title: 'Chỉnh sửa Phường/Xã', name: 'ward.edit' }
					]
				}
			},
			// branch
			{
				path: 'branch',
				name: 'branch.index',
				component: page('category/branch/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chi nhánh',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Chi nhánh', name: 'branch.index' }
					]
				}
			},
			{
				path: 'branch/create',
				name: 'branch.create',
				component: page('category/branch/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Tạo chi nhánh',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Chi nhánh', name: 'branch.index' },
						{ title: 'Tạo chi nhánh', name: 'branch.create' }
					]
				}
			},
			{
				path: 'branch/edit',
				name: 'branch.edit',
				component: page('category/branch/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa chi nhánh',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Chi nhánh', name: 'branch.index' },
						{ title: 'Chỉnh sửa chi nhánh', name: 'branch.edit' }
					]
				}
			},
			// apartment
			{
				path: 'apartment',
				name: 'apartment.index',
				component: page('category/apartment/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chung cư',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Chung cư', name: 'apartment.index' }
					]
				}
			},
			{
				path: 'apartment/create',
				name: 'apartment.create',
				component: page('category/apartment/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Tạo chung cư',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Chung cư', name: 'apartment.index' },
						{ title: 'Tạo chung cư', name: 'apartment.create' }
					]
				}
			},
			{
				path: 'apartment/edit',
				name: 'apartment.edit',
				component: page('category/apartment/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa chung cư',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Chung cư', name: 'apartment.index' },
						{ title: 'Chỉnh sửa chung cư', name: 'apartment.edit' }
					]
				}
			},
			{
				path: 'dictionary',
				name: 'dictionary.index',
				component: page('category/dictionaries/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Danh mục',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Danh mục', name: 'dictionary.index' }
					]
				}
			},
			{
				path: 'dictionary/create',
				name: 'dictionary.create',
				component: page('category/dictionaries/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Thêm Danh mục',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Danh mục', name: 'dictionary.index' },
						{ title: 'Thêm danh mục', name: 'dictionary.create' }
					]
				}
			},
			{
				path: 'dictionary/edit',
				name: 'dictionary.edit',
				component: page('category/dictionaries/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa danh mục',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Danh mục', name: 'dictionary.index' },
						{ title: 'Chỉnh sửa danh mục', name: 'dictionary.edit' }
					]
				}
			},
			// map
			{
				path: 'map',
				name: 'map.index',
				component: page('map/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Bản đồ',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Bản đồ', name: 'map.index' }
					]
				}
			},
			// building
			{
				path: 'building',
				name: 'building.index',
				component: page('category/building/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Công trình xây dựng',
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Công trình xây dựng', name: 'building.index' }
					]
				}
			},
			{
				path: 'building/create',
				name: 'building.create',
				component: page('category/building/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Thêm Công trình xây dựng',
					permissions: [PERMISSIONS.ADD_CATEGORY],
					breadcrumbs: [
						{ title: 'Công trình xây dựng', name: 'building.index' },
						{ title: 'Thêm công trình xây dựng', name: 'building.create' }
					]
				}
			},
			{
				path: 'building/edit',
				name: 'building.edit',
				component: page('category/building/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Chỉnh sửa công trình xây dựng',
					permissions: [PERMISSIONS.EDIT_CATEGORY],
					breadcrumbs: [
						{ title: 'Công trình xây dựng', name: 'building.index' },
						{ title: 'Chỉnh sửa công trình xây dựng', name: 'building.edit' }
					]
				}
			}
		]
	},

	// Appraise
	{
		path: '/appraise',
		component: Resource,
		children: [
			// element
			{
				path: 'element',
				name: 'element.index',
				component: page('appraise/element/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Yếu tố so sánh',
					// fix_permission
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Yếu tố so sánh', name: 'element.index' }
					]
				}
			},
			{
				path: 'appraisal-construction',
				name: 'appraisal-construction.index',
				component: page('appraise/appraisal_construction/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Khai báo CTXD thẩm định',
					// fix_permission
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Khai báo CTXD thẩm định', name: 'appraisal-construction.index' }
					]
				}
			},
			{
				path: 'appraise-other',
				name: 'appraise-other.index',
				component: page('appraise/appraise_other/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Khai báo thông tin TDK',
					// fix_permission
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Khai báo thông tin TDK', name: 'appraise-other.index' }
					]
				}
			},
			{
				path: 'appraise-law',
				name: 'appraise-law.index',
				component: page('appraise/appraise_law/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Khai báo văn bản pháp luật',
					// fix_permission
					permissions: [PERMISSIONS.VIEW_CATEGORY],
					breadcrumbs: [
						{ title: 'Khai báo văn bản pháp luật', name: 'appraise-law.index' }
					]
				}
			},
			{
				path: 'appraiser-company',
				name: 'appraiser-company.index',
				component: page('appraise/appraiser_company/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Thông tin công ty',
					// fix_permission
					permissions: [PERMISSIONS.VIEW_ROLE],
					breadcrumbs: [
						{ title: 'Thông tin công ty', name: 'appraiser-company.index' }
					]
				}
			},
			{
				path: 'appraiser-company/create',
				name: 'appraiser-company.create',
				component: page('appraise/appraiser_company/Create.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Thêm thông tin công ty',
					// fix_permission
					permissions: [PERMISSIONS.ADD_ROLE],
					breadcrumbs: [
						{ title: 'Thông tin công ty', name: 'appraiser-company.index' },
						{ title: 'Thêm thông tin công ty', name: 'appraiser-company.create' }
					]
				}
			},
			{
				path: 'appraiser-company/detail',
				name: 'appraiser-company.detail',
				component: page('appraise/appraiser_company/Detail.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Thông tin công ty',
					// fix_permission
					permissions: [PERMISSIONS.ADD_ROLE],
					breadcrumbs: [
						{ title: 'Thông tin công ty', name: 'appraiser-company.index' },
						{ title: 'Cập nhật thông tin công ty', name: 'appraiser-company.edit' }
					]
				}
			},
			{
				path: 'appraiser-company/edit',
				name: 'appraiser-company.edit',
				component: page('appraise/appraiser_company/Edit.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Cập nhật thông tin công ty',
					// fix_permission
					permissions: [PERMISSIONS.ADD_ROLE],
					breadcrumbs: [
						{ title: 'Thông tin công ty', name: 'appraiser-company.index' }
						// { title: 'Cập nhật thông tin công ty', name: 'appraiser-company.edit' }
					]
				}
			},
			{
				path: 'document-config',
				name: 'watermark.index',
				component: page('appraise/watermark/Index.vue'),
				beforeEnter: ResolveGuard([AuthGuard]),
				meta: {
					title: 'Thông tin tài liệu',
					// fix_permission
					permissions: [PERMISSIONS.VIEW_ROLE],
					breadcrumbs: [
						{ title: 'Thông tin tài liệu', name: 'watermark.index' }
					]
				}
			}
		]
	},
	{
		path: '/certification_asset',
		component: Resource,
		children: [{
			path: '/certification_asset/real-estate',
			name: 'certification_asset.index',
			component: page('certification_asset/real_estate/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_asset.index' },
					{ title: 'Bất động sản', name: 'certification_asset.index' }
				]
			}
		},
		{
			path: '/certification_asset/personal-property',
			name: 'certification_personal_property.index',
			component: page('certification_asset/personal_property/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tài sản thẩm định',
				// denied: ['trial'],
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' }
				]
			}
		},
		{
			path: '/certification_asset/real-estate/create',
			name: 'certification_asset.create',
			component: page('certification_asset/real_estate/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tạo mới tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.ADD_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_asset.index' },
					{ title: 'Bất động sản', name: 'certification_asset.index' },
					{ title: 'Tạo mới tài sản thẩm định', name: 'certification_asset.create' }
				]
			}
		},
		{
			path: '/certification_asset/real-estate/detail',
			name: 'certification_asset.detail',
			component: page('certification_asset/real_estate/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_asset.index' },
					{ title: 'Bất động sản', name: 'certification_asset.index' },
					{ title: 'Chi tiết tài sản thẩm định', name: 'certification_asset.detail' }
				]
			}
		},
		{
			path: '/certification_asset/real-estate/edit',
			name: 'certification_asset.edit',
			component: page('certification_asset/real_estate/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_asset.index' },
					{ title: 'Bất động sản', name: 'certification_asset.index' },
					{ title: 'Chỉnh sửa tài sản thẩm định', name: 'certification_asset.edit' }
				]
			}
		},
			// ------------------------------------------Apartment ------------------------------------------------------------------
		{
			path: '/certification_asset/apartment/create',
			name: 'certification_asset.apartment.create',
			component: page('certification_asset/real_estate/apartment/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tạo mới tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.ADD_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_asset.index' },
					{ title: 'Bất động sản', name: 'certification_asset.index' },
					{ title: 'Tạo mới tài sản thẩm định', name: 'certification_asset.apartment.create' }
				]
			}
		},
		{
			path: '/certification_asset/apartment/edit',
			name: 'certification_asset.apartment.edit',
			component: page('certification_asset/real_estate/apartment/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_asset.index' },
					{ title: 'Bất động sản', name: 'certification_asset.index' },
					{ title: 'Chỉnh sửa tài sản thẩm định', name: 'certification_asset.apartment.edit' }
				]
			}
		},
		{
			path: '/certification_asset/apartment/detail',
			name: 'certification_asset.apartment.detail',
			component: page('certification_asset/real_estate/apartment/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_asset.index' },
					{ title: 'Bất động sản', name: 'certification_asset.index' },
					{ title: 'Chi tiết tài sản thẩm định', name: 'certification_asset.apartment.detail' }
				]
			}
		},
			// ----------------------------------------Other-Purpose ----------------------------------------------------------------
		{
			path: '/certification_asset/other-purpose/create',
			name: 'certification_asset.other_purpose.create',
			component: page('certification_asset/personal_property/other_purpose_form/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Tạo mới động sản khác', name: 'certification_asset.other_purpose.create' }
				]
			}
		},
		{
			path: '/certification_asset/other-purpose/edit',
			name: 'certification_asset.other_purpose.edit',
			component: page('certification_asset/personal_property/other_purpose_form/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Chỉnh sửa động sản khác', name: 'certification_asset.other_purpose.edit' }
				]
			}
		},
		{
			path: '/certification_asset/other-purpose/detail',
			name: 'certification_asset.other_purpose.detail',
			component: page('certification_asset/personal_property/other_purpose_form/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Chi tiết động sản khác', name: 'certification_asset.other_purpose.detail' }
				]
			}
		},
			// ---------------------------------------- vehicle ----------------------------------------------------------------
		{
			path: '/certification_asset/vehicle/create',
			name: 'certification_asset.vehicle.create',
			component: page('certification_asset/personal_property/vehicle_form/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Tạo mới phương tiện vận tải', name: 'certification_asset.vehicle.create' }
				]
			}
		},
		{
			path: '/certification_asset/vehicle/edit',
			name: 'certification_asset.vehicle.edit',
			component: page('certification_asset/personal_property/vehicle_form/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Chỉnh sửa phương tiện vận tải', name: 'certification_asset.vehicle.edit' }
				]
			}
		},
		{
			path: '/certification_asset/vehicle/detail',
			name: 'certification_asset.vehicle.detail',
			component: page('certification_asset/personal_property/vehicle_form/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Chi tiết phương tiện vận tải', name: 'certification_asset.vehicle.detail' }
				]
			}
		},
			// ---------------------------------------- Machine ----------------------------------------------------------------
		{
			path: '/certification_asset/machine/create',
			name: 'certification_asset.machine.create',
			component: page('certification_asset/personal_property/machine_form/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Thêm mới máy móc thiết bị', name: 'certification_asset.machine.create' }
				]
			}
		},
		{
			path: '/certification_asset/machine/edit',
			name: 'certification_asset.machine.edit',
			component: page('certification_asset/personal_property/machine_form/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Chỉnh sửa máy móc thiết bị', name: 'certification_asset.machine.edit' }
				]
			}
		},
		{
			path: '/certification_asset/machine/detail',
			name: 'certification_asset.machine.detail',
			component: page('certification_asset/personal_property/machine_form/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết tài sản thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_ASSET],
				breadcrumbs: [
					{ title: 'Tài sản thẩm định', name: 'certification_personal_property.index' },
					{ title: 'Động sản', name: 'certification_personal_property.index' },
					{ title: 'Chi tiết máy móc thiết bị', name: 'certification_asset.machine.edit' }
				]
			}
		}
		]
	},

	// certificate
	// {
	// 	path: '/certificate',
	// 	component: Resource,
	// 	children: [{
	// 		path: '',
	// 		name: 'certificate.index',
	// 		component: page('certificate/Index.vue'),
	// 		beforeEnter: ResolveGuard([AuthGuard]),
	// 		meta: {
	// 			title: 'Tài sản thẩm định',
	// 			// fix_permission
	// 			permissions: [PERMISSIONS.VIEW_PRICE],
	// 			breadcrumbs: [
	// 				{ title: 'Tài sản thẩm định', name: 'certificate.index' }
	// 			]
	// 		}
	// 	},
	// 	{
	// 		path: '/certificate/create',
	// 		name: 'certificate.create',
	// 		component: page('certificate/Create.vue'),
	// 		beforeEnter: ResolveGuard([AuthGuard]),
	// 		meta: {
	// 			title: 'Tạo mới tài sản thẩm định',
	// 			// fix_permission
	// 			permissions: [PERMISSIONS.ADD_PRICE],
	// 			breadcrumbs: [
	// 				{ title: 'Tài sản thẩm định', name: 'certificate.index' },
	// 				{ title: 'Tạo mới tài sản thẩm định', name: 'certificate.create' }
	// 			]
	// 		}
	// 	},
	// 	{
	// 		path: '/certificate/detail',
	// 		name: 'certificate.detail',
	// 		component: page('certificate/Detail.vue'),
	// 		beforeEnter: ResolveGuard([AuthGuard]),
	// 		meta: {
	// 			title: 'Chi tiết tài sản thẩm định',
	// 			// fix_permission
	// 			permissions: [PERMISSIONS.VIEW_PRICE],
	// 			breadcrumbs: [
	// 				{ title: 'Tài sản thẩm định', name: 'certificate.index' },
	// 				{ title: 'Chi tiết tài sản thẩm định', name: 'certificate.detail' }
	// 			]
	// 		}
	// 	},
	// 	{
	// 		path: '/certificate/edit',
	// 		name: 'certificate.edit',
	// 		component: page('certificate/Edit.vue'),
	// 		beforeEnter: ResolveGuard([AuthGuard]),
	// 		meta: {
	// 			title: 'Chỉnh sửa tài sản thẩm định',
	// 			// fix_permission
	// 			permissions: [PERMISSIONS.EDIT_PRICE],
	// 			breadcrumbs: [
	// 				{ title: 'Tài sản thẩm định', name: 'certificate.index' },
	// 				{ title: 'Chỉnh sửa tài sản thẩm định', name: 'certificate.edit' }
	// 			]
	// 		}
	// 	}
	// 	]
	// },
	// appraisal
	{
		path: '/appraisal',
		component: Resource,
		children: [{
			path: '',
			name: 'appraisal.index',
			component: page('appraisal/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_PRICE],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'appraisal.index' }
				]
			}
		},
		{
			path: '/appraisal/create',
			name: 'appraisal.create',
			component: page('appraisal/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tạo mới hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.ADD_PRICE],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'appraisal.index' },
					{ title: 'Tạo mới hồ sơ thẩm định', name: 'appraisal.create' }
				]
			}
		},
		{
			path: '/appraisal/detail',
			name: 'appraisal.detail',
			component: page('appraisal/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_PRICE],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'appraisal.index' },
					{ title: 'Chi tiết hồ sơ thẩm định', name: 'appraisal.detail' }
				]
			}
		},
		{
			path: '/appraisal/edit',
			name: 'appraisal.edit',
			component: page('appraisal/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_PRICE],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'appraisal.index' },
					{ title: 'Chỉnh sửa hồ sơ thẩm định', name: 'appraisal.edit' }
				]
			}
		}
		]
	},
	// certification_brief
	{
		path: '/certification_brief',
		component: Resource,
		children: [{
			path: '',
			name: 'certification_brief.index',
			component: page('certification_brief/Index2.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_BRIEF],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'certification_brief.index' }
				]
			}
		},
		{
			path: '/certification_brief/create',
			name: 'certification_brief.create',
			component: page('certification_brief/Create.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Tạo mới hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.ADD_CERTIFICATE_BRIEF],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'certification_brief.index' },
					{ title: 'Tạo mới hồ sơ thẩm định', name: 'certification_brief.create' }
				]
			}
		},
		{
			path: '/certification_brief/detail',
			name: 'certification_brief.detail',
			component: page('certification_brief/Detail.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chi tiết hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.VIEW_CERTIFICATE_BRIEF],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'certification_brief.index' },
					{ title: 'Chi tiết hồ sơ thẩm định', name: 'certification_brief.detail' }
				]
			}
		},
		{
			path: '/certification_brief/edit',
			name: 'certification_brief.edit',
			component: page('certification_brief/Edit.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Chỉnh sửa hồ sơ thẩm định',
				// fix_permission
				permissions: [PERMISSIONS.EDIT_CERTIFICATE_BRIEF],
				breadcrumbs: [
					{ title: 'Hồ sơ thẩm định', name: 'certification_brief.index' },
					{ title: 'Chỉnh sửa hồ sơ thẩm định', name: 'certification_brief.edit' }
				]
			}
		}
		]
	},
	// dashboard
	{
		path: '/dashboard',
		component: Resource,
		children: [{
			path: '',
			name: 'dashboard.index',
			component: page('dashboard/Index.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Bảng thông tin tổng hợp',
				permissions: [PERMISSIONS.VIEW_DASHBOARD],
				breadcrumbs: [
					{ title: 'Bảng thông tin tổng hợp', name: 'dashboard.index' }
				]
			}
		}]
	},
	// Error
	{
		path: '/error',
		component: Resource,
		children: [{
			path: '/403',
			name: 'error.403',
			component: page('error/Error403.vue'),
			meta: {
				title: '',
				// permissions: PERMISSIONS.ALL,
				breadcrumbs: [
					{ title: 'error', name: 'error.403' }
				]
			}
		},
		{
			path: '/404',
			name: 'error.404',
			component: page('error/Error404.vue'),
			meta: {
				title: '',
				// permissions: PERMISSIONS.ALL,
				breadcrumbs: [
					{ title: 'error', name: 'error.404' }
				]
			}
		},
		{
			path: '/409',
			name: 'error.409',
			component: page('error/Error409.vue'),
			meta: {
				title: '',
				// permissions: PERMISSIONS.ALL,
				breadcrumbs: [
					{ title: 'error', name: 'error.409' }
				]
			}
		},
		{
			path: '/429',
			name: 'error.429',
			component: page('error/Error429.vue'),
			meta: {
				title: '',
				// permissions: PERMISSIONS.ALL,
				breadcrumbs: [
					{ title: 'error', name: 'error.429' }
				]
			}
		},
		{
			path: '/500',
			name: 'error.500',
			component: page('error/Error500.vue'),
			meta: {
				title: '',
				// permissions: PERMISSIONS.ALL,
				breadcrumbs: [
					{ title: 'error', name: 'error.500' }
				]
			}
		},
		{
			path: '/503',
			name: 'error.503',
			component: page('error/Error503.vue'),
			meta: {
				title: '',
				// permissions: PERMISSIONS.ALL,
				breadcrumbs: [
					{ title: 'error', name: 'error.503' }
				]
			}
		}
		]
	},

	// Page not found
	{
		path: '*',
		component: page('error/Error404.vue')
	},

	{
		path: '/',
		component: Resource,
		children: [{
			path: '',
			name: 'page-not-found',
			component: page('PageNotFound.vue'),
			beforeEnter: ResolveGuard([AuthGuard]),
			meta: {
				title: 'Không tìm thấy trang',
				permissions: [PERMISSIONS.VIEW_DASHBOARD],
				breadcrumbs: [
					//   { title: 'Bất động sản' }
				]
			}
		}]
	}
]
