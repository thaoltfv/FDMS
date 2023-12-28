<template>
	<div class="col-12" :style="isMobile ? { padding: '0' } : {}">
		<ModalDelete
			v-if="openModalDelete"
			@cancel="openModalDelete = false"
			@action="handleDelete"
		/>
		<ModalViewDocument
			v-if="isShowPrint"
			@cancel="isShowPrint = false"
			:filePrint="filePrint"
			title="Xem trước tập tin"
		/>
		<div
			v-if="type === 'Appendix'"
			class="card"
			:style="isMobile ? { 'margin-bottom': '150px' } : {}"
		>
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<div class="row d-flex justify-content-between align-items-center">
						<h3 class="title">
							{{ title }}
							<label :for="'image_property' + type" class="ml-2">
								<font-awesome-icon
									:style="{ color: 'orange', cursor: 'pointer' }"
									icon="cloud-upload-alt"
									size="1x"
								/>
							</label>
						</h3>

						<input
							class="btn-upload "
							type="file"
							:ref="'file' + type"
							:id="'image_property' + type"
							multiple
							:accept="
								type === 'Appendix'
									? 'image/png, image/gif, image/jpeg, image/jpg, .doc, .docx, .xlsx, .xls, application/pdf'
									: '.doc, .docx, .xlsx, .xls, application/pdf'
							"
							@change="onImageChange($event)"
							style="display: none;"
						/>
					</div>

					<img
						class="img-dropdown"
						:class="!showCardDetailFile ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailFile = !showCardDetailFile"
					/>
				</div>
			</div>

			<div class="card-body card-info" v-show="showCardDetailFile">
				<div class="card-body card-info">
					<div
						class="row input_download_certificate mb-2"
						v-for="(file, index) in lstFile"
					>
						<div :key="index" class="d-flex align-items-center col">
							<!-- <img
								class="img_input_download"
								src="@/assets/icons/ic_document.svg"
								alt="document"
							/> -->
							<div
								class="title_input_content title_input_download cursor_pointer"
							>
								{{ file.name }}
							</div>
						</div>
						<div
							class="d-flex align-items-center justify-content-end col-1 pr-3"
						>
							<div>
								<img
									src="@/assets/icons/ic_search_3.svg"
									alt="search"
									class="img_document_action mr-3"
									@click="getPreviewUrl(file)"
								/>
							</div>
							<div>
								<img
									v-if="file.isUpload === false"
									@click="deleteOtherFile(file, index)"
									src="@/assets/icons/ic_delete_2.svg"
									alt="tag_2"
									class="img_document_action"
								/>
								<img
									v-else-if="permission.allowDelete"
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

		<div class="card-body card-info" v-if="type === 'Result'">
			<div class="row">
				<InputCurrency
					v-if="!fromComponent"
					v-model="dataPC.total_preliminary_value"
					vid="service_fee"
					:max="99999999999999"
					label="Tổng giá trị sơ bộ"
					class="col-sm-6 col-md-6"
					style="margin-left:-10px;"
				/>
				<div
					v-else
					class="d-flex container_content"
					style="margin-left:-10px;margin-bottom: -25px;"
				>
					<strong class="margin_content_inline">Tổng giá trị sơ bộ:</strong>
					<p>
						{{
							dataPC.total_preliminary_value
								? formatNumber(dataPC.total_preliminary_value)
								: 0
						}}đ
					</p>
				</div>
			</div>
			<div class="row ">
				<div class="title" style="margin-left:-10px;">
					File kèm kết quả sơ bộ
					<label class="ml-2" for="image_property">
						<font-awesome-icon
							:style="{ color: 'orange', cursor: 'pointer' }"
							icon="cloud-upload-alt"
							size="1x"
						/>
					</label>
				</div>

				<input
					class="btn-upload "
					type="file"
					ref="file"
					id="image_property"
					multiple
					:accept="
						type === 'Appendix'
							? 'image/png, image/gif, image/jpeg, image/jpg, .doc, .docx, .xlsx, .xls, application/pdf'
							: '.doc, .docx, .xlsx, .xls, application/pdf'
					"
					@change="onImageChange($event)"
					style="display: none;"
				/>
			</div>
			<div
				class="row input_download_certificate mb-2"
				v-for="(file, index) in lstFile"
			>
				<div :key="index" class="d-flex align-items-center col">
					<!-- <img
								class="img_input_download"
								src="@/assets/icons/ic_document.svg"
								alt="document"
							/> -->
					<div class="title_input_content title_input_download cursor_pointer">
						{{ file.name }}
					</div>
				</div>
				<div class="d-flex align-items-center justify-content-end col-1 pr-3">
					<div>
						<img
							src="@/assets/icons/ic_search_3.svg"
							alt="search"
							class="img_document_action mr-3"
							@click="getPreviewUrl(file)"
						/>
					</div>
					<div>
						<img
							v-if="file.isUpload === false"
							@click="deleteOtherFile(file, index)"
							src="@/assets/icons/ic_delete_2.svg"
							alt="tag_2"
							class="img_document_action"
						/>
						<img
							v-else-if="permission.allowDelete"
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
</template>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
import InputCurrency from "@/components/Form/InputCurrency";

