// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import WareHouse from "@/models/WareHouse";
import { usePriceEstimatesStore } from "@/store/priceEstimates";
import { storeToRefs } from "pinia";
export const useDataStorePE = defineStore(
	"dataStorePE",
	() => {
		const configs = ref({ hstdConfig: null, ycsbConfig: null });
		const configThis = ref({ toast: null, route: null });
		const miscInfo = ref({
			current_create_by: null,
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
			full_address: null,
			isHaveContruction: null
		});
		const priceEstimateStore = usePriceEstimatesStore();
		const { priceEstimates, isSubmit } = storeToRefs(priceEstimateStore);
		function formatCapitalize(word) {
			return word.replace(/(?:^|\s|[-"'([{])+\S/g, function(x) {
				return x.toUpperCase();
			});
		}
		function formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
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
				item.name = formatCapitalize(item.name);
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
			dataInfo.value.full_address =
				`${dataInfo.value.wardName ? dataInfo.value.wardName + ", " : ""}` +
				`${
					dataInfo.value.districtName ? dataInfo.value.districtName + ", " : ""
				}` +
				`${
					dataInfo.value.provinceName
						? dataInfo.value.provinceName.includes("Thành phố")
							? dataInfo.value.provinceName
							: "Tỉnh " + dataInfo.value.provinceName.trim()
						: ""
				}`;
			dataInfo.value.full_address_street =
				`${dataInfo.value.streetName ? dataInfo.value.streetName + ", " : ""}` +
				`${dataInfo.value.wardName ? dataInfo.value.wardName + ", " : ""}` +
				`${
					dataInfo.value.districtName ? dataInfo.value.districtName + ", " : ""
				}` +
				`${dataInfo.value.provinceName ? dataInfo.value.provinceName : ""}`;
			if (configThis.value.route.name === "certification_asset.create") {
				getInfo();
			}
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
				dataInfo.value.isHaveContruction = true;
			} else {
				dataInfo.value.isHaveContruction = false;
			}
			getInfo();
		}
		function getInfo() {
			if (
				dataInfo.value.assetName === "Đất trống" &&
				dataInfo.value.full_address
			) {
				priceEstimates.value.step_1.general_infomation.appraise_asset = `Quyền sử dụng đất tại ${dataInfo.value.full_address}`;
			} else if (
				dataInfo.value.assetName === "Đất có nhà" &&
				dataInfo.value.full_address
			) {
				priceEstimates.value.step_1.general_infomation.appraise_asset = `Quyền sử dụng đất và CTXD tại ${dataInfo.value.full_address}`;
			}
			let streetName = dataInfo.value.streetName
				? dataInfo.value.streetName.toLowerCase()
				: "";
			let fullAddress =
				`${
					streetName.length > 0
						? (streetName.substring(0, 5) === "đường" ? "" : "Đường ") +
						  formatCapitalize(streetName) +
						  ", "
						: ""
				}` + dataInfo.value.full_address;
			priceEstimates.value.step_1.general_infomation.full_address = fullAddress;
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
					configThis.value.route.name === "certification_asset.edit" ||
					("id" in configThis.value.route.params &&
						configThis.value.route.name === "certification_asset.create")
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
				dataInfo.value.value.districtName &&
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
			priceEstimates.value.form.step_1.general_infomation.distance_id = "";
			if (priceEstimates.value.form.step_1.general_infomation.street_id) {
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
				getWardsByDistrictId(id);
				getStreetByDistrictId(id);
			}
			findDistrict();
		}
		return {
			isSubmit,
			configs,
			miscInfo,
			dataInfo,
			addressName,

			getDictionary,
			changeProvince,
			getProvinces,
			changeAssetType,
			findDistance,
			changeStreet,
			findWard,
			changeProvince,
			changeDistrict
		};
	},
	{
		persist: true
	}
);
