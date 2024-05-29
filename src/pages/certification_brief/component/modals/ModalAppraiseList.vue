<template>
	<div>
		<ValidationObserver tag="form" ref="observer">
			<div
				class="modal-detail d-flex justify-content-center align-items-center"
			>
				<div class="card">
					<!-- <div class="container-title">
						<div class="d-lg-flex d-block shadow-bottom">
							<h2 class="title">Danh sách tài sản thẩm định</h2>
						</div>
					</div> -->
					<div class="contain-detail table-wrapper">
						<a-table
							bordered
							:columns="columnAssets"
							:data-source="listCertificates"
							:loading="isLoading"
							:row-selection="{
								selectedRowKeys: selectedRowKeys,
								onChange: onSelectChange
							}"
							:rowKey="record => record.id"
							table-layout="top"
							:pagination="{
								...pagination
							}"
							@change="onPageChange"
							style="text-transform: unset !important"
							class="table_appraise_list"
						>
							<template slot="asset" slot-scope="asset">
								<p :id="asset.id" class="full-address mb-0">
									{{ asset.full_address ? asset.full_address : asset.name }}
								</p>
								<b-tooltip :target="asset.id.toString()">
									{{
										asset.full_address ? asset.full_address : asset.name
									}}</b-tooltip
								>
							</template>
							<!-- <template slot="land" slot-scope="land">
								<p class="text-none mb-0">{{land.properties && land.properties.length > 0 && land.properties[0].property_detail && land.properties[0].property_detail.length > 0 ? land.properties[0].property_detail[0].land_type_purpose.acronym : ''}} {{land.properties && land.properties.length > 0 && land.properties[0].property_detail && land.properties[0].property_detail.length > 1 ? ', ' + land.properties[0].property_detail[1].land_type_purpose.acronym : ''}}</p>
							</template> -->
							<template slot="area" slot-scope="area">
								<p class="text-none mb-0">
									{{ area ? formatNumber(formatArea(area)) : 0 }} m <sup>2</sup>
								</p>
							</template>
							<template slot="price" slot-scope="price">
								<p class="text-none mb-0">
									{{ price ? formatNumber(price) : 0 }} đ
								</p>
							</template>
							<template slot="created_at" slot-scope="created_at">
								<p class="public_date mb-0">{{ formatDate(created_at) }}</p>
							</template>
							<template slot="id" slot-scope="id">
								<p class="link-detail mb-0">{{ "TSTD_" + id }}</p>
							</template>
						</a-table>
					</div>
					<div class="container-title container-title__footer">
						<div class="d-flex justify-content-between justify-content-lg-end">
							<button
								class="btn btn-white btn-action-modal"
								type="button"
								@click="handleCancel"
							>
								<img
									src="../../../../assets/icons/ic_cancel.svg"
									class="mr-1"
									alt="save"
								/>Trở lại
							</button>
							<button
								class="btn btn-orange btn-action-modal"
								type="button"
								@click="handleAction"
								:class="{ 'btn-loading disabled': isSubmit }"
							>
								<img
									src="../../../../assets/icons/ic_save.svg"
									class="mr-1"
									alt="save"
								/>Lưu
							</button>
						</div>
					</div>
				</div>
			</div>
		</ValidationObserver>
	</div>
</template>

<script>
import { BTooltip } from "bootstrap-vue";
import CertificationBrief from "@/models/CertificationBrief";
import AppraiseAsset from "@/models/AppraiseAsset";
import { convertPagination } from "@/utils/filters";
import moment from "moment";

