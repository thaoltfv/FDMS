<template>
	<div class="modal-delete d-flex justify-content-center align-items-center">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<InputNumberFormat
						v-model="filter_map.radius"
						label="Nhập bán kính (km)"
						:max="15"
						:min="0"
						@change="changeRadius($event)"
						class="form-group-container col-12 col-lg-6"
					/>
					<InputCategory
						v-model="filter_map.year"
						vid="year"
						label="Năm"
						:options="optionsYears"
						placeholder="Năm"
						class="form-group-container col-12 col-lg-6"
						@change="changeFilterYear($event)"
					/>
				</div>

				<div class="seperator  mt-3 col-12">
					<h3><span>Loại giao dịch</span></h3>
				</div>
				<div class="property-filter form-group-container">
					<div class="row">
						<!-- <div class="col-4">
              <div class="d-flex align-items-center">
                <label class="input_checkbox">
                  <input class="cursor-pointer" type="checkbox" v-model="all_transaction" @change="handleAllTransaction">
                  <span class="check-mark"></span>
                </label>
                <p class="property_content">Tất cả</p>
              </div>
            </div> -->
						<div class="col-4 pr-0">
							<div class="d-flex align-items-center">
								<label class="input_checkbox">
									<input
										class="cursor-pointer"
										type="checkbox"
										:value="51"
										v-model="filter_map.transaction"
										@change="handleSold"
									/>
									<span class="check-mark"></span>
								</label>
								<p class="property_content color_content">Đã bán</p>
							</div>
						</div>
						<div class="col-4 p-0">
							<div class="d-flex align-items-center">
								<label class="input_checkbox">
									<input
										class="cursor-pointer"
										type="checkbox"
										:value="52"
										v-model="filter_map.transaction"
										@change="handleForSale"
									/>
									<span class="check-mark"></span>
								</label>
								<p class="property_content color_content">Rao bán</p>
							</div>
						</div>
						<div class="col pl-0">
							<div class="d-flex align-items-center">
								<label class="input_checkbox">
									<input
										class="cursor-pointer"
										type="checkbox"
										v-model="filter_map.bothSide"
										@change="handleIsCheckBothSide"
									/>
									<span class="check-mark"></span>
								</label>
								<p class="property_content color_content">Mặt tiền & hẻm</p>
							</div>
						</div>
					</div>
				</div>
				<div class="btn__group">
					<button
						class="btn btn-white font-weight-normal font-weight-bold"
						@click.prevent="handleCancel"
					>
						Trở lại
					</button>
					<button
						class="btn btn-white btn-orange font-weight-bold ml-2"
						@click.prevent="handleAction"
					>
						Chọn
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import InputText from "@/components/Form/InputText";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCategory from "@/components/Form/InputCategory";
import moment from "moment";
export default {
	name: "ModalFilterMap",
	props: [
		"radius",
		"frontSide",
		"bothSide",
		"transaction",
		"assetType",
		"year"
	],
	components: {
		InputText,
		InputCategory,
		InputNumberFormat
	},
	mounted() {
		this.filter_map.radius = parseFloat(this.radius / 1000).toFixed(1);
		this.filter_map.frontSide = this.frontSide;
		this.filter_map.bothSide = this.bothSide;
		this.filter_map.transaction = this.transaction;
		this.filter_map.assetType = this.assetType;
		this.filter_map.year = this.year;
	},
	data() {
		return {
			filter_map: {
				radius: "",
				frontSide: "",
				bothSide: "",
				transaction: [],
				assetType: [],
				year: ""
			},
			years: [
				{
					name: "1 năm trước",
					id: moment()
						.subtract(1, "year")
						.format("YYYY-MM-DD")
				},
				{
					name: "2 năm trước",
					id: moment()
						.subtract(2, "year")
						.format("YYYY-MM-DD")
				}
			],
			radius_input: ""
		};
	},
	computed: {
		optionsYears() {
			return {
				data: this.years,
				id: "id",
				key: "name"
			};
		}
	},
	methods: {
		changeRadius(event) {
			this.filter_map.radius = parseFloat(event).toFixed(2);
		},
		changeFilterYear(event) {
			if (event) {
				var now = moment(new Date()); //todays date
				var end = moment(event); // another date
				var duration = moment.duration(now.diff(end));
				var year_change = parseInt(duration.asYears());
				this.$emit("filter_year", year_change);
			}
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		handleAllTransaction() {},
		handleSold() {},
		handleForSale() {},
		handleIsCheckBothSide() {},
		handleAction(event) {
			let data = this.filter_map;
			data.radius = parseInt(this.filter_map.radius * 1000);
			this.$emit("action", data);
			this.$emit("cancel", event);
		}
	}
};
</script>

<style lang="scss" scoped>
.modal-delete {
	position: fixed;
	z-index: 300;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);

	@media (max-width: 787px) {
		padding: 20px;
	}
	.card {
		max-width: 500px;
		width: 100%;
		margin-bottom: 0;
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
			text-align: right;
			padding: 15px;
			.btn__group {
				.btn {
					max-width: fit-content;
					width: 100%;
					margin: 14px 0 0;
				}
			}
		}
	}
}
.title {
	font-weight: 500;
	font-size: 1.125rem;
	text-align: left;
	margin-bottom: 7px;
}
.btn {
	&-orange {
		background: #faa831;
		color: #ffffff;
		font-weight: 700 !important;
	}
	&-white {
		min-width: auto;
	}
}
.form-group-container {
	margin-top: 10px;
}
.property_content {
	font-weight: 600;
}
.input_checkbox {
	margin-right: 10px;
}
.seperator h3 {
	display: flex;
	justify-content: center;
	align-items: center;
}

.seperator h3::after {
	content: "";
	display: block;
	flex-grow: 1;
	height: 1px;
	background: #ccc;
}

.seperator h3 span {
	padding-right: 0.5em;
}
</style>