// import ModalViewDocument from "@/pages/certification_brief/component/modals/ModalViewDocument";
import ModalViewDocument from "@/components/PreCertificate/ModalViewDocument";
import Vue from "vue";
import Icon from "buefy";
import ModalDelete from "@/components/Modal/ModalDelete";
import File from "@/models/File";
// import mammoth from "mammoth";
Vue.use(Icon);
export default {
	props: {
		type: {
			type: String
		},
		fromComponent: {
			type: String
		}
	},
	components: {
		InputCurrency,
		ModalDelete,
		ModalViewDocument
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
		const showCardDetailFile = ref(false);
		const isMobile = ref(checkMobile());
		const preCertificateStore = usePreCertificateStore();
		const {
			dataPC,
			preCertificateOtherDocuments,
			permission,
			other
		} = storeToRefs(preCertificateStore);
		const title = ref("Tài liệu đính kèm");
		const lstFile = ref([]);
		if (props.type === "Appendix") {
			title.value = "Tài liệu đính kèm";
			lstFile.value = preCertificateOtherDocuments.value.Appendix;
		} else {
			title.value = "Kết quả sơ bộ";
			lstFile.value = preCertificateOtherDocuments.value.Result;
		}

		if (lstFile.value.length > 0 && props.type === "Result") {
			context.emit("action");
		}
		showCardDetailFile.value = lstFile.value.length > 0 ? true : false;
		const downloadOtherFile = file => {
			if (this.exportAction) {
				axios({
					url:
						process.env.API_URL +
						"/api/certificate/other-document/download/" +
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
					other.value.toast.open({
						message: `Tải xuống thành công`,
						type: "success",
						position: "top-right",
						duration: 3000
					});
				});
			}
		};

		const openModalDelete = ref(false);
		const fileDelete = ref({ id: null, isUpload: false });
		const deleteOtherFile = (file, index) => {
			openModalDelete.value = true;
			fileDelete.value = { id: file.id, isUpload: file.isUpload, index };
		};
		const handleDelete = async () => {
			if (fileDelete.value.isUpload === false) {
				lstFile.value.splice(fileDelete.value.index, 1);
				other.value.toast.open({
					message: "Xóa thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				return;
			}
			const res = await File.deleteFilePreCertificate(fileDelete.value.id);
			if (res.data) {
				lstFile.value.splice(fileDelete.value.index, 1);
				other.value.toast.open({
					message: "Xóa thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else if (res.error) {
				other.value.toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		};
		const openFile = file => {
			if (file.link) {
				window.open(file.link, "_blank");
			} else {
				other.value.toast.open({
					message: "Có lỗi xảy ra vui lòng thử lại",
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
			// window.open(
			// 	process.env.API_URL +
			// 		"/api/certificate/other-document/download/" +
			// 		file.id,
			// 	"_blank"
			// );
		};
		const isShowPrint = ref(false);
		const filePrint = ref(null);
		const getPreviewUrl = async file => {
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
				} else if (file.type === "application/pdf") {
					filePrint.value = {
						link: blob,
						type: "pdf"
					};
					isShowPrint.value = true;
				} else {
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
						} else if (file.type === "pdf") {
							filePrint.value = {
								link: blob,
								type: "pdf"
							};
						} else {
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
		};
		return {
			showCardDetailFile,
			dataPC,
			preCertificateOtherDocuments,
			permission,
			title,
			isMobile,
			lstFile,
			openModalDelete,
			isShowPrint,
			filePrint,
			handleDelete,
			deleteOtherFile,
			downloadOtherFile,
			openFile,
			getPreviewUrl
		};
	},
	methods: {
		formatNumber(num) {
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
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
							this.preCertificateOtherDocuments[this.type] = res.data.data;
							const tempList = [];
							for (let index = 0; index < res.data.data.length; index++) {
								const element = res.data.data[index];
								if (element.type_document === this.type) {
									tempList.push(element);
								}
							}
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
	width: 2rem;
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