export default {
	name: "ModalAppraiseList",
	props: ["data", "idData", "isCheckPrice"],
	components: {
		"b-tooltip": BTooltip
	},
	data() {
		return {
			isSubmit: false,
			isLoading: false,
			selectedRowKeys: [],
			selectedRows: [],
			listCertificates: [],
			pagination: {},
			isCheckRealEstate: true
		};
	},
	computed: {
		columnAssets() {
			let dataColumn = [
				{
					title: "Mã TSTĐ",
					align: "left",
					scopedSlots: { customRender: "id" },
					dataIndex: "id",
					hiddenItem: false
				},
				{
					title: "Loại tài sản",
					align: "left",
					dataIndex: "asset_type.description",
					hiddenItem: false
				},
				{
					title: "Địa chỉ tài sản",
					align: "left",
					scopedSlots: { customRender: "asset" },
					hiddenItem: false
				},
				// {
				// 	title: 'Loại đất',
				// 	align: 'left',
				// 	scopedSlots: {customRender: 'land'},
				// 	hiddenItem: this.isCheckRealEstate
				// },
				{
					title: "Tổng diện tích",
					align: "right",
					scopedSlots: { customRender: "area" },
					dataIndex: "total_area",
					hiddenItem: this.isCheckRealEstate
				},
				{
					title: "Tổng giá trị",
					align: "right",
					scopedSlots: { customRender: "price" },
					dataIndex: "total_price",
					hiddenItem: false
				},
				{
					title: "Người tạo",
					align: "left",
					dataIndex: "created_by.name",
					hiddenItem: false
				},
				{
					title: "Ngày tạo",
					align: "right",
					scopedSlots: { customRender: "created_at" },
					dataIndex: "created_at",
					hiddenItem: false
				}
			];
			return dataColumn.filter(item => item.hiddenItem === false);
		}
	},
	beforeMount() {
		if (this.data.document_type && this.data.document_type.length > 0) {
			this.data.document_type.forEach(item => {
				if (item === "BDS") {
					this.isCheckRealEstate = false;
				}
			});
		} else {
			this.isCheckRealEstate = false;
		}
	},
	async mounted() {
		await this.getListAppraise();
		if (this.data.general_asset.length > 0) {
			this.data.general_asset.forEach(item => {
				this.selectedRowKeys.push(item.general_asset_id);
				this.selectedRows.push(item);
			});
		}
	},
	methods: {
		async getSelectRows() {
			//   const ids = this.selectedRowKeys
			//   if (this.isHaveAppraiseId && this.isHaveAppraiseId.length > 0) {
			//   }
			//   const res = await CertificationBrief.getAppraisals(ids)
			//   this.selectedRows = [...res.data]
		},
		formatDate(value) {
			return moment(String(value)).format("DD/MM/YYYY");
		},
		formatNumber(num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		formatArea(value) {
			let num = (value / 1).toFixed(2).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		async getListAppraise(params = {}) {
			this.isLoading = true;
			const filter = {};
			try {
				const resp = await AppraiseAsset.paginate({
					query: {
						page: 1,
						limit: 15,
						status: 2,
						popup: true,
						certificate_id: this.idData,
						...params,
						...filter
					}
				});
				if (resp) {
					this.listCertificates = [...resp.data.data];
					this.pagination = convertPagination(resp.data);
					this.isLoading = false;
				}
			} catch (e) {
				this.isLoading = false;
			}
		},
		async onPageChange(pagination) {
			this.perPage = pagination.pageSize;

			const params = {
				page: pagination.current,
				limit: pagination.pageSize
			};
			await this.getListAppraise(params);
		},
		onSelectChange(selectedRowKeys, selectedRows) {
			this.selectedRowKeys = selectedRowKeys;
			this.selectedRows = selectedRows;
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		async handleAction(event) {
			let dataSend = {
				general_asset: [],
				check_price: this.isCheckPrice
			};
			await this.selectedRows.forEach(item => {
				let itemId = item.general_asset_id;
				if (itemId === undefined) {
					itemId = item.id;
				}
				dataSend.general_asset.push({
					general_asset_id: itemId,
					asset_type_id: item.asset_type_id
				});
			});
			this.isSubmit = true;
			const res = await CertificationBrief.updateAppraiseCertificate(
				this.idData,
				dataSend
			);
			if (res.data) {
				this.$toast.open({
					message: "Cập nhập tài sản thẩm định thành công",
					type: "success",
					position: "top-right"
				});
				this.$emit("updateAppraises", res.data);
			} else {
				this.$toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			}
			this.isSubmit = false;
			this.$emit("cancel", event);
		}
	}
};
</script>

<style scoped lang="scss">
.modal-detail {
	position: fixed;
	z-index: 1031;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);
	@media (max-width: 768px) {
		padding: 20px;
	}
	.card {
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		max-width: 1300px;
		width: 100%;
		height: 70vh;
		margin-bottom: 0;
		padding: 20px 30px;
		@media (max-width: 768px) {
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
.title-property {
	font-weight: 700;
	font-size: 1.2rem;
	margin-bottom: 18px;
}
.input-contain {
	margin-bottom: 25px;
}
.card-table {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	max-width: 99%;
	margin: 50px auto 75px;
}
.card-table tbody tr:last-child td,
.card-table tbody tr:last-child th {
	border-bottom: 1px solid #e5e5e5;
}
.card {
	.contain-detail {
		overflow-y: auto;
		overflow-x: hidden;
		margin-top: 20px;
		margin-bottom: 20px;
		&::-webkit-scrollbar {
			width: 2px;
		}
	}
	&-title {
		background: #f3f2f7;
		padding: 16px 20px;
		margin-bottom: 0;
		&__img {
			padding: 8px 20px;
		}
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

.btn-delete {
	cursor: pointer;
	display: flex;
	align-items: center;
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
	overflow-x: auto;
	@media (max-width: 1024px) {
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
.btn-property {
	padding: 10px;
}
.img-dropdown {
	cursor: pointer;
	width: 18px;
	&__hide {
		transform: rotate(90deg);
		transition: 0.3s;
	}
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
	height: auto;
	position: relative;
	.img {
		width: 100%;
	}
	.delete {
		position: absolute;
		top: 0;
		right: 0;
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
.contain-total {
	&__left {
		color: #000000;
		.num {
			padding: 0 11px 0 24px;
			width: 340px;
			height: 35px;
			line-height: 1.5;
			border-radius: 5px;
			border: 1px solid #555555;
			display: flex;
			align-items: center;
			justify-content: flex-end;
			background: #f1f1f1 !important;
			cursor: not-allowed;
			user-select: none;
			p {
				margin-bottom: 0;
			}
		}
		.name {
			min-width: 175px;
			margin-bottom: 0;
			margin-right: 20px;
		}
	}
}
.img-locate {
	cursor: pointer;
	position: absolute;
	right: 15px;
	top: 35px;
}
.form-control {
	width: 100%;
}
.btn-orange {
	background: #faa831;
	text-align: center;
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
	height: 35px;
	width: 146px;
	color: #fff;
	margin-right: 15px;
	box-sizing: border-box;
	&:hover {
		border-color: #dc8300;
	}
}
.container-title {
	margin: -35px -95px auto;
	padding: 35px 95px 0;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	@media (max-width: 767px) {
		margin: -20px -10px auto;
		padding: 20px 10px 0;
	}
	.title {
		font-size: 1.2rem;
		margin-bottom: 25px;
		font-weight: 700;
		@media (max-width: 767px) {
			font-size: 1.125rem;
		}
	}
	&__footer {
		margin: auto -95px -35px;
		padding: 20px 95px 30px;
		@media (max-width: 767px) {
			margin: auto -10px -20px;
			padding: 20px 10px 0;
			.btn-white {
				margin-bottom: 20px;
			}
		}
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
		margin-right: 0;
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
.container-img {
	padding: 0.75rem 0;
	border: 1px solid #0b0d10;
}
.loading {
	display: none;
	&__true {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		height: 100dvh;
		background: rgba(0, 0, 0, 0.62);
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
.input-disabled {
	min-height: 30px;
	height: 33px;
}
.text-none {
	text-transform: none;
}
.full-address {
	width: 15rem;
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
	color: #faa831;
	font-weight: 600;
	text-transform: uppercase;
}
.table-wrapper {
	text-transform: unset !important;
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
		max-height: unset !important;
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
</style>
