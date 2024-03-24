<template>
	<div>
		<div class="card">
			<div class="card-body card-info" v-show="showChoosingAsset">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="container_map">
								<div :key="reRenderMap" class="d-flex all-map">
									<div class="main-map">
										<div id="mapid" class="layer-map">
											<l-map
												ref="map_step6"
												style="height: 100%;"
												:zoom="map.zoom"
												:center="map.center"
												@update:zoom="zoomUpdated"
												@update:center="centerUpdated"
												:maxZoom="20"
												:options="{ zoomControl: false }"
											>
												<l-tile-layer
													:url="url"
													:options="{ maxNativeZoom: 19, maxZoom: 20 }"
												></l-tile-layer>
												<l-control-zoom position="bottomright"></l-control-zoom>
												<l-circle
													:lat-lng="circle.center"
													:radius="circle.radius"
													:color="circle.color"
												/>
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
												<l-control position="bottomleft">
													<button
														class="btn btn-orange mini_btn"
														:class="{ 'btn_loading disabled': isRefesh }"
														type="button"
														id="btnRefesh"
														@click="handleRefeshMap"
													>
														<font-awesome-icon icon="sync" />
													</button>
												</l-control>
												<l-marker :lat-lng="markerLatLng">
													<l-icon
														class-name="someExtraClass"
														:iconAnchor="[30, 58]"
													>
														<img
															style="width: 60px !important"
															src="@/assets/images/svg_home.svg"
															alt=""
														/>
													</l-icon>
													<l-tooltip>Vị trí tài sản</l-tooltip>
												</l-marker>
												<l-marker
													v-for="(location, index) in listAssetGeneral"
													:key="index"
													:lat-lng="location.center"
													@click="handleMarker(location)"
												>
													<l-icon class-name="someExtraClass">
														<img
															v-if="
																location.id === assetDetails.id &&
																	location.isChoosing
															"
															class="img-location-marker checking"
															src="@/assets/icons/ic_check_location.svg"
															alt=""
														/>
														<img
															v-else-if="
																location.id === assetDetails.id &&
																	!location.isChoosing
															"
															class="img-location-marker checking"
															src="@/assets/icons/ic_check_location.svg"
															alt=""
														/>
														<img
															v-else-if="
																location.id === assetDetails.id ||
																	location.isChoosing
															"
															class="img-location-marker"
															src="@/assets/icons/ic_check_location.svg"
															alt=""
														/>
														<!-- :src="require(`@/assets/icons/ic_pin_${marker_colors[location.transaction_type_id]}.svg`)" :alt="location.transaction_type_id"/> -->
														<img
															v-if="
																location.id === assetDetails.id &&
																	location.isChoosing
															"
															class="img-location-checked checking"
															src="@/assets/icons/ic_checked_location.svg"
															alt=""
														/>
														<img
															v-else-if="location.isChoosing"
															class="img-location-checked"
															src="@/assets/icons/ic_checked_location.svg"
															alt=""
														/>
														<!-- <div class="marker marker__blue"
                                 :class="location.center === map.center ? 'marker__active' : ''"
                                 v-if="location.transaction_type_id === 51"/>
                            <div class="marker marker__orange"
                                 :class="location.center === map.center ? 'marker__active' : ''"
                                 v-if="location.transaction_type_id === 53"/>
                            <div class="marker marker__purple"
                                 :class="location.center === map.center ? 'marker__active' : ''"
                                 v-if="location.transaction_type_id === 52"/>
                            <div class="marker marker__green"
                                 :class="location.center === map.center ? 'marker__active' : ''"
                                 v-if="location.transaction_type_id === 54"/> -->
														<img
															v-if="
																!location.isChoosing &&
																	location.id !== assetDetails.id
															"
															:id="'img_' + location.id"
															class="img-location-marker1"
															:src="
																require(`@/assets/icons/ic_pin_${
																	marker_colors[location.transaction_type_id]
																}.svg`)
															"
															:alt="location.transaction_type_id"
														/>
														<div
															v-if="
																!location.isChoosing &&
																	location.id !== assetDetails.id
															"
															:id="'price_' + location.id"
															class="price-marker"
														>
															{{
																location.total_amount
																	? formatPrice(location.total_amount)
																	: "-"
															}}
														</div>
													</l-icon>
												</l-marker>
											</l-map>
										</div>
									</div>
									<PropertiesList
										:key="key_render_list"
										@hiddenListTSSS="handleHiddenTSSS"
										:hiddenFromMapTSSS="hiddenListTSSS"
										:listTSSS="listAssetGeneral"
										:max_listTSSS="listAssetGeneralMax"
										:marker_id="marker_id"
										@get_center="handleCenter"
										@show_marker="handleShowMarker"
										@changeList="changeList"
									/>
									<div class="position-relative" style="z-index: 111;">
										<div
											v-if="showDetailAsset"
											type="button"
											class="d-flex btn__hide"
										>
											<img
												@click="cancelShowDetailAsset"
												class="button_hidden_property"
												style="width: unset;
                            left: 38px;
                            position: absolute;
                            z-index: 333;"
												src="@/assets/icons/ic_delete_1.svg"
												:class="{
													open_property: hiddenList,
													button_action_hide: !showDetailAsset
												}"
												alt=""
											/>
											<div v-if="showDetailAsset" class="card_detail_asset">
												<div
													:key="reRender"
													class="row content_detail_asset justify-content-end mx-0"
												>
													<button
														v-if="!assetDetails.isChoosing"
														class="btn btn-white btn-orange btn-add"
														type="button"
														@click="handleAddProperty(assetDetails, true)"
													>
														Chọn
													</button>
													<button
														v-if="!!assetDetails.isChoosing"
														class="btn btn-white btn-orange btn-add"
														style="background: #fa8484"
														type="button"
														@click="handleDeleteProperty(assetDetails, false)"
													>
														Bỏ Chọn
													</button>
												</div>
												<div class="w-100 container_carousel">
													<b-carousel
														style="max-height: 200px"
														v-if="
															assetDetails.pic && assetDetails.pic.length > 0
														"
														controls
														:interval="0"
													>
														<b-carousel-slide
															style="max-height: 200px"
															v-for="image in assetDetails.pic"
															:key="image.id"
															:img-src="image.link"
														/>
													</b-carousel>
													<div class="content_detail_asset d-flex">
														<img
															style="height:20px; margin-right:5px"
															src="@/assets/icons/ic_location_detail.svg"
															alt=""
														/>
														<p>
															<strong>{{ assetDetails.full_address }}</strong>
														</p>
													</div>
												</div>
												<div
													style="margin:unset; background-color:#F6F7FB"
													class="row content_detail_asset"
												>
													<div
														class="d-flex justify-content-between w-100 mt-1"
													>
														<div class="name_title color_content">Mã:</div>
														<div class="content_detail color_content">
															{{ `TSS_${assetDetails.id}` }}
														</div>
													</div>
													<div
														class="d-flex justify-content-between w-100 mt-1"
													>
														<div class="name_title color_content">
															Loại giao dịch:
														</div>
														<div class="content_detail color_content">
															{{
																assetDetails.transaction_type &&
																	assetDetails.transaction_type.description
															}}
														</div>
													</div>
													<div
														class="d-flex justify-content-between w-100 mt-1"
													>
														<div class="name_title color_content">
															Diện tích:
														</div>
														<div class="content_detail color_content">
															{{
																formatNumberArea(
																	assetDetails.properties[0]
																		.asset_general_land_sum_area
																)
															}}
															m<sup>2</sup>
														</div>
													</div>
													<div
														class="d-flex justify-content-between w-100 mt-1"
													>
														<div class="name_title color_content">
															Tổng giá trị:
														</div>
														<div class="content_detail color_content">
															{{ formatNumberArea(assetDetails.total_amount) }}
															đ
														</div>
													</div>
													<div
														class="d-flex justify-content-between w-100 mt-1"
													>
														<div class="name_title color_content">
															Ngày xác thực:
														</div>
														<div class="content_detail color_content">
															{{ formatDate(assetDetails.created_at) }}
														</div>
													</div>
													<div
														class="d-flex justify-content-between w-100 mt-1"
													>
														<div class="name_title color_content">
															Nhân viên xác thực:
														</div>
														<div class="content_detail color_content">
															{{
																assetDetails.created_by
																	? assetDetails.created_by.name
																	: "-"
															}}
														</div>
													</div>
												</div>
												<div class="content_tab_detail">
													<Tabs
														class="tab_details"
														:theme="theme"
														:navAuto="true"
													>
														<TabItem name="Mô tả chung">
															<div class="row content_detail_asset">
																<div
																	class="d-flex justify-content-between w-100 mt-2"
																>
																	<div class="name_title color_content">
																		Vị trí:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.properties[0].front_side ===
																			1
																				? "Mặt tiền"
																				: "Hẻm"
																		}}
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Bề rộng đường:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			formatNumberArea(
																				assetDetails.properties[0]
																					.front_side === 1
																					? assetDetails.properties[0]
																							.front_side_width
																					: assetDetails.properties[0]
																							.compare_property_turning_time[
																							assetDetails.properties[0]
																								.compare_property_turning_time
																								.length - 1
																					  ].main_road_length
																			)
																		}}
																		m
																	</div>
																</div>
																<div
																	v-if="
																		assetDetails.properties[0]
																			.compare_property_doc[0].doc_num
																	"
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Số tờ:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.properties[0]
																				.compare_property_doc[0].doc_num
																				? assetDetails.properties[0]
																						.compare_property_doc[0].doc_num
																				: "Không có"
																		}}
																	</div>
																</div>
																<div
																	v-else
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Số tờ:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.doc_no
																				? assetDetails.doc_no
																				: "Không có"
																		}}
																	</div>
																</div>
																<div
																	v-if="
																		assetDetails.properties[0]
																			.compare_property_doc[0].plot_num
																	"
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Số thửa:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.properties[0]
																				.compare_property_doc[0].plot_num
																				? assetDetails.properties[0]
																						.compare_property_doc[0].plot_num
																				: "Không có"
																		}}
																	</div>
																</div>
																<div
																	v-else
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Số thửa:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.land_no
																				? assetDetails.land_no
																				: "Không có"
																		}}
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Loại tài sản:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.asset_type &&
																				assetDetails.asset_type.description
																		}}
																	</div>
																</div>
															</div>
														</TabItem>
														<TabItem class="item_2" name="Thông tin đất">
															<div class="row content_detail_asset">
																<div
																	class="d-flex justify-content-between w-100 mt-2"
																>
																	<div class="name_title color_content">
																		Loại đất:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.properties[0].land_type
																				.description
																		}}
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-2"
																>
																	<div class="name_title color_content">
																		Mã MĐSD đất:
																	</div>
																	<div class="content_detail color_content">
																		<span
																			v-for="item in assetDetails.properties[0]
																				.property_detail"
																			:key="item"
																			>[
																			{{ item.land_type_purpose_data.acronym }}
																			]</span
																		>
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Tổng diện tích:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			formatNumberArea(
																				assetDetails.properties[0]
																					.asset_general_land_sum_area
																			)
																		}}
																		m<sup>2</sup>
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Kích thước:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			`${assetDetails.properties[0].front_side_width} m x ${assetDetails.properties[0].insight_width} m`
																		}}
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Hình dáng:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.properties[0].land_shape
																				.description
																		}}
																	</div>
																</div>
															</div>
														</TabItem>
														<TabItem class="item_3" name="Giá trị BĐS">
															<div class="row content_detail_asset">
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Giá rao bán:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			formatNumberArea(
																				assetDetails.total_amount
																			)
																		}}
																		đ
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Giá trị CTXD:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			assetDetails.total_construction_amount
																				? formatNumberArea(
																						assetDetails.total_construction_amount
																				  )
																				: 0
																		}}
																		đ
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Đơn giá đất:
																	</div>
																	<div class="content_detail color_content">
																		{{
																			formatNumberArea(
																				assetDetails.properties[0]
																					.property_detail[0].price_land
																			)
																		}}
																		đ
																	</div>
																</div>
															</div>
														</TabItem>
														<TabItem class="item_4" name="Nguồn">
															<div class="row content_detail_asset">
																<div
																	class="d-flex justify-content-between w-100 mt-2"
																>
																	<div class="name_title color_content">
																		Người liên hệ:
																	</div>
																	<div class="content_detail color_content">
																		{{ assetDetails.contact_person }}
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Điện thoại:
																	</div>
																	<div class="content_detail color_content">
																		{{ assetDetails.contact_phone }}
																	</div>
																</div>
																<div
																	class="d-flex justify-content-between w-100 mt-1"
																>
																	<div class="name_title color_content">
																		Ngày đăng tin:
																	</div>
																	<div class="content_detail color_content">
																		{{ formatDate(assetDetails.public_date) }}
																	</div>
																</div>
															</div>
														</TabItem>
													</Tabs>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="d-flex justify-content-between w-100">
							<div class="d-flex">
								<button
									@click="openSelectType"
									class="btn btn-white text-nowrap index-screen-button btn_create_asset"
								>
									<img
										src="@/assets/icons/ic_add_2.svg"
										style="margin-right: 8px"
										alt="search"
									/>Tạo mới TSSS
								</button>
								<button
									@click="handleShowFilterMap"
									class="btn btn-white text-nowrap index-screen-button btn_white_border"
								>
									<img
										src="@/assets/icons/ic_filter_2.svg"
										style="margin-right: 8px"
										alt="search"
									/>Điều kiện lọc
								</button>
							</div>
							<div>
								<button
									@click="showDetailsSelectedAsset"
									class="btn btn-white text-nowrap index-screen-button align-items-end"
								>
									<img
										height="25px"
										src="@/assets/icons/ic_fv_location.svg"
										style="margin-right: 8px"
									/>Đã chọn
									<strong style="color: #FF963D; margin-left: 5px">{{
										` (${assetHasChoose.length})`
									}}</strong>
								</button>
								<button
									@click="showMapGetPic"
									class="btn btn-white text-center align-items-end"
								>
									<img
										src="@/assets/icons/ic_search.svg"
										class="view_map_btn"
									/><span>Chụp bản đồ</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-title" v-if="step_2.map_img">
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
				<div
					v-if="step_2.map_img"
					:key="renderImage"
					class="container-fluid container_imageMap"
				>
					<img class="w-100" :src="step_2.map_img" alt="map" />
				</div>
			</div>
		</div>

		<ModalDetailSelectedAsset
			v-if="showDetailAllSelected && assetHasChoose.length > 0"
			:assetHasChoose="showChoosingAssetDetails"
			@cancel="handleCancelShowAllDetail"
		/>
		<ModalFilterMap
			v-if="isFilterMap"
			:radius="circle.radius"
			:frontSide="step_1.traffic_infomation.front_side"
			:bothSide="bothSide"
			:transaction="transaction"
			:assetType="assetType"
			:year="yearRange"
			@cancel="isFilterMap = false"
			@action="handleActionFilterMap"
			@filter_year="changeFilterYear"
		/>
		<ModalScreenShotMapAssets
			v-if="showModalTakeAPic && assetHasChoose.length > 0"
			:assets="assetHasChoose"
			:coordinates="step_1.general_infomation.coordinates"
			@cancel="showModalTakeAPic = false"
			@saveImageMap="saveImageMap"
		/>
		<ModalSelectTypeAsset
			v-if="open_select_type"
			@cancel="open_select_type = false"
		/>
	</div>
