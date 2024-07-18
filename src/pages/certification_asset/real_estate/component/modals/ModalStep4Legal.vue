<template>
	<div>
		<ValidationObserver
			tag="form"
			ref="formLegal"
			@submit.prevent="validateLegal"
		>
			<div
				class="modal-detail d-flex justify-content-center align-items-center"
			>
				<div class="card">
					<div class="container-title">
						<div class="d-lg-flex d-block shadow-bottom">
							<h2 class="title">Thông tin về pháp lý tài sản</h2>
						</div>
					</div>
					<div class="contain-detail">
						<div class="row">
							<div class="col-12 col-xl-6">
								<div class="row flex-column h-100">
									<div class="col">
										<InputCategory
											v-model="form.appraise_law_id"
											vid="appraise_law_id"
											label="Loại pháp lý"
											rules="required"
											:options="optionsJuridicals"
											@change="handleChangeTypeLegal"
										/>
									</div>
									<div v-if="form.appraise_law_id === 0" class="col">
										<InputText
											v-model="form.description"
											vid="description"
											class="form-group-container"
											label="Tên pháp lý"
											rules="required"
										/>
									</div>
									<div class="col">
										<div class="row">
											<InputTextarea
												v-model="form.date"
												vid="date"
												class="form-group-container col-6"
												label="Số pháp lý"
												rules="required"
												:autosize="true"
											/>
											<InputDatePicker
												v-model="form.law_date"
												vid="law_date"
												label="Ngày pháp lý"
												placeholder="Ngày/tháng/năm"
												class="form-group-container col-6"
												formatDate="DD/MM/YYYY"
												@change="changeLegalDate"
											/>
										</div>
									</div>
									<div
										class="col input-contain"
										v-if="form.appraise_law_id !== 0"
									>
										<div class="row">
											<InputText
												v-model="form.duration"
												vid="duration"
												label="Thời hạn sử dụng"
												class="form-group-container col-6"
											/>

											<InputTextarea
												v-model="form.origin_of_use"
												vid="origin_of_use"
												label="Nguồn gốc sử dụng"
												class="form-group-container col-6"
												:autosize="true"
											/>
										</div>
									</div>
									<div
										class="col"
										ref="purposeDetails"
										v-if="
											form.appraise_law_id !== 0 &&
												form.purpose_details.length > 0
										"
									>
										<div
											class="row"
											v-for="(itemPurpose, index) in form.purpose_details"
											:key="index"
										>
											<div class="col-12 col-lg-6 item_land input-contain">
												<InputCategoryCustom
													v-model="itemPurpose.land_type_purpose_id"
													vid="land_type_purpose_id"
													class="form-group-container"
													label="Mục đích sử dụng"
													:options="optionsTypePurposes"
													@change="changeLandtypepurpose($event, index)"
												/>
											</div>
											<div
												class="col-12 col item_land input-contain"
												:class="[
													form.purpose_details.length > 1
														? 'col-lg-5'
														: 'col-lg-6'
												]"
											>
												<InputAreaCustom
													v-model="itemPurpose.total_area"
													vid="total_area"
													label="Diện tích"
													class="form-group-container"
													@change="changeTotal_area($event, index)"
												/>
											</div>
											<div
												v-if="form.purpose_details.length > 1"
												class="button_delete_land col-12 col-lg-1 d-flex align-items-end p-0"
											>
												<button
													class="btn-delete"
													type="button"
													@click="handleDeletePurpose(index)"
												>
													<img
														alt="delete_land"
														src="@/assets/icons/ic_delete_2.svg"
													/>
												</button>
											</div>
										</div>
										<div class="row">
											<div class="d-flex justify-content-end w-100 pr-0">
												<button
													class="btn text-warning btn-ghost btn-add pr-0"
													type="button"
													@click="handleAddPurpose"
												>
													<img
														alt="add"
														src="@/assets/icons/ic_add-white.svg"
														class="mr-0"
													/>
													+ Thêm
												</button>
											</div>
										</div>
									</div>
									<div
										class="col"
										ref="landDetails"
										v-if="
											form.appraise_law_id !== 0 && form.land_details.length > 0
										"
									>
										<div
											class="row"
											v-for="(itemLand, index) in form.land_details"
											:key="index"
										>
											<div class="col-12 col-lg-6 item_land input-contain">
												<InputText
													v-model="itemLand.doc_no"
													vid="doc_num"
													label="Số tờ"
													class="form-group-container"
													@change="changeDocNo($event, index)"
												/>
											</div>
											<div
												class="col-12 col item_land input-contain"
												:class="[
													form.land_details.length > 1 ? 'col-lg-5' : 'col-lg-6'
												]"
											>
												<InputText
													v-model="itemLand.land_no"
													vid="plot_num"
													label="Số thửa"
													class="form-group-container"
													@change="changeLandNo($event, index)"
												/>
											</div>
											<div
												v-if="form.land_details.length > 1"
												class="button_delete_land col-12 col-lg-1 d-flex align-items-end p-0"
											>
												<button
													class="btn-delete"
													type="button"
													@click="handleDeleteLand(index)"
												>
													<img
														alt="delete_land"
														src="@/assets/icons/ic_delete_2.svg"
													/>
												</button>
											</div>
										</div>
										<div class="row">
											<div class="d-flex justify-content-end w-100 pr-0">
												<button
													class="btn text-warning btn-ghost btn-add pr-0"
													type="button"
													@click="handleAddLandDoc"
												>
													<img
														alt="add"
														src="@/assets/icons/ic_add-white.svg"
														class="mr-0"
													/>
													+ Thêm
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12 col-xl-6">
								<div class="row flex-column h-100">
									<div class="col input-contain">
										<InputText
											v-model="form.certifying_agency"
											vid="certifying_agency"
											label="Cơ quan các cấp xác nhận"
											rules="required"
										/>
									</div>
									<div class="col" v-if="form.appraise_law_id !== 0">
										<InputText
											v-model="form.legal_name_holder"
											class="form-group-container"
											vid="name_building"
											label="Người đứng tên pháp lý"
											@change="changeLegal"
											rules="required"
										/>
									</div>
									<div class="col input-contain">
										<InputTextarea
											v-model="form.note"
											vid="note"
											label="Thông tin khác"
											class="form-group-container"
											:rows="contentRowsNote"
										/>
									</div>

									<div class="col input-contain">
										<InputTextarea
											v-model="form.content"
											vid="content"
											label="Nội dung"
											rules="required"
											class="form-group-container"
											:rows="contentRows"
										/>
									</div>
								</div>
							</div>
							<!-- <div class="card" :style="checkMobile ? { 'margin-bottom': '150px' } : {}"> -->
							<div class="card-title" style="margin-bottom: 20px">
								<div class="d-flex justify-content-between align-items-center">
									<div
										class="row d-flex justify-content-between align-items-center"
									>
										<h3 class="title ml-1">
											Tài liệu đính kèm
											<label
												:for="'image_property_upload'"
												style="color: orange; cursor: pointer"
											>
												<font-awesome-icon icon="cloud-upload-alt" size="1x" />
											</label>
										</h3>

										<input
											class="btn-upload"
											type="file"
											ref="file"
											:id="'image_property_upload'"
											multiple
											accept="image/png, image/gif, image/jpeg, image/jpg, .doc, .docx, .xlsx, .xls, application/pdf"
											@change="onUploadDocument($event)"
											style="display: none"
										/>
									</div>
								</div>
								<div class="row mt-3">
									<div
										v-for="(file, index) in form.document_file"
										:key="index"
										class="col-3"
									>
										<div
											style="cursor: pointer"
											@click="downloadOtherFile(file)"
											class="d-flex"
										>
											<img
												class="mr-1"
												style="width: 1rem"
												src="@/assets/icons/ic_taglink.svg"
												alt="tag_2"
											/>
											<div
												class="mr-3 text-truncate"
												style="font-weight: bold; color: #3d4d65"
												:id="'file' + index"
											>
												{{ file.originalName }}
											</div>
											<b-tooltip :target="'file' + index" placement="bottom">
												{{ file.originalName }}
											</b-tooltip>
											<img
												style="cursor: pointer; width: 1rem"
												@click="deleteOtherFile(file, index)"
												src="@/assets/icons/ic_delete_2.svg"
												alt="tag_2"
											/>
										</div>
									</div>
								</div>

								<!-- </div> -->
							</div>

							<!-- <div class="col-12 col-xl-12">
								<div
									class="card"
									:style="checkMobile ? { 'margin-bottom': '150px' } : {}"
								>
									<div class="card-title">
										<div
											class="d-flex justify-content-between align-items-center"
										>
											<h3 class="title">Tài liệu đính kèm</h3>
										</div>
									</div>
									<div class="card-body card-info">
										<div class="row">
											<div class="col-12 mt-3">
												<div
													class="input_upload_file d-flex justify-content-center align-items-center"
												>
													<font-awesome-icon
														:style="{ color: 'orange', position: 'absolute' }"
														icon="cloud-upload-alt"
														size="5x"
													/>
													<input
														class="btn-upload"
														type="file"
														ref="file"
														id="image_property"
														multiple
														accept="image/png, image/gif, image/jpeg, image/jpg, .doc, .docx, .xlsx, .xls, application/pdf"
														@change="onUploadDocument($event)"
													/>
												</div>
											</div>
										</div>
										<div class="row mt-3">
											<div
												v-for="(file, index) in form.document_file"
												:key="index"
												class="d-flex"
											>
												<div
													style="cursor: pointer;"
													@click="downloadOtherFile(file)"
													class="d-flex"
												>
													<img
														class="mr-1"
														style="width: 1rem;"
														src="@/assets/icons/ic_taglink.svg"
														alt="tag_2"
													/>
													<div class="mr-3">{{ file.name }}</div>
												</div>
												<img style="cursor: pointer" class="mr-1" @click="downloadOtherFile(file)" src="@/assets/icons/ic_taglink.svg" alt="tag_2"/>
											<div class="mr-3">{{file.name}}</div>
												<img
													style="cursor: pointer; width: 1rem;"
													@click="deleteOtherFile(file, index)"
													src="@/assets/icons/ic_delete_2.svg"
													alt="tag_2"
												/>
											</div>
										</div>
									</div>
								</div> 
							 </div> -->
						</div>
					</div>
					<div class="container-title container-title__footer">
						<div class="d-lg-flex d-block justify-content-end shadow-bottom">
							<button class="btn btn-white" type="button" @click="handleCancel">
								<img
									src="@/assets/icons/ic_cancel.svg"
									style="margin-right: 5px"
									alt="cancel"
								/>
								Trở lại
							</button>
							<button
								class="btn btn-white btn-orange text-nowrap"
								type="button"
								@click.prevent="validateLegal"
							>
								<img
									src="@/assets/icons/ic_save.svg"
									style="margin-right: 5px"
									alt="save"
								/>
								Lưu
							</button>
						</div>
					</div>
				</div>
			</div>
		</ValidationObserver>
		<!-- <ModalViewDocument
			v-if="isShowPrint"
			@cancel="isShowPrint = false"
			:filePrint="filePrint"
			:title="title"
		/> -->
		<ModalDelete
			v-if="openModalDelete"
			@cancel="openModalDelete = false"
			@action="handleDelete"
		/>
	</div>
