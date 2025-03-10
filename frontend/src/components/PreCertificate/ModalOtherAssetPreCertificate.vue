<template>
	<div class="col-12" :style="isMobile() ? { padding: '0' } : {}">
		<div class="modal-detail d-flex justify-content-center align-items-center">
			<div class="card">
				<ValidationObserver
					@submit.prevent="handleAction"
					tag="form"
					ref="other_asset"
				>
					<div class="row" style="margin-right: -32px">
						<InputCategory
							v-model="dataForm.asset_type"
							class="col-12"
							vid="asset_type"
							:requiredIcon="true"
							label="Loại tài sản"
							rules="required"
							:options="optionsAssetType"
							style="margin-left: -10px; margin-bottom: 10px"
						/>
					</div>
					<div class="row" style="margin-right: -32px">
						<InputTextarea
							:rows="3"
							:disableInput="false"
							v-model="dataForm.asset_name"
							label="Tên tài sản"
							:requiredIcon="true"
							rules="required"
							class="form-group-container col-12"
							style="margin-left: -10px; margin-bottom: 10px"
						/>
					</div>
					<div class="row" style="margin-right: -32px">
						<InputCurrency
							v-model="dataForm.asset_price"
							vid="asset_price"
							:max="99999999999999"
							label="Giá trị tài sản"
							class="col-12"
							style="margin-left: -10px; margin-bottom: 10px"
							:requiredIcon="true"
							rules="required"
						/>
					</div>
					<div class="container-title container-title__footer">
						<div class="d-flex justify-content-between justify-content-lg-end">
							<button
								class="btn btn-orange"
								:class="{ 'btn-loading disabled': isSubmit }"
								@click="handleAction"
							>
								Lưu
							</button>
							<button class="btn" type="button" @click="handleCancel">
								Trở lại
							</button>
						</div>
					</div>
				</ValidationObserver>
				<!-- <div class="row d-flex justify-content-between" style="margin-top: 5px">
				<strong class="margin_content_inline" style="margin-left: -10px"
					>File kèm kết quả sơ bộ:<b class="ml-1 text-red">*</b></strong
				>

				<label
					style="
						padding: 1px;
						padding-left: 5px;
						padding-right: 5px;
						border: 1px solid #b6d5f3;
					"
					v-if="allowEdit"
					for="image_property"
				>
					<font-awesome-icon
						:style="{ color: '#00507c', cursor: 'pointer' }"
						icon="cloud-upload-alt"
						size="1x"
					/>
				</label>

				<input
					v-if="allowEdit"
					class="btn-upload"
					type="file"
					ref="file"
					id="image_property"
					multiple
					@change="onImageChange($event)"
					style="display: none"
				/>
			</div>
			<div
				class="row input_download_pre_certificate mb-2 mt-2"
				v-for="(file, index) in lstFile"
				:key="index"
			>
				<div
					:key="index"
					class="d-flex align-items-center col"
					@click="downloadOtherFile(file)"
				>
					<img
						v-if="!file.isUpload"
						class="mr-1"
						style="width: 1rem"
						src="@/assets/icons/ic_taglink.svg"
						alt="tag_2"
					/>
					<div class="title_input_content title_input_download cursor_pointer">
						{{
							file.name
								? file.name.length > 20
									? file.name.substring(20, 0) + "..."
									: file.name
								: ""
						}}
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
							v-else-if="permission.allowDelete && allowEdit"
							@click="deleteOtherFile(file, index)"
							src="@/assets/icons/ic_delete_2.svg"
							alt="tag_2"
							class="img_document_action"
						/>
					</div>
				</div>
			</div> -->
			</div>
		</div>
	</div>
</template>
<script>
import InputCurrency from "@/components/Form/InputCurrency";
import InputTextarea from "@/components/Form/InputTextarea";
import InputCategory from "@/components/Form/InputCategory";
import Vue from "vue";
import Icon from "buefy";
import _ from "lodash";
import File from "@/models/File";
import axios from "@/plugins/axios";
Vue.use(Icon);
export default {
	data() {
		return {
			isSubmit: false,
		};
	},
	props: ["dataForm", "isEdit"],
	components: {
		InputCurrency,
		InputTextarea,
		InputCategory,
	},
	computed: {
		optionsAssetType() {
			return {
				data: [
					{
						id: "BDS",
						name: "Bất động sản",
					},
					{
						id: "DS",
						name: "Động sản",
					},
				],
				id: "id",
				key: "name",
			};
		},
	},

	methods: {
		async handleAction() {
			const isValid = await this.$refs.other_asset.validate();

			if (isValid) {
				this.$emit("action", this.dataForm);
			}
		},
		handleCancel() {
			this.$emit("cancel");
		},
		formatNumber(num) {
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
	},
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
		max-width: 500px;
		width: 100%;
		max-height: 90vh;
		margin-bottom: 0;
		// padding: 35px 50px;
		padding: 25px 50px 25px;
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

	::v-deep .ant-table-thead > tr > th {
		font-weight: 700 !important;
		background-color: #dee6ee !important;
		color: #3d4d65 !important;
		// border-right: 1px solid white !important;
	}
	::v-deep .ant-table-wrapper .ant-spin-container .ant-table {
		border: 1px solid #dee6ee;
	}

	::v-deep .ant-table-column-title {
		color: #00507c;
	}

	::v-deep .table-striped td {
		background-color: #f6f7fb;
		border-color: #dee6ee;
		border-width: 0;
	}

	::v-deep .ant-table-tbody,
	::v-deep .ant-table-body {
		box-shadow: none;
		min-height: 10vh;
	}
	::v-deep .ant-table-pagination {
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

			::v-deep .ant-pagination-total-text {
				height: unset;
				flex-grow: 1;

				@media (max-width: 1024px) {
					width: 100%;
					text-align: center;
					margin-bottom: 20px;
				}
			}

			::v-deep .ant-pagination-item-active {
				background: #007ec6;

				a {
					color: #ffffff;
				}
			}

			::v-deep .ant-pagination-prev,
			::v-deep .ant-pagination-next {
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
::v-deep .ant-timeline-item-content {
	margin-left: 25px;
	p {
		margin-bottom: 0.2em;
	}
}
::v-deep .ant-timeline-item-tail {
	border-left: 2px solid #26bf5fad;
}
.text-none {
	text-transform: none !important;
}
.btn_group {
	@media (max-width: 767px) {
		width: 100%;
	}
	::v-deep .btn-secondary {
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
