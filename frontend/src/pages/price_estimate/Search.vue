<template>
	<div class="row justify-content-between">
		<div class="filter-timer col-12 col-lg-2 ">
			<InputCategory
				v-model="address.province_id"
				vid="province_id"
				label=""
				:options="optionsProvince"
				placeholder="Tỉnh/Thành"
				@change="changeProvince($event)"
				class="label-none form-group-container"
			/>
		</div>
		<div class="filter-timer col-12 col-lg-2">
			<InputCategory
				v-model="address.district_id"
				vid="district_id"
				label=""
				:options="optionsDistrict"
				@change="changeDistrict($event)"
				placeholder="Quận/Huyện"
				class="label-none form-group-container"
			/>
		</div>
		<div class="filter-timer col-12 col-lg-2">
			<InputCategory
				v-model="address.ward_id"
				vid="ward_id"
				label=""
				:options="optionsWard"
				placeholder="Phường/Xã"
				@change="changeWard($event)"
				class="label-none form-group-container"
			/>
		</div>
		<div class="filter-timer col-12 col-lg-5">
			<InputCategory
				v-model="address.street_id"
				vid="street_id"
				label=""
				:options="optionsStreet"
				placeholder="Đường"
				@change="changeStreet"
				class="label-none form-group-container"
			/>
		</div>
		<div class="col-12 col-lg-1">
			<button
				class="btn btn-orange w-100 text-nowrap float-right"
				id="coordinateSearch"
			>
				<img src="../../assets/icons/ic_search-white.svg" alt="filter" />
				<!--        Tìm kiếm-->
			</button>
		</div>
		<!--    <div class="search-container d-flex justify-content-end w-100">-->
		<!--    </div>-->
	</div>
</template>

