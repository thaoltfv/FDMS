<template>
	<div class="pannel">
		<ValidationObserver
			tag="form"
			ref="observer"
			@submit.prevent="validateBeforeSubmit"
		>
			<div class="position-relative info-container">
				<div class="card">
					<div class="card-title">
						<div class="d-flex justify-content-between align-items-center">
							<h3 class="title">Thông tin đối tác</h3>
						</div>
					</div>
					<div class="card-body card-info">
						<!-- <div class="container-filter">
							<div class="row align-items-center">
								<div class="col-12 col-lg-3">
									<div class="card-body__avatar">
										<div class="img-avatar mb-3">
											<img
												v-if="
													form.customer_picture === '' ||
														form.customer_picture === undefined ||
														form.customer_picture === null
												"
												class="w-100 h-100"
												src="../../assets/icons/ic_user.svg"
												alt="avatar"
											/>
											<img
												v-if="
													form.customer_picture !== '' &&
														form.customer_picture !== undefined &&
														form.customer_picture !== null
												"
												class="w-100 h-100 img__avatar"
												:src="form.customer_picture"
												alt="img"
											/>
										</div>
										<div class="container__input">
											<button
												type="button"
												class="btn btn-orange mr-0"
												@click="showAvatar()"
											>
												Thay đổi avatar
											</button>
										</div>
									</div>
								</div>
								<div class="col-12 col-lg-9">
									<div class="container-img">
										<div
											class="container__input d-flex justify-content-end mb-1"
											style="margin-right: 0.75rem"
										>
											<button type="button" class="btn btn-orange mr-0">
												Tải ảnh lên
											</button>
											<input
												type="file"
												id="image"
												accept="image/png, image/gif, image/jpeg, image/jpg"
												class="input__image"
												multiple
												@change="onImageChange($event)"
											/>
										</div>
										<div class="row mr-0 ml-0">
											<div
												class="img-empty m-auto text-center"
												v-if="form.pic.length === 0"
											>
												<img
													src="../../assets/images/img_emply.svg"
													alt="empty"
												/>
												<p class="empty-content">Chưa có hình</p>
											</div>
											<div
												class="contain-img col-4 col-lg-2 contain-img__property"
												v-for="(images, index) in form.pic"
												:key="images.id"
											>
												<div class="delete" @click="removeImage(index)">X</div>
												<img
													class="img"
													:src="images.link"
													alt="img"
													@click="showImages"
												/>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div> -->
						<div class="container-fluid">
							<div class="row justify-content-between">
								<div class="col-12 col-md-4 col-lg-4 form-group-container">
									<label class="font-weight-bold color-black">Mã đối tác</label>
									<div
										class="form-control form-control__id disabled"
										v-if="$route.name === 'customer.create'"
									>
										<p class="mb-0"></p>
									</div>
									<div
										class="form-control form-control__id disabled"
										v-if="$route.name === 'customer.edit'"
									>
										<p class="mb-0" style="color: #FAA831">
											{{ "DT_" + form.id }}
										</p>
									</div>
								</div>

								<InputText
									v-model="form.name"
									vid="name"
									label="Họ và tên đối tác"
									rules="required"
									class="col-12 col-md-4 col-lg-4 form-group-container"
								/>
								<InputText
									v-model="form.phone"
									vid="phone"
									label="Số điện thoại"
									type="number"
									:max-length="10"
									:min="0"
									rules="required"
									class="col-12 col-md-4 col-lg-4 form-group-container"
								/>
								<!-- <InputText
									v-model="form.tax_code"
									vid="tax_code"
									label="Mã số thuế"
									type="number"
									:max-length="15"
									class="col-12 col-md-4 col-lg-4 form-group-container"
								/> -->
								<!-- <div class="col-12 col-md-6 col-lg-3 form-group-container">
									<label class="font-weight-bold color-black">Ngày tạo</label>
									<div class="form-control disabled">
										<p class="mb-0">{{ form.created_date }}</p>
									</div>
								</div>
								<div class="col-12 col-md-6 col-lg-3 form-group-container">
									<label class="font-weight-bold color-black">Người tạo</label>
									<div class="form-control disabled">
										<p class="mb-0">{{ form.created_by }}</p>
									</div>
								</div>
								<div class="col-12 col-md-6 col-lg-4 form-group-container">
									<label class="font-weight-bold color-black">Trạng thái</label>
									<div class="form-control disabled">
										<p class="mb-0">
											{{
												form.status === "inactive"
													? "Đang vô hiệu hóa"
													: form.status === "active"
													? "Đang hoạt động"
													: ""
											}}
										</p>
									</div>
								</div> -->
							</div>
							<div class="card-info">
								<div class="row">
									<InputText
										v-model="form.address"
										vid="full_address"
										label="Địa chỉ đầy đủ"
										class="col-12 form-group-container"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="loading" :class="{ loading__true: isSubmit }">
					<a-spin />
				</div>
			</div>
			<div
				class="btn-footer d-md-flex d-block justify-content-end align-items-center"
			>
				<div class="d-lg-flex d-block button-contain">
					<button
						class="btn btn-white btn-orange text-nowrap"
						:class="{ 'btn-loading disabled': isSubmit }"
						type="submit"
					>
						<img
							src="../../assets/icons/ic_save.svg"
							:class="{ 'd-none': isSubmit }"
							style="margin-right: 12px"
							alt="save"
						/>
						Lưu
					</button>
					<button
						@click.prevent="handleOpenModalCancel"
						type="button"
						class="btn btn-white text-nowrap"
						:class="{ disabled: isSubmit }"
					>
						<img
							src="../../assets/icons/ic_destroy.svg"
							style="margin-right: 12px"
							alt="save"
						/>
						Hủy
					</button>
					<button
						class="btn btn-white text-nowrap"
						:class="{ disabled: isSubmit }"
						type="button"
						v-if="$route.name === 'customer.edit' && form.status === 'active'"
						@click="changeStatusInactive"
					>
						<img
							src="../../assets/icons/ic_lock.svg"
							style="margin-right: 12px"
							alt="save"
						/>
						Vô hiệu hóa
					</button>
					<button
						type="button"
						class="btn btn-white text-nowrap"
						:class="{ disabled: isSubmit }"
						v-if="$route.name === 'customer.edit' && form.status === 'inactive'"
						@click="changeStatusActive"
					>
						<img
							src="../../assets/icons/ic_unlock.svg"
							style="margin-right: 12px"
							alt="save"
						/>
						Kích hoạt lại
					</button>
					<button
						class="btn btn-white text-nowrap"
						:class="{ disabled: isSubmit }"
						v-if="$route.name === 'customer.edit'"
						disabled
					>
						<img
							src="../../assets/icons/ic_destroy.svg"
							style="margin-right: 12px"
							alt="save"
						/>
						Xem doanh thu
					</button>
				</div>
			</div>
		</ValidationObserver>
		<ModalCancelCustomer
			v-if="openModalCancel"
			@cancel="openModalCancel = false"
			:message="this.messageCustomer"
			@action="handleCancel"
		/>
		<ModalNotification
			v-if="openNotification"
			v-bind:notification="message"
			@cancel="openNotification = false"
			@action="handleHome"
		/>
		<ModalViewImageCustomer
			v-if="showModalImage"
			@cancel="showModalImage = false"
			v-bind:pics="form.pic"
		/>
		<ModalAvatarCustomer
			v-if="showModalImageAvatar"
			@cancel="showModalImageAvatar = false"
			v-bind:pics="form.pic"
			@choose-image="handleAvatar"
		/>
		<ModalNotificationCustomer
			v-if="modalNotificationCustomer"
			@cancel="modalNotificationCustomer = false"
			v-bind:notification="this.messageNotification"
			@action="actionDisable"
		/>
	</div>
