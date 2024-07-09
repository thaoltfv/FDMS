<template>
	<div class="col-12" :style="isMobile ? { padding: '0' } : {}">
		<ModalDelete
			v-if="openModalDelete"
			@cancel="openModalDelete = false"
			@action="handleDelete"
		/>
		<ModalViewDocument
			v-if="isShowPreview"
			@cancel="isShowPreview = false"
			:filePrint="filePreview"
			:title="title"
		/>
		<ModalNotificationCertificate
			v-if="isReUpload"
			@cancel="isReUpload = false"
			v-bind:notification="reUploadMessage"
			@action="openUploadFile"
		/>
		<div class="card" :style="isMobile ? {} : {}">
			<div class="card-title">
				<div class=" align-items-center">
					<div class="d-flex justify-content-between">
						<h3 class="title ml-3 mt-2">
							Giấy tờ liên quan
							<!-- <b-tooltip
								:target="'download_all_auto_export_document'"
								placement="auto"
								>Tải xuống tất cả Giấy tờ liên quan
							</b-tooltip> -->

							<!-- <font-awesome-icon
								:id="'download_all_auto_export_document'"
								@click="handleDownloadAll('TaiLieuTuDongHanhChinh')"
								:style="{
									color: 'orange',
									height: '1.5rem',
									width: '2rem',
									cursor: 'pointer'
								}"
								icon="download"
								size="1x"
								class="mr-2"
							/>
							<b-tooltip
								:target="'download_all_export_document'"
								placement="auto"
								>Tải xuống tất cả tài liệu
							</b-tooltip>
							<font-awesome-icon
								:id="'download_all_export_document'"
								@click="handleDownloadAll('TaiLieuHanhChinh')"
								:style="{
									color: '#2682bfad',
									height: '1.5rem',
									width: '2rem',
									cursor: 'pointer'
								}"
								icon="download"
								size="1x"
								class="mr-2"
							/> -->
						</h3>
						<a-dropdown v-if="!isMobile">
							<a-button class="mr-3">
								<a-icon type="download" />
							</a-button>
							<template #overlay>
								<a-menu @click="handleMenuClick">
									<a-menu-item key="1">
										Tải xuống tất cả Giấy tờ liên quan chính thức
									</a-menu-item>
									<a-menu-item key="2">
										Tải xuống tất cả Giấy tờ liên quan tự động
									</a-menu-item>
								</a-menu>
							</template>
						</a-dropdown>
					</div>

					<!-- <img
						class="img-dropdown"
						:class="!showCardDetailFile ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailFile = !showCardDetailFile"
					/> -->
				</div>
			</div>

			<div class="card-body card-info">
				<div class="ml-n3 mt-2 row" :key="keyRefresh">
					<div
						class="mb-4 col-lg-4 col-md-6 col-sm-12"
						v-for="(file, index) in lstFile"
						:key="index"
					>
						<div
							class="d-flex flex-column input_download_certificate mx-1 justify-content-between"
							style="height: auto "
						>
							<div class="d-flex flex-column">
								<div class="d-flex justify-content-between  w-100">
									<div class="ml-3 title_input_content title_input_download">
										{{ file.nameTitle }}
									</div>
									<div class="d-flex mr-3">
										<!-- <label
										:style="{ visibility: allowEdit ? 'visible' : 'hidden' }"
										class="d-flex align-items-center justify-content-end col-1 pr-3"
									> -->
										<b-tooltip
											:target="'preview_' + file.type_document"
											placement="auto"
											>Xem trước tài liệu tự động
											{{ " " + file.nameTitle }}</b-tooltip
										>

										<div
											v-if="file.isAutoExport"
											:id="'preview_' + file.type_document"
											@click="handleViewDocument(file.type_document)"
											class="mr-2"
										>
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <font-awesome-icon
											v-if="file.isAutoExport"
											:id="'preview_' + file.type_document"
											@click="handleViewDocument(file.type_document)"
											:style="{
												color: '#2682bfad',
												height: '1.5rem',
												width: '2rem',
												cursor: 'pointer'
											}"
											icon="search"
											size="1x"
											class="mr-2"
										/> -->
										<b-tooltip
											:target="'download_' + file.type_document"
											placement="auto"
											>Tải xuống tài liệu tự động
											{{ " " + file.nameTitle }}</b-tooltip
										>
										<div
											v-if="file.isAutoExport && !isMobile"
											:id="'download_' + file.type_document"
											@click="handleDownloadAutoDocument(file.type_document)"
											class="mr-2"
										>
											<img
												src="@/assets/icons/ic_download_3.png"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<!-- <font-awesome-icon
											v-if="file.isAutoExport"
											:id="'download_' + file.type_document"
											@click="handleDownloadAutoDocument(file.type_document)"
											:style="{
												color: '#2682bfad',
												height: '1.5rem',
												width: '2rem',
												cursor: 'pointer'
											}"
											icon="download"
											size="1x"
											class="mr-2"
										/> -->
										<div
											v-if="
												allowEdit &&
													statusText !== 'Hoàn thành' &&
													statusText !== 'In hồ sơ' &&
													statusText !== 'Bàn giao khách hàng' &&
													statusText !== 'Hủy' &&
													!isMobile
											"
											class="d-flex align-items-center"
										>
											<b-tooltip
												:target="'upload_' + file.type_document"
												placement="auto"
												>Tải lên tài liệu {{ " " + file.nameTitle }}</b-tooltip
											>
											<div
												:id="'upload_' + file.type_document"
												@click="checkFileUpload(file)"
											>
												<img
													src="@/assets/icons/ic_upload.png"
													alt="search"
													class="img_document_action"
												/>
											</div>
											<!-- <font-awesome-icon
												:id="'upload_' + file.type_document"
												@click="checkFileUpload(file)"
												:style="{
													color: '#2682bfad',
													height: '2rem',
													width: '2rem',
													cursor: 'pointer'
												}"
												icon="cloud-upload-alt"
												size="2x"
											/> -->
											<!-- <input
												class="btn-upload-mini"
												@click="checkFileUpload(file)"
											/> -->
											<input
												type="file"
												:ref="file.type_document"
												:id="'image_property' + file.type_document"
												accept=".doc, .docx, application/pdf"
												@change="onImageChange($event, file.type_document)"
												hidden
											/>
										</div>
										<!-- </label> -->
									</div>
								</div>
								<hr
									v-if="file.name"
									style="border: none; height: 1px; background: #333; margin: 0.5rem 10px;"
								/>
								<!-- Divider -->
								<div
									v-if="file.name"
									class="row d-flex justify-content-between mt-1"
									style="margin-left:1px;margin-right:1px;position: relative;padding:  0px;"
								>
									<div
										class="d-flex align-items-center col-10"
										@click="downloadOtherFile(file)"
									>
										<img
											class="mr-2"
											style="width: 1.5rem;"
											src="@/assets/icons/ic_taglink_2.svg"
											alt="tag_2"
										/>
										<div
											class="title_input_content-2 title_input_download cursor_pointer"
											style="color: #45AAF2;"
										>
											{{ file.name }}
										</div>
									</div>
									<div
										class="d-flex align-items-center justify-content-end col-2"
									>
										<div @click="handleViewDocumentUpload(file)" class="mr-2">
											<img
												src="@/assets/icons/ic_search_3.svg"
												alt="search"
												class="img_document_action"
											/>
										</div>
										<div
											v-if="
												allowEdit &&
													statusText !== 'Hoàn thành' &&
													statusText !== 'In hồ sơ' &&
													statusText !== 'Bàn giao khách hàng' &&
													statusText !== 'Hủy' &&
													!isMobile
											"
											class=""
										>
											<img
												@click="deleteOtherFile(file, index)"
												src="@/assets/icons/ic_delete_2.svg"
												alt="tag_2"
												class="img_document_action"
											/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import Certificate from "@/models/Certificate";
