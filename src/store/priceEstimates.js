// @ts-nocheck
import { defineStore } from "pinia";
import WareHouse from "@/models/WareHouse";
import PriceEstimateModel from "@/models/PriceEstimates";
import PreCertificate from "@/models/PreCertificate";

import moment from "moment";
import { ref } from "vue";
import _ from "lodash";
export const usePriceEstimatesStore = defineStore(
	"priceEstimatesStore",
	() => {
		const configs = ref({ hstdConfig: null, ycsbConfig: null });
		const configThis = ref({
			toast: null,
			route: null,
			router: null,
			isMobile: false,
			wizard: null,
			step_1: null
		});
		const miscVariable = ref({
			isApartment: false,
			step1AreaValidate: null,
			step_edit: "",
			step_active: null,
			showConfirmEdit: false,
			messageConfirm: "",
			filterYear: null,

			full_address: null,
			full_address_street: null,
			filter_year: 1,
			distance_max: null,
			isHaveContruction: null
		});
		const miscInfo = ref({
			key_step_1: 0,
			key_step_2: 0,
			key_step_3: 0,
			current_create_by: null,
			points: [],
			blocks: [],
			floors: [],
			projects: [],
			directions: [],
			propertyTypes: [],
			topographic: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			distances: [],
			materials: [],
			imageDescriptions: [],
			fengshuies: [],
			conditions: [],
			zones: [],
			businesses: [],
			socialSecurities: [],
			appraiser_purposes: [],
			optionMainChoose: [
				{
					id: 1,
					description: "Chính"
				},
				{
					id: 0,
					description: "Phụ"
				}
			]
		});

		const addressName = ref({
			province: null,
			district: null,
			ward: null,
			street: null,
			distance: null
		});
		const dataInfo = ref({
			provinceName: null,
			districtName: null,
			wardName: null,
			radius: null,
			radius: null,
			distance: null,
			assetName: null,
			isHaveContruction: null
		});

		const isSubmit = ref(false);
		const priceEstimatesOrigin = ref(null);
		const priceEstimates = ref({
			step_1: {
				asset_type_id: null,
				general_infomation: {
					province_id: null,
					district_id: null,
					ward_id: null,
					street_id: null,
					distance_id: null,
					appraise_asset: null,
					full_address: null
				},
				apartment_properties: {},
				traffic_infomation: {
					description: null,
					property_turning_time: []
				},
				economic_infomation: {},
				land_details: {},
				total_area: [],
				planning_area: []
			},
			step_2: { assets_general: [], filter_year: 1, map_img: null },
			step_3: {
				petitioner_name: "",
				request_date: "",
				appraise_purpose_id: "",
				asset_type_id: "",
				appraise_asset: "",
				full_address: "",
				description: "",
				coordinates: "",
				total_area: [],
				planning_area: [],
				tangible_assets: [],
				appraise_land_sum_area: 0
			}
		});

		function formatCapitalize(word) {
			if (word)
				return word.replace(/(?:^|\s|[-"'([{])+\S/g, function (x) {
					return x.toUpperCase();
				});
			else return word;
		}
		function formatSentenceCase(phrase) {
			if (!phrase) return phrase;
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		}
		function toTitleCase(str) {
			if (!str) return str;
			return str.replace(/\w\S*/g, function (txt) {
				return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
			});
		}
		async function getDictionary() {
			await WareHouse.getDictionaries()
				.then(resp => {
					miscInfo.value.landType = resp.data.loai_dat;
					miscInfo.value.topographic = resp.data.dia_hinh;
					miscInfo.value.landShapes = resp.data.hinh_dang_dat;
					miscInfo.value.socialSecurities = resp.data.an_ninh_moi_truong_song;
					miscInfo.value.businesses = resp.data.kinh_doanh;
					miscInfo.value.paymentMethods = resp.data.dieu_kien_thanh_toan;
					miscInfo.value.conditions = resp.data.dieu_kien_ha_tang;
					miscInfo.value.fengshuies = resp.data.phong_thuy;
					miscInfo.value.zones = resp.data.quy_hoach_hien_trang;
					miscInfo.value.materials = resp.data.giao_thong_chat_lieu;
					miscInfo.value.roughes = resp.data.giao_thong;
					miscInfo.value.points = resp.data.vi_tri_dat;
					miscInfo.value.imageDescriptions = resp.data.mo_ta_hinh_anh;
					// data for step 3
					miscInfo.value.buildingCategories = resp.data.cap_nha;
					miscInfo.value.housingTypes = resp.data.loai_nha;
					miscInfo.value.buildingRates = resp.data.hang_nha;
					miscInfo.value.buildingStructure = resp.data.cau_truc_biet_thu;
					miscInfo.value.buildingAperture = resp.data.khau_do;
					miscInfo.value.buildingFactoryType = resp.data.loai_nha_may;
					miscInfo.value.propertyTypes = resp.data.loai_tai_san.filter(
						item => item.dictionary_acronym === "BDS" && item.acronym !== "CC"
					);
					miscInfo.value.propertyTypes.forEach(item => {
						item.description = formatSentenceCase(item.description);
					});
					miscInfo.value.housingTypes.forEach(item => {
						item.description = formatSentenceCase(item.description);
					});
					miscInfo.value.materials.forEach(item => {
						item.description = formatSentenceCase(item.description);
					});
					miscInfo.value.landShapes.forEach(item => {
						item.description = formatSentenceCase(item.description);
					});
					miscInfo.value.type_purposes = resp.data.loai_dat_chi_tiet.filter(
						i => i.status === 1
					);
					miscInfo.value.type_purposes.sort((a, b) => a.id - b.id);
					miscInfo.value.type_purposes.forEach(item => {
						item.description = formatSentenceCase(item.description);
					});
					miscInfo.value.furniture_list = resp.data.chat_luong_noi_that;
					miscInfo.value.furniture_list.forEach(item => {
						item.description = formatSentenceCase(item.description);
					});
					miscInfo.value.directions = resp.data.huong_can_ho;
					miscInfo.value.buildingCrane = resp.data.cau_truc_nha_xuong;
					miscInfo.value.key_step_1 += 1;
				})
				.catch(err => {
					isSubmit.value = false;
					throw err;
				});

			await WareHouse.getProvince()
				.then(resp => {
					miscInfo.value.provinces = resp.data;
				})
				.catch(err => {
					isSubmit.value = false;
					throw err;
				});

			const resp2 = await PreCertificate.getAppraiseOthers();
			miscInfo.value.appraiser_purposes = [
				...resp2.data.muc_dich_tham_dinh_gia
			];
		}
		function changeProvince(id) {
			priceEstimates.value.step_1.general_infomation.district_id = "";
			priceEstimates.value.step_1.general_infomation.ward_id = "";
			priceEstimates.value.step_1.general_infomation.street_id = "";
			priceEstimates.value.step_1.general_infomation.distance_id = "";
			miscInfo.value.districts = [];
			miscInfo.value.wards = [];
			miscInfo.value.streets = [];
			miscInfo.value.distances = [];
			if (priceEstimates.value.step_1.general_infomation.province_id !== 0) {
				getDistrictsByProvinceId(id);
			}
			findProvince();
		}
		async function getDistrictsByProvinceId(id) {
			await WareHouse.getDistrictsByProvinceId(id)
				.then(resp => {
					miscInfo.value.districts = resp.data;
					if (priceEstimates.value.step_1.general_infomation.district_id) {
						if (miscVariable.value.isApartment) {
							getProjectsByDistrictId(
								priceEstimates.value.step_1.general_infomation.district_id
							);
						}
						getWardsByDistrictId(
							priceEstimates.value.step_1.general_infomation.district_id
						);
						getStreetByDistrictId(
							priceEstimates.value.step_1.general_infomation.district_id
						);
					}
				})
				.catch(err => {
					isSubmit.value = false;
					throw err;
				});
		}
		function getWardsByDistrictId(id) {
			let wards = miscInfo.value.districts.filter(item => item.id === id);
			miscInfo.value.wards = wards[0].wards;
			miscInfo.value.wards.forEach(item => {
				item.name = formatCapitalize(item.name);
			});
		}
		function getStreetByDistrictId(id) {
			let streets = miscInfo.value.districts.filter(item => item.id === id);
			miscInfo.value.streets = streets[0].streets;
			miscInfo.value.streets.forEach(item => {
				item.name = toTitleCase(item.name);
			});
			if (
				priceEstimates.value.step_1.general_infomation.street_id !== "" &&
				priceEstimates.value.step_1.general_infomation.street_id !==
				undefined &&
				priceEstimates.value.step_1.general_infomation.street_id !== null
			) {
				getDistanceByStreetId(
					priceEstimates.value.step_1.general_infomation.street_id
				);
			}
		}
		function getDistanceByStreetId(id) {
			let distances = miscInfo.value.streets.filter(item => item.id === id);
			miscInfo.value.distances = distances[0].distances;
		}
		function findProvince() {
			const province = miscInfo.value.provinces.find(
				province =>
					province.id ===
					priceEstimates.value.step_1.general_infomation.province_id
			);
			if (province) {
				dataInfo.value.provinceName = province.name;
			} else {
				dataInfo.value.provinceName = null;
			}
			findDistrict();
			getFullAddress();
		}
		function getFullAddress() {
			if (miscVariable.value.isApartment) {
				return;
			}
			miscInfo.value.full_address =
				`${priceEstimates.value.step_1.general_infomation.land_no
					? "Thửa đất số " +
					priceEstimates.value.step_1.general_infomation.land_no +
					", "
					: ""
				}` +
				`${priceEstimates.value.step_1.general_infomation.doc_no
					? "Tờ bản đồ số " +
					priceEstimates.value.step_1.general_infomation.doc_no +
					", "
					: ""
				}` +
				`${priceEstimates.value.step_1.general_infomation.address_number
					? "Số " +
					priceEstimates.value.step_1.general_infomation.address_number +
					", "
					: ""
				}` +
				`${dataInfo.value.streetName ? dataInfo.value.streetName + ", " : ""}` +
				`${dataInfo.value.wardName ? dataInfo.value.wardName + ", " : ""}` +
				`${dataInfo.value.districtName ? dataInfo.value.districtName + ", " : ""
				}` +
				`${dataInfo.value.provinceName
					? dataInfo.value.provinceName.includes("Thành phố")
						? dataInfo.value.provinceName
						: "Tỉnh " + dataInfo.value.provinceName.trim()
					: ""
				}`;
			miscInfo.value.full_address_street =
				`${priceEstimates.value.step_1.general_infomation.land_no
					? "Thửa đất số " +
					priceEstimates.value.step_1.general_infomation.land_no +
					", "
					: ""
				}` +
				`${priceEstimates.value.step_1.general_infomation.doc_no
					? "Tờ bản đồ số " +
					priceEstimates.value.step_1.general_infomation.doc_no +
					", "
					: ""
				}` +
				`${priceEstimates.value.step_1.general_infomation.address_number
					? "Số " +
					priceEstimates.value.step_1.general_infomation.address_number +
					", "
					: ""
				}` +
				`${dataInfo.value.streetName ? dataInfo.value.streetName + ", " : ""}` +
				`${dataInfo.value.wardName ? dataInfo.value.wardName + ", " : ""}` +
				`${dataInfo.value.districtName ? dataInfo.value.districtName + ", " : ""
				}` +
				`${dataInfo.value.provinceName ? dataInfo.value.provinceName : ""}`;
			priceEstimates.value.step_1.general_infomation.full_address =
				miscInfo.value.full_address;
			priceEstimates.value.step_1.general_infomation.full_address_street =
				miscInfo.value.full_address_street;
			getInfo();
		}

		function findDistrict() {
			const district = miscInfo.value.districts.find(
				district =>
					district.id ===
					priceEstimates.value.step_1.general_infomation.district_id
			);
			if (district) {
				dataInfo.value.districtName = district.name;
			} else {
				dataInfo.value.districtName = null;
			}
			if (
				dataInfo.value.districtName &&
				(dataInfo.value.districtName.toLowerCase() === "thành phố biên hòa" ||
					dataInfo.value.districtName.toLowerCase() === "thành phố long khánh")
			) {
				dataInfo.value.radius = 1;
				dataInfo.value.distance = 1000;
			} else {
				dataInfo.value.radius = 2;
				dataInfo.value.distance = 2000;
			}
			findWard();
			findStreet();
			getFullAddress();
		}
		function findWard() {
			priceEstimates.value.step_1.general_infomation.ward_id =
				priceEstimates.value.step_1.general_infomation.ward_id;
			const ward = miscInfo.value.wards.find(
				ward =>
					ward.id === priceEstimates.value.step_1.general_infomation.ward_id
			);
			if (ward) {
				dataInfo.value.wardName = ward.name;
			} else {
				dataInfo.value.wardName = null;
			}
			getFullAddress();
		}
		function findStreet() {
			const street = miscInfo.value.streets.find(
				street =>
					street.id === priceEstimates.value.step_1.general_infomation.street_id
			);
			if (street) {
				dataInfo.value.streetName = street.name;
			} else {
				dataInfo.value.streetName = null;
			}
			findDistance();
			getFullAddress();
		}

		async function getProjectsByDistrictId(id) {
			await WareHouse.getProjectsByDistrictId(id)
				.then(resp => {
					miscInfo.value.projects = resp.data;
				})
				.catch(err => {
					throw err;
				})
				.finally(() => {
					getProjects();
				});
		}
		async function handleChangeProject(projectId) {
			miscInfo.value.blocks = [];
			miscInfo.value.floors = [];
			priceEstimates.value.step_1.apartment_properties.block_id = "";
			priceEstimates.value.step_1.apartment_properties.floor_id = "";
			if (projectId) {
				let project = miscInfo.value.projects.filter(
					item => item.id === projectId
				);
				priceEstimates.value.step_1.general_infomation.coordinates =
					project[0].coordinates;
				if (project[0].utilities) {
					priceEstimates.value.step_1.apartment_properties.utilities =
						project[0].utilities;
				}
				priceEstimates.value.step_1.general_infomation.province_id =
					project[0].province_id;
				priceEstimates.value.step_1.general_infomation.district_id =
					project[0].district_id;
				priceEstimates.value.step_1.general_infomation.ward_id =
					project[0].ward_id;
				priceEstimates.value.step_1.general_infomation.street_id =
					project[0].street_id;
				priceEstimates.value.step_1.general_infomation.distance_id =
					project[0].distance_id;
				priceEstimates.value.step_1.general_infomation.apartment_number =
					project[0].apartment_number;
				priceEstimates.value.step_1.general_infomation.position_by_unbd_id =
					project[0].position_by_unbd_id;

				getProvinces();
				let provinceName = "";
				let districtName = "";
				let wardName = "";
				let streetName = "";
				const nameAparment = project[0].apartment_number
					? project[0].apartment_number + ", "
					: "";
				if (project[0].province) {
					provinceName = project[0].province.name;
				}
				if (project[0].district) {
					districtName = project[0].district.name + ", ";
				}
				if (project[0].ward) {
					wardName = project[0].ward.name + ", ";
				}
				if (project[0].street) {
					streetName =
						project[0].street.name
							.toLowerCase()
							.replace(/(^|\s)\S/g, function (l) {
								return l.toUpperCase();
							}) + ", ";
				}
				if (project[0].address) {
					priceEstimates.value.step_1.general_infomation.full_address =
						project[0].address;
				} else {
					priceEstimates.value.step_1.general_infomation.full_address =
						nameAparment + streetName + wardName + districtName + provinceName;
				}
				priceEstimates.value.step_1.general_infomation.full_address_street =
					priceEstimates.value.step_1.general_infomation.full_address;
				getBlocks(+projectId);
				miscInfo.value.key_step_1 += 1;
			}
		}
		function handleChangeBlock(blockId) {
			miscInfo.value.floors = [];
			priceEstimates.value.step_1.apartment_properties.floor_id = "";
			// this.form.step_1.apartment_properties.apartment_id = ''
			if (blockId) {
				let block = miscInfo.value.blocks.filter(item => item.id === blockId);
				priceEstimates.value.step_1.apartment_properties.handover_year =
					block[0].handover_year;
				getFloors(blockId);
			}
		}

		async function getProjects() {
			if (priceEstimates.value.step_1.general_infomation.project_id) {
				getBlocks(priceEstimates.value.step_1.general_infomation.project_id);
			}
		}
		function getBlocks(id) {
			let project = miscInfo.value.projects.filter(item => item.id === id);
			if (project && project.length > 0) {
				miscInfo.value.blocks = project[0].block;
			}
			if (priceEstimates.value.step_1.apartment_properties.block_id) {
				getFloors(priceEstimates.value.step_1.apartment_properties.block_id);
			}
		}
		function getFloors(id) {
			let block = miscInfo.value.blocks.filter(item => item.id === id);
			if (block && block.length > 0) {
				miscInfo.value.floors = block[0].floor;
			}
		}
		function findDistance() {
			const distance = miscInfo.value.distances.find(
				distances =>
					distances.id ===
					priceEstimates.value.step_1.general_infomation.distance_id
			);
			if (distance) {
				dataInfo.value.distance = distance.name;
			} else {
				dataInfo.value.distance = null;
			}
		}
		function changeAssetType(id) {
			priceEstimates.value.step_1.general_infomation.asset_type_id = id;
			const assetType = miscInfo.value.propertyTypes.find(
				assetType =>
					assetType.id ===
					priceEstimates.value.step_1.general_infomation.asset_type_id
			);
			if (assetType) {
				dataInfo.value.assetName = assetType.description;
			} else {
				dataInfo.value.assetName = null;
			}
			if (id === 38) {
				miscVariable.value.isHaveContruction = true;
			} else {
				miscVariable.value.isHaveContruction = false;
			}
			getInfo(false);
		}
		function getInfo(shouldChangeAssetType = true) {
			if (
				shouldChangeAssetType &&
				!dataInfo.value.assetName &&
				priceEstimates.value.step_1.general_infomation.asset_type_id !== 39
			) {
				changeAssetType(
					priceEstimates.value.step_1.general_infomation.asset_type_id
				);
			}
			if (
				dataInfo.value.assetName === "Đất trống" &&
				miscInfo.value.full_address
			) {
				priceEstimates.value.step_1.general_infomation.appraise_asset = `Quyền sử dụng đất tại ${miscInfo.value.full_address}`;
			} else if (
				dataInfo.value.assetName === "Đất có nhà" &&
				miscInfo.value.full_address
			) {
				priceEstimates.value.step_1.general_infomation.appraise_asset = `Quyền sử dụng đất và CTXD tại ${miscInfo.value.full_address}`;
			}
			// let streetName = dataInfo.value.streetName
			// 	? dataInfo.value.streetName.toLowerCase()
			// 	: "";
			// let fullAddress =
			// 	`${
			// 		streetName.length > 0
			// 			? (streetName.substring(0, 5) === "đường" ? "" : "Đường ") +
			// 			  formatCapitalize(streetName) +
			// 			  ", "
			// 			: ""
			// 	}` + miscInfo.value.full_address;
			// let fullAddressStreet =
			// 	(priceEstimates.value.step_1.general_infomation.doc_no
			// 		? "Tờ " + step1.general_infomation.doc_no + ", "
			// 		: "") +
			// 	(priceEstimates.value.step_1.general_infomation.land_no
			// 		? "Thửa " + step1.general_infomation.land_no + ", "
			// 		: "") +
			// 	fullAddress;
			// priceEstimates.value.step_1.general_infomation.full_address = fullAddress;
			// priceEstimates.value.step_1.general_infomation.full_address_street = fullAddressStreet;
		}

		function changeAssetTypeFinal(id) {
			const assetType = miscInfo.value.propertyTypes.find(
				assetType => assetType.id === priceEstimates.value.step_3.asset_type_id
			);

			if (assetType) {
				dataInfo.value.finalAssetName = assetType.description;
			} else {
				dataInfo.value.finalAssetName = null;
			}
			if (id === 38) {
				miscVariable.value.isHaveContruction = true;
			} else {
				miscVariable.value.isHaveContruction = false;
				priceEstimates.value.step_3.tangible_assets = [];
			}
			getInfoFinal();
		}
		function getInfoFinal() {
			if (
				dataInfo.value.finalAssetName === "Đất trống" &&
				miscInfo.value.full_address
			) {
				priceEstimates.value.step_1.general_infomation.appraise_asset = `Quyền sử dụng đất tại ${miscInfo.value.full_address}`;
			} else if (
				dataInfo.value.finalAssetName === "Đất có nhà" &&
				miscInfo.value.full_address
			) {
				priceEstimates.value.step_3.appraise_asset = `Quyền sử dụng đất và CTXD tại ${miscInfo.value.full_address}`;
			}
			let streetName = dataInfo.value.streetName
				? dataInfo.value.streetName.toLowerCase()
				: "";
			let fullAddress =
				(priceEstimates.value.step_1.general_infomation.doc_no
					? "Tờ " + step1.general_infomation.doc_no + ", "
					: "") +
				(priceEstimates.value.step_1.general_infomation.land_no
					? "Thửa " + step1.general_infomation.land_no + ", "
					: "") +
				`${streetName.length > 0
					? (streetName.substring(0, 5) === "đường" ? "" : "Đường ") +
					formatCapitalize(streetName) +
					", "
					: ""
				}` +
				miscInfo.value.full_address;
			priceEstimates.value.step_3.full_address = fullAddress;
		}

		function getProvinces() {
			if (
				priceEstimates &&
				priceEstimates.value &&
				priceEstimates.value.step_1 &&
				priceEstimates.value.step_1.general_infomation &&
				priceEstimates.value.step_1.general_infomation.province_id
			) {
				getDistrictsByProvinceId(
					priceEstimates.value.step_1.general_infomation.province_id
				);
				if (
					configThis.value.route.name === "price_estimates.edit" ||
					("id" in configThis.value.route.params &&
						configThis.value.route.name === "price_estimates.create")
				) {
					getAddressEdit();
				}
			}
		}
		function getAddressEdit() {
			const province = priceEstimates.value.step_1.general_infomation.province;
			const district = priceEstimates.value.step_1.general_infomation.district;
			const ward = priceEstimates.value.step_1.general_infomation.ward;
			const street = priceEstimates.value.step_1.general_infomation.street;
			const distance = priceEstimates.value.step_1.general_infomation.distance;
			if (province) {
				dataInfo.value.provinceName = province.name;
				addressName.value.province = province.name;
			} else {
				dataInfo.value.provinceName = null;
				addressName.value.province = null;
			}
			if (district) {
				dataInfo.value.districtName = district.name;
				addressName.value.district = district.name;
			} else {
				dataInfo.value.districtName = null;
				addressName.value.district = null;
			}
			if (
				dataInfo.value.districtName &&
				(dataInfo.value.districtName.toLowerCase() === "thành phố biên hòa" ||
					dataInfo.value.districtName.toLowerCase() === "thành phố long khánh")
			) {
				dataInfo.value.radius = 1;
				dataInfo.value.distance = 1000;
			} else {
				dataInfo.value.radius = 2;
				dataInfo.value.distance = 2000;
			}
			if (ward) {
				dataInfo.value.wardName = ward.name;
				addressName.value.ward = ward.name;
			} else {
				dataInfo.value.wardName = null;
				addressName.value.ward = null;
			}
			if (street) {
				dataInfo.value.streetName = street.name;
				addressName.value.street = street.name;
			} else {
				dataInfo.value.streetName = null;
				addressName.value.street = null;
			}
			if (distance) {
				addressName.value.distance = distance.name;
			} else {
				addressName.value.distance = null;
			}
			getFullAddress();
		}
		function changeStreet(id) {
			dataInfo.value.distances = [];
			priceEstimates.value.step_1.general_infomation.distance_id = "";
			if (priceEstimates.value.step_1.general_infomation.street_id) {
				getDistanceByStreetId(id);
			}
			findStreet();
		}
		function changeDistrict(id) {
			dataInfo.value.wards = [];
			dataInfo.value.streets = [];
			dataInfo.value.distances = [];
			priceEstimates.value.step_1.general_infomation.ward_id = "";
			priceEstimates.value.step_1.general_infomation.street_id = "";
			priceEstimates.value.step_1.general_infomation.distance_id = "";
			if (priceEstimates.value.step_1.general_infomation.district_id) {
				getProjectsByDistrictId(id);
				getWardsByDistrictId(id);
				getStreetByDistrictId(id);
			}
			findDistrict();
		}
		function choosingAsset(assets) {
			priceEstimates.value.step_2.assets_general = assets;
		}
		async function handleSubmitStep_1(dataStep1) {
			if (isSubmit.value == true) {
				configThis.value.toast.open({
					message: "Hệ thống đang xử lý, vui lòng đợi trong giây lát.",
					type: "warning",
					position: "top-right"
				});
				return;
			} else {
				isSubmit.value = true;
			}
			let id = priceEstimates.value.id ? priceEstimates.value.id : "";
			const res = await PriceEstimateModel.submitStep1(
				dataStep1,
				id,
				miscVariable.value.isApartment
			);
			if (res.data) {
				isSubmit.value = false;
				priceEstimates.value.id = res.data.general_infomation.id;
				priceEstimates.value.step_1.land_details.coordinates =
					res.data.general_infomation.coordinates;
				configThis.value.toast.open({
					message: "Lưu thông tin chung thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				if (!configThis.value.isMobile) {
					configThis.value.wizard.maxStep = 1;
					configThis.value.wizard.tabs.forEach((tab, index) => {
						if (index > 1) {
							tab.checked = false;
						}
					});
					miscInfo.value.key_step_2 += 1;
					await configThis.value.wizard.nextTab();
					priceEstimates.value.status_text = "Mới";
				} else {
					await configThis.value.router
						.push({ name: "price_estimates.index" })
						.catch(_ => { });
				}
			} else if (res.error) {
				isSubmit.value = false;
				configThis.value.toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				isSubmit.value = false;
				configThis.value.toast.open({
					message: "Lưu thất bại",
					type: "error",
					position: "top-right"
				});
			}
		}
		async function validateSubmitStep1() {
			if (miscVariable.value.isApartment) {
				validateApartmentSubmitStep1();
				return;
			}
			const isValid = await configThis.value.step_1.validate();

			let step_1 = priceEstimates.value.step_1;
			let checkValidPurpose = false;
			step_1.total_area.forEach(item => {
				if (!item.land_type_purpose_id) {
					checkValidPurpose = true;
				}
			});
			step_1.planning_area.forEach(item => {
				if (!item.land_type_purpose_id) {
					checkValidPurpose = true;
				}
			});

			if (isValid) {
				if (step_1.total_area.length === 0) {
					configThis.value.toast.open({
						message: "Vui lòng nhập diện tích theo mục đích sử dụng",
						type: "error",
						position: "top-right"
					});
				} else if (checkValidPurpose) {
					configThis.value.toast.open({
						message: "Vui lòng nhập mục đích sử dụng",
						type: "error",
						position: "top-right"
					});
				} else if (miscVariable.value.step1AreaValidate) {
					configThis.value.toast.open({
						message: miscVariable.value.step1AreaValidate,
						type: "error",
						position: "top-right"
					});
				} else {
					confirmSavePreviousStep(1);
				}
			} else {
				configThis.value.toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		}
		async function validateApartmentSubmitStep1() {
			const isValid = await configThis.value.step_1.validate();

			if (isValid) {
				confirmSavePreviousStep(1);
			} else {
				configThis.value.toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		}
		function confirmSavePreviousStep(step_edit) {
			miscVariable.value.step_edit = step_edit;
			if (miscVariable.value.step_active > step_edit) {
				miscVariable.value.showConfirmEdit = true;
				miscVariable.value.messageConfirm = `Dữ liệu ở sau bước ${step_edit} sẽ phải xác nhận lại. Bạn vẫn muốn tiếp tục ?`;
			} else {
				confirmEditStep();
			}
		}
		function confirmEditStep() {
			let step = miscVariable.value.step_edit;
			if (step === 1) {
				handleSubmitStep_1(
					priceEstimates.value.step_1,
					priceEstimates.value.id
				);
			} else if (step === 2) {
				handleSubmitStep_2(
					priceEstimates.value.step_2,
					priceEstimates.value.id
				);
			} else if (step === 3) {
				handleSubmitStep_3(
					priceEstimates.value.step_3,
					priceEstimates.value.step_3.id
				);
			}
			if (step < 3) {
				// Remove the automated selection of comparison asset.
				// this.isAutomation = true
			}

			miscVariable.value.step_active = step;
			miscVariable.value.showConfirmEdit = false;
		}
		async function validateSubmitStep2() {
			let step_2 = priceEstimates.value.step_2;
			// if (step_2.assets_general.length === 0) {
			// 	configThis.value.toast.open({
			// 		message: "Vui lòng chọn tài sản so sánh",
			// 		type: "error",
			// 		position: "top-right"
			// 	});
			// }
			if (step_2.assets_general.length > 3) {
				configThis.value.toast.open({
					message: "Chỉ được chọn tối đa 3 tài sản so sánh",
					type: "error",
					position: "top-right"
				});
			}
			// else if (step_2.assets_general.length < 1) {
			// 	configThis.value.toast.open({
			// 		message: "Vui lòng chọn ít nhất 1 tài sản so sánh",
			// 		type: "error",
			// 		position: "top-right"
			// 	});
			// }
			else {
				confirmSavePreviousStep(2);
			}
		}
		async function handleSubmitStep_2(dataStep2, id) {
			// console.log("dataStep2", dataStep2);
			// return;

			if (isSubmit.value == true) {
				configThis.value.toast.open({
					message: "Hệ thống đang xử lý, vui lòng đợi trong giây lát.",
					type: "warning",
					position: "top-right"
				});
				return;
			} else {
				isSubmit.value = true;
			}
			if (!dataStep2.map_img) {
				isSubmit.value = false;
				configThis.value.toast.open({
					message: "Vui lòng chụp bản đồ để tiếp tục",
					type: "error",
					position: "top-right"
				});
				return;
			}

			// dataStep2.asset_type_id =
			// 	priceEstimates.value.step_1.general_infomation.asset_type_id;
			const res = await PriceEstimateModel.submitStep2(dataStep2, id);
			if (res.data) {
				await getDataAllStep(id);

				configThis.value.toast.open({
					message: "Lưu lựa chọn tài sản so sánh thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				miscVariable.value.distance_max = res.data.distance_max;
				miscVariable.value.filter_year = res.data.filter_year;

				configThis.value.wizard.maxStep = 2;
				configThis.value.wizard.tabs.forEach((tab, index) => {
					if (index > 2) {
						tab.checked = false;
					}
				});
				miscInfo.value.key_step_3 += 1;
				if (priceEstimates.value.step_3.id) {
					priceEstimates.value.step_3.id = null;
					priceEstimates.value.step_3.reInit = true;
					configThis.value.router.push({
						name: "price_estimates.detail",
						query: { id: priceEstimates.value.id },
						params: { step: 3 }
					});
				} else {
					configThis.value.router.push({
						name: "price_estimates.detail",
						query: { id: priceEstimates.value.id },
						params: { step: 3 }
					});
					// await configThis.value.wizard.nextTab();
				}
			} else if (res.error) {
				isSubmit.value = false;
				configThis.value.toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				isSubmit.value = false;
				configThis.value.toast.open({
					message: "Lưu thất bại",
					type: "error",
					position: "top-right"
				});
			}
			isSubmit.value = false;
		}
		async function validateSubmitStep3() {
			let step_3 = priceEstimates.value.step_3;
			if (!miscVariable.value.isApartment && step_3.total_area.length === 0) {
				configThis.value.toast.open({
					message: "Vui lòng chọn diện tích theo mục đích sử dụng",
					type: "error",
					position: "top-right"
				});
			} else {
				if (
					!priceEstimates.value.step_3.tangible_assets.every(
						asset =>
							asset.remaining_quality !== null &&
							asset.remaining_quality !== undefined &&
							asset.remaining_quality !== "" &&
							asset.remaining_quality >= 0 &&
							asset.remaining_quality <= 100
					)
				) {
					configThis.value.toast.open({
						message: "Vui lòng kiểm tra lại CLCL",
						type: "error",
						position: "top-right"
					});
					return;
				}
				handleSubmitStep_3(
					priceEstimates.value.step_3,
					priceEstimates.value.id
				);
			}
		}
		async function handleSubmitStep_3(dataStep3, id) {
			if (isSubmit.value == true) {
				configThis.value.toast.open({
					message: "Hệ thống đang xử lý, vui lòng đợi trong giây lát.",
					type: "warning",
					position: "top-right"
				});
				return;
			} else {
				isSubmit.value = true;
			}
			const tempUpdate = _.cloneDeep(dataStep3);
			let temp = 0;
			let temp1 = 0;
			let temp2 = 0;

			if (!miscVariable.value.isApartment) {
				if (tempUpdate.total_area && tempUpdate.total_area.length > 0) {
					temp = tempUpdate.total_area.reduce((total, area) => {
						area.total_price = area.total_price || 0;
						return total + Number(area.total_price);
					}, 0);
				}
				if (tempUpdate.planning_area && tempUpdate.planning_area.length > 0) {
					temp1 = tempUpdate.planning_area.reduce((total, area) => {
						area.total_price = area.total_price || 0;
						return total + Number(area.total_price);
					}, 0);
				}
				if (
					tempUpdate.tangible_assets &&
					tempUpdate.tangible_assets.length > 0
				) {
					temp2 = tempUpdate.tangible_assets.reduce((total, area) => {
						area.total_price = area.total_price || 0;
						return total + Number(area.total_price);
					}, 0);
				}
			} else {
				if (
					tempUpdate.apartment_finals &&
					tempUpdate.apartment_finals.length > 0
				) {
					temp = tempUpdate.apartment_finals.reduce((total, area) => {
						area.total_price = area.total_price || 0;
						return total + Number(area.total_price);
					}, 0);
				}
			}
			tempUpdate.total_price = temp + temp1 + temp2;
			tempUpdate.price_estimate_id = priceEstimates.value.id;
			if (moment(tempUpdate.request_date, "DD/MM/YYYY", true).isValid()) {
				tempUpdate.request_date = moment(
					tempUpdate.request_date,
					"DD-MM-YYYY"
				).format("YYYY-MM-DD");
			}
			const res = await PriceEstimateModel.submitStep3(
				tempUpdate,
				id,
				miscVariable.value.isApartment
			);
			if (res.data) {
				configThis.value.toast.open({
					message: "Lưu giá trị tài sản thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				priceEstimates.value.step_3.id = res.data.id;
				configThis.value.router.push({
					name: "price_estimates.detail",
					query: { id: priceEstimates.value.id },
					params: { step: 1 }
				});
				miscInfo.value.key_step_3++;
			} else if (res.error) {
				isSubmit.value = false;
				configThis.value.toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				isSubmit.value = false;
				configThis.value.toast.open({
					message: "Lưu thất bại",
					type: "error",
					position: "top-right"
				});
			}
			isSubmit.value = false;
		}
		async function getDataAllStep(id, boolRunGetProvince = true) {
			const res = await PriceEstimateModel.getDataAllStep(id);

			if (res && res.data) {
				await resetPriseEstimate();
				priceEstimatesOrigin.value = res.data;
				const bindDataStep = res.data;
				priceEstimates.value.id = bindDataStep.id;
				priceEstimates.value.appraise_id = bindDataStep.appraise_id;
				priceEstimates.value.pre_certificate_id =
					bindDataStep.pre_certificate_id;
				priceEstimates.value.apartment_asset_id =
					bindDataStep.apartment_asset_id;
				priceEstimates.value.isTransfer =
					bindDataStep.appraise_id || bindDataStep.apartment_asset_id
						? true
						: false;

				// step 1
				if (bindDataStep.created_at) {
					priceEstimates.value.createdAtString = moment(
						bindDataStep.created_at
					).format("DDMMYYYY");
				}
				if (bindDataStep.created_by) {
					priceEstimates.value.createdBy = bindDataStep.created_by.name;
					priceEstimates.value.created_by = bindDataStep.created_by;
				}
				priceEstimates.value.max_version = bindDataStep.max_version || 1;
				if (bindDataStep.apartment_properties) {
					priceEstimates.value.step_1.apartment_properties =
						bindDataStep.apartment_properties;
				}
				if (bindDataStep.economic_infomation) {
					priceEstimates.value.step_1.economic_infomation =
						bindDataStep.economic_infomation;
				}
				if (bindDataStep.general_infomation) {
					priceEstimates.value.step_1.general_infomation =
						bindDataStep.general_infomation;
					if (bindDataStep.asset_type_id != 39) {
						miscVariable.value.isApartment = false;
					} else {
						miscVariable.value.isApartment = true;
					}
				}

				if (bindDataStep.traffic_infomation) {
					priceEstimates.value.step_1.traffic_infomation =
						bindDataStep.traffic_infomation;
				}
				if (bindDataStep.land_details) {
					priceEstimates.value.step_1.land_details = bindDataStep.land_details;
				}
				if (bindDataStep.total_area && bindDataStep.total_area.length > 0) {
					priceEstimates.value.step_1.total_area = bindDataStep.total_area;
				}
				if (
					bindDataStep.planning_area &&
					bindDataStep.planning_area.length > 0
				) {
					priceEstimates.value.step_1.planning_area =
						bindDataStep.planning_area;
				}
				if (bindDataStep.step === 3) {
					miscVariable.value.step_active = 2;
				} else {
					miscVariable.value.step_active = bindDataStep.step;
				}

				if (bindDataStep.apartment_properties) {
					priceEstimates.value.step_1.apartment_properties =
						bindDataStep.apartment_properties[0];
				}
				// step 2
				// if (
				// 	bindDataStep.comparison_factor &&
				// 	bindDataStep.comparison_factor.length > 0
				// ) {
				// 	priceEstimates.value.step_2.comparison_factor =
				// 		bindDataStep.comparison_factor;
				// }
				if (bindDataStep.map_img) {
					priceEstimates.value.step_2.map_img = bindDataStep.map_img;
				}

				if (
					bindDataStep.assets_general &&
					bindDataStep.assets_general.length > 0
				) {
					priceEstimates.value.step_2.assets_general =
						bindDataStep.assets_general;
				}
				if (bindDataStep.distance_max) {
					miscVariable.value.distance_max = bindDataStep.distance_max;
				}
				if (bindDataStep.filter_year) {
					miscVariable.value.filter_year = bindDataStep.filter_year;
				}

				// step 3
				if (bindDataStep.final_estimate) {
					priceEstimates.value.step_3 = bindDataStep.final_estimate;
					priceEstimates.value.step_3.petitioner_name = null;
					priceEstimates.value.step_3.request_date = null;
					priceEstimates.value.step_3.appraise_purpose = null;
					if (bindDataStep.pre_certificate) {
						priceEstimates.value.step_3.petitioner_name =
							bindDataStep.pre_certificate.petitioner_name;
						const tempDateHere = bindDataStep.pre_certificate.pre_date;
						priceEstimates.value.step_3.request_date = moment(
							tempDateHere
						).format("DD/MM/YYYY");
						priceEstimates.value.step_3.appraise_purpose_id =
							bindDataStep.pre_certificate.appraise_purpose_id;
						priceEstimates.value.step_3.appraise_purpose =
							bindDataStep.pre_certificate.appraise_purpose;
					}
				}
				priceEstimates.value.updated_at = bindDataStep.updated_at;
				if (
					bindDataStep.general_infomation &&
					bindDataStep.general_infomation.asset_type &&
					bindDataStep.general_infomation.asset_type.description ===
					"ĐẤT CÓ NHÀ"
				) {
					miscVariable.value.isHaveContruction = true;
				} else {
					miscVariable.value.isHaveContruction = false;
				}
				// this.status_text = bindDataStep.status_text;
				// priceEstimates.value.status = bindDataStep.status;
				// this.idData = await priceEstimates.value.step_1.general_infomation.id;
				// if (
				// 	priceEstimates.value.step_1.general_infomation.asset_type_id === 38
				// ) {
				// 	this.isHaveContruction = true;
				// } else {
				// 	this.isHaveContruction = false;
				// }
				// if (bindDataStep.step >= 6) {
				// 	this.step_active = 5;
				// } else this.step_active = bindDataStep.step;
				// if (!this.isMobile()) {
				// 	await this.$refs.wizard.tabs.forEach((tab, index) => {
				// 		if (index <= this.step_active) {
				// 			tab.checked = true;
				// 		}
				// 	});

				// if (miscVariable.value.step_active === 3) {
				// 	await configThis.value.wizard.changeTab(0, 1);
				// } else {
				// 	await configThis.value.wizard.changeTab(
				// 		0,
				// 		miscVariable.value.step_active
				// 	);
				// }
				// }
				// if (this.form.step_2.comparison_factor.length > 1) {
				// 	await this.comparison.forEach(item => {
				// 		this.form.step_2.comparison_factor.forEach(itemFactor => {
				// 			if (item.slug === itemFactor && item.visible === false) {
				// 				item.visible = true;
				// 			}
				// 		});
				// 	});
				// }
			}
			if (boolRunGetProvince) await getProvinces();
			// priceEstimatesOrigin;
			return res;
		}
		function resetVariables() {
			resetPriseEstimate();
			configs.value = { hstdConfig: null, ycsbConfig: null };
			configThis.value = {
				toast: null,
				route: null,
				router: null,
				isMobile: false,
				wizard: null,
				step_1: null
			};

			miscInfo.value = {
				key_step_1: 0,
				key_step_2: 0,
				key_step_3: 0,
				current_create_by: null,
				points: [],
				blocks: [],
				floors: [],
				projects: [],
				directions: [],
				propertyTypes: [],
				topographic: [],
				provinces: [],
				districts: [],
				wards: [],
				streets: [],
				distances: [],
				materials: [],
				imageDescriptions: [],
				fengshuies: [],
				conditions: [],
				zones: [],
				businesses: [],
				socialSecurities: [],
				appraiser_purposes: [],
				optionMainChoose: [
					{
						id: 1,
						description: "Chính"
					},
					{
						id: 0,
						description: "Phụ"
					}
				]
			};
			addressName.value = {
				province: null,
				district: null,
				ward: null,
				street: null,
				distance: null
			};
			dataInfo.value = {
				provinceName: null,
				districtName: null,
				wardName: null,
				radius: null,
				distance: null,
				assetName: null,
				isHaveContruction: null
			};
			isSubmit.value = false;
		}
		function resetPriseEstimate() {
			miscVariable.value = {
				isApartment: null,
				step1AreaValidate: null,
				step_edit: "",
				step_active: null,
				showConfirmEdit: false,
				messageConfirm: "",
				filterYear: null,
				full_address: null,
				full_address_street: null,
				filter_year: 1,
				distance_max: null,
				isHaveContruction: null
			};

			priceEstimatesOrigin.value = null;
			priceEstimates.value = {
				step_1: {
					asset_type_id: null,
					general_infomation: {
						province_id: null,
						district_id: null,
						ward_id: null,
						street_id: null,
						distance_id: null,
						appraise_asset: null,
						full_address: null
					},
					traffic_infomation: {
						description: null,
						property_turning_time: []
					},
					apartment_properties: {
						floor_id: null,
						block_id: null,
						handover_year: null,
						direction_id: null,
						furniture_quality_id: null
					},
					economic_infomation: {},
					land_details: {},
					total_area: [],
					planning_area: []
				},
				step_2: { assets_general: [], filter_year: 1 },
				step_3: {
					petitioner_name: "",
					request_date: "",
					appraise_purpose_id: "",
					asset_type_id: "",
					appraise_asset: "",
					full_address: "",
					description: "",
					coordinates: "",
					total_area: [],
					apartment_finals: [
						{
							name: "",
							total_area: "",
							unit_price: "",
							total_price: ""
						}
					],
					planning_area: [],
					tangible_assets: [],
					appraise_land_sum_area: 0
				}
			};
		}
		return {
			isSubmit,
			priceEstimates,

			isSubmit,
			configs,
			configThis,
			miscInfo,
			dataInfo,
			addressName,
			miscVariable,
			priceEstimatesOrigin,

			handleChangeProject,
			handleChangeBlock,
			resetVariables,
			getFullAddress,
			getDictionary,
			changeProvince,
			getProvinces,
			changeAssetType,
			findDistance,
			changeStreet,
			findWard,
			changeDistrict,
			confirmEditStep,
			choosingAsset,
			changeAssetTypeFinal,

			getDataAllStep,
			validateSubmitStep1,
			validateSubmitStep2,
			validateSubmitStep3
		};
	},
	{
		persist: false
	}
);
