<template>
	<div v-if="step_3">
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title" style="margin-left: 30px;">
						KẾT QUẢ ƯỚC TÍNH SƠ BỘ
					</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailTraffic = !showCardDetailTraffic"
					/>
				</div>
			</div>
			<div
				class="card-body card-info"
				v-show="showCardDetailTraffic"
				v-if="!miscVariable.isApartment"
			>
				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-custom-11-5">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2"
										>Phần diện tích phù hợp quy hoạch</label
									>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-6  font-weight-bold">
									Mục đích sử dụng
								</div>
								<div class="col-custom-1-5 font-weight-bold text-right">
									Diện tích (m<sup>2</sup>)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Đơn giá (đ)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Thành tiền (đ)
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div
								v-for="(main_land, index) in step_3.total_area"
								:key="index"
								class=" mb-2 row content_form"
							>
								<div class="col-6">
									<span class="text-style-input">
										{{
											main_land.land_type_purpose
												? formatText(main_land.land_type_purpose.description)
												: ""
										}}
									</span>
								</div>
								<div :key="renderInputMainArea" class="col-custom-1-5">
									<span class="number-style-input">
										{{ main_land.main_area }}
									</span>
								</div>
								<div class="col-2">
									<InputCurrencyUnit
										v-if="!priceEstimates.isTransfer"
										:id="`main_landunit_price${index}`"
										v-model="main_land.unit_price"
										:vid="'unit_price' + index"
										:max="999999999"
										nonLabel="Đơn giá"
										:required="true"
										class="w-100"
										:disabled="!isEdit"
										@change="handleChangePriceMainArea($event, index)"
									/>

									<span class="number-style-input" v-else>
										{{
											main_land.unit_price
												? formatNumber(main_land.unit_price)
												: ""
										}}
									</span>
								</div>
								<div class="col-2">
									<span
										class="number-style-input"
										:key="keyRefreshTotalPriceMainLand"
									>
										{{
											main_land.total_price !== null &&
											main_land.total_price !== undefined
												? formatNumber(main_land.total_price)
												: ""
										}}
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-custom-11-5">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2"
										>Phần diện tích vi phạm quy hoạch</label
									>
								</div>
							</div>
						</div>
						<div v-if="step_3.planning_area.length > 0" class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-6  font-weight-bold">
									Mục đích sử dụng
								</div>
								<div class="col-custom-1-5 font-weight-bold text-right">
									Diện tích (m<sup>2</sup>)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Đơn giá (đ)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Thành tiền (đ)
								</div>
							</div>
						</div>
						<div v-if="step_3.planning_area.length > 0" class="col-12 mt-2">
							<div
								v-for="(planning_area, index) in step_3.planning_area"
								:key="index"
								class="mb-2 row content_form"
							>
								<div class="col-6">
									<span class="text-style-input">
										{{
											planning_area.land_type_purpose
												? formatText(
														planning_area.land_type_purpose.description
												  )
												: ""
										}}
									</span>
								</div>
								<div class="col-custom-1-5">
									<span class="number-style-input">
										{{ planning_area.planning_area }}
									</span>
								</div>
								<div class="col-2">
									<InputCurrencyUnit
										v-if="!priceEstimates.isTransfer"
										:key="key_render"
										:id="`planning_areaunit_price${index}`"
										v-model="planning_area.unit_price"
										:vid="'unit_price' + index"
										:max="999999999"
										nonLabel="Đơn giá"
										:disabled="!isEdit"
										:required="true"
										class="w-100"
										@change="handleChangePricePlanningArea($event, index)"
									/><span class="number-style-input" v-else>
										{{
											planning_area.unit_price
												? formatNumber(planning_area.unit_price)
												: ""
										}}
									</span>
								</div>
								<div class="col-2 ">
									<span class="number-style-input">
										{{
											planning_area.total_price !== null &&
											planning_area.total_price !== undefined
												? formatNumber(planning_area.total_price)
												: ""
										}}
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2" v-if="computeShowTangibleAset">
					<div class="row">
						<div class="col-custom-11-5">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2">Công trình xây dựng</label>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-custom-4-5 font-weight-bold ">Loại CTXD</div>
								<div class="col-custom-1-5 font-weight-bold text-right">
									CLCL
								</div>
								<div class="col-custom-1-5 font-weight-bold text-right">
									Diện tích sàn (m<sup>2</sup>)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Đơn giá (đ)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Thành tiền (đ)
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div
								v-for="(tangible, index) in step_3.tangible_assets"
								:key="index"
								class=" mb-2 row content_form"
							>
								<div class="col-custom-4-5">
									<InputCategoryCustom
										v-if="!priceEstimates.isTransfer"
										v-model="tangible.building_type_id"
										vid="building_type_id"
										nonLabel="Loại CTXD"
										rules="required"
										:options="optionsHousingType"
										:disabled="!isEdit"
										@change="tangible.building_type_id = $event"
									/>
									<span class="text-style-input" v-else>
										{{
											tangible.building_type
												? tangible.building_type.description
												: ""
										}}
									</span>
								</div>
								<div class="col-custom-1-5">
									<InputPercent
										v-if="!priceEstimates.isTransfer"
										:key="tangible.remaining_quality"
										v-model="tangible.remaining_quality"
										vid="remaining_quality"
										nonLabel="CLCL theo tuổi đời"
										:max="100"
										:decimal="0"
										rules="required"
										:disabled="!isEdit"
										@change="handleChangeTangible(tangible, index)"
									/>
									<span class="number-style-input" v-else>
										{{
											tangible.remaining_quality
												? formatNumber(tangible.remaining_quality) + " %"
												: ""
										}}
									</span>
								</div>
								<div :key="renderInputMainArea" class="col-custom-1-5">
									<InputAreaCustom
										v-if="!priceEstimates.isTransfer"
										v-model="tangible.total_construction_area"
										:decimal="2"
										vid="total_construction_area"
										:required="true"
										:max="9999999999999999"
										:min="0"
										rules="required"
										:sufix="false"
										:text_center="true"
										nonLabel="Diện tích sàn"
										:disabled="!isEdit"
										@change="handleChangeTangible(tangible, index)"
										:errorCustom="validateContructionArea()"
									/><span class="number-style-input" v-else>
										{{
											tangible.total_construction_area
												? formatNumber(tangible.total_construction_area)
												: ""
										}}
									</span>
								</div>
								<div class="col-2">
									<InputCurrencyUnit
										v-if="!priceEstimates.isTransfer"
										:id="`tangibleunit_price${index}`"
										v-model="tangible.unit_price"
										:vid="'unit_price' + index"
										:max="999999999"
										nonLabel="Đơn giá"
										:required="true"
										class="w-100"
										:disabled="!isEdit"
										@change="handleChangeTangible(tangible, index)"
									/><span class="number-style-input" v-else>
										{{
											tangible.unit_price
												? formatNumber(tangible.unit_price)
												: ""
										}}
									</span>
								</div>

								<div class="col-2">
									<span class="number-style-input">
										{{
											tangible.total_price !== null &&
											tangible.total_price !== undefined
												? formatNumber(tangible.total_price)
												: ""
										}}
									</span>
								</div>
								<div
									class="col-custom-0-5 px-3 d-flex align-items-end"
									v-if="isEdit && !priceEstimates.isTransfer"
								>
									<div
										@click="handleDeleteTangibleAsset(index)"
										class="btn-delete"
									>
										<img src="@/assets/icons/ic_delete_2.svg" alt="delete" />
									</div>
								</div>
							</div>
						</div>
						<div
							class="d-flex mt-n2"
							v-if="isEdit && !priceEstimates.isTransfer"
						>
							<div class="d-flex justify-content-end">
								<button
									class="btn text-warning btn-ghost btn-add"
									type="button"
									@click="handleAddTangibleAsset"
								>
									+ Thêm
								</button>
							</div>
							<div class="col-1 px-3"></div>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2 ">
					<div class="row">
						<div class="col-custom-11-5">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2">Tổng giá trị sơ bộ</label>
								</div>
							</div>
						</div>
						<div class="col-custom-11-5 mt-3">
							<div class=" table-responsive">
								<table class="table border">
									<tbody>
										<!-- <tr>
									<td>Tổng giá trị sơ bộ</td>
									<td></td>
								</tr> -->
										<tr>
											<td class="">Quyền sử dụng đất</td>
											<td class="text-right">
												{{
													totalPriceTotalArea || totalPriceTotalArea === 0
														? formatNumber(totalPriceTotalArea)
														: ""
												}}
											</td>
										</tr>

										<tr v-if="computeShowTangibleAset">
											<td class="">Công trình xây dựng</td>
											<td class="text-right">
												{{
													totalPriceTangibleAsset ||
													totalPriceTangibleAsset === 0
														? formatNumber(totalPriceTangibleAsset)
														: ""
												}}
											</td>
										</tr>
										<tr>
											<td class="font-weight-bold">Tổng cộng</td>
											<td class="text-right font-weight-bold">
												{{
													totalAllPrice || totalAllPrice === 0
														? formatNumber(totalAllPrice)
														: ""
												}}
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2 ">
					<div class="row">
						<div class="col-custom-11-5">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2">Thông tin khác</label>
								</div>
							</div>
						</div>
						<div class="col-custom-11-5 mt-3">
							<InputPercentNegative
								v-model="step_3.difference_amplitude"
								vid="difference_amplitude"
								:disabled="!isEdit"
								label="Biên độ chệnh lệch (%)"
								class="form-group-container"
							/>
							<InputTextarea
								rows="5"
								v-model="step_3.note"
								vid="note"
								:disableInput="!isEdit"
								label="Ghi chú"
								class="form-group-container"
							/>
						</div>
					</div>
				</div>
				<!-- <div class="container-fluid color_content">
					<div class="row mt-3">
						<div class="col-custom-11-5">
							<hr style="border-top: 5px solid #007ec6; margin: 0;" />
							<div class="row mt-3">
								<div class="col-md-12 col-lg-12">
									<InputTextPrefixCustom
										id="petitioner_name"
										placeholder="Ông / Bà"
										v-model="step_3.petitioner_name"
										vid="petitioner_name"
										:disabledInput="priceEstimates.isTransfer ? true : false"
										:iconUser="true"
										:showIcon="true"
										label="Tên người yêu cầu"
										:requiredIcon="true"
										rules="required"
										class="form-group-container "
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputDatePicker
										v-model="step_3.request_date"
										vid="request_date"
										label="Ngày yêu cầu"
										placeholder="Ngày / tháng / năm"
										rules="required"
										:disabled="priceEstimates.isTransfer ? true : false"
										:requiredIcon="true"
										:formatDate="'DD/MM/YYYY'"
										class="form-group-container "
										@change="step_3.request_date = $event"
									/>
								</div>

								<div class="col-md-12 col-lg-6">
									<InputCategory
										v-model="step_3.appraise_purpose_id"
										class="form-group-container input_certification_brief"
										vid="appraise_purpose_id"
										label="Mục đích thẩm định"
										:disabled="priceEstimates.isTransfer ? true : false"
										rules="required"
										:requiredIcon="true"
										:options="optionsAppraisalPurposes"
										@change="step_3.appraise_purpose_id = $event"
									/>
								</div>
							</div>
						</div>
					</div>
				</div> -->
			</div>
			<div class="card-body card-info" v-show="showCardDetailTraffic" v-else>
				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-6  font-weight-bold">
									Tên tài sản
								</div>
								<div class="col-custom-1-5 font-weight-bold text-right">
									Diện tích (m<sup>2</sup>)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Đơn giá (đ)
								</div>
								<div class="col-2 font-weight-bold text-right">
									Thành tiền (đ)
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div
								v-for="(apartment, index) in step_3.apartment_finals"
								:key="index"
								class=" mb-2 row content_form"
							>
								<div class="col-6">
									<span class="text-style-input">
										{{ apartment.name || "" }}
									</span>
								</div>
								<div :key="renderInputMainArea" class="col-custom-1-5">
									<span class="number-style-input">
										{{ apartment.total_area || "" }}
									</span>
								</div>
								<div class="col-2">
									<InputCurrencyUnit
										v-if="!priceEstimates.apartment_asset_id"
										:id="`apartmentunit_price${index}`"
										v-model="apartment.unit_price"
										:vid="'unit_price' + index"
										:max="999999999"
										nonLabel="Đơn giá"
										:required="true"
										class="w-100"
										@change="handleChangePriceApartment($event, index)"
									/>

									<span class="number-style-input" v-else>
										{{
											apartment.unit_price
												? formatNumber(apartment.unit_price)
												: ""
										}}
									</span>
								</div>
								<div class="col-2">
									<span class="number-style-input">
										{{
											apartment.total_price || apartment.total_price === 0
												? formatNumber(apartment.total_price)
												: ""
										}}
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2 ">
					<div class="row">
						<div class="col-custom-11-5">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2">Thông tin khác</label>
								</div>
							</div>
						</div>
						<div class="col-custom-11-5 mt-3">
							<InputPercentNegative
								v-model="step_3.difference_amplitude"
								vid="difference_amplitude"
								:disabled="!isEdit"
								label="Biên độ chệnh lệch (%)"
								class="form-group-container"
							/>
							<InputTextarea
								rows="5"
								v-model="step_3.note"
								vid="note"
								:disableInput="!isEdit"
								label="Ghi chú"
								class="form-group-container"
							/>
						</div>
					</div>
				</div>
				<!-- <div class="container-fluid color_content">
					<div class="row">
						<div class="col-custom-11-5">
							<hr style="border-top: 5px solid #007ec6; margin: 0;" />
							<div class="row">
								<div class="col-md-12 col-lg-12">
									<InputTextPrefixCustom
										id="petitioner_name"
										placeholder="Ông / Bà"
										v-model="step_3.petitioner_name"
										vid="petitioner_name"
										:disabledInput="priceEstimates.isTransfer ? true : false"
										:iconUser="true"
										:showIcon="true"
										:label="
											!miscVariable.isApartment
												? 'Tên người yêu cầu'
												: 'Tên Khách hàng / đơn vị / tổ chức yêu cầu'
										"
										:requiredIcon="true"
										rules="required"
										class="form-group-container "
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputDatePicker
										v-model="step_3.request_date"
										vid="request_date"
										label="Ngày yêu cầu"
										placeholder="Ngày / tháng / năm"
										rules="required"
										:disabled="priceEstimates.isTransfer ? true : false"
										:requiredIcon="true"
										:formatDate="'DD/MM/YYYY'"
										class="form-group-container "
										@change="step_3.request_date = $event"
									/>
								</div>

								<div class="col-md-12 col-lg-6">
									<InputCategory
										v-model="step_3.appraise_purpose_id"
										class="form-group-container input_certification_brief"
										vid="appraise_purpose_id"
										label="Mục đích thẩm định"
										:disabled="priceEstimates.isTransfer ? true : false"
										rules="required"
										:requiredIcon="true"
										:options="optionsAppraisalPurposes"
										@change="step_3.appraise_purpose_id = $event"
									/>
								</div>
							</div>
						</div>
					</div>
				</div> -->
			</div>
		</div>

		<ModalMap
			v-if="openModalMap"
			@cancel="openModalMap = false"
			:location="location"
			:address="step_3.full_address_street"
			:center_map="step_3.coordinates"
			@action="handleCoordinates"
		/>
	</div>