import ModalNotificationCertificate from "@/components/Modal/ModalNotificationCertificate";
// import ModalViewDocument from "@/components/PreCertificate/ModalViewDocument";
import ModalViewDocument from "@/pages/certification_brief/component/modals/ModalViewDocument";
import Vue from "vue";
import Icon from "buefy";
import _ from "lodash";
import ModalDelete from "@/components/Modal/ModalDelete";
import { BTooltip } from "bootstrap-vue";
import File from "@/models/File";
import axios from "@/plugins/axios";
import formMixin from "@/mixins/form.mixin";
Vue.use(Icon);
export default {
	props: {
		is_pc: {
			type: Boolean
		},
		allowEdit: {
			type: Boolean,
			default: true
		},
		idData: {
			type: Number
		},
		lstFileExport: {
			type: Array
		},
		permission: {
			type: Object
		},
		toast: {
			type: Object
		},
		statusText: {
			type: String
		}
	},
	data() {
		return {
			isShowPreview: false,
			filePreview: "",
			title: ""
		};
	},
	components: {
		ModalNotificationCertificate,
		ModalDelete,
		ModalViewDocument,
		"b-tooltip": BTooltip
	},
	setup(props, context) {
		const checkMobile = () => {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		};
		const showCardDetailFile = ref(true);
		const isMobile = ref(checkMobile());
		const lstFile = ref([
			{
				type_document: "GYC",
				nameTitle: "Giấy yêu cầu TĐG",
				isAutoExport: true
			},
			{ type_document: "HDTDG", nameTitle: "Hợp đồng", isAutoExport: true },
			{
				type_document: "BBTL",
				nameTitle: "Thanh lý hợp đồng",
				isAutoExport: true
			},
			{ type_document: "KHTDG", nameTitle: "Kế hoạch TĐG", isAutoExport: true },

			{
				type_document: "HSPL",
				nameTitle: "Hồ sơ pháp lý",
				isAutoExport: false
			},
			{
				type_document: "BBKSHT",
				nameTitle: "Biên bản khảo sát hiện trạng",
				isAutoExport: false
			}
		]);
		const lstFileOriginal = ref([
			{
				type_document: "GYC",
				nameTitle: "Giấy yêu cầu TĐG",
				isAutoExport: true
			},
			{ type_document: "HDTDG", nameTitle: "Hợp đồng", isAutoExport: true },
			{ type_document: "KHTDG", nameTitle: "Kế hoạch TĐG", isAutoExport: true },
			{
				type_document: "BBTL",
				nameTitle: "Thanh lý hợp đồng",
				isAutoExport: true
			},
			{
				type_document: "HSPL",
				nameTitle: "Hồ sơ pháp lý",
				isAutoExport: false
			},
			{
				type_document: "BBKSHT",
				nameTitle: "Biên bản khảo sát hiện trạng",
				isAutoExport: false
			}
		]);
		if (props.lstFileExport) {
			lstFile.value = lstFile.value.map(file => {
				const matchingElement = props.lstFileExport.find(
					element => element.type_document === file.type_document
				);
				return matchingElement ? { ...file, ...matchingElement } : file;
			});
		}

		const downloadOtherFile = async file => {
			if (props.permission.allowExport && file.name && !isMobile) {
				axios({
					url:
						process.env.API_URL +
						(props.is_pc
							? "/api/pre-certificates/export-document-pc/download/"
							: "/api/pre-certificates/export-document-certificate/download/") +
						file.id,
					method: "GET",
					responseType: "blob"
				}).then(response => {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement("a");
					link.href = url;
					link.setAttribute("download", file.name);
					document.body.appendChild(link);
					link.click();
					window.URL.revokeObjectURL(link);
					props.toast.open({
						message: `Tải xuống thành công`,
						type: "success",
						position: "top-right",
						duration: 3000
					});
				});
			} else if (!props.permission.allowExport) {
				props.toast.open({
					message: `Bạn không có quyền tải tài liểu sơ bộ này`,
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else {
				props.toast.open({
					message: `Tài liệu chưa được tải lên`,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		};

		const openModalDelete = ref(false);
		const fileDelete = ref({ id: null, isUpload: false });
		const deleteOtherFile = (file, index) => {
			openModalDelete.value = true;
			fileDelete.value = {
				id: file.id,
				isUpload: file.isUpload,
				index,
				nameTitle: file.nameTitle
			};
		};
		const keyRefresh = ref(0);
		const handleDelete = async () => {
			const formData = new FormData();
			formData.append("id", fileDelete.value.id);
			formData.append("is_pc", props.is_pc);
			formData.append("delete_what", fileDelete.value.nameTitle);
			const res = await File.deleteFilePreCertificateExport(formData);
			if (res.data) {
				lstFile.value[fileDelete.value.index] = {
					...lstFileOriginal.value[fileDelete.value.index]
				};
				keyRefresh.value++;
				props.toast.open({
					message: "Xóa thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else if (res.error) {
				props.toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			} else {
				props.toast.open({
					message: "Có lỗi xảy ra trong lúc xóa file",
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		};

		const isShowPrint = ref(false);
		const filePrint = ref(null);
		const reportType = ref(null);
		const isReUpload = ref(false);
		const reUploadMessage = ref("");
		return {
			reUploadMessage,
			keyRefresh,
			isReUpload,
			reportType,
			showCardDetailFile,
			isMobile,
			lstFile,
			openModalDelete,
			isShowPrint,
			filePrint,
			handleDelete,
			deleteOtherFile,
			downloadOtherFile
		};
	},
	methods: {
		handleViewDocument(type) {
			if (type === "GYC") {
				this.viewGYC();
			} else if (type === "HDTDG") {
				this.viewHDTDG();
			} else if (type === "KHTDG") {
				this.viewKHTDG();
			} else if (type === "BBTL") {
				this.viewBBTL();
			}
		},
		handleViewDocumentUpload(file) {
			this.filePreview = file.link;
			this.title = file.nameTitle;
			this.isShowPreview = true;
		},
		async viewGYC() {
			await Certificate.getPrintGYC(this.idData).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePreview = file.url;
					this.isShowPreview = true;
					this.title = "Giấy yêu cầu TĐG";
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewHDTDG() {
			await Certificate.getPrintHDTDG(this.idData).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePreview = file.url;
					this.isShowPreview = true;
					this.title = "Hợp đồng TĐG";
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewBBTL() {
			await Certificate.getPrintBBTL(this.idData).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePreview = file.url;
					this.isShowPreview = true;
					this.title = "Biên bản thanh lý hợp đồng";
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewKHTDG() {
			await Certificate.getPrintKHTDG(this.idData).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePreview = file.url;
					this.isShowPreview = true;
					this.title = "Kế hoạch TĐG";
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		handleDownloadAutoDocument(type) {
			this.$emit("handleDownloadAutoDocument", type);
		},
		handleDownloadAll(type) {
			this.$emit("handleDownloadAll", type);
		},
		truncateFilename(filename, limit) {
			if (!filename) return "";
			if (filename.length > limit) {
				return filename.substring(0, limit) + "...";
			}
			return filename;
		},
		checkFileUpload(file) {
			this.reportType = file.type_document;
			if (file.name) {
				this.isReUpload = true;
				this.reUploadMessage =
					file.nameTitle +
					" đã có" +
					"<br>" +
					"Bạn có muốn upload " +
					file.nameTitle +
					" mới ?";
			} else {
				this.openUploadFile();
			}
		},
		formatNumber(num) {
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		openUploadFile() {
			const id = "image_property" + this.reportType;
			document.getElementById(id).click();
		},
		handleMenuClick(e) {
			if (e.key === "1") {
				this.handleDownloadAll("TaiLieuHanhChinh");
			} else {
				this.handleDownloadAll("TaiLieuTuDongHanhChinh");
			}
		},
		async onImageChange(e, type) {
			this.reportType = type;
			const formData = new FormData();
			formData.append("is_pc", this.is_pc);
			formData.append("type", type);
			let check = true;
			let files = e.target.files;
			console.log(files);
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];

				let link = URL.createObjectURL(this.file);
				this.file.link = link;
			}
			if (check) {
				this.showCardDetailFile = true;
				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						files[i].isUpload = false;
						formData.append("files[" + i + "]", files[i]);
					}
					let res = null;
					if (this.idData) {
						res = await File.uploadFilePreCertificateExport(
							formData,
							this.idData
						);
						if (res.data) {
							console.log("Có data nè", res.data);
							const tempList = [];
							for (let index = 0; index < res.data.data.length; index++) {
								const element = res.data.data[index];
								tempList.push(element);
							}
							this.lstFile = this.lstFile.map(file => {
								const matchingElement = tempList.find(
									element => element.type_document === file.type_document
								);
								return matchingElement ? { ...file, ...matchingElement } : file;
							});

							this.$toast.open({
								message: "Thêm file thành công",
								type: "success",
								position: "top-right",
								duration: 3000
							});
						} else {
							this.$toast.open({
								message: "Có lỗi xảy ra vui lòng kiểm tra lại sau",
								type: "error",
								position: "top-right",
								duration: 3000
							});
						}
					}
				}
			}
		}
	}
};
</script>
<style scoped lang="scss">
.div_radio {
	margin-bottom: 0.5rem;
}
.form-map {
	height: 100%;
	flex: 1;
}

.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 15px;
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
.card-status {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;

	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}

.form-group-container {
	margin-top: 10px;
}

.color-black {
	color: #333333;
}

.img_document_action {
	width: 2rem;
	height: 2rem;
	cursor: pointer;
	background: #ffffff;
	min-width: 1.5rem;
	min-height: 1.5rem;
}
.btn-edit {
	cursor: pointer;
	display: flex;
	border-radius: 5.88235px;
	align-items: end;
	img {
		width: 20px;
		height: 14px;
		height: auto;
	}
}
.btn {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		// width: 100px;
		color: #fff;
		// margin: 15px 0 0;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
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
	width: 0rem;
	// min-height: 10rem;
	cursor: pointer;
	// position: absolute;
	padding: 0;
	border: 0;
}
.btn_list_appraise {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		min-width: 150px;
		color: #fff;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
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

.img-locate {
	cursor: pointer;
	position: absolute;
	right: 14px;
	top: 2.1rem;
	background-color: #f5f5f5;
	height: 2.1rem;
	width: 32px;
	display: grid;
	place-items: center;

	img {
		height: 60%;
	}
}

.text-error {
	color: #cd201f;
}

.select-group {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 16px 22px;

	.select-title {
		color: #00507c;
		font-weight: 700;
		white-space: nowrap;
		margin-bottom: unset !important;
	}
}

.content_form {
	padding-left: 0.75rem;
}
.border_disable {
	border-color: #d9d9d9 !important;
}
.detail_certification_brief {
	// padding: 0 1rem;
	margin-bottom: 60px;
	@media (max-width: 449px) {
		margin-bottom: 120px;
	}
}
.detail_certificate_1 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #b5e5ff;
	background-color: #eef9ff;
}
.detail_certificate_2 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #e8e8e8;
	background-color: #f6f7fb;
}
.margin_content_inline {
	margin-right: 10px;
}
.container_content {
	min-height: 20px;
	p {
		margin-bottom: unset !important;
	}
}

.btn {
	&-history {
		position: fixed;
		right: 0;
		top: 170px;
		z-index: 100;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
}
.input_download_certificate {
	position: relative;
	border: 1px solid #b6d5f3;
	border-radius: 5px;
	height: 3.85rem;
	padding: 0.85rem 0px;
}
.input_download_pre_certificate {
	position: relative;
	border: 1px solid #b6d5f3;
	border-radius: 5px;
	height: 3.85rem;
	padding: 0.85rem 0px;
}

.title_input_download {
	color: #00507c;
	font-weight: 600;
}
.img_input_download {
	margin-right: 10px;
	max-width: 2rem;
}

.title_input_content {
	font-size: 18px;
}

.title_input_content-2 {
	font-size: 18px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
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
.img-upload {
	margin-left: 20px;
	position: relative;
	width: 123px;
	height: 35px;
	color: #fff;
	background: #faa831;
	font-size: 1.125rem;
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
.table-wrapper {
	.ant-table-filter-dropdown-btns {
		background-color: white !important;
	}

	.ant-table-filter-dropdown-link.confirm {
		color: red;
	}

	/deep/ .ant-table-thead > tr > th {
		font-weight: 700 !important;
		background-color: #dee6ee !important;
		color: #3d4d65 !important;
		// border-right: 1px solid white !important;
	}
	/deep/ .ant-table-wrapper .ant-spin-container .ant-table {
		border: 1px solid #dee6ee;
	}

	/deep/ .ant-table-column-title {
		color: #00507c;
	}

	/deep/ .table-striped td {
		background-color: #f6f7fb;
		border-color: #dee6ee;
		border-width: 0;
	}

	/deep/ .ant-table-tbody,
	/deep/ .ant-table-body {
		box-shadow: none;
		min-height: 10vh;
	}
	/deep/ .ant-table-pagination {
		display: none;
	}

	.pagination-wrapper {
		margin-top: 16px;
		display: flex;
		justify-content: space-between;
		align-items: center;

		.ant-select {
			margin-left: 11px;
			margin-right: 11px;
		}

		.page-size {
			display: flex;
			align-items: center;
			margin-right: 20px;
		}

		.ant-pagination {
			flex-grow: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-wrap: wrap;
			row-gap: 10px;

			/deep/ .ant-pagination-total-text {
				height: unset;
				flex-grow: 1;

				@media (max-width: 1024px) {
					width: 100%;
					text-align: center;
					margin-bottom: 20px;
				}
			}

			/deep/ .ant-pagination-item-active {
				background: #007ec6;

				a {
					color: #ffffff;
				}
			}

			/deep/ .ant-pagination-prev,
			/deep/ .ant-pagination-next {
				border: 1px solid #d9d9d9;

				&:hover {
					border-color: #1890ff;
					transition: all 0.3s;
				}

				a:hover {
					i {
						color: #1890ff;
					}
				}
			}
		}

		@media (max-width: 1024px) {
			flex-direction: column;
			gap: 20px;
		}
	}
}

.dot-image {
	width: 2em;
	border-radius: 2em;
	height: 2em;
	object-fit: cover;
}
/deep/ .ant-timeline-item-content {
	margin-left: 25px;
	p {
		margin-bottom: 0.2em;
	}
}
/deep/ .ant-timeline-item-tail {
	border-left: 2px solid #26bf5fad;
}
.text-none {
	text-transform: none !important;
}
.btn_group {
	@media (max-width: 767px) {
		width: 100%;
	}
	/deep/ .btn-secondary {
		background-image: none;
		border-color: none !important;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
	}
}
.container_total {
	margin-left: 2rem;
	padding: 6px;
	border: 1px solid;
	color: #007ec6;
	font-weight: 600;
	border-radius: 5px;
	background-color: #eef9ff;
}
.total {
	color: #007ec6;
	font-weight: 700;
	font-size: 1.2rem;
}
.full-address {
	width: 200px;
	white-space: nowrap;
	-webkit-line-clamp: 2 !important;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-bottom: 0;
	text-transform: none;

	&:first-letter {
		text-transform: none;
	}
}
.link-detail {
	white-space: nowrap;
	text-transform: uppercase;
	background: transparent;
	border: none;
	cursor: pointer;
	&:hover,
	&:focus,
	&:active {
		color: #faa831;
		border: none;
		outline: none;
	}
}
.btn_dropdown {
	border: white;
	border-radius: 5px;
	height: 35px;
	@media (max-width: 767px) {
		margin-top: 10px;
	}
}
.infor-box {
	padding: 1rem;
	border-radius: 12px 15px;
	background-color: #eef9ff;
	border: 1px solid #007ec6;
	color: #446b92;
	@media (max-height: 660px) {
		font-size: 12px;
	}
	@media (max-height: 970px) and (min-height: 660px) {
	}
	.row_hidden {
		visibility: hidden;
	}
}
.cursor_pointer {
	cursor: pointer;
}
.title_color {
	color: lightgray;
}
.img_filter {
	filter: grayscale(100%) invert(100%);
}
</style>