</template>
<script>
import Vue from "vue";
import VueNumeric from "vue-numeric";
import InputText from "@/components/Form/InputText";
import InputCategory from "@/components/Form/InputCategory";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputSwitch from "@/components/Form/InputSwitch";
import ModalImage from "@/components/Modal/ModalImage";
import File from "@/models/File";
import { STATUS_CUSTOMER } from "@/enum/status-customer.enum";
import Customer from "../../models/Customer";
import ModalCancel from "../../components/Modal/ModalCancel";
import ModalNotification from "../../components/Modal/ModalNotification";
import ModalViewImageCustomer from "../../components/Modal/ModalViewImageCustomer";
import ModalAvatarCustomer from "../../components/Modal/ModalAvatarCustomer";
import ModalNotificationCustomer from "@/components/Modal/ModalNotificationCustomer";
import ModalCancelCustomer from "../../components/Modal/ModalCancelCustomer";
import moment from "moment";
import WareHouse from "@/models/WareHouse";
Vue.use(VueNumeric);
export default {
	name: "",
	components: {
		ModalImage,
		InputText,
		InputNumberFormat,
		InputCategory,
		InputSwitch,
		ModalCancel,
		ModalNotification,
		ModalAvatarCustomer,
		ModalViewImageCustomer,
		ModalNotificationCustomer,
		ModalCancelCustomer
	},
	data() {
		return {
			message: "",
			street: "",
			property_index: "",
			property: "",
			image: "",
			price: "",
			unit_price: {},
			openModalWarning: false,
			openNotification: false,
			openTangible: false,
			showAsset: true,
			showInfo: true,
			showTable: true,
			showLand: true,
			showOther: true,
			showNote: true,
			openModal: false,
			openEdit: false,
			openImage: false,
			openError: false,
			showInfoTransaction: true,
			openModalCancel: false,
			purpose: "",
			center: { lat: 10.964112, lng: 106.856461 },
			counter: 0,
			counterOther: 0,
			show: false,
			types: [],
			customerGroups: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			distances: [],
			landTypes: [],
			propertyTypes: [],
			housingTypes: [],
			infoSources: [],
			transactionType: [],
			type_purposes: [],
			unit: "",
			isSubmit: false,
			file: null,
			form: {
				name: "",
				phone: "",
				customer_picture: "",
				tax_code: "",
				created_date: "",
				created_by: "",
				status: STATUS_CUSTOMER.ACTIVE,
				address: "",
				// customer_group_id: "",
				pic: []
			},
			showModalImage: false,
			showModalImageAvatar: false,
			modalNotificationCustomer: false,
			disable: false,
			active: false,
			messageCustomer: ""
		};
	},
	created() {
		if ("id" in this.$route.query && this.$route.name === "customer.edit") {
			this.form = Object.assign(this.form, {
				...this.$route.meta["detail"]
			});
			if (this.form.province === null || this.form.province === undefined) {
				this.form.province_id = null;
			}
			if (this.form.district === null || this.form.district === undefined) {
				this.form.dictrict_id = null;
			}
			if (this.form.ward === null || this.form.ward === undefined) {
				this.form.ward_id = null;
			}
			if (this.form.street === null || this.form.street === undefined) {
				this.form.street_id = null;
			}
			if (this.form.distance === null || this.form.distance === undefined) {
				this.form.distance_id = null;
			}
		} else {
		}
	},
	mounted() {
		if (this.$route.name === "customer.create") {
			this.getCreatedAt();
			this.messageCustomer = "Bạn có chắc muốn hủy quá trình tạo mới?";
		} else if (this.$route.name === "customer.edit") {
			this.messageCustomer = "Bạn có chắc muốn hủy quá trình chỉnh sửa?";
		}
		this.image = process.env.API_URL;
		// this.getDictionary();
	},
	computed: {
		sortedArray: function() {
			function compare(a, b) {
				if (a.price_land > b.price_land) {
					return -1;
				}
				if (a.price_land < b.price_land) {
					return 1;
				}
				return 0;
			}
			// eslint-disable-next-line vue/no-side-effects-in-computed-properties
			return this.purpose_use_lands.sort(compare);
		},
		optionsCustomerGroup() {
			return {
				data: this.customerGroups,
				id: "id",
				key: "description"
			};
		},
		optionsType() {
			return {
				data: this.propertyTypes,
				id: "id",
				key: "description"
			};
		},
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
		},
		optionsDistance() {
			return {
				data: this.distances,
				id: "id",
				key: "detail"
			};
		},
		optionsInfo() {
			return {
				data: this.infoSources,
				id: "id",
				key: "description"
			};
		},
		optionsTransactionType() {
			return {
				data: this.transactionType,
				id: "id",
				key: "description"
			};
		},
		optionsHousingType() {
			return {
				data: this.housingTypes,
				id: "id",
				key: "description"
			};
		},
		optionsHousing() {
			return {
				data: this.building_categories,
				id: "id",
				key: "description"
			};
		},
		optionsLandType() {
			return {
				data: this.landTypes,
				id: "id",
				key: "description"
			};
		},
		optionsTopographic() {
			return {
				data: this.topographics,
				id: "id",
				key: "description"
			};
		}
	},
	methods: {
		// async getDictionary() {
		// 	const resp = await WareHouse.getDictionaries();
		// 	this.customerGroups = resp.data.nhom_doi_tac;
		// },
		handleAvatar(event) {
			this.form.customer_picture = event;
		},
		showAvatar() {
			this.showModalImageAvatar = true;
		},
		showImages() {
			this.showModalImage = true;
		},
		changeStatusInactive() {
			this.messageNotification =
				"Bạn có chắc muốn chuyển trạng thái sang vô hiệu hóa không?";
			this.disable = true;
			this.active = false;
			this.modalNotificationCustomer = true;
		},
		changeStatusActive() {
			this.messageNotification =
				"Bạn có chắc muốn chuyển trạng thái sang hoạt động không?";
			this.disable = false;
			this.active = true;
			this.modalNotificationCustomer = true;
		},
		actionDisable() {
			if (this.disable) {
				this.form.status = STATUS_CUSTOMER.INACTIVE;
			}
			if (this.active) {
				this.form.status = STATUS_CUSTOMER.ACTIVE;
			}
		},
		formatDate(value) {
			return moment(String(value)).format("DD/MM/YYYY");
		},
		getCreatedAt() {
			const today = new Date();
			this.form.created_date =
				`${today.getDate() < 10 ? "0" + today.getDate() : today.getDate()}` +
				"/" +
				`${
					today.getMonth() + 1 < 10
						? "0" + (today.getMonth() + 1)
						: today.getMonth() + 1
				}` +
				"/" +
				today.getFullYear();
		},
		getCenter(center) {
			this.center = center;
		},
		openModalImage(data) {
			this.openImage = true;
			this.image_detail = data;
		},
		formatNumber(value) {
			let num = (value / 1).toFixed(0).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		retype() {
			this.$router.push({ name: "customer.create" });
		},
		onImageChange(e) {
			let files = e.target.files || e.dataTransfer.files;
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];
				if (
					this.file.type === "image/png" ||
					this.file.type === "image/jpeg" ||
					this.file.type === "image/jpg" ||
					this.file.type === "image/gif"
				) {
					this.createImage();
					this.uploadImage();
				} else {
					this.$toast.open({
						message: "Hình không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
		},
		createImage() {
			let reader = new FileReader();
			let v = this;
			reader.onload = e => {
				v.image = e.target.result;
			};
			reader.readAsDataURL(this.file);
		},
		uploadImage() {
			this.isSubmit = true;
			const formData = new FormData();
			formData.append("image", this.file);
			return File.upload({ data: formData }).then(response => {
				if (response && response.data && response.data.data) {
					const item = {
						link: response.data.data.link,
						picture_type: response.data.data.picture_type
					};
					this.form.pic.push(item);
					if (
						this.form.customer_picture === "" ||
						this.form.customer_picture === undefined ||
						this.form.customer_picture === null
					) {
						this.form.customer_picture = this.form.pic[0].link;
					}
					this.isSubmit = false;
				} else if (response.data.error) {
					this.isSubmit = false;
					this.$toast.open({
						message: response.data.error.message,
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		removeImage(index) {
			this.form.pic.splice(index, 1);
			document.getElementById("image").value = "";
		},
		handleActionWarning() {
			this.openModal = true;
		},
		handleOpenModalCancel() {
			this.openModalCancel = true;
		},
		addOtherAsset() {
			this.counterOther++;
			this.form.other_assets.push({
				other_asset: "",
				total_amount: 0,
				file: null,
				image: null,
				pic: []
			});
		},
		findStreet() {
			let streetName = "";
			const data = this.form;
			this.streets.forEach(street => {
				if (street.id === data.street_id) {
					streetName = street.name;
				}
			});
			this.street = streetName;
		},
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (isValid) {
				this.handleSubmit();
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		handleSubmit() {
			this.isSubmit = true;
			let data = this.form;
			if (this.$route.name === "customer.edit") {
				this.updateDictionary(data);
			} else {
				this.createDictionary(data);
			}
		},
		async createDictionary(data) {
			try {
				const resp = await Customer.create(data);
				if (resp) {
					if (resp.data) {
						this.isSubmit = false;
						this.message = "Tạo mới đối tác thành công.";
						this.openNotification = true;
					} else if (resp.error) {
						this.$toast.open({
							message: resp.error.message,
							type: "error",
							position: "top-right",
							duration: 3000
						});
						this.isSubmit = false;
					}
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		async updateDictionary(data) {
			try {
				const resp = new Customer(data);
				await resp.save();
				if (resp.data) {
					this.isSubmit = false;
					this.message = "Chỉnh sửa đối tác thành công.";
					this.openNotification = true;
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: "error",
						position: "top-right",
						duration: 3000
					});
					this.isSubmit = false;
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		async handleHome() {
			if (this.$route.name === "customer.create") {
				this.$router.push({ name: "customer.index" }).catch(_ => {});
			} else if (this.$route.name === "customer.edit") {
				this.$router.go(-1);
			}
		},
		async getProfiles() {
			const profile = this.$store.getters.profile;
			this.form.created_by = profile.data.user.name;
		},
		onCancel() {
			return this.$router.push({ name: "customer.index" });
		},
		async handleCancel() {
			this.isSubmit = true;
			if (this.$route.name === "customer.create") {
				return this.$router.push({ name: "customer.index" });
			} else if (this.$route.name === "customer.edit") {
				this.$router.go(-1);
			}
		},
		compareAsset() {
			const properties = this.form.properties;
			let compare_assets = [];
			this.compare_assets = [];
			properties.forEach(property => {
				property.compare_property_doc.forEach(compare_property_doc => {
					compare_assets.push({
						id: compare_property_doc.plot_num,
						plot_num:
							"Số tờ: " +
							compare_property_doc.doc_num +
							", Số thửa: " +
							compare_property_doc.plot_num
					});
				});
			});
			this.compare_assets = compare_assets;
		},
		sortArrayPropertyDetail() {
			let purpose_use_lands = [];
			this.purpose_use_lands = [];
			this.form.average_land_unit_price = 0;
			this.form.properties.forEach(property => {
				property.property_detail.forEach(property_detail => {
					purpose_use_lands.push(property_detail);
				});
				this.purpose_use_lands = purpose_use_lands;
				this.averageLandUnitPrice();
			});
		},
		averageLandUnitPrice() {
			let average_land_unit_price = 0;
			this.purpose_use_lands.forEach(purpose_use_land => {
				average_land_unit_price =
					average_land_unit_price + purpose_use_land.price_land;
			});
			this.form.average_land_unit_price = parseFloat(
				average_land_unit_price / this.purpose_use_lands.length
			).toFixed(0);
		},
		async handleSaveTangible(tangible, tangible_index) {
			if (this.isEditTangible === false) {
				this.form.tangible_assets.push(tangible);
			} else {
				this.form.tangible_assets[tangible_index] = tangible;
			}
			const total = this.form.tangible_assets;
			const property = this.form.properties;
			let land_use = [];
			let total_tangible_area = 0;
			let total_tangible_amount = 0;
			let max_value = 0;
			let total_area = 0;
			let purpose_max = "";
			let type_purposes = this.type_purposes;
			total.forEach(item => {
				total_tangible_area =
					total_tangible_area +
					parseFloat(item.total_construction_area).toFixed(0);
				total_tangible_amount =
					total_tangible_amount + parseInt(item.estimation_value);
			});
			property.forEach(item => {
				total_area = total_area + parseInt(item.asset_general_land_sum_area);
				item.property_detail.forEach(property_detail => {
					land_use.push(property_detail);
				});
			});
			land_use.forEach(land => {
				if (land.circular_unit_price > max_value) {
					max_value = land.circular_unit_price;
					purpose_max = land.land_type_purpose;
				}
			});
			type_purposes.forEach(purposes => {
				if (purposes.id === purpose_max) {
					this.form.max_value_description = purposes.description;
				}
			});
			this.form.total_construction_area = total_tangible_area;
			this.form.total_construction_amount = total_tangible_amount;
			this.form.total_raw_amount =
				parseInt(this.form.total_estimate_amount) -
				parseInt(this.form.total_construction_amount) -
				parseInt(this.form.total_order_amount);
			this.form.total_land_unit_price =
				this.form.total_raw_amount + this.form.convert_fee_total;
			property.forEach(item => {
				item.property_detail.forEach(unit => {
					if (
						this.form.total_land_unit_price / total_area -
							(max_value - unit.circular_unit_price) >
						0
					) {
						unit.price_land = parseInt(
							this.form.total_land_unit_price / total_area -
								(max_value - unit.circular_unit_price)
						);
					} else {
						unit.price_land = 0;
					}
				});
			});
			this.sortArrayPropertyDetail();
		},
		remainingQuality(event, index) {
			for (let i = 0; i < this.form.tangible_assets.length; i++) {
				if (i === index) {
					this.form.tangible_assets[i].remaining_quality = +event;
				}
			}
			this.form.tangible_assets[index].estimation_value =
				this.form.tangible_assets[index].total_construction_base *
				this.form.tangible_assets[index].unit_price_m2 *
				(this.form.tangible_assets[index].remaining_quality / 100);
		},
		async handleCoordinates(coordinates) {
			this.form.coordinates = coordinates;
			this.location.lat = coordinates.split(",")[0];
			this.location.lng = coordinates.split(",")[1];
		}
	},
	async beforeMount() {
		if (this.$route.name === "customer.create") {
			await this.getProfiles();
		}
	}
};
</script>
<style scoped lang="scss">
.card__order {
	max-width: 100%;
	.contain-table {
		overflow: hidden;
		@media (max-width: 1023px) {
			overflow: hidden;
		}
	}
	.contain-img {
		.img-table {
			min-width: 50px;
			min-height: 50px;
			width: 70px;
			height: 70px;
		}
	}
}
.title {
	margin-bottom: 0;
	font-size: 24px;
	font-weight: 700;
}
.btn {
	&-white {
		max-height: none;

		line-height: 19.07px;
		min-width: 153px;
		margin-right: 15px;
		&:last-child {
			margin-right: 0;
		}
	}
	&-orange {
		max-height: none;

		line-height: 19.07px;
		min-width: 153px;
		margin-right: 15px;
		color: #ffffff;
		background: #faa831;
	}
	&-contain {
		margin-bottom: 55px;
		@media (max-width: 768px) {
			margin-bottom: 30px;
		}
	}
}
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 25px;
	@media (max-width: 768px) {
		margin-bottom: 20px;
	}
	@media (max-width: 418px) {
		margin-bottom: 20px;
	}
	&-title {
		background: #f3f2f7;
		padding: 16px 20px;
		margin-bottom: 0;
		&__img {
			padding: 8px 20px;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}
		.title {
			font-size: 1.125rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}
	&-body {
		padding: 35px 30px 40px;
		@media (max-width: 787px) {
			padding: 15px;
		}
	}
	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;
		}
	}
	&-land {
		position: relative;
		padding: 0;
	}
}
.contain-input {
	display: flex;
}

.input {
	&-grid {
		margin-top: 28px;
		display: grid;
		grid-template-columns: 1fr 1fr 506px;
		grid-column-gap: 8%;
		grid-row-gap: 25px;
		@media (max-width: 1680px) {
			grid-template-columns: 1fr 1fr 1.5fr;
		}
		@media (max-width: 768px) {
			grid-template-columns: 1fr;
		}
	}
}
.img-locate {
	cursor: pointer;
	position: absolute;
	right: 14px;
	top: 2.1rem;
	background: #ffffff;
	height: 2.1rem;
	width: 32px;
	display: grid;
	place-items: center;
	img {
		height: 60%;
	}
}
.img-dropdown {
	cursor: pointer;
	width: 18px;
	&__hide {
		transform: rotate(90deg);
		transition: 0.3s;
	}
}
.table-property {
	width: 100%;
	font-weight: 500;
	color: #000000;
	text-align: center;
	thead {
		th {
			padding: 12px 0;
			font-weight: 500;
			@media (max-width: 787px) {
				padding: 12px;
			}
		}
	}
	tbody {
		td {
			border: 1px solid #e5e5e5;
			&:first-child {
				border-left: none;
				border-right: none;
			}
			padding: 20px 14px;
		}
	}
	&__order {
		tbody {
			td {
				&:first-child {
					width: 40%;
				}
				&:last-child {
					width: 70px;
				}
				padding: 20px 70px;
				@media (max-width: 1023px) {
					padding: 20px 30px;
				}
			}
		}
	}
}
.btn-property {
	padding: 10px;
}
.btn-delete {
	display: flex;
	align-items: center;
	cursor: pointer;
	background: #ffffff;
	border: 0.777778px solid #000000;
	border-radius: 5.88235px;
	padding: 10px;
	margin: auto;
	width: 36px;
	height: 36px;
	img {
		width: 100%;
		height: auto;
	}
}
.contain-table {
	&__tangible {
		overflow-y: hidden;
		overflow-x: auto;
	}
	@media (max-width: 1440px) {
		overflow-y: hidden;
		overflow-x: auto;
	}
	.table-property {
		width: 100%;
	}
}
.contain-file {
	display: flex;
	align-items: center;
	h3 {
		margin-top: 8px;
		margin-bottom: 0;
	}
}
.btn-upload {
	background: #ffffff;
	white-space: nowrap;
	border: 1px solid #555555;
	box-sizing: border-box;
	border-radius: 5px;
	padding: 5px 19px;
	font-size: 10px;
}
.img-upload {
	margin-left: 20px;
	position: relative;
	width: 123px;
	height: 35px;
	color: #fff;
	background: #faa831;

	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	display: flex;
	justify-content: center;
	align-items: center;
	box-sizing: border-box;
	cursor: pointer;
	input {
		cursor: pointer !important;
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		width: 100%;
		opacity: 0;
	}
}
.contain-img {
	aspect-ratio: 1/1;
	overflow: hidden;
	height: auto;
	position: relative;
	text-align: center;
	margin-bottom: 10px;
	.img {
		object-fit: cover;
		width: 100%;
		height: 100%;
		cursor: pointer;
		&-table {
			margin: auto;
			min-width: 50px;
			min-height: 50px;
			width: 50px;
			height: 50px;
			object-fit: cover;
		}
	}
	&__table {
		width: auto;
		margin-bottom: 0;
	}
	.delete {
		position: absolute;
		top: 0;
		right: 0.75rem;
		background: #000000;
		color: #ffffff;
		width: 20px;
		height: 20px;
		text-align: center;
		line-height: 1.5;
		cursor: pointer;
		font-weight: 700;
		border-radius: 5px;
	}
}
.btn-loading {
	color: #ffffff !important;
}
.contain-total {
	display: grid !important;
	margin-right: 0;
	grid-template-columns: 1fr 1fr;
	color: #333333;
	@media (max-width: 1440px) {
		display: block !important;
	}
	.num {
		padding: 0 11px 0 24px;
		height: 35px;
		line-height: 1.5;
		width: 180px;
		border-radius: 5px;
		border: 1px solid #555555;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		background: #f1f1f1 !important;
		cursor: not-allowed;
		user-select: none;
		@media (max-width: 787px) {
			width: 100% !important;
		}
		p {
			margin-bottom: 0;
		}
		&-id {
			color: #faa831;
			text-align: center !important;
			background: #ffffff !important;
			border: none;
			width: 100%;
			padding: 0;
		}
	}
	.name {
		margin-bottom: 0;
		font-size: 1.125rem;
		font-weight: 500;
		margin-right: 20px;
		@media (max-width: 1440px) {
			margin-bottom: 10px;
			font-weight: 700;
		}
	}
	&__last {
		.num {
			width: 315px;
			@media (max-width: 767px) {
				width: calc(100vw - 120px);
			}
		}
	}
	&__table {
		grid-template-columns: 1fr;
		.num {
			text-align: left;
			margin: auto;
			&-id {
				cursor: pointer;
				color: #faa831;
				text-align: center !important;
			}
		}
	}
}
.loading {
	display: none;
	&__true {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		height: 100dvh;
		background: rgba(255, 255, 255, 0.62);
		z-index: 100000;
		display: flex;
		align-items: center;
		justify-content: center;
		&.btn-loading {
			&:after {
				width: 2rem !important;
				height: 2rem !important;
			}
		}
	}
}

.form-group-container {
	margin-top: 15px;
}
.contain-table {
	overflow: auto;
}
.percent {
	top: 0;
	right: 0;
	bottom: 0;
	position: absolute;
	background: #f1f1f1;
	border: 1px solid #000000;
	border-left: none;
	height: 100%;
	line-height: 1.5;
	border-bottom-right-radius: 5px;
	border-top-right-radius: 5px;
	padding: 5.5px;
	box-sizing: border-box;
}
.info-container {
	margin-bottom: 85px;
	@media (max-width: 767px) {
		margin-bottom: 145px;
	}
}
.coordinate {
	color: #000000;
	background: #f1f1f1;
	padding: 0 11px 0 24px;
	display: flex;
	align-items: center;
	height: 35px;
	border-radius: 5px;
	border: 1px solid #555555;
	.num {
		p {
			margin-bottom: 0;
		}
	}
}
hr {
	background: #0b0d10;
	opacity: 0.5;
}
.done {
	text-align: center;
	font-size: 20px;
}
.color-black {
	color: #333333;
}
.container-img {
	padding: 0.75rem 0;
	border: 1px solid #0b0d10;
}
ul,
li {
	list-style: none;
}
ul {
	padding-left: 0;
}
.text-error {
	color: #cd201f;
	font-size: 12px;
}
.card-body {
	&__avatar {
		display: flex;
		flex-direction: column;
		align-items: center;
		.img-avatar {
			width: 120px;
			height: 120px;
			border-radius: 50%;
			img {
				object-fit: cover;
				border-radius: 50%;
			}
		}
	}
}
.container {
	&__input {
		position: relative;
		.input {
			&__image {
				position: absolute;
				width: 100%;
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
				opacity: 0;
				cursor: pointer;
			}
		}
	}
	&__image {
		padding: 10px;
		border: 1px solid #000000;
		border-radius: 10px;
		box-sizing: border-box;
		height: 100%;
	}
}
</style>