</template>

<script>
import InputNumberNoneFormat from "@/components/Form/InputNumberNoneFormat";
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputAreaCustom from "@/components/Form/InputAreaCustom";
import InputCategoryCustom from "@/components/Form/InputCategoryCustom";
import WareHouse from "@/models/WareHouse";
import ModalDelete from "@/components/Modal/ModalDelete";
import File from "@/models/File";
import { BTooltip } from "bootstrap-vue";
// import ModalViewDocument from "./component/modals/ModalViewDocument";
// import moment from 'moment'
export default {
	name: "ModalBuildingDetail",
	props: ["data", "juridicals", "provinceName", "full_address", "indexEdit"],
	components: {
		InputCategory,
		InputText,
		InputTextarea,
		InputNumberNoneFormat,
		InputDatePicker,
		InputAreaCustom,
		InputCategoryCustom,
		ModalDelete,
		"b-tooltip": BTooltip
		// ModalViewDocument
	},
	data() {
		return {
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			showCardDetailFile: false,
			isShowPrint: false,
			filePrint: null,
			openModalDelete: false,
			indexDelete: -1,
			file: "",
			link_file_delete: null,
			contentRows: 3,
			contentRowsNote: 6,
			type_purposes: []
		};
	},

	computed: {
		optionsJuridicals() {
			return {
				data: this.juridicals,
				id: "id",
				key: "content"
			};
		},
		optionsTypePurposes() {
			return {
				data: this.type_purposes,
				id: "id",
				key: "description"
			};
		}
	},
	mounted() {
		this.setContentRows();
	},
	async beforeMount() {
		this.getDictionaryLand();
	},
	methods: {
		async getDictionaryLand() {
			const resp = await WareHouse.getDictionariesLand();
			this.type_purposes = [...resp.data];
			this.type_purposes.forEach(item => {
				item.description = this.formatSentenceCase(item.description);
			});
		},
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
		async getAppraiseLaws() {
			await Certificate.getAppraiseLaws().then(resp => {
				if (resp.data && resp.data.phap_ly) {
					this.juridicals = resp.data.phap_ly;
					this.juridicals.push({
						content: "Văn bản pháp lý khác",
						created_at: new Date(),
						date: "",
						deleted_at: null,
						document_type: "",
						id: 0,
						is_defaults: false,
						provinces: "Tất cả",
						type: "PHAP_LY"
					});
				} else {
					this.juridicals = [];
				}
			});
		},
		changeLegalDate(event) {
			if (event) {
				this.form.law_date = event;
			}
		},
		handleAddLandDoc() {
			this.form.land_details.push({
				doc_no: "",
				land_no: ""
			});
			this.setContentRows();
		},
		handleAddPurpose() {
			this.form.purpose_details.push({
				land_type_purpose_id: "",
				total_area: ""
			});
			this.setContentRows();
		},
		changeLegal() {
			this.getContent();
		},
		changeDocNo(event, index) {
			if (event) {
				this.form.land_details[index].doc_no = event;
			} else {
				this.form.land_details[index].doc_no = "";
			}
			this.getContent();
		},
		changeLandNo(event, index) {
			if (event) {
				this.form.land_details[index].land_no = event;
			} else {
				this.form.land_details[index].land_no = "";
			}
			this.getContent();
		},
		changeLandtypepurpose(event, index) {
			if (event) {
				this.form.purpose_details[index].land_type_purpose_id = event;
			} else {
				this.form.purpose_details[index].land_type_purpose_id = "";
			}
			this.getContent();
		},
		changeTotal_area(event, index) {
			if (event) {
				this.form.purpose_details[index].total_area = event;
			} else {
				this.form.purpose_details[index].total_area = "";
			}
			this.getContent();
		},
		async getContent() {
			let land_description = "";
			// console.log('data', this.form)
			const map = new Map();
			if (this.form.land_details.length > 0) {
				await this.form.land_details.forEach(item => {
					let land_no_description = "";
					let land_description_item = "";
					if (!map.has(item.doc_no)) {
						map.set(item.doc_no, true);
						let filterArray = this.form.land_details.filter(
							itemFilter => item.doc_no === itemFilter.doc_no
						);
						land_description_item = "";
						land_no_description = "";
						let land_no_number = null;
						if (filterArray.length > 0) {
							filterArray.forEach(landItem => {
								land_no_number = landItem.land_no;
								if (!land_no_description) {
									land_no_description =
										`${land_no_description} ` +
										`${land_no_number === 0 ? 0 : land_no_number || ""}`;
								} else
									land_no_description =
										`${land_no_description}, ` + `${land_no_number}`;
							});
						}
						land_description_item =
							"thửa đất số" +
							`${land_no_description || ""}` +
							" tờ bản đồ số " +
							`${item.doc_no || item.doc_no === 0 ? item.doc_no : ""}`;
						if (!land_description) {
							land_description =
								`${land_description}` + `${land_description_item}`;
						} else
							land_description =
								`${land_description}, ` + `${land_description_item}`;
					}
				});
				this.setContentRows();
			}
			this.form.content =
				(await "Chứng nhận ") +
				`${
					this.form.legal_name_holder ? this.form.legal_name_holder + " " : ""
				}` +
				"được quyền sử dụng đất và CTXD thuộc " +
				`${land_description} ` +
				`${this.full_address ? this.full_address : ""}.`;
			land_description = "";
		},
		getProvince() {
			this.form.certifying_agency = `Sở Tài nguyên và Môi trường ${
				this.provinceName &&
				this.provinceName.toLowerCase().includes("thành phố")
					? this.provinceName
					: this.provinceName
					? "Tỉnh " + this.provinceName
					: ""
			}`;
		},
		setContentRows() {
			if (this.form.appraise_law_id === 0) {
				this.contentRows = 9;
			} else this.contentRows = this.form.land_details.length * 3;
		},
		handleDeleteLand(index) {
			this.form.land_details.splice(index, 1);
			this.setContentRows();
			this.getContent();
		},
		handleDeletePurpose(index) {
			this.form.purpose_details.splice(index, 1);
			this.setContentRows();
			this.getContent();
		},
		handleChangeTypeLegal(event) {
			if (event === 0) {
				this.form.content = "";
				this.form.certifying_agency = "";
				this.form.land_details = [
					{
						doc_no: "",
						land_no: ""
					}
				];
			} else {
				this.form.description = "";
				this.getContent();
				this.getProvince();
			}
			this.setContentRows();
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		async validateLegal() {
			const valid = await this.$refs.formLegal.validate();
			// console.log('form', this.form)
			if (valid) {
				let getLaw = await this.juridicals.filter(
					item => item.id === this.form.appraise_law_id
				);
				this.form.law = getLaw[0];
				let checkDocLand = true;
				this.form.land_details.forEach(item => {
					if (
						(item.doc_no || item.doc_no === 0) &&
						(item.land_no || item.land_no === 0)
					) {
						checkDocLand = false;
					}
				});
				if (checkDocLand && this.form.appraise_law_id !== 0) {
					return this.$toast.open({
						message: "Vui lòng nhập Số tờ , Số thửa",
						type: "error",
						position: "top-right"
					});
				}
				await this.handleAction();
			}
		},
		handleAction() {
			this.$emit("action", this.form);
		},
		async onImageChange(e) {
			const formData = new FormData();
			let check = true;
			let files = e.target.files;
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];
				if (
					this.file.type === "image/png" ||
					this.file.type === "image/jpeg" ||
					this.file.type === "image/jpg" ||
					this.file.type === "image/gif" ||
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
					this.file.type === "application/pdf"
				) {
					let link = URL.createObjectURL(this.file);
					this.file.link = link;
				} else {
					check = false;
					this.$toast.open({
						message: "Hình không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
			if (check) {
				this.showCardDetailFile = true;
				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						files[i].isUpload = false;
						formData.append("files[" + i + "]", files[i]);
					}
					let res = null;
					if (this.dataPC.id) {
						res = await File.uploadFilePreCertificate(
							formData,
							this.dataPC.id,
							this.type
						);
						if (res.data) {
							const tempList = [];
							for (let index = 0; index < res.data.data.length; index++) {
								const element = res.data.data[index];
								if (element.type_document === this.type) {
									tempList.push(element);
								}
							}

							this.preCertificateOtherDocuments[this.type] = [...tempList];
							this.lstFile = [...tempList];

							this.$toast.open({
								message: "Thêm file thành công",
								type: "success",
								position: "top-right",
								duration: 3000
							});
						}
					} else {
						this.lstFile = [...this.lstFile, ...files];
						this.dataPC.uploadFile = this.lstFile;
					}
				}
			}
		},
		async onUploadDocument(e) {
			const formData = new FormData();
			let check = true;
			let files = e.target.files;
			if (!files.length) {
				return;
			}

			// Khai báo một mảng để lưu trữ các tệp được chọn
			let selectedFiles = [];

			for (let i = 0; i < files.length; i++) {
				let file = files[i]; // Lưu trữ từng file vào biến file

				if (
					file.type === "image/png" ||
					file.type === "image/jpeg" ||
					file.type === "image/jpg" ||
					file.type === "image/gif" ||
					file.type ===
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
					file.type ===
						"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
					file.type === "application/pdf"
				) {
					// Nếu file hợp lệ, thêm vào mảng selectedFiles
					selectedFiles.push(file);
				} else {
					check = false;
					this.$toast.open({
						message: "File dữ liệu không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}

			if (check && selectedFiles.length) {
				for (let i = 0; i < selectedFiles.length; i++) {
					formData.append("files[" + i + "]", selectedFiles[i]);
				}
				let res = null;
				res = await File.uploadDocumentLaw(formData);
				if (res.data) {
					if (this.form.document_file) {
						this.form.document_file.push(...res.data.data);
					} else {
						this.form.document_file = res.data.data;
					}
					console.log("", this.form.document_file);
					this.$toast.open({
						message: "Thêm file thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
				}
			}
		},

		downloadOtherFile(file) {
			fetch(file.link)
				.then(response => {
					if (response.ok) return response.blob();
					throw new Error("Network response was not ok.");
				})
				.then(blobContent => {
					// Now we have the file content as a Blob
					let objectURL = window.URL.createObjectURL(blobContent);
					let link = document.createElement("a");
					link.href = objectURL;
					link.setAttribute("download", file.originalName);
					document.body.appendChild(link);
					link.click();

					// Clean up link element from DOM and revoke the blob URL
					document.body.removeChild(link);
					window.URL.revokeObjectURL(objectURL);
				})
				.catch(error => {
					console.error(
						"There has been a problem with your fetch operation:",
						error
					);
				});
		},
		deleteOtherFile(file, index) {
			this.openModalDelete = true;
			this.indexDelete = index;
			this.link_file_delete = file.link;
		},
		async handleDelete() {
			const formData = new FormData();
			formData.append("appraise_law_id", this.form.id);
			formData.append("link_file_delete", this.link_file_delete);
			const res = await File.deleteDocumentLaw({ data: formData });
			if (res.data) {
				this.form.document_file.splice(this.indexDelete, 1);
				// this.files = this.form.files
				this.$emit("deleteDoc", this.form.document_file, this.indexEdit);
				this.$toast.open({
					message: "Xóa thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},

		getPreviewUrl(file) {
			if (file.isUpload === false) {
				if (
					file.type === "image/png" ||
					file.type === "image/jpeg" ||
					file.type === "image/jpg" ||
					file.type === "image/gif"
				) {
					filePrint.value = {
						link: file.link,
						type: "image"
					};
					isShowPrint.value = true;
				}
				//  else if (file.type === "application/pdf") {
				// 	filePrint.value = {
				// 		link: blob,
				// 		type: "pdf"
				// 	};
				// 	isShowPrint.value = true;
				// }
				else {
					other.value.toast.open({
						message: "Tạm thời chưa hỗ trợ xem trước loại file này",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			} else if (file.link) {
				fetch(file.link)
					.then(response => response.blob())
					.then(blob => {
						// let reader = new FileReader();
						// reader.onload = event => {
						// 	mammoth
						// 		.convertToHtml({ arrayBuffer: event.target.result })
						// 		.then(result => {
						// 			filePrint.value = result.value;
						// 			isShowPrint.value = true;
						// 		})
						// 		.catch(console.error);
						// };
						// reader.readAsArrayBuffer(blob);

						if (
							file.type === "png" ||
							file.type === "jpeg" ||
							file.type === "jpg" ||
							file.type === "gif"
						) {
							filePrint.value = {
								link: URL.createObjectURL(blob),
								type: "image"
							};
						}
						//  else if (file.type === "pdf") {
						// 	filePrint.value = {
						// 		link: blob,
						// 		type: "pdf"
						// 	};
						// }
						else {
							other.value.toast.open({
								message: "Tạm thời chưa hỗ trợ xem trước loại file này",
								type: "error",
								position: "top-right",
								duration: 3000
							});
						}
						isShowPrint.value = true;
					})
					.catch(console.error);
			} else {
				other.value.toast.open({
					message: "Có lỗi xảy ra vui lòng thử lại",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return null;
			}
		},
		checkMobile(file) {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.btn-delete {
	cursor: pointer;
	display: flex;
	align-items: center;
	background: #ffffff;
	border: none;
	margin-bottom: 0.6rem;
	img {
		width: 100%;
		height: auto;
		min-width: 0.75rem;
	}
}
.title {
	font-size: 1.125rem;
	font-weight: 700;
	margin-bottom: 25px;
	color: #000000;
}
.modal-detail {
	position: fixed;
	z-index: 1031;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);
	.card {
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		max-width: 1300px;
		width: 100%;
		max-height: 90vh;
		margin-bottom: 0;
		padding: 35px 95px;
		@media (max-width: 787px) {
			padding: 20px 10px;
		}
		&-header {
			border-bottom: 1px solid #dddddd;
			h3 {
				color: #333333;
			}
			img {
				cursor: pointer;
			}
		}
		&-body {
			text-align: center;
			p {
				color: #333333;
				margin-bottom: 40px;
			}

			.btn__group {
				.btn {
					max-width: 150px;
					width: 100%;
					margin: 0 10px;
				}
			}
		}
	}
}
.card {
	.contain-detail {
		overflow-y: auto;
		overflow-x: hidden;
		margin-top: 20px;
		&::-webkit-scrollbar {
			width: 2px;
		}
	}
	&-title {
		background: #f3f2f7;
		padding: 16px 20px;
		margin-bottom: 0;
		.title {
			font-size: 1.125rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}
	&-table {
		border-radius: 5px;
		background: #ffffff;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		width: 99%;
		margin: 50px auto 50px;
	}
	&-body {
		padding: 35px 30px 40px;
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
.img {
	margin-right: 13px;
}
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 75px;
	&-title {
		background: #f3f2f7;
		padding: 16px 20px;
		margin-bottom: 0;
		.title {
			font-size: 1.125rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}
	&-body {
		padding: 35px 30px 40px;
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
.card__order {
	max-width: 50%;
	margin-bottom: 1.25rem;
	@media (max-width: 767px) {
		max-width: 100%;
	}
}
.btn {
	&-white {
		max-height: none;

		line-height: 19.07px;
		margin-right: 15px;
		&:last-child {
			margin-right: 0;
		}
	}
	&-contain {
		margin-bottom: 55px;
	}
}
.d-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	grid-column-gap: 8.9%;
	&:first-child {
		margin-top: 0;
	}
	&__checkbox {
		grid-template-columns: 1fr 1fr;
	}
	@media (max-width: 767px) {
		grid-template-columns: 1fr;
	}
}
.content {
	&-detail {
	}
	&-title {
		color: #555555;
		margin-bottom: 5px;

		font-weight: 500;
	}
	&-name {
		font-size: 1.125rem;
		color: #000000;
		margin-bottom: 15px;
		font-weight: 600;
		&__code {
			color: #faa831;
		}
	}
}
.contain-table {
	@media (max-width: 767px) {
		overflow-y: hidden;
		overflow-x: auto;
	}
	.table-property {
		width: 100%;
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
		}
	}
	tbody {
		td {
			border: 1px solid #e5e5e5;
			&:first-child {
				border-left: none;
				width: 180px;
			}
			&:last-child {
				border-right: none;
			}
			box-sizing: border-box;
			padding: 14px;
		}
	}
}
.img-content {
	color: #000000;

	font-weight: 600;
	span {
		font-weight: 500;
		margin-left: 10px;
	}
}
.input-code {
	color: #000000;
	border-radius: 5px;
	width: 180px;
	border: 1px solid #000000;
	background: #f5f5f5;
	height: 35px;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
}
.img-dropdown {
	cursor: pointer;
	width: 18px;
	&__hide {
		transform: rotate(90deg);
		transition: 0.3s;
	}
}
//.img-contain {
//  max-width: 200px;
//  max-height: 200px;
//  height: auto;
//  margin-left: 20px;
//  &__table{
//    margin: auto;
//    max-width: 50px;
//    img{
//      cursor: pointer;
//      display: flex;
//      justify-content: center;
//    }
//  }
//  img{
//    object-fit: contain;
//    width: 100%;
//    height: auto;
//    max-width: 200px;
//    max-height: 200px;
//  }
//}
.img-contain {
	aspect-ratio: 1/1;
	overflow: hidden;
	img {
		height: 100%;
		cursor: pointer;
		object-fit: cover;
	}
	&__table {
		margin: auto;
		max-width: 50px;
		max-height: 50px;
		img {
			object-fit: cover;
			object-position: top;
			cursor: pointer;
			display: flex;
			justify-content: center;
			max-width: 50px;
			max-height: 50px;
		}
	}
}
.container-title {
	margin: -35px -95px auto;
	padding: 35px 95px 0;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	.title {
		font-size: 1.2rem;
		@media (max-width: 767px) {
			font-size: 1.125rem;
		}
	}
	&__footer {
		margin: auto -95px -35px;
		padding: 20px 95px 20px;
		@media (max-width: 767px) {
			.btn-white {
				margin-bottom: 20px;
			}
		}
	}
}
.card {
	border-radius: 5px;
	// box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;
	margin-top: 1rem;
	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 20px 25px 10px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-bottom: 0;
		}
	}

	&-body {
		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;

			&-highlight {
				background: rgba(252, 194, 114, 0.53);
				text-align: center;
				padding: 10px 0;
				border-radius: 2px;
			}
		}
	}

	&-land {
		position: relative;
		padding: 0;
	}
}
.container-img {
	padding: 0.75rem 0;
	border: 1px solid #0b0d10;
}
.input_upload_file {
	background-image: repeating-linear-gradient(
			0deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			90deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			180deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			270deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		);
	background-size: 2px 100%, 100% 2px, 2px 100%, 100% 2px;
	background-position: 0 0, 0 0, 100% 0, 0 100%;
	background-repeat: no-repeat;
	// border: dotted 1px solid #B6D5F3;
	cursor: pointer;
	min-height: 8rem;
	border-radius: 5px;
}
.btn-upload {
	left: 0;
	opacity: 0;
	width: 100%;
	min-height: 10rem;
	cursor: pointer;
	// position: absolute;
}
.btn-upload-mini {
	left: 0;
	opacity: 0;
	width: 2rem;
	// min-height: 10rem;
	cursor: pointer;
	// position: absolute;
	padding: 0;
	border: 0;
}
.card-title {
	width: 100%;
}
</style>