<script>
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
import InputSwitch from "@/components/Form/InputSwitch";
import WareHouse from "@/models/WareHouse";
export default {
	name: "search",
	props: ["address"],
	components: {
		InputCategory,
		InputText,
		InputSwitch
	},
	data() {
		return {
			front_side: false,
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			search: {
				front_side: 0,
				province_id: "",
				district_id: "",
				ward_id: "",
				street_id: ""
			}
		};
	},
	async mounted() {
		await this.getProvinces();
		this.$gmapApiPromiseLazy().then(await this.initMap());
		if (
			this.address.province_id !== "" &&
			this.address.province_id !== undefined &&
			this.address.province_id !== null
		) {
			await this.changeProvinceCoordinate();
		}
		if (
			this.address.district_id !== "" &&
			this.address.district_id !== undefined &&
			this.address.district_id !== null
		) {
			await this.changeDistrictCoordinate();
		}
		if (
			this.address.street_id !== "" &&
			this.address.street_id !== undefined &&
			this.address.street_id !== null
		) {
			await this.changeStreetCoordinate();
		}
	},
	created() {},
	computed: {
		optionsProvince() {
			return {
				data: this.provinces,
				id: "id",
				key: "name"
			};
		},
		optionsDistrict() {
			return {
				data: this.districts,
				id: "id",
				key: "name"
			};
		},
		optionsWard() {
			return {
				data: this.wards,
				id: "id",
				key: "name"
			};
		},
		optionsStreet() {
			return {
				data: this.streets,
				id: "id",
				key: "name"
			};
		}
	},
	methods: {
		initMap() {
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder();
			document
				.getElementById("coordinateSearch")
				.addEventListener("click", async () => {
					this.$emit("coordinate_search", geocoder);
				});
		},
		reset() {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = "";
				}
			}
			this.$emit("filter-changed", this.filter);
		},
		changeSwitchFrontSide() {
			if (this.front_side === true) {
				this.search.front_side = 1;
			} else {
				this.search.front_side = 0;
			}
		},
		changeProvince(provinceId) {
			this.districts = [];
			this.wards = [];
			this.streets = [];
			this.address.district_id = "";
			this.address.ward_id = "";
			this.address.street_id = "";
			this.getDistrictsByProvinceId(+provinceId);
			const data = this.address;
			let provinceName = "";
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name;
				}
			});
			this.address.province = provinceName;
			this.address.full_address = provinceName;
		},
		async changeProvinceCoordinate() {
			const data = this.address;
			let provinceName = "";
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name;
				}
			});
			await this.getDistrictsByProvinceId(data.province_id);
			this.address.province = provinceName;
			this.address.full_address = provinceName;
			this.$emit("action");
		},
		async changeDistrictCoordinate() {
			const data = this.address;
			let provinceName = "";
			let districtName = "";
			await this.getWardsByDistrictId(data.district_id);
			await this.getStreetByDistrictId(data.district_id);
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name;
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name;
						}
					});
				}
			});
			this.address.district = districtName;
			this.address.full_address = districtName + "," + provinceName;
			this.$emit("action");
		},
		changeDistrict(districtId) {
			this.wards = [];
			this.streets = [];
			this.address.ward_id = "";
			this.address.street_id = "";
			if (this.address.district_id !== 0) {
				this.getWardsByDistrictId(+districtId);
				this.getStreetByDistrictId(+districtId);
			}
			const data = this.address;
			let provinceName = "";
			let districtName = "";
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name;
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name;
						}
					});
				}
			});
			this.address.district = districtName;
			this.address.full_address = districtName + "," + provinceName;
		},

		changeStreetCoordinate() {
			const data = this.address;
			let provinceName = "";
			let districtName = "";
			let wardName = "";
			let streetName = "";
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name;
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name;
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name;
								}
							});
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name;
								}
							});
						}
					});
				}
			});
			this.address.ward = wardName;
			this.address.street = streetName;
			if (wardName === "") {
				this.address.full_address =
					streetName + ", " + districtName + ", " + provinceName;
			} else if (streetName === "") {
				this.address.full_address =
					wardName + ", " + districtName + ", " + provinceName;
			} else {
				this.address.full_address =
					streetName +
					", " +
					wardName +
					", " +
					districtName +
					", " +
					provinceName;
			}
			this.$emit("action");
		},
		changeWard() {
			const data = this.address;
			let provinceName = "";
			let districtName = "";
			let wardName = "";
			let streetName = "";
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name;
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name;
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name;
								}
							});
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name;
								}
							});
						}
					});
				}
			});
			this.address.ward = wardName;
			if (wardName === "") {
				this.address.full_address =
					streetName + ", " + districtName + ", " + provinceName;
			} else if (streetName === "") {
				this.address.full_address =
					wardName + ", " + districtName + ", " + provinceName;
			} else {
				this.address.full_address =
					streetName +
					", " +
					wardName +
					", " +
					districtName +
					", " +
					provinceName;
			}
		},
		changeStreet() {
			const data = this.address;
			let provinceName = "";
			let districtName = "";
			let wardName = "";
			let streetName = "";
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name;
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name;
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name;
								}
							});
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name;
								}
							});
						}
					});
				}
			});
			this.address.street = streetName;
			if (wardName === "") {
				this.address.full_address =
					streetName + ", " + districtName + ", " + provinceName;
			} else if (streetName === "") {
				this.address.full_address =
					wardName + ", " + districtName + ", " + provinceName;
			} else {
				this.address.full_address =
					streetName +
					", " +
					wardName +
					", " +
					districtName +
					", " +
					provinceName;
			}
		},
		async getProvinces() {
			try {
				const resp = await WareHouse.getProvince();
				this.provinces = [...resp.data];
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},

		async getDistrictsByProvinceId(id) {
			try {
				const resp = await WareHouse.getDistrictsByProvinceId(id);
				this.districts = [...resp.data];
				if (this.address.district_id !== "" && this.address.district_id !== 0) {
					await this.getWardsByDistrictId(this.address.district_id);
					await this.getStreetByDistrictId(this.address.district_id);
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},

		async getWardsByDistrictId(id) {
			this.wards = this.districts.find(item => item.id === id).wards;
			this.wards.forEach(item => {
				item.name = this.formatCapitalize(item.name);
			});
		},
		async getStreetByDistrictId(id) {
			this.streets = this.districts.find(item => item.id === id).streets;
			this.streets.forEach(item => {
				item.name = this.formatCapitalize(item.name);
			});
		},
		formatCapitalize(word) {
			return word.toLowerCase().replace(/(?:^|\s|[-"'([{])+\S/g, function(x) {
				return x.toUpperCase();
			});
		}
	},
	beforeMount() {}
};
</script>
<style scoped lang="scss">
.filter-timer {
	@media (max-width: 1023px) {
		margin-bottom: 10px;
	}
}
.search-container {
	margin-top: 15px;
}
</style>
