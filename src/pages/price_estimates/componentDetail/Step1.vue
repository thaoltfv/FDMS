<template>
	<div v-if="step_1">
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Thông tin chung về tài sản thẩm định</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailAppraise = !showCardDetailAppraise"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showCardDetailAppraise">
				<div class="container-fluid color_content">
					<div class="row">
						<div class="col-12 col-lg-7">
							<div class="row">
								<div
									class="col-md-12 col-lg-6"
									v-if="!miscVariable.isApartment"
								>
									<InputCategory
										:disabled="true"
										v-model="step_1.general_infomation.asset_type_id"
										vid="asset_type_id"
										label="Loại tài sản"
										rules="required"
										class="form-group-container"
										:options="optionsType"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										:disabled="true"
										v-model="step_1.general_infomation.province_id"
										vid="province_id"
										label="Tỉnh/Thành"
										rules="required"
										class="form-group-container"
										:options="optionsProvince"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										:disabled="true"
										v-model="step_1.general_infomation.district_id"
										vid="district_id"
										label="Quận/Huyện"
										rules="required"
										class="form-group-container"
										:options="optionsDistrict"
									/>
								</div>
								<div class="col-12" v-if="miscVariable.isApartment">
									<InputCategory
										v-model="step_1.general_infomation.project_id"
										vid="project_id"
										:disabled="true"
										rules="required"
										label="Tên chung cư"
										class="form-group-container"
										:options="optionsProjects"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										:disabled="true"
										v-model="step_1.general_infomation.ward_id"
										vid="ward_id"
										label="Phường/Xã"
										rules="required"
										class="form-group-container"
										:options="optionsWard"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputCategory
										:disabled="true"
										v-model="step_1.general_infomation.street_id"
										vid="street_id"
										label="Đường/Phố"
										rules="required"
										class="form-group-container"
										:options="optionsStreet"
									/>
								</div>
								<div
									class="col-md-12 col-lg-6"
									v-if="!miscVariable.isApartment"
								>
									<InputCategory
										:disabled="true"
										v-model="step_1.general_infomation.distance_id"
										vid="distance_id"
										label="Đoạn"
										class="form-group-container"
										:options="optionsDistance"
									/>
								</div>
								<div class="col-md-12 col-lg-6">
									<InputText
										:disabledInput="true"
										v-model="step_1.general_infomation.address_number"
										vid="number_address"
										label="Số nhà"
										rules="required"
										class="form-group-container"
									/>
								</div>
								<div class="col-md-12 col-lg-6" v-if="miscVariable.isApartment">
									<InputCategory
										v-model="step_1.general_infomation.position_by_unbd_id"
										vid="position_by_unbd_id"
										label="Vị trí theo UBND"
										:disabled="true"
										class="form-group-container"
										:options="optionsPosition"
									/>
								</div>
								<div class="col-md-6 col-lg-3" v-if="!miscVariable.isApartment">
									<InputText
										:disabledInput="true"
										v-model="step_1.general_infomation.doc_no"
										vid="doc_no"
										label="Số thửa"
										rules="required"
										class="form-group-container"
									/>
								</div>

								<div class="col-md-6 col-lg-3" v-if="!miscVariable.isApartment">
									<InputText
										:disabledInput="true"
										v-model="step_1.general_infomation.land_no"
										vid="land_no"
										label="Số tờ"
										rules="required"
										class="form-group-container"
									/>
								</div>
								<div class="col-12">
									<InputText
										v-model="step_1.general_infomation.full_address"
										:disabledInput="true"
										vid="doc_no"
										label="Địa chỉ"
										rules="required"
										class="form-group-container"
									/>
								</div>
								<div class="col-12" v-if="!miscVariable.isApartment">
									<InputText
										:disabledInput="true"
										v-model="step_1.general_infomation.appraise_asset"
										vid="appraise_asset"
										label="Tên tài sản thẩm định giá"
										rules="required"
										class="form-group-container"
									/>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-5">
							<div class="d-flex flex-column h-100">
								<div class="form-group-container position-relative w-100">
									<InputText
										id="coordinate"
										:disabledInput="true"
										v-model="step_1.general_infomation.coordinates"
										vid="coordinates"
										label="Tọa độ"
										class="coordinates"
										rules="required"
									/>
								</div>
								<!-- Map -->
								<div
									class="col-12 w-100 h-100 mt-3 d-none d-lg-block layer-map"
									style="flex: 1"
								>
									<div class="d-flex all-map w-100 h-100">
										<div class="main-map w-100 h-100">
											<div id="mapid" class="layer-map w-100 h-100">
												<l-map
													ref="map_step1"
													:zoom="map.zoom"
													:center="map.center"
													:maxZoom="20"
													:options="{
														attributionControl: false,
														zoomControl: false,
														dragging: false,
														touchZoom: false,
														scrollWheelZoom: false,
														doubleClickZoom: false
													}"
												>
													<l-tile-layer
														:url="url"
														:options="{ maxNativeZoom: 20, maxZoom: 20 }"
													></l-tile-layer>
													<l-tile-layer
														v-for="tileProvider in tileProviders"
														:key="tileProvider.name"
														:name="tileProvider.name"
														:visible="tileProvider.visible"
														:url="tileProvider.url"
														:attribution="tileProvider.attribution"
														:layer-type="tileProvider.type"
														:options="{ maxNativeZoom: 20, maxZoom: 20 }"
													/>
													<!-- <l-tile-layer :url="url"></l-tile-layer> -->
													<l-control-zoom
														position="bottomright"
													></l-control-zoom>

													<l-control position="bottomleft">
														<button
															class="btn btn-map"
															@click="handleView"
															type="button"
														>
															<img
																v-if="!imageMap"
																src="@/assets/images/im_map.png"
																alt=""
															/>
															<img
																v-if="imageMap"
																src="@/assets/images/im_satellite.png"
																alt=""
															/>
														</button>
													</l-control>
													<l-control-layers
														position="bottomleft"
													></l-control-layers>
													<l-marker :lat-lng="markerLatLng">
														<l-icon
															class-name="someExtraClass"
															:iconAnchor="[30, 58]"
														>
															<img
																style="width: 60px !important"
																class="icon_marker"
																src="@/assets/images/svg_home.svg"
																alt=""
															/>
														</l-icon>
														<l-tooltip>Vị trí tài sản</l-tooltip>
													</l-marker>
												</l-map>
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
		<div class="card" v-if="!miscVariable.isApartment">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Đặc điểm tài sản</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailTraffic = !showCardDetailTraffic"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showCardDetailTraffic">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2">Vị trí tài sản</label>
									<div class="w-100 row">
										<div class="col-12 col-md-3 col-lg-2"></div>
										<div class="col-12 col-md-3 col-lg-2">
											<div class="d-flex">
												<input
													disabled
													type="radio"
													name="front_side"
													:value="1"
													id="front_side1"
													:checked="false"
													v-model="step_1.traffic_infomation.front_side"
												/>

												<div style="margin-left: 0.5rem" class="">
													<label
														class="color_content font-weight-normal"
														style="margin-bottom: unset !important"
														for="front_side1"
														>Mặt tiền</label
													>
												</div>
											</div>
										</div>
										<div class="col-12 col-md-3 col-lg-2">
											<div class="d-flex">
												<input
													type="radio"
													name="front_side"
													:value="0"
													id="front_side2"
													:checked="false"
													v-model="step_1.traffic_infomation.front_side"
												/>
												<div style="margin-left: 0.5rem" class="">
													<label
														class="color_content font-weight-normal"
														style="margin-bottom: unset !important"
														for="front_side2"
														>Trong hẻm</label
													>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div v-if="step_1.traffic_infomation.front_side" class="col-12">
							<div class="row content_form">
								<div class="col-12 col-md-5 col-lg-3">
									<InputLengthArea
										:disabled="true"
										label="Độ rộng đường nhỏ nhất"
										:required="true"
										rules="required"
										v-model="step_1.traffic_infomation.main_road_length"
										:decimal="2"
										class="form-group-container"
									/>
								</div>
								<div class="col-12 col-md-7 col-lg-3">
									<InputCategory
										:disabled="true"
										v-model="step_1.traffic_infomation.material_id"
										label="Chất liệu đường"
										rules="required"
										class="form-group-container"
										:options="optionsMaterial"
									/>
								</div>
								<div class="infor-box pl-2 pb-0 col d-none mt-3 mr-3 d-lg-flex">
									<p>
										- <b>Độ rộng đường nhỏ nhất</b>: Chiều rộng mặt đường tiếp
										giáp với tài sản <br />
										- <b>Chất liệu đường</b>: Chất liệu mặt đường tiếp giáp với
										tài sản
									</p>
								</div>
							</div>
						</div>
						<!-- Không -->
						<div
							v-if="step_1.traffic_infomation.front_side === 0"
							class="col-12"
						>
							<div
								v-for="(detail_alley, index) in step_1.traffic_infomation
									.property_turning_time"
								:key="index"
								class="d-flex"
							>
								<div class="w-100 row content_form">
									<div class="col-12 col-lg-2">
										<InputText
											:disabledInput="true"
											label="Số lần rẽ/quẹo"
											v-model="detail_alley.turning"
											vid="turning"
											rules="required"
											disabled-input
											class="form-group-container"
										/>
									</div>
									<div class="col-12 col-lg">
										<InputLengthArea
											:disabled="true"
											label="Độ rộng đường nhỏ nhất"
											:required="true"
											rules="required"
											:value="detail_alley.main_road_length"
											class="form-group-container"
											:decimal="2"
										/>
									</div>
									<div class="col-12 col-lg-3">
										<InputCategory
											:disabled="true"
											v-model="detail_alley.material_id"
											label="Chất liệu đường"
											rules="required"
											class="form-group-container"
											:options="optionsMaterial"
										/>
									</div>
									<div class="col-12 col-lg-3">
										<InputLengthArea
											:disabled="true"
											label="Khoảng cách đến đường chính"
											:required="true"
											rules="required"
											v-model="detail_alley.main_road_distance"
											class="form-group-container"
											:decimal="2"
										/>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 mb-3">
							<InputTextarea
								:disableInput="true"
								:autosize="true"
								:maxLength="1000"
								v-model="step_1.traffic_infomation.description"
								label="Mô tả vị trí"
								class="form-group-container"
							/>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-12">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2"
										>Diện tích theo mục đích sử dụng</label
									>
								</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-5 font-weight-bold">Mục đích sử dụng</div>
								<div class="col-3 font-weight-bold text-center">
									Diện tích (m<sup>2</sup>)
								</div>
								<div class="col-3 font-weight-bold">Phân mục đích</div>
							</div>
						</div>
						<div class="col-12 mt-2">
							<div
								v-for="(main_land, index) in step_1.total_area"
								:key="index"
								class=" mb-2 row content_form"
							>
								<div class="col-5">
									<InputCategoryCustom
										:disabled="true"
										v-model="main_land.land_type_purpose_id"
										vid="land_type_purpose_id"
										nonLabel="Mục đích sử dụng"
										rules="required"
										:options="optionsTypePurposes"
									/>
								</div>
								<div :key="renderInputMainArea" class="col-3">
									<InputAreaCustom
										:disabled="true"
										v-model="main_land.total_area"
										:decimal="2"
										vid="total_area"
										:required="true"
										:max="9999999999999999"
										:min="0"
										rules="required"
										:sufix="false"
										:text_center="true"
										nonLabel="Diện tích phù hợp"
									/>
								</div>
								<div class="col-3">
									<InputCategoryBoolean
										:disabled="true"
										v-model="main_land.is_transfer_facility"
										vid="is_transfer_facility"
										nonLabel="Phân mục đích"
										:options="optionMainOrNot"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="container-fluid mb-2">
					<div class="row">
						<div class="col-12">
							<div class="select-group sub_header_title">
								<div class="d-flex">
									<label class="select-title pr-2"
										>Phần diện tích vi phạm quy hoạch</label
									>
									<!-- <label class="input-checkbox">
										<input
											id="vio_land"
											type="checkbox"
											:key="updateCheckBox"
											value="checkShowPlanning"
											v-model="checkShowPlanning"
										/>
										<span class="check-mark" />
									</label> -->
								</div>
							</div>
						</div>
						<!-- <div class="col-12 mt-3">
							<div
								v-if="step_1.planning_area.length == 0"
								class="infor-box pl-2 w-100"
							>
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
								Tài sản thẩm định chưa khai báo diện tích vi phạm quy hoạch!
							</div>
						</div> -->
						<div v-if="step_1.planning_area.length > 0" class="col-12 mt-2">
							<div class="row content_form color_content">
								<div class="col-5 font-weight-bold">Mục đích sử dụng</div>
								<div class="col-3 font-weight-bold text-center">
									Diện tích (m<sup>2</sup>)
								</div>
								<div class="col-3 font-weight-bold">Loại quy hoạch</div>
							</div>
						</div>
						<div v-if="step_1.planning_area.length > 0" class="col-12 mt-2">
							<div
								v-for="(planning_area, index) in step_1.planning_area"
								:key="index"
								class="mb-2 row content_form"
							>
								<div class="col-5">
									<InputCategoryCustom
										:disabled="true"
										v-model="planning_area.land_type_purpose_id"
										vid="land_type_purpose_id"
										nonLabel="Mục đích sử dụng"
										rules="required"
										:options="optionsTypePurposes"
									/>
								</div>
								<div class="col-3">
									<InputAreaCustom
										:disabled="true"
										v-model="planning_area.planning_area"
										:decimal="2"
										:max="9999999999999999"
										:min="0"
										vid="planning_area"
										:required="true"
										rules="required"
										:sufix="false"
										:text_center="true"
										nonLabel="Diện tích vi phạm"
									/>
								</div>
								<div class="col-3">
									<div class="col-12">
										<InputText
											:disabledInput="true"
											v-model="planning_area.type_zoning"
											vid="appraise_asset"
											nonLabel="Loại quy hoạch"
											rules="required"
										/>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div style="z-index: 1" class="card-footer">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="row content_form">
								<div class="col-5">
									<label style="margin-top: 0.5rem" class="font-weight-bold">
										Tổng diện tích thẩm định (m<sup>2</sup>)
									</label>
								</div>
								<div class="col-3 result_total_appraise">
									<strong>{{
										formatNumber(
											parseFloat(
												step_1.land_details.appraise_land_sum_area
											).toFixed(2)
										)
									}}</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card" v-else>
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Chi tiết căn hộ</h3>
					<img
						class="img-dropdown"
						:class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
						src="@/assets/images/icon-btn-down.svg"
						alt="dropdown"
						@click="showCardDetailTraffic = !showCardDetailTraffic"
					/>
				</div>
			</div>
			<div class="card-body card-info" v-show="showCardDetailTraffic">
				<div class="container-fluid">
					<div class="row">
						<InputCategory
							:disabled="true"
							v-model="step_1.apartment_properties.block_id"
							vid="block_id"
							rules="required"
							label="Block (khu)"
							class="col-12 col-lg-4 form-group-container"
							:options="optionsBlocks"
						/>
						<InputCategory
							:disabled="true"
							v-model="step_1.apartment_properties.floor_id"
							vid="floor_id"
							rules="required"
							label="Tầng"
							class="col-12 col-lg-4 form-group-container"
							:options="optionsFloors"
						/>
						<InputText
							v-model="step_1.apartment_properties.apartment_name"
							vid="apartment_name"
							:disabledInput="true"
							label="Mã căn hộ"
							rules="required"
							class="col-12 col-lg-4 form-group-container"
						/>
						<InputArea
							v-model="step_1.apartment_properties.area"
							vid="area"
							:disabled="true"
							label="Diện tích (m²)"
							rules="required"
							:max="99999999"
							class="col-12 col-lg-4 form-group-container"
						/>

						<InputNumberNoneFormat
							v-model="step_1.apartment_properties.bedroom_num"
							:disabled="true"
							vid="bedroom_num"
							label="Số phòng ngủ"
							:max="9999"
							class="col-12 col-lg-4 form-group-container"
						/>
						<InputNumberNoneFormat
							v-model="step_1.apartment_properties.wc_num"
							:disabled="true"
							vid="wc_num"
							label="Số phòng WC"
							:max="9999"
							class="col-12 col-lg-4 form-group-container"
						/>
						<InputCategory
							:disabled="true"
							v-model="step_1.apartment_properties.handover_year"
							class="col-12 col-lg-4 form-group-container"
							vid="handover_year"
							label="Năm sử dụng"
							:options="optionYearBuild"
						/>
						<InputCategory
							:disabled="true"
							v-model="step_1.apartment_properties.direction_id"
							vid="direction_id"
							label="Hướng chính"
							class="col-12 col-lg-4 form-group-container"
							:options="optionDirection"
						/>

						<InputCategory
							:disabled="true"
							v-model="step_1.apartment_properties.furniture_quality_id"
							vid="furniture_quality_id"
							label="Tình trạng nội thất"
							class="col-12 col-lg-4 form-group-container"
							:options="optionFurniture"
						/>

						<InputText
							:disabledInput="true"
							v-model="step_1.general_infomation.appraise_asset"
							vid="data.appraise_asset"
							label="Tên căn hộ"
							rules="required"
							class="col-12 form-group-container"
						/>
						<InputTextarea
							:disableInput="true"
							label="Mô tả"
							v-model="step_1.apartment_properties.description"
							vid="description"
							:rows="4"
							:maxLength="1000"
							class="col-12 form-group-container"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<style lang="scss">
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputSwitch from "@/components/Form/InputSwitch";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCategoryBoolean from "@/components/Form/InputCategoryBoolean";
import InputCategoryCustom from "@/components/Form/InputCategoryCustom";
import InputAreaCustom from "@/components/Form/InputAreaCustom";
import InputArea from "@/components/Form/InputArea";
import InputNumberNoneFormat from "@/components/Form/InputNumberNoneFormat";
import ModalDeleteIndex from "@/components/Modal/ModalDeleteIndex";
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
import _ from "lodash";