</template>
<style lang="scss">
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import InputPercent from "@/components/Form/InputPercent";
import InputCurrencyUnit from "@/components/Form/InputCurrencyUnit";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputSwitch from "@/components/Form/InputSwitch";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCategoryBoolean from "@/components/Form/InputCategoryBoolean";
import InputCategoryCustom from "@/components/Form/InputCategoryCustom";
import InputAreaCustom from "@/components/Form/InputAreaCustom";

import ModalDeleteIndex from "@/components/Modal/ModalDeleteIndex";
import ModalMap from "./modals/ModalMap";
import { Tabs, TabItem } from "vue-material-tabs";
import File from "@/models/File";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";

import {
	LMap,
	LControlZoom,
	LTileLayer,
	LMarker,
	LTooltip,
	LIcon,
	LControl,
	LControlLayers
} from "vue2-leaflet";
import Vue from "vue";
import Icon from "buefy";
import InputLengthArea from "@/components/Form/InputLengthArea.vue";
import InputPercentNegative from "@/components/Form/InputPercentNegative";
import _ from "lodash";

Vue.use(Icon);
export default {
	name: "Step1",
	props: ["isEdit"],
	components: {
		InputPercent,
		InputCurrencyUnit,
		InputTextPrefixCustom,
		InputAreaCustom,
		InputPercentNegative,
		InputCategoryCustom,
		InputCategoryBoolean,
		InputCategory,
		InputText,
		InputTextarea,
		InputSwitch,
		InputNumberFormat,
		InputDatePicker,
		ModalMap,
		ModalDeleteIndex,
		Tabs,
		TabItem,
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LControlLayers,
		LIcon,
		InputLengthArea
	},
	setup() {
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
		const isMobile = ref(checkMobile());

		const priceEstimateStore = usePriceEstimatesStore();
		const { priceEstimates, miscInfo, miscVariable, configThis } = storeToRefs(
			priceEstimateStore
		);

		// const step_3 = ref(_.cloneDeep(priceEstimates.value.step_3));

		const step_3 = ref(null);
		const keyRefreshTotalPriceMainLand = ref(0);
		const getStartedLand = () => {
			const step1 = priceEstimates.value.step_1;
			const tempTotalArea = [];
			if (step1.total_area) {
				for (let index = 0; index < step1.total_area.length; index++) {
					const element = step1.total_area[index];
					if (element.main_area > 0) {
						tempTotalArea.push(element);
					}
				}
			}

			let totals = {};
			let counts = {};

			if (priceEstimates.value.step_2.assets_general) {
				if (priceEstimates.value.step_2.assets_general.length > 0) {
					priceEstimates.value.step_2.assets_general.forEach(asset => {
						if (asset.properties) {
							asset.properties.forEach(property => {
								if (property.property_detail) {
									property.property_detail.forEach(detail => {
										const id = detail.land_type_purpose;
										const unit_price = Number(detail.price_land); // price_land

										if (!isNaN(unit_price)) {
											if (!totals[id]) {
												totals[id] = 0;
												counts[id] = 0;
											}

											totals[id] += unit_price;
											counts[id]++;
										}
									});
								}
							});
						}
					});

					for (let id in totals) {
						let average = Math.floor(totals[id] / counts[id]);

						tempTotalArea.forEach(area => {
							if (area.land_type_purpose_id == id) {
								area.unit_price = average;
								area.total_price = (area.main_area * average).toFixed(0);
							}
						});
					}
				} else {
					tempTotalArea.forEach(area => {
						area.unit_price = 0;
						area.total_price = 0;
					});
				}
			}

			if (priceEstimates.value.step_3 && priceEstimates.value.step_3.id) {
				step_3.value = priceEstimates.value.step_3;

				if (step_3.value.asset_type_id === 38) {
					miscVariable.value.isHaveContruction = true;
				} else {
					miscVariable.value.isHaveContruction = false;
					priceEstimates.value.step_3.tangible_assets = [];
				}
				if (!step_3.value.total_area || step_3.value.total_area.length === 0) {
					step_3.value.total_area = tempTotalArea;
					step_3.value.planning_area = step1.planning_area
						? step1.planning_area.map(area => ({
								...area,
								unit_price: 0,
								total_price: 0
						  }))
						: [];
				}
				if (!step_3.value.difference_amplitude) {
					step_3.value.difference_amplitude = 0;
				}

				if (!step_3.value.note) {
					step_3.value.note = `- Phòng thẩm định của NOVA sơ bộ giá trị của tài sản căn cứ vào các thông tin được ghi nhận trên Hồ sơ pháp lý, thông tin mà khách hàng cung cấp. Giá trị sơ bộ này sẽ thay đổi khi có sự sai lệch giữa thực tế và hồ sơ pháp lý, thông tin khách hàng cung cấp khi tiến hành thẩm định thực tế.
- Trong trường hợp tài sản có hạn chế lớn phát sinh (quy hoạch mới, đường đâm, gầm mộ, tranh chấp, ...) thì có thể sai số lớn hơn dự kiến.
- Trong phạm vi hồ sơ này. NOVA chỉ xem xét giá trị phần diện tích đất phù hợp quy hoạch, phần đất không phù hợp quy hoạch sẽ được tính toán trên cơ sở giá do UBND tỉnh công bố.`;
				}
			} else {
				if (
					step1.general_infomation &&
					((step1.general_infomation.assetType &&
						step1.general_infomation.assetType.description === "ĐẤT CÓ NHÀ") ||
						step1.general_infomation.asset_type_id === 38)
				) {
					miscVariable.value.isHaveContruction = true;
				} else {
					miscVariable.value.isHaveContruction = false;
					priceEstimates.value.step_3.tangible_assets = [];
				}

				const address =
					// (step1.general_infomation.doc_no
					// 	? "Tờ " + step1.general_infomation.doc_no + ", "
					// 	: "") +
					// (step1.general_infomation.land_no
					// 	? "Thửa " + step1.general_infomation.land_no + ", "
					// 	: "") +
					step1.general_infomation.full_address_street;

				if (priceEstimates.value.step_3.reInit) {
					priceEstimates.value.step_3 = {
						petitioner_name: priceEstimates.value.step_3.petitioner_name,
						request_date: priceEstimates.value.step_3.request_date,
						appraise_purpose_id:
							priceEstimates.value.step_3.appraise_purpose_id,
						asset_type_id: step1.general_infomation.asset_type_id,
						appraise_asset: step1.general_infomation.appraise_asset,
						full_address: address,
						description: step1.traffic_infomation.description,
						coordinates: step1.general_infomation.coordinates,
						total_area: tempTotalArea
							? tempTotalArea.map(area => ({
									...area
							  }))
							: [],
						planning_area: step1.planning_area
							? step1.planning_area.map(area => ({
									...area,
									unit_price: 0,
									total_price: 0
							  }))
							: [],
						tangible_assets: [],
						appraise_land_sum_area: step1.land_details.appraise_land_sum_area,
						difference_amplitude:
							priceEstimates.value.step_3.difference_amplitude,
						note: priceEstimates.value.step_3.note
					};
				} else {
					priceEstimates.value.step_3 = {
						petitioner_name: "Ông / Bà",
						request_date: "",
						appraise_purpose_id: "",
						asset_type_id: step1.general_infomation.asset_type_id,
						appraise_asset: step1.general_infomation.appraise_asset,
						full_address: address,
						description: step1.traffic_infomation.description,
						coordinates: step1.general_infomation.coordinates,
						total_area: tempTotalArea
							? tempTotalArea.map(area => ({
									...area
							  }))
							: [],
						planning_area: step1.planning_area
							? step1.planning_area.map(area => ({
									...area,
									unit_price: 0,
									total_price: 0
							  }))
							: [],
						tangible_assets: [],
						appraise_land_sum_area: step1.land_details.appraise_land_sum_area,
						difference_amplitude: 0,
						note: `- Phòng thẩm định của NOVA sơ bộ giá trị của tài sản căn cứ vào các thông tin được ghi nhận trên Hồ sơ pháp lý, thông tin mà khách hàng cung cấp. Giá trị sơ bộ này sẽ thay đổi khi có sự sai lệch giữa thực tế và hồ sơ pháp lý, thông tin khách hàng cung cấp khi tiến hành thẩm định thực tế.
- Trong trường hợp tài sản có hạn chế lớn phát sinh (quy hoạch mới, đường đâm, gầm mộ, tranh chấp, ...) thì có thể sai số lớn hơn dự kiến.
- Trong phạm vi hồ sơ này. NOVA chỉ xem xét giá trị phần diện tích đất phù hợp quy hoạch, phần đất không phù hợp quy hoạch sẽ được tính toán trên cơ sở giá do UBND tỉnh công bố.`
					};
				}
				step_3.value = priceEstimates.value.step_3;
			}
		};
		const getStartedApartment = () => {
			const step1 = priceEstimates.value.step_1;
			let total = 0;
			let count = 0;

			if (priceEstimates.value.step_2.assets_general) {
				priceEstimates.value.step_2.assets_general.forEach(asset => {
					total += Number(asset.average_land_unit_price);
					count++;
				});
			}
			const average = Math.floor(total / count) || 0;
			const totalPrice = step1.apartment_properties.area * average;
			if (priceEstimates.value.step_3 && priceEstimates.value.step_3.id) {
				step_3.value = priceEstimates.value.step_3;
				if (step_3.value.apartment_finals.length === 0) {
					step_3.value.apartment_finals = [
						{
							name: step1.general_infomation.full_address,
							total_area: step1.apartment_properties.area,
							unit_price: average,
							total_price: totalPrice
						}
					];
				}
				if (!step_3.value.difference_amplitude) {
					step_3.value.difference_amplitude = 0;
				}
				if (!step_3.value.note) {
					step_3.value.note = `- Phòng thẩm định của NOVA sơ bộ giá trị của tài sản căn cứ vào các thông tin được ghi nhận trên Hồ sơ pháp lý, thông tin mà khách hàng cung cấp. Giá trị sơ bộ này sẽ thay đổi khi có sự sai lệch giữa thực tế và hồ sơ pháp lý, thông tin khách hàng cung cấp khi tiến hành thẩm định thực tế.
- Trong trường hợp tài sản có hạn chế lớn phát sinh (quy hoạch mới, đường đâm, gầm mộ, tranh chấp, ...) thì có thể sai số lớn hơn dự kiến.
- Trong phạm vi hồ sơ này. NOVA chỉ xem xét giá trị phần diện tích đất phù hợp quy hoạch, phần đất không phù hợp quy hoạch sẽ được tính toán trên cơ sở giá do UBND tỉnh công bố.`;
				}
			} else {
				const address = step1.general_infomation.full_address;
				if (priceEstimates.value.step_3.reInit) {
					priceEstimates.value.step_3 = {
						petitioner_name: priceEstimates.value.step_3.petitioner_name,
						request_date: priceEstimates.value.step_3.request_date,
						appraise_purpose_id:
							priceEstimates.value.step_3.appraise_purpose_id,
						asset_type_id: step1.general_infomation.asset_type_id,
						appraise_asset: step1.general_infomation.appraise_asset,
						full_address: address,
						description: step1.apartment_properties.description,
						coordinates: step1.general_infomation.coordinates,
						total_area: [],
						planning_area: [],
						tangible_assets: [],
						apartment_finals: [
							{
								name: address,
								total_area: step1.apartment_properties.area,
								unit_price: average,
								total_price: totalPrice
							}
						],
						appraise_land_sum_area: null,
						difference_amplitude:
							priceEstimates.value.step_3.difference_amplitude,
						note: priceEstimates.value.step_3.note
					};
				} else {
					priceEstimates.value.step_3 = {
						petitioner_name: "Ông / Bà",
						request_date: "",
						appraise_purpose_id: "",
						asset_type_id: step1.general_infomation.asset_type_id,
						appraise_asset: step1.general_infomation.appraise_asset,
						full_address: address,
						description: step1.apartment_properties.description,
						coordinates: step1.general_infomation.coordinates,
						total_area: [],
						planning_area: [],
						tangible_assets: [],
						apartment_finals: [
							{
								name: address,
								total_area: step1.apartment_properties.area,
								unit_price: average,
								total_price: totalPrice
							}
						],
						appraise_land_sum_area: null,
						difference_amplitude: 0,
						note: `- Phòng thẩm định của NOVA sơ bộ giá trị của tài sản căn cứ vào các thông tin được ghi nhận trên Hồ sơ pháp lý, thông tin mà khách hàng cung cấp. Giá trị sơ bộ này sẽ thay đổi khi có sự sai lệch giữa thực tế và hồ sơ pháp lý, thông tin khách hàng cung cấp khi tiến hành thẩm định thực tế.
				- Trong trường hợp tài sản có hạn chế lớn phát sinh (quy hoạch mới, đường đâm, gầm mộ, tranh chấp, ...) thì có thể sai số lớn hơn dự kiến.
				- Trong phạm vi hồ sơ này. NOVA chỉ xem xét giá trị phần diện tích đất phù hợp quy hoạch, phần đất không phù hợp quy hoạch sẽ được tính toán trên cơ sở giá do UBND tỉnh công bố.`
					};
				}
				step_3.value = priceEstimates.value.step_3;
			}
		};
		if (!miscVariable.value.isApartment) {
			getStartedLand();
		} else {
			getStartedApartment();
		}

		const checkShowPlanning = ref(false);
		return {
			keyRefreshTotalPriceMainLand,
			step_3,
			isMobile,
			miscInfo,
			miscVariable,
			priceEstimates,
			priceEstimateStore,
			checkShowPlanning
		};
	},
	computed: {
		computeShowTangibleAset() {
			let boolA = false;
			if (this.miscVariable.isHaveContruction && this.step_3.tangible_assets) {
				boolA = true;
			}
			if (
				this.priceEstimates.appraise_id &&
				this.step_3.tangible_assets.length === 0
			) {
				boolA = false;
			}

			return boolA;
		},
		totalPriceTotalArea() {
			const temp = this.step_3.total_area.reduce((total, area) => {
				area.total_price = area.total_price || 0;
				return total + Number(area.total_price);
			}, 0);
			const temp2 = this.step_3.planning_area.reduce((total, area) => {
				area.total_price = area.total_price || 0;
				return total + Number(area.total_price);
			}, 0);
			return temp + temp2;
		},

		totalPriceTangibleAsset() {
			return this.step_3.tangible_assets.reduce((total, area) => {
				area.total_price = area.total_price || 0;
				return total + Number(area.total_price);
			}, 0);
		},
		totalAllPrice() {
			return this.totalPriceTotalArea + this.totalPriceTangibleAsset;
		},
		optionsTypePurposes() {
			return {
				data: this.miscInfo.type_purposes,
				id: "id",
				key: "description"
			};
		},
		optionMainOrNot() {
			return {
				data: this.miscInfo.optionMainChoose,
				id: "id",
				key: "description"
			};
		},
		optionsType() {
			return {
				data: this.miscInfo.propertyTypes,
				id: "id",
				key: "description"
			};
		},
		optionsTopographic() {
			return {
				data: this.miscInfo.topographic,
				id: "id",
				key: "description"
			};
		},
		optionsProvince() {
			return {
				data: this.miscInfo.provinces,
				id: "id",
				key: "name"
			};
		},
		optionsDistrict() {
			return {
				data: this.miscInfo.districts,
				id: "id",
				key: "name"
			};
		},
		optionsWard() {
			return {
				data: this.miscInfo.wards,
				id: "id",
				key: "name"
			};
		},
		optionsStreet() {
			return {
				data: this.miscInfo.streets,
				id: "id",
				key: "name"
			};
		},
		optionsDistance() {
			return {
				data: this.miscInfo.distances,
				id: "id",
				key: "name"
			};
		},
		optionsMaterial() {
			return {
				data: this.miscInfo.materials,
				id: "id",
				key: "description"
			};
		},
		optionsAppraisalPurposes() {
			return {
				data: this.miscInfo.appraiser_purposes,
				id: "id",
				key: "name"
			};
		},

		optionsHousingType() {
			return {
				data: this.miscInfo.housingTypes,
				id: "id",
				key: "description"
			};
		}
	},
	data() {
		return {
			key_render: 10002,
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			showCardDetailAppraise: true,
			showCardDetailTraffic: true,
			showCardDetailEconomicAndSocial: true,
			showCardDetailImage: true,
			openModalMap: false,
			imageMap: true,
			location: {
				lng: "",
				lat: ""
			},
			updateCheckBox: 1200,
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},
			url:
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff",
			type: "",
			file: "",
			renderInputMainArea: 23123123,
			material: "",
			imageType: null,
			imgOverall: null,
			imageCurrentStatus: null,
			imageJuridical: null,
			tileProviders: [
				{
					name: "Bản đồ ranh tờ, thửa",
					visible: true,
					url: "https://cdn.estatemanner.com/tile/ranh_thua/{z}/{x}/{y}.png",
					attribution: "© Fastvalue",
					type: "overlay"
				},
				{
					name: "Bản đồ thông tin quy hoạch",
					visible: false,
					attribution: "© Fastvalue",
					url: "https://cdn.estatemanner.com/tile/qhsdd/{z}/{x}/{y}.png",
					type: "overlay"
				},
				{
					name: "Bản đồ quy hoạch lộ giới",
					visible: false,
					attribution: "© Fastvalue",
					url: "https://cdn.estatemanner.com/tile/qhlg/{z}/{x}/{y}.png",
					type: "overlay"
				}
			]
		};
	},
	async mounted() {
		if (this.$refs.map_step1 && this.$refs.map_step1.mapObject) {
			this.$nextTick(() => {
				this.$refs.map_step1.mapObject.invalidateSize();
			});
		}
		await this.initMap();
		await this.getImageDescriptions(this.miscInfo.imageDescriptions);
	},
	methods: {
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
		getImageDescriptions(data) {
			this.imageType = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() ===
					"đường tiếp giáp tài sản thẩm định giá"
			);
			this.imgOverall = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() ===
					"tổng thể tài sản thẩm định giá"
			);
			this.imageCurrentStatus = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() ===
					"hiện trạng tài sản thẩm định giá"
			);
			this.imageJuridical = data.find(
				imageDescription =>
					imageDescription.description.toLowerCase() === "pháp lý tài sản"
			);
		},
		async initMap() {
			// eslint-disable-next-line no-undef
			if (this.step_3.coordinates) {
				this.map.center = [
					this.step_3.coordinates.split(",")[0],
					this.step_3.coordinates.split(",")[1]
				];
				this.markerLatLng = [
					this.step_3.coordinates.split(",")[0],
					this.step_3.coordinates.split(",")[1]
				];
				this.map.zoom = 17;
			} else {
				this.markerLatLng = [10.964112, 106.856461];
				this.map.center = [10.964112, 106.856461];
			}
		},
		changeProvince(provinceId) {
			this.priceEstimateStore.changeProvince(provinceId);
		},
		changeDistrict(id) {
			this.priceEstimateStore.changeDistrict(id);
		},
		changeWard(id) {
			this.priceEstimateStore.findWard(id);
		},
		changeStreet(id) {
			this.priceEstimateStore.changeStreet(id);
		},
		changeDistance(id) {
			this.priceEstimateStore.findDistance(id);
		},
		changeAssetTypeFinal(id) {
			if (id === 39) {
				this.step_3.asset_type_id = "";
				this.$toast.open({
					message: "Hiện tại chức năng này chưa được mở ở phiên bản dùng thử",
					type: "error",
					position: "top-right",
					duration: 5000
				});
			} else this.priceEstimateStore.changeAssetTypeFinal(id);
		},
		// handle coordinates from map
		handleOpenModalMap() {
			this.openModalMap = true;
			this.key_map += 1;
		},
		handleCoordinates(coordinates) {
			this.step_3.coordinates = coordinates;
			this.location.lat = coordinates.split(",")[0];
			this.location.lng = coordinates.split(",")[1];
			this.map.center = [
				parseFloat(this.location.lat),
				parseFloat(this.location.lng)
			];
			this.markerLatLng = [
				parseFloat(this.location.lat),
				parseFloat(this.location.lng)
			];
		},

		handleView() {
			if (
				this.url ===
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff"
			) {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url =
					"https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.imageMap = false;
			} else {
				this.url =
					"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.imageMap = true;
			}
		},
		formatFloat(value) {
			let num = (value / 1).toFixed(2).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		titleCase(str) {
			var splitStr = str.toLowerCase().split(" ");
			for (var i = 0; i < splitStr.length; i++) {
				// You do not need to check if i is larger than splitStr length, as your for does that for you
				// Assign it back to the array
				splitStr[i] =
					splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
			}
			// Directly return the joined string
			return splitStr.join(" ");
		},

		deleteMainArea(index) {
			if (this.step_3.total_area.length > 1) {
				this.step_3.total_area.splice(index, 1);
				// this.step_3.UBND_price.splice(index, 1);
			} else {
				this.$toast.open({
					message: "Diện tích phù hợp quy hoạch không được rỗng",
					type: "error",
					position: "top-right"
				});
			}
			let checkIsCheckFacility = this.step_3.total_area.filter(
				item => item.is_transfer_facility === true
			);
			if (checkIsCheckFacility && checkIsCheckFacility.length === 0) {
				this.step_3.total_area[0].is_transfer_facility = true;
			}
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
		},
		addPlanningArea() {
			this.step_3.planning_area.push({
				land_type_purpose_id: "",
				planning_area: "",
				type_zoning: "",
				land_type_purpose: {}
			});
			this.handleGetTotalArea();
			this.handleChangeUBNDPrice();
		},
		deletePlanningArea(index) {
			if (this.step_3.planning_area.length > 0) {
				this.step_3.planning_area.splice(index, 1);
			}
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
		},
		formatText(text) {
			if (!text) return "";
			text = text.toString().toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
		async changeLandTypePurpose(index, land_type_purpose_id) {
			if (land_type_purpose_id) {
				// set land_type_purpose for total_area
				let getDataTypePrupose = this.miscInfo.type_purposes.filter(
					item => item.id === land_type_purpose_id
				);
				if (getDataTypePrupose && getDataTypePrupose.length > 0) {
					this.step_3.total_area[index].land_type_purpose =
						getDataTypePrupose[0];
				}
			} else {
				this.step_3.total_area[index].land_type_purpose = {};
			}
			await this.handleChangeUBNDPrice();
			await this.handleGetTotalArea();
		},
		async changeLandPlanningPurpose(index, land_type_purpose_id) {
			if (land_type_purpose_id) {
				// set land_type_purpose for planning_area
				let getDataTypePrupose = this.miscInfo.type_purposes.filter(
					item => item.id === land_type_purpose_id
				);
				if (getDataTypePrupose && getDataTypePrupose.length > 0) {
					this.step_3.planning_area[index].land_type_purpose =
						getDataTypePrupose[0];
				} else {
					this.step_3.planning_area[index].land_type_purpose = {};
				}
			}
			await this.handleChangeUBNDPrice();
			await this.handleGetTotalArea();
		},

		handleChangeUBNDPrice() {
			const map = new Map();
			const map1 = new Map();
			let allPurposeArray = [];
			// check total_area and create data UBND_price
			this.step_3.total_area.forEach(itemMainArea => {
				if (!map.has(itemMainArea.land_type_purpose_id)) {
					map.set(itemMainArea.land_type_purpose_id);
					allPurposeArray.push({
						position_type_id: "",
						circular_unit_price: "",
						land_type_purpose_id: itemMainArea.land_type_purpose_id,
						land_type_purpose: itemMainArea.land_type_purpose
					});
				}
			});
			this.step_3.planning_area.forEach(itemPlanningArea => {
				if (!map1.has(itemPlanningArea.land_type_purpose_id)) {
					map1.set(itemPlanningArea.land_type_purpose_id);
					let checkUBNDPrice1 = allPurposeArray.filter(
						item =>
							item.land_type_purpose_id ===
							itemPlanningArea.land_type_purpose_id
					);
					if (
						checkUBNDPrice1 &&
						checkUBNDPrice1.length === 0 &&
						itemPlanningArea.land_type_purpose_id
					) {
						allPurposeArray.push({
							position_type_id: "",
							circular_unit_price: "",
							land_type_purpose_id: itemPlanningArea.land_type_purpose_id,
							land_type_purpose: itemPlanningArea.land_type_purpose
						});
					}
				}
			});
			// allPurposeArray.forEach(itemPurpose => {
			// 	let checkUBNDIsExist = this.step_3.UBND_price.filter(
			// 		itemUBND =>
			// 			itemUBND.land_type_purpose_id === itemPurpose.land_type_purpose_id
			// 	);
			// 	if (checkUBNDIsExist && checkUBNDIsExist.length > 0) {
			// 		itemPurpose.position_type_id = checkUBNDIsExist[0].position_type_id;
			// 		itemPurpose.circular_unit_price =
			// 			checkUBNDIsExist[0].circular_unit_price;
			// 	}
			// });
			// this.step_3.UBND_price = allPurposeArray;
		},
		handleChangeTangible(tangible, index) {
			let unit_price = Number(tangible.unit_price);
			let total_construction_area = Number(tangible.total_construction_area);
			let remaining_quality = Number(tangible.remaining_quality);

			if (
				!isNaN(unit_price) &&
				!isNaN(total_construction_area) &&
				!isNaN(remaining_quality)
			) {
				this.step_3.tangible_assets[index].total_price = (
					unit_price *
					total_construction_area *
					(remaining_quality / 100)
				).toFixed(0);
			}
		},
		handleChangeMainArea(value, index) {
			if (value) {
				this.step_3.total_area[index].total_area = value;
				if (this.step_3.total_area[index].unit_price) {
					this.step_3.total_area[index].total_price = (
						this.step_3.total_area[index].unit_price *
						this.step_3.total_area[index].main_area
					).toFixed(0);
				}
				this.keyRefreshTotalPriceMainLand++;
				this.handleGetTotalArea();
			}
		},
		handleChangePriceMainArea(value, index) {
			if (value) {
				this.step_3.total_area[index].unit_price = value;
				if (this.step_3.total_area[index].main_area) {
					this.step_3.total_area[index].total_price = (
						this.step_3.total_area[index].unit_price *
						this.step_3.total_area[index].main_area
					).toFixed(0);
					this.keyRefreshTotalPriceMainLand++;
				} else {
					this.step_3.total_area[index].total_price = 0;
				}
			}
		},
		handleChangePriceApartment(value, index) {
			if (value) {
				this.step_3.apartment_finals[index].unit_price = value;
				if (this.step_3.apartment_finals[index].total_area) {
					this.step_3.apartment_finals[index].total_price = (
						this.step_3.apartment_finals[index].unit_price *
						this.step_3.apartment_finals[index].total_area
					).toFixed(0);
				} else {
					this.step_3.apartment_finals[index].total_price = 0;
				}
			}
		},
		handleChangePlanningArea(value, index) {
			if (value) {
				this.step_3.planning_area[index].planning_area = value;
				if (this.step_3.planning_area[index].unit_price) {
					this.step_3.planning_area[index].total_price = (
						this.step_3.planning_area[index].unit_price *
						this.step_3.planning_area[index].planning_area
					).toFixed(0);
				}
				this.handleGetTotalArea();
			}
		},
		handleChangePricePlanningArea(value, index) {
			if (value) {
				this.step_3.planning_area[index].unit_price = value;
				if (this.step_3.planning_area[index].planning_area) {
					this.step_3.planning_area[index].total_price = (
						this.step_3.planning_area[index].unit_price *
						this.step_3.planning_area[index].planning_area
					).toFixed(0);
				} else {
					this.step_3.planning_area[index].total_price = 0;
				}
			}
		},
		handleGetTotalArea() {
			let total = 0;
			let map = new Map();
			this.step_3.total_area.forEach(item => {
				if (!map.has(item.land_type_purpose_id)) {
					map.set(item.land_type_purpose_id, item.total_area);
				}
				total += item.total_area;
			});
			this.step_3.planning_area.forEach(item => {
				if (!map.has(item.land_type_purpose_id)) {
					total += item.planning_area;
				}
			});
			this.step_3.appraise_land_sum_area = +total;
		},
		changeStatusPlanning(status) {
			if (status) {
				this.step_3.planning_area.push({
					land_type_purpose_id: "",
					planning_area: "",
					type_zoning: ""
				});
			} else {
				this.step_3.planning_area = [];
				this.handleGetTotalArea();
				this.handleChangeUBNDPrice();
			}
		},
		validateArea(landTypePurposeId) {
			let error = "";
			if (landTypePurposeId) {
				let planningAreaData = this.step_3.planning_area.filter(
					item => item.land_type_purpose_id === landTypePurposeId
				);
				let totalAreaData = this.step_3.total_area.filter(
					item => item.land_type_purpose_id === landTypePurposeId
				);
				if (
					planningAreaData &&
					totalAreaData &&
					planningAreaData.length > 0 &&
					totalAreaData.length > 0
				) {
					let planningArea = planningAreaData[0].planning_area;
					let totalArea = totalAreaData[0].total_area;
					if (planningArea > totalArea)
						error = "Diện tích vi phạm không được lớn hơn diện tích sử dụng";
					else error = "";
				} else error = "";
			} else error = "";
			this.miscInfo.step1AreaValidate = error;

			return error;
		},
		validateContructionArea() {
			let error = "";
			let total_construction_area = 0;
			for (let i = 0; i < this.step_3.tangible_assets.length; i++) {
				total_construction_area += parseFloat(
					this.step_3.tangible_assets[i].total_construction_area
				);
			}

			// if (total_construction_area > this.step_3.appraise_land_sum_area)
			// 	error = "Diện tích sàn không được lớn hơn tổng diện tích sử dụng";
			this.miscInfo.step3AreaValidate = error;

			return error;
		},

		handleDeleteMainArea(index) {
			if (this.step_3.total_area.length > 1) {
				this.step_3.total_area.splice(index, 1);
				// this.step_3.UBND_price.splice(index, 1);
			} else {
				this.$toast.open({
					message: "Diện tích phù hợp quy hoạch không được rỗng",
					type: "error",
					position: "top-right"
				});
			}
			let checkIsCheckFacility = this.step_3.total_area.filter(
				item => item.is_transfer_facility === true
			);
			if (checkIsCheckFacility && checkIsCheckFacility.length === 0) {
				this.step_3.total_area[0].is_transfer_facility = true;
			}
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
			this.renderInputMainArea += 1;
		},
		handleAddMainArea() {
			let checkIsCheckFacility = this.step_3.total_area.filter(
				item => item.is_transfer_facility === true
			);
			if (checkIsCheckFacility && checkIsCheckFacility.length > 0) {
				this.step_3.total_area.push({
					land_type_purpose_id: "",
					main_area: "",
					is_transfer_facility: false
				});
			} else {
				this.step_3.total_area.push({
					land_type_purpose_id: "",
					main_area: "",
					is_transfer_facility: true
				});
			}
			this.handleGetTotalArea();
			this.handleChangeUBNDPrice();
		},
		handleDeleteTangibleAsset(index) {
			if (this.step_3.tangible_assets.length > 0) {
				this.step_3.tangible_assets.splice(index, 1);
			}

			this.renderInputMainArea += 1;
		},
		handleAddTangibleAsset() {
			this.step_3.tangible_assets.push({
				building_type_id: "",
				total_construction_area: "",
				remaining_quality: "",
				unit_price: "",
				total_price: ""
			});
		},
		handleChangeStatusPlanning(event) {
			if (event.target.checked) {
				this.changeStatusPlanning(true);
			} else {
				this.showConfirmPlanning = true;
				this.message =
					"Tất cả thông tin diện tích vi phạm quy hoạch sẽ bị xóa, bạn có chắc muôn bỏ phần diện tích quy hoạch đã nhập";
			}
		},
		handleDeletePlanningArea(index) {
			if (this.step_3.planning_area.length > 0) {
				this.step_3.planning_area.splice(index, 1);
			}
			this.handleChangeUBNDPrice();
			this.handleGetTotalArea();
		},
		handleAddPlanningArea() {
			this.step_3.planning_area.push({
				land_type_purpose_id: "",
				planning_area: "",
				type_zoning: "",
				land_type_purpose: {}
			});
			this.handleGetTotalArea();
			this.handleChangeUBNDPrice();
		},
		checkFacility(facility) {
			if (facility) {
				let checkFacility = this.step_3.total_area.filter(
					item => item.is_transfer_facility === true
				);
				if (checkFacility && checkFacility.length > 1) {
					return true;
				} else return false;
			} else return false;
		},

		formatNumber(num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			} else if (num === 0) {
				return 0;
			} else {
				return "";
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
			font-weight: 600;
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

.form-group-container {
	margin-top: 10px;
}

.btn-delete {
	cursor: pointer;
	display: flex;
	align-items: end;
	background: #ffffff;
	border-radius: 5.88235px;
	padding: 0.5rem;
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

// .text-error {
//   color: #cd201f;
//   font-size: 14pxfont-size: 14px;
// }

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
.container_input_img {
	background-image: url("../../../assets/images/add_img.png");
	background-repeat: no-repeat;
	background-size: cover;
	background-color: transparent;
	border: 2px solid #617f9e;
	border-radius: 10px;
	min-width: 10rem;
	min-height: 10rem;
	// cursor: pointer;
	position: relative;
	margin-bottom: 5px;
}
.input_file_4 {
	left: 0;
	opacity: 0;
	width: 100%;
	height: 100%;
	cursor: pointer;
	position: absolute;
}
// map
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
.icon_marker {
	width: 25px;
}
.content_economy {
	font-weight: 500;
	margin-left: 1.5rem;
}
.main-map {
	position: relative;
	height: 100%;
	width: 100%;
	transition-timing-function: ease;
	transition-duration: 0.25s;
	overflow-x: hidden;
	@media (max-width: 1023px) {
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
}
.content_form {
	padding-left: 1.5rem;
}

.sub_header_title {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 0.5rem 2rem;
	position: relative;
	color: #00507c;
	font-weight: 700;
	font-size: 1.125rem;
	.label {
		margin-right: 15px;
	}
	label {
		margin: 0;
	}
	&::before {
		content: "";
		position: absolute;
		height: calc(100% - 16px);
		width: 3px;
		background-color: #99d161;
		border-radius: 3px;
		top: 50%;
		left: 0;
		transform: translateY(-50%);
	}
}
.result_total_appraise {
	text-align: center;
	background: #eef9ff;
	border: 1px solid #007ec6;
	border-radius: 3px;
	padding-top: 0.5rem;
	padding-bottom: 0.4rem;
}
.delete {
	background: #617f9e;
	color: #ffffff;
	width: 23px;
	height: 26px;
	text-align: center;
	line-height: 1.5;
	cursor: pointer;
	font-weight: 700;
	position: absolute;
	border-radius: 4px;
}
.contain-img__property {
	padding-left: 0px;
	padding-bottom: 5px;
}
.asset-img {
	height: 10rem;
	border: 1px var(--primary) solid;
	border-radius: 9px;
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
.text-style-input {
	display: flex;
	align-items: center;
	font-size: 1rem !important;
	height: 100%;
}

.number-style-input {
	display: flex;
	align-items: center;
	justify-content: flex-end;
	font-size: 1rem !important;
	height: 100%;
}
.col-custom-1-5 {
	flex: 0 0 12.5%; /* 1.5 out of 12 is 12.5% */
	max-width: 12.5%;
}
.col-custom-4-5 {
	flex: 0 0 37.5%; /* 4.5 out of 12 is 37.5% */
	max-width: 37.5%;
}
.col-custom-5-5 {
	flex: 0 0 45.83%; /* 5.5 out of 12 is 45.83% */
	max-width: 45.83%;
}
.col-custom-11-5 {
	flex: 0 0 95.83%; /* 11.5 out of 12 is 95.83% */
	max-width: 95.83%;
}
.col-custom-0-5 {
	flex: 0 0 4.16%; /* 0.5 out of 12 is 4.16% */
	max-width: 4.16%;
}
</style>
