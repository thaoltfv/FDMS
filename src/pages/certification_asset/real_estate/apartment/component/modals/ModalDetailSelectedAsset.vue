<template>
	<div>
		<div
			class="modal-detail d-flex justify-content-center align-items-center"
			@click.self="handleCancel"
		>
			<div class="card">
				<div class="container-title">
					<div class="d-flex justify-content-between">
						<h2 class="title">Tài sản so sánh</h2>
						<img
							height="35px"
							@click="handleCancel"
							class="cancel"
							src="@/assets/icons/ic_cancel_2.svg"
							alt=""
						/>
					</div>
				</div>
				<div class="contain-detail">
					<div class="row heigh_div w-100">
						<div class="header_title col">Tài sản</div>
						<div
							class="header_title col"
							v-for="asset in assetHasChoose"
							:key="asset.id"
						>
							{{ `TSS_${asset.id}` }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div
							class="w-100 heigh_div col-12 title_details_assets header_title_detail"
						>
							Mô tả chung
						</div>
					</div>

					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Loại giao dịch</div>
						<div
							class="content_details_assets color_content col"
							v-for="asset in assetHasChoose"
							:key="asset.id"
						>
							{{ asset.transaction_type.description }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Giá</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ formatNumber(asset.total_amount) }}đ
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Tổng diện tích</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ formatNumber(asset.room_details[0].area) }} m<sup>2</sup>
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Đơn giá</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ formatNumber(asset.average_land_unit_price) }}đ
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Pháp lý</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{
								asset.room_details[0].legal
									? asset.room_details[0].legal.description
									: ""
							}}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Tình trạng nội thất</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ asset.room_details[0].furniture_quality.description }}
						</div>
					</div>
					<div class="row heigh_div w-100 main_title header_title_detail">
						<div class="col">Thông tin căn hộ</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Tên chung cư</div>
						<div
							class="content_details_assets color_content col text-wrap"
							v-for="asset in assetHasChoose"
							:key="asset.id"
						>
							{{ asset.project ? asset.project.name : "" }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Block</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ asset.block ? asset.block.name : "" }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Tầng</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ asset.floor ? asset.floor.name : "" }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Mã căn hộ</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{
								asset.apartment_specification
									? asset.apartment_specification.apartment_name
									: ""
							}}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Số phòng ngủ</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ asset.room_details[0].bedroom_num }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Số phòng WC</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ asset.room_details[0].wc_num }}
						</div>
					</div>
					<div class="row heigh_div w-100 main_title header_title_detail">
						<div class="col">Nguồn thông tin</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Người liên hệ</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ asset.contact_person }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Điện thoại</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ asset.contact_phone }}
						</div>
					</div>
					<div class="row heigh_div w-100">
						<div class="title_details_assets col">Ngày đăng tin</div>
						<div
							v-for="asset in assetHasChoose"
							:key="asset.id"
							class="content_details_assets color_content col"
						>
							{{ formatDate(asset.public_date) }}
						</div>
					</div>
				</div>
				<!-- <div class="container-title container-title__footer">
            <div class="d-lg-flex d-block justify-content-end shadow-bottom">
              <button class="btn btn-white" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
            </div>
          </div> -->
			</div>
		</div>
	</div>
</template>

<script>
import moment from "moment";
export default {
	name: "ModalPropertyDetail",
	props: ["assetHasChoose"],
	data() {
		return {
			isOneItem: false,
			isTwoItem: false,
			isThreeItem: false
		};
	},
	components: {},
	computed: {},
	created() {
		if (this.assetHasChoose.length === 1) {
			this.isOneItem = true;
			this.isTwoItem = false;
			this.isThreeItem = false;
		} else if (this.assetHasChoose.length === 2) {
			this.isOneItem = false;
			this.isTwoItem = true;
			this.isThreeItem = false;
		} else if (this.assetHasChoose.length === 3) {
			this.isOneItem = false;
			this.isTwoItem = false;
			this.isThreeItem = true;
		}
	},
	methods: {
		formatDate(date) {
			return moment(date).format("DD/MM/YYYY");
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
		format(value) {
			let num = (value / 1).toFixed(0).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		},
		formatFloat(value) {
			let num = (value / 1).toFixed(2).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		},
		formatArea(value) {
			let num = (value / 1).toString().replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (isValid) {
				this.handleAction();
			}
		}
	},
	beforeMount() {}
};
</script>

<style lang="scss" scoped>
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
		// padding: 35px 50px;
		padding: 25px 50px 65px;
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
		@media (max-width: 767px) {
		}
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
			padding: 12px 5px;
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
	// padding: 35px 95px 0;
	padding: 15px 50px 10px 95px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	.title {
		margin-top: 20px;
		margin-bottom: 20px;
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
.container-img {
	padding: 0.75rem 0;
	border: 1px solid #0b0d10;
}
.traffic-light {
	color: black;
	padding: 0 5px;
	background: rgba(252, 194, 114, 0.53);
	width: fit-content;
}
.input-switch__detail {
	margin-bottom: 25px;
}
.container-table {
	border-radius: 5px;
	border: 1px solid #f3f2f7;
}
.heigh_div {
	min-height: 35px;
	border-bottom: 1px solid #e8e8e8;
}
.header_title {
	background: #007ec6;
	color: #f5f5f5;
	font-weight: 600;
	padding-left: 1.2rem;
	padding-top: 0.5rem;
}
.content_details_assets {
	padding-top: 0.5rem;
	padding-bottom: 0.5rem;
	font-weight: 500;
}
.title_details_assets {
	padding-top: 0.5rem;
	padding-bottom: 0.5rem;
	font-weight: 600;
	color: #617f9e;
}
.header_title_detail {
	color: #3d4d65 !important;
	background-color: rgba(222, 230, 238, 0.5);
}
.main_title {
	padding-top: 0.5rem;
	padding-bottom: 0.5rem;
	font-weight: 600;
}
.row {
	margin-right: unset !important;
	margin-left: unset !important;
}
</style>