</template>
<style lang="scss">
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
</style>
<script>
import CertificateAsset from "@/models/CertificateAsset";
import AppraiseData from "@/models/AppraiseData";
import { TabItem, Tabs } from "vue-material-tabs";
import { BCarousel, BCarouselSlide } from "bootstrap-vue";
import ModalDetailSelectedAsset from "./modals/ModalDetailSelectedAsset";
import ModalFilterMap from "./modals/ModalFilterMap";
import ModalScreenShotMapAssets from "./modals/ModalScreenShotMapAssets";
import moment from "moment";
import Vue from "vue";
import Icon from "buefy";
import {
	LCircle,
	LControl,
	LControlZoom,
	LIcon,
	LMap,
	LMarker,
	LPopup,
	LTileLayer,
	LTooltip
} from "vue2-leaflet";
import Vue2LeafletMarkerCluster from "vue2-leaflet-markercluster";
import ModalSelectTypeAsset from "./modals/ModalSelectTypeAsset.vue";
import PropertiesList from "./PropertiesList";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";

Vue.use(Icon);
export default {
	name: "Step2",
	components: {
		ModalScreenShotMapAssets,
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon,
		LCircle,
		LPopup,
		"v-marker-cluster": Vue2LeafletMarkerCluster,
		BCarousel,
		BCarouselSlide,
		Tabs,
		TabItem,
		ModalDetailSelectedAsset,
		ModalFilterMap,
		ModalSelectTypeAsset,
		PropertiesList
	},
	computed: {},
	setup() {
		const priceEstimateStore = usePriceEstimatesStore();
		const { priceEstimates, miscVariable } = storeToRefs(priceEstimateStore);

		// const step_1 = ref(_.cloneDeep(priceEstimates.value.step_1));
		const step_2 = ref(priceEstimates.value.step_2);
		const step_1 = ref(priceEstimates.value.step_1);

		const circle = ref({
			center: [10.964112, 106.856461],
			radius: miscVariable.value.distance_max
				? miscVariable.value.distance_max * 1000
				: 2000,
			color: "blue"
		});
		const yearRange = ref(
			moment()
				.subtract(miscVariable.value.filter_year, "year")
				.format("YYYY-MM-DD")
		);
		return {
			step_1,
			step_2,
			miscVariable,
			circle,
			yearRange,
			priceEstimateStore
		};
	},
	data() {
		return {
			key_render_list: 9898989,
			hiddenListTSSS: false,
			marker_colors: {
				0: "green",
				51: "blue",
				52: "purple",
				53: "orange",
				54: "green"
			},
			theme: {
				navItem: "#000000",
				navActiveItem: "#007EC6",
				slider: "#007EC6",
				arrow: "#000000"
			},
			reRenderMap: 4000000,
			renderImage: 1500000,
			showChoosingAsset: true,
			showChoosingAssetDetails: [],
			showChoosingComparisonFactor: true,
			showDetailAllSelected: false,
			showTookAPhoto: true,
			showModalTakeAPic: false,
			hiddenList: false,
			imageMap: true,
			showDetailAsset: false,
			isFilterMap: false,
			imageMapScreenShot: "",
			markerLatLng: [10.964112, 106.856461],
			reRender: 321,
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},

			marker_id: "",
			listAssetGeneral: [],
			listAssetGeneralMax: [],
			assetHasChoose: [],
			bothSide: false,

			assetType: [38, 37],
			assetDetails: "",
			transaction: [51, 52],
			url: "https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}",
			open_select_type: false,
			isRefesh: false
		};
	},
	async mounted() {
		if (this.step_1.general_infomation.coordinates) {
			this.circle.center = [
				this.step_1.general_infomation.coordinates.split(",")[0],
				this.step_1.general_infomation.coordinates.split(",")[1]
			];
			this.markerLatLng = [
				this.step_1.general_infomation.coordinates.split(",")[0],
				this.step_1.general_infomation.coordinates.split(",")[1]
			];
			this.map.center = [
				this.step_1.general_infomation.coordinates.split(",")[0],
				this.step_1.general_infomation.coordinates.split(",")[1]
			];
		}
		this.assetHasChoose = this.step_2.assets_general;
		if (this.miscVariable.step_active >= 1) {
			await this.getListAsset();
		}
		if (this.$refs.map_stemap_step6p2) {
			setTimeout(() => {
				this.$refs.map_step6.mapObject.invalidateSize();
			}, 2000);
		}
		await this.step_2.assets_general.forEach(item => {
			this.listAssetGeneral.forEach(asset => {
				if (item.id === asset.id) {
					asset["isChoosing"] = true;
				}
			});
		});
		this.listAssetGeneralMax = this.listAssetGeneral;

		this.renderImage += 1;
		this.reRenderMap += 1;
	},
	beforeUpdate() {},
	methods: {
		async saveImageMap(image) {
			if (image) {
				const data = {
					data: image
				};
				const res = await AppraiseData.getImage(data);
				if (res.data) {
					this.imageMapScreenShot = res.data.link;
					this.step_2.map_img = res.data.link;
					this.renderImage += 1;
					this.assetHasChoose = this.assetHasChoose.sort((a, b) => b.id - a.id);
					this.showModalTakeAPic = false;
				}
			}
		},
		changeFilterYear(payload) {
			// console.log('year change', payload)
			this.$emit("filter_year", payload);
		},
		openSelectType() {
			this.open_select_type = true;
		},
		handleChangeRouter() {
			const routeData = this.$router.resolve({
				name: "warehouse.create",
				query: { asset_type_id: this.step_1.asset_type_id }
			});
			window.open(routeData.href, "_blank");
		},
		formatNumberArea(num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		formatPrice(value) {
			let num = parseFloat(value / 1)
				.toFixed(0)
				.replace(".", ",");
			if (num.length > 3 && num.length <= 6) {
				return parseFloat(num / 1000).toFixed(0) + " ng";
			} else if (num.length > 6 && num.length <= 9) {
				return parseFloat(num / 1000000).toFixed(0) + " tr";
			} else if (num.length > 9) {
				return parseFloat(num / 1000000000).toFixed(1) + " tỷ";
			} else if (num < 900) {
				return num + " đ"; // if value < 1000, nothing to do
			}
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		formatDate(value) {
			return moment(String(value)).format("DD-MM-YYYY");
		},
		format(value) {
			let num = (value / 1).toFixed(0).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		},
		formatFloat(value) {
			let num = (value / 1).toFixed(2).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		},
		formatNumber(value) {
			let num = (value / 1).toFixed(0).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		async getListAsset() {
			this.isRefesh = true;
			const distance = parseFloat(this.circle.radius / 1000).toFixed(2);
			const location = this.circle.center;
			const frontSide = this.step_1.traffic_infomation.front_side ? 1 : 0;
			const bothSide = this.bothSide;
			const transaction = "[" + this.transaction + "]";
			const assetType = "[" + this.assetType + "]";
			const yearRange = this.yearRange;
			const getAllAsset = await CertificateAsset.getSearchAllAsset(
				distance,
				location,
				frontSide,
				transaction,
				assetType,
				bothSide,
				yearRange
			);
			this.listAssetGeneral = [...getAllAsset.data];
			let checkAsset = [];
			this.listAssetGeneral.forEach(item => {
				item["center"] = [
					parseFloat(item.coordinates.split(",")[0]),
					parseFloat(item.coordinates.split(",")[1])
				];
				checkAsset = this.step_2.assets_general.filter(
					asset => asset.id === item.id
				);
				if (checkAsset.length > 0) {
					item["isChoosing"] = true;
				} else {
					item["isChoosing"] = false;
				}
			});
			this.listAssetGeneralMax = this.listAssetGeneral;
			this.isRefesh = false;
		},
		handleHidden() {
			this.hiddenList = !this.hiddenList;
			setTimeout(() => {
				this.$refs.lmap.mapObject.invalidateSize();
			}, 501);
		},
		handleHiddenTSSS(event) {
			this.hiddenListTSSS = event;
			setTimeout(() => {
				this.$refs.map_step6.mapObject.invalidateSize();
			}, 501);
			this.key_render_list++;
		},
		async handleMarker(asset) {
			const data = [asset];
			const getDetailAsset = await CertificateAsset.getDetailAssetStep6(data);
			if (getDetailAsset.data) {
				this.assetDetails = getDetailAsset.data[0];
				let checkAsset = this.listAssetGeneral.filter(
					item => item.id === asset.id && item.isChoosing === true
				);
				// // console.log('checkAsset', checkAsset)
				if (checkAsset && checkAsset.length > 0) {
					this.assetDetails["isChoosing"] = true;
				}
				this.showDetailAsset = true;
			}
		},
		hoverDetailToMarker(property) {
			this.map.center = property.center;
			this.marker_id = property.id;
		},
		handleView() {
			if (
				this.url === "https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}"
			) {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url =
					"https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile";
				this.imageMap = false;
			} else {
				this.url = "https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}";
				this.imageMap = true;
			}
		},
		handleShowFilterMap() {
			this.isFilterMap = true;
		},
		handleRefeshMap() {
			this.getListAsset();
		},
		async handleActionFilterMap(dataFilter) {
			this.circle.radius = dataFilter.radius;
			this.frontSide = dataFilter.frontSide;
			this.transaction = dataFilter.transaction;
			this.assetType = dataFilter.assetType;
			this.yearRange = dataFilter.year;
			this.bothSide = dataFilter.bothSide;
			const distance = parseFloat(dataFilter.radius / 1000).toFixed(2);
			this.distance_max = distance;
			const location = this.circle.center;
			const frontSide = dataFilter.frontSide;
			const bothSide = dataFilter.bothSide;
			const transaction = "[" + dataFilter.transaction + "]";
			const assetType = "[" + dataFilter.assetType + "]";
			const yearRange = this.yearRange;
			const getAllAsset = await CertificateAsset.getSearchAllAsset(
				distance,
				location,
				frontSide,
				transaction,
				assetType,
				bothSide,
				yearRange
			);
			this.listAssetGeneral = [...getAllAsset.data];
			this.listAssetGeneral.forEach(item => {
				item["center"] = [
					parseFloat(item.coordinates.split(",")[0]),
					parseFloat(item.coordinates.split(",")[1])
				];
				// item['isChoosing'] = false
				if (this.assetHasChoose && this.assetHasChoose.length > 0) {
					let checkAssetChoose = this.assetHasChoose.filter(
						asset => asset.id === item.id
					);
					if (checkAssetChoose && checkAssetChoose.length > 0) {
						item["isChoosing"] = true;
					} else {
						item["isChoosing"] = false;
					}
				} else {
					item["isChoosing"] = false;
				}
			});
			this.listAssetGeneralMax = this.listAssetGeneral;
			this.reRenderMap += 1;
		},
		zoomUpdated(zoom) {
			this.map.zoom = zoom;
		},
		centerUpdated(center) {
			this.center = center;
		},

		cancelShowDetailAsset() {
			this.showDetailAsset = false;
			this.assetDetails = "";
		},
		handleAddProperty(assetDetail, select) {
			for (let i = 0; i < this.listAssetGeneral.length; i++) {
				if (this.listAssetGeneral[i].id === assetDetail.id) {
					this.listAssetGeneral[i].isChoosing = select;
				}
			}
			this.assetHasChoose.push(assetDetail);
			this.assetHasChoose = this.assetHasChoose.sort((a, b) => b.id - a.id);
			this.priceEstimateStore.choosingAsset(this.assetHasChoose);
			this.reRender += 1;
			this.assetDetails.isChoosing = select;
		},
		handleDeleteProperty(assetDetail, select) {
			for (let i = 0; i < this.listAssetGeneral.length; i++) {
				if (this.listAssetGeneral[i].id === assetDetail.id) {
					this.listAssetGeneral[i].isChoosing = select;
				}
			}
			let isChoose = this.assetHasChoose.filter(
				item => item.id !== assetDetail.id
			);
			this.assetHasChoose = isChoose;
			this.priceEstimateStore.choosingAsset(isChoose);
			this.reRender += 1;
			this.assetDetails.isChoosing = select;
		},

		async showDetailsSelectedAsset() {
			if (this.assetHasChoose.length === 0) {
				this.showDetailAllSelected = false;
				this.$toast.open({
					message: "Vui lòng chọn tài sản so sánh",
					type: "error",
					position: "top-right"
				});
			} else if (this.assetHasChoose.length > 3) {
				this.$toast.open({
					message: "Chỉ được chọn tối đa 3 tài sản so sánh",
					type: "error",
					position: "top-right"
				});
			} else {
				this.assetHasChoose = this.assetHasChoose.sort((a, b) => b.id - a.id);
				const dataDetail = await CertificateAsset.getDetailAssetStep6(
					this.assetHasChoose
				);
				if (dataDetail.data) {
					this.showChoosingAssetDetails = dataDetail.data;
					this.showDetailAllSelected = true;
				}
			}
		},
		handleCancelShowAllDetail() {
			this.showDetailAllSelected = false;
		},
		showMapGetPic() {
			if (this.assetHasChoose.length === 0) {
				this.showModalTakeAPic = false;
				this.$toast.open({
					message: "Vui lòng chọn tài sản so sánh",
					type: "error",
					position: "top-right"
				});
			} else if (this.assetHasChoose.length > 3) {
				this.$toast.open({
					message: "Chỉ được chọn tối đa 3 tài sản so sánh",
					type: "error",
					position: "top-right"
				});
			} else {
				this.assetHasChoose = this.assetHasChoose.sort((a, b) => b.id - a.id);
				this.showModalTakeAPic = true;
			}
		},

		async handleCenter(asset) {
			this.handleMarker(asset);
		},
		handleShowMarker(asset) {
			this.marker_id = asset.id;
		},
		changeList(asset) {
			this.listAssetGeneral = asset;
		}
	}
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
		padding: 15px 15px 15px;

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
	height: 98%;
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
.img-location-marker1 {
	width: 40px !important;
	position: absolute;
	bottom: 2px;
	right: -15px;
	&.checking {
		animation: fade 1s infinite ease;
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
	margin-top: 5px;

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
			left: -370px;
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

.price-marker {
	width: 38px;
	height: 20px;
	font-size: 10px;
	font-weight: 500;
	position: absolute;
	padding: 2px 0;
	top: -24px;
	left: -12px;
	z-index: -1;
	color: var(--primary);
	background: white;
	text-align: center;
	&.checking {
		animation: fade 1s infinite ease;
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
		// background-image: url("@/assets/icons/ic_fv_location.svg");
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
	font-size: 0.875rem;
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
	margin-top: 1rem;
	font-size: 0.875rem;
}

@keyframes fade {
	0% {
		transform: scale(1, 1) translateY(0);
	}
	10% {
		transform: scale(1.1, 0.9) translateY(0);
	}
	30% {
		transform: scale(0.9, 1.1) translateY(-15px);
	}
	50% {
		transform: scale(1.05, 0.95) translateY(0);
	}
	57% {
		transform: scale(1, 1) translateY(-5px);
	}
	64% {
		transform: scale(1, 1) translateY(0);
	}
	100% {
		transform: scale(1, 1) translateY(0);
	}
}

.img-location-marker {
	width: 60px !important;
	position: absolute;
	bottom: 2px;
	right: -26px;
	&.checking {
		animation: fade 1s infinite ease;
	}
}

.img-location-checked {
	width: 60px !important;
	position: absolute;
	bottom: 2px;
	right: -26px;
	&.checking {
		animation: fade 1s infinite ease;
	}
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
/deep/ .leaflet-container .leaflet-marker-pane img {
	width: -webkit-fill-available;
}
.tabs {
	padding: 0 15px;

	/deep/ {
		ul {
			li {
				span {
					text-transform: none !important;
				}

				&.active {
					border-radius: unset;
					height: 20px;
				}
			}
		}
	}
}
.view_map_btn {
	margin-right: 5px;
	height: 1.1rem;
	margin-bottom: 0.2rem;
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
				background-color: white;
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
.mini_btn {
	width: 32px;
	height: 32px;
	padding: unset !important;
}
.btn_loading {
	position: relative;
	color: white !important;
	text-shadow: none !important;
	pointer-events: none;
}
.btn_loading:after {
	content: "";
	display: inline-block;
	vertical-align: text-bottom;
	border: 1px solid wheat;
	border-right-color: transparent;
	border-radius: 50%;
	color: #ffffff;
	position: absolute;
	width: 1rem;
	height: 1rem;
	left: calc(50% - 0.5rem);
	top: calc(50% - 0.5rem);
	-webkit-animation: spinner-border 0.75s linear infinite;
	animation: spinner-border 0.75s linear infinite;
}
</style>
