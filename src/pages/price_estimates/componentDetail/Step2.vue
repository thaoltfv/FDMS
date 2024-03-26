<template>
	<div>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Hình ảnh bản đồ</h3>
					<img
						class="img-dropdown"
						:class="!showTookAPhoto ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="
							() => {
								showTookAPhoto = !showTookAPhoto;
							}
						"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showTookAPhoto">
				<div class="container-fluid container_imageMap" v-if="step_2.map_img">
					<img class="w-100" :src="step_2.map_img" alt="map" />
				</div>
				<div class="infor-box pl-2 w-100" v-else>
					<span class="mr-1">
						<svg
							width="13"
							height="13"
							viewBox="0 0 12 13"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
								fill="#007EC6"
							/>
						</svg>
					</span>
					Chưa có hình ảnh bản đồ
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import { Tabs, TabItem } from "vue-material-tabs";
import { BCarousel, BCarouselSlide } from "bootstrap-vue";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";

import Vue from "vue";
import Icon from "buefy";
Vue.use(Icon);
export default {
	name: "Step6",
	props: [
		"data",
		"comparison",
		"frontSide",
		"coordinates",
		"propertyTypes",
		"type_purposes",
		"step_active"
	],
	components: {
		BCarousel,
		BCarouselSlide,
		Tabs,
		TabItem
	},
	computed: {},
	setup() {
		const priceEstimateStore = usePriceEstimatesStore();
		const { priceEstimates } = storeToRefs(priceEstimateStore);

		const step_2 = ref(priceEstimates.value.step_2);

		return {
			step_2,
			priceEstimateStore
		};
	},
	data() {
		return {
			theme: {
				navItem: "#000000",
				navActiveItem: "#007EC6",
				slider: "#007EC6",
				arrow: "#000000"
			},

			showTookAPhoto: true
		};
	},
	async mounted() {},
	beforeUpdate() {},
	methods: {}
};
</script>
<style scoped lang="scss">
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
			font-weight: 600;
			margin-bottom: 0;
		}
	}

	&-body {
		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-sub_header_title {
		padding: 15px 24px;
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
.card_detail_asset {
	position: relative;
	height: 100%;
	width: 90%;
	border-radius: 10px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	overflow-y: auto;
	overflow-x: hidden;
	&-footer {
		padding: 15px 24px;
	}
	@media (max-width: 768px) {
		margin-bottom: 20px;
	}
}
.open_property {
	transform: rotate(180deg);
	transition: 0.2s;
}
.button_hidden_property {
	height: 35px;
	width: 10%;
	object-fit: cover;
}
.container_carousel {
	position: relative;
}
.hidden_detail_asset {
	position: absolute;
	z-index: 10;
	right: 0;
}
.all-map {
	position: relative;
	height: 100%;
	width: 100%;
	.loading {
		display: none;
		&__map {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: rgba(0, 0, 0, 0.62);
			z-index: 1031;
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
}
.form-group-container {
	margin-top: 10px;
}

.btn-delete {
	cursor: pointer;
	display: flex;
	align-items: center;
	background: #ffffff;
	padding: 10px;
	border: none;

	// margin: auto;
	// width: 36px;
	// height: 36px;

	img {
		width: 100%;
		height: auto;
		min-width: 0.75rem;
	}
}

.btn {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		width: 100px;
		color: #fff;
		margin: 15px 0 0;
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
.container_map {
	max-width: 95vw;
	width: 100%;
	height: 70vh;
	margin-bottom: 0;
	@media (max-width: 767px) {
		max-width: 100dvh;
		height: 100dvh;
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
		padding: 40px;
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
.main-map {
	position: relative;
	height: 100%;
	width: 100%;
	transition-timing-function: ease;
	transition-duration: 0.25s;
	overflow-x: hidden;
	transition: 0.5s;
	@media (max-width: 1024px) {
		width: 100%;
	}
	.layer-map {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 0;
		transition-timing-function: ease;
		transition-duration: 0.25s;
	}
	&--hidden {
		transition: 0.5s;
		width: 100%;
	}
}
.btn {
	&-radius {
		width: 34px;
		height: 2.295rem;
		padding: 2px;
		.img {
			width: 100%;
			height: 100%;
		}
	}
	&__hide {
		height: 100%;
		width: 245px;
		// padding: 5px 13px;
		position: absolute;
		// white-space: nowrap;
		border-top-right-radius: 0;
		border-bottom-right-radius: 0;
		left: -245px;
		justify-content: end;
		@media (max-width: 1024px) {
			display: none;
		}
		@media (max-width: 1366px) {
			width: 360px;
			left: -362px;
		}
		@media (min-width: 1367px) {
			width: 370px;
			left: -372px;
		}
	}
}
.hidden_asset_detail_container {
	width: 35px;
	left: -35px;
}
.button_action_hide {
	width: 100% !important;
}
.w-25 {
	width: 25%;
}
.property-hidden {
	&.w-25 {
		width: 0 !important;
	}
	.container {
		&-property {
			&--hidden {
				width: 0;
				visibility: hidden;
				transition: 0.5s;
			}
		}
	}
}
.container-property {
	height: 100%;
}
.property {
	width: 100%;
	height: 100%;
	&--hidden {
		width: 0;
		overflow: hidden;
		.property {
			&-filter {
				overflow: scroll;
			}
		}
	}
	&-filter {
		padding: 15px 12px;
		background: #f28c1c;
	}
	&-empty {
		text-align: center;
		color: #999999;
		font-size: 1.125rem;
		margin-top: 12.5%;
	}
	&-content {
		margin-bottom: 0;
		margin-left: 10px;
		color: #ffffff;
		white-space: nowrap;
	}
	&-contain {
		height: calc(100% - 0px);
		overflow-y: auto;
		overflow-x: hidden;
	}
	&-info {
		cursor: pointer;
		display: flex;
		align-items: center;
		padding: 5px 12px;
		border-bottom: 2px solid #f28c1c;
		transition: 0.3s;
		&--img {
			width: 100px;
			height: 100px;
			margin-right: 7px;
			display: flex;
			justify-content: center;
			img {
				border-radius: 5px;
				width: 100%;
				height: 100%;
				object-fit: cover;
			}
			.img__empty {
				width: 60%;
				height: 60%;
				margin: auto;
			}
		}
		&--content {
			width: calc(100%);
			.info {
				&-content {
					margin-bottom: 0;
					font-weight: 600;

					color: #000000;
					overflow: hidden;
					text-overflow: ellipsis;
					white-space: nowrap;
					width: 100%;
					&__detail {
						text-align: right;
						font-weight: 500;
					}
				}
			}
		}
		&:hover {
			background: rgba(0, 0, 0, 0.1);
			border-bottom: 4px solid #f28c1c;
		}
		&__active {
			background: rgba(0, 0, 0, 0.1);
			border-bottom: 4px solid #f28c1c;
		}
	}
}
.btn-map {
	background: #ffffff;
	border-radius: 5px;
	border: 3px solid #ffffff;
	padding: 0;
	box-sizing: border-box;
	img {
		max-width: 50px;
		height: auto;
	}
}
.content_economy {
	font-weight: 500;
	margin-left: 1.5rem;
}
.div_radio {
	margin-bottom: 0.5rem;
}
.marker {
	width: 15px;
	height: 15px;
	border-radius: 50%;
	&:hover {
		border: 2px solid;
	}
	&__red {
		background: #de1616;
	}
	&__blue {
		background: #37c3f4;
	}

	&__purple {
		background: #8659fa;
	}

	&__orange {
		background: #faa831;
	}

	&__green {
		background: #1f8b24;
	}
	&__active {
		width: 15px;
		height: 15px;
		background: #de1616;
		// background-image: url("../../../assets/icons/ic_fv_location.svg");
		background-repeat: no-repeat;
		background-size: cover;
		// background-color: transparent;
		&:hover {
			border: none;
		}
	}
}
.img-fluid {
	border-top-left-radius: 10px !important;
	border-top-right-radius: 10px !important;
}
.content_detail_asset {
	margin-top: 10px;
	font-size: 12px;
	// padding-left: 1rem;
	// padding-right: 1rem;
	padding: 0.25rem 1rem;
}
.name_title {
	font-weight: 700;
}
.content_detail {
	font-weight: 600;
}
.content_tab_detail {
	margin-top: 0.75rem;
	font-size: 12px;
}
.img-location-choose {
	width: 50px !important;
	position: absolute;
	bottom: 0px;
	right: -21px;
}
.img-choosing {
	position: absolute;
	width: 18px;
	right: -4px;
	top: -23px;
}
.padding_unset {
	padding: unset !important;
}
.container_imageMap {
	border: 1px solid;
	padding: 10px;
}
.btn_create_asset {
	background: #e2f3fc;
	border: 1px solid #007ec6;
	font-weight: 600;
	color: #007ec6;
}
.btn_white_border {
	border: 1px solid #617f9e;
}
.input-checkbox {
	position: relative;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 22px;
	height: 22px;
	input {
		width: 22px;
		height: 22px;
		position: absolute;
		cursor: pointer;
		opacity: 0;
		&:checked {
			& ~ .check-mark {
				background-color: #dee6ee;
				&:after {
					display: block;
				}
			}
		}
		&:disabled {
			& ~ .check-mark {
				background-color: #dee6ee;
			}
		}
	}
	.check-mark {
		position: absolute;
		top: 0px;
		left: 0;
		cursor: pointer;
		width: 22px;
		height: 22px;
		font-size: 18px;
		font-weight: bold;
		color: #617f9e;
		// background-color: #617F9E;
		border: 2px solid #617f9e;
		border-radius: 4px;
		&:after {
			content: "\2713";
			position: absolute;
			display: none;
			left: 50%;
			top: -3px;
			width: 5px;
			height: 10px;
			// border: solid #FFFFFF;
			// border-width: 0 3px 3px 0;
			-webkit-transform: rotate(0deg) translate(-125%, -25%);
			-ms-transform: rotate(0deg) translate(-125%, -25%);
			transform: rotate(0deg) translate(-125%, -25%);
		}
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
		font-size: 14px;
	}
}
</style>