Vue.use(Icon);
export default {
	name: "Step1",
	props: ["isEdit", "addressName"],
	components: {
		InputNumberNoneFormat,
		InputArea,
		InputAreaCustom,
		InputCategoryCustom,
		InputCategoryBoolean,
		InputCategory,
		InputText,
		InputTextarea,
		InputSwitch,
		InputNumberFormat,
		InputDatePicker,
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
		const { priceEstimates, miscInfo, addressName, miscVariable } = storeToRefs(
			priceEstimateStore
		);
		// const step_1 = ref(_.cloneDeep(priceEstimates.value.step_1));
		const step_1 = ref(priceEstimates.value.step_1);
		const checkShowPlanning = ref(false);
		return {
			step_1,
			isMobile,
			miscInfo,
			miscVariable,
			addressName,
			priceEstimates,
			priceEstimateStore,
			checkShowPlanning
		};
	},
	computed: {
		optionYearBuild() {
			return {
				data: this.built_years,
				id: "year",
				key: "year"
			};
		},
		optionFurniture() {
			return {
				data: this.miscInfo.furniture_list,
				id: "id",
				key: "description"
			};
		},
		optionsPosition() {
			return {
				data: this.miscInfo.points,
				id: "id",
				key: "description"
			};
		},
		optionDirection() {
			return {
				data: this.miscInfo.directions,
				id: "id",
				key: "description"
			};
		},
		optionsProjects() {
			return {
				data: this.miscInfo.projects,
				id: "id",
				key: "name"
			};
		},
		optionsBlocks() {
			return {
				data: this.miscInfo.blocks,
				id: "id",
				key: "name"
			};
		},
		optionsFloors() {
			return {
				data: this.miscInfo.floors,
				id: "id",
				key: "name"
			};
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
			// console.log('llll tỉnh', this.miscInfo.provinces)
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
		}
	},
	data() {
		return {
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
	},
	methods: {
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
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
		async initMap() {
			// eslint-disable-next-line no-undef
			if (this.step_1.general_infomation.coordinates) {
				this.map.center = [
					this.step_1.general_infomation.coordinates.split(",")[0],
					this.step_1.general_infomation.coordinates.split(",")[1]
				];
				this.markerLatLng = [
					this.step_1.general_infomation.coordinates.split(",")[0],
					this.step_1.general_infomation.coordinates.split(",")[1]
				];
				this.map.zoom = 17;
			} else {
				this.markerLatLng = [10.964112, 106.856461];
				this.map.center = [10.964112, 106.856461];
			}
		},

		formatNumber(num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
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
</style>
