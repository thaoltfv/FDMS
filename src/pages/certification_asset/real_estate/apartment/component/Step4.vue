<template>
  <div>
    <div class="card">
      <div class="card-body card-info" v-show="showChoosingAsset">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="container_map">
                <div  class="d-flex all-map">
                  <div class="main-map">
                    <div :key="reRenderMap" id="mapid" class="layer-map">
                      <l-map
                        ref="map_step6"
                        style="height: 100%;"
                        :zoom="map.zoom"
                        :center="map.center"
                        @update:zoom="zoomUpdated"
                        @update:center="centerUpdated"
                        :maxZoom="20"
                        :options="{zoomControl: false}"
                      >
                        <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                        <l-control-zoom position="bottomright"></l-control-zoom>
                        <l-control position="bottomleft">
                          <button class="btn btn-orange mini_btn" type="button" id="filterButton" @click="handleRefeshMap">
                            <font-awesome-icon icon="sync" />
                          </button>
                        </l-control>
                        <l-circle
                          :lat-lng="circle.center"
                          :radius="circle.radius"
                          :color="circle.color"
                        />
                        <l-control position="bottomleft">
                          <button class="btn btn-map" @click="handleView" type="button">
                            <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                            <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
                          </button>
                        </l-control>
                        <l-marker :lat-lng="markerLatLng">
                          <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                            <img style="width: 60px !important" src="@/assets/images/svg_home.svg" alt="">
                          </l-icon>
                          <l-tooltip>Vị trí của bạn</l-tooltip>
                        </l-marker>
                        <v-marker-cluster  @clusterclick="handleClickOut">
                          <l-marker v-for="(location, index) in listAssetGeneral" :key="index" :lat-lng="location.center" @click="handleMarker($event, location)">
                            <l-icon :key="reRenderCluster" class-name="someExtraClass">
															<img v-if="location.id === assetDetails.id && location.isChoosing"
																	class="img-location-marker checking"
																	src="@/assets/icons/ic_check_location.svg" alt=""/>
															<img v-else-if="location.id === assetDetails.id && !location.isChoosing"
																	class="img-location-marker checking"
																	src="@/assets/icons/ic_check_location.svg" alt=""/>
															<img v-else-if="location.id === assetDetails.id || location.isChoosing"
																	class="img-location-marker"
																	src="@/assets/icons/ic_check_location.svg" alt=""/>
															<img v-if="location.id === assetDetails.id && location.isChoosing"
																class="img-location-checked checking"
                                 src="@/assets/icons/ic_checked_location.svg" alt=""/>
                            <img v-else-if="location.isChoosing"
                                 class="img-location-checked"
                                 src="@/assets/icons/ic_checked_location.svg" alt=""/>
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
                                  <img v-if="!location.isChoosing && location.id !== assetDetails.id" :id="'img_'+location.id" class="img-location-marker1" :src="require(`@/assets/icons/ic_pin_${marker_colors[location.transaction_type_id]}.svg`)" :alt="location.transaction_type_id">
					<div v-if="!location.isChoosing && location.id !== assetDetails.id" :id="'price_'+location.id" class="price-marker"> {{location.total_amount ? formatPrice(location.total_amount) : '-'}} </div>
                            </l-icon>
                          </l-marker>
                        </v-marker-cluster>
                      </l-map>
                    </div>
                  </div>
                  <div class="position-relative">
                    <div v-if="showDetailAsset" type="button" class="d-flex btn__hide">
                      <img @click="cancelShowDetailAsset" class="button_hidden_property"
                           src="@/assets/icons/ic_hidden_map.svg"
                           :class="{'open_property': hiddenList, 'button_action_hide':!showDetailAsset}" alt=""/>
                      <div v-if="showDetailAsset" class="card_detail_asset">
                        <div class="w-100 container_carousel">
                          <b-carousel style="max-height: 200px" v-if="assetDetails.pic && assetDetails.pic.length > 0"
                                      controls :interval="0">
                            <b-carousel-slide style="max-height: 200px" v-for="image in assetDetails.pic"
                                              :key="image.id"
                                              :img-src="image.link"/>
                          </b-carousel>
                          <div class="content_detail_asset d-flex">
                            <img style="height:20px; margin-right:5px" src="@/assets/icons/ic_location_detail.svg"
                                 alt=""/>
                            <p><strong>{{ assetDetails.full_address }}</strong></p>
                          </div>
                        </div>
                        <div style="margin:unset; background-color:#F6F7FB" class="row content_detail_asset">
                          <div class="d-flex justify-content-between w-100 mt-1">
                            <div class="name_title color_content">Mã:</div>
                            <div class="content_detail color_content">{{ `TSS_${assetDetails.id}` }}</div>
                          </div>
                          <div class="d-flex justify-content-between w-100 mt-1">
                            <div class="name_title color_content">Loại giao dịch:</div>
                            <div class="content_detail color_content">
                              {{ assetDetails.transaction_type && assetDetails.transaction_type.description }}
                            </div>
                          </div>
                          <div class="d-flex justify-content-between w-100 mt-1">
                            <div class="name_title color_content">Diện tích:</div>
                            <div class="content_detail color_content">
                              {{ formatNumberArea(assetDetails.room_details[0].area) }}
                              m<sup>2</sup>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between w-100 mt-1">
                            <div class="name_title color_content">Đơn giá:</div>
                            <div class="content_detail color_content">{{ formatNumberArea(assetDetails.average_land_unit_price) }} đ
                            </div>
                          </div>
                          <div class="d-flex justify-content-between w-100 mt-1">
                            <div class="name_title color_content">Tổng giá trị:</div>
                            <div class="content_detail color_content">{{ formatNumberArea(assetDetails.total_amount) }} đ
                            </div>
                          </div>
                          <div class="d-flex justify-content-between w-100 mt-1">
                            <div class="name_title color_content">Ngày xác thực:</div>
                            <div class="content_detail color_content">{{ formatDate(assetDetails.created_at) }}
                            </div>
                          </div>
                          <div class="d-flex justify-content-between w-100 mt-1">
                            <div class="name_title color_content">Nhân viên xác thực:</div>
                            <div class="content_detail color_content">{{ assetDetails.created_by ? assetDetails.created_by.name : '-' }}
                            </div>
                          </div>
                        </div>
                        <div class="content_tab_detail">
                          <Tabs class="tab_details" :theme="theme" :navAuto="true">
                            <TabItem name="Thông tin chung cư">
                              <div class="row content_detail_asset">
                                <div class="d-flex justify-content-between w-100 mt-2">
                                  <div class="name_title color_content">Tên chung cư:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.project ? assetDetails.project.name : '' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Tổng số block:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.project ? assetDetails.project.total_blocks : '' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Tổng số căn hộ:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.project ? assetDetails.project.total_apartments : '' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Pháp lý:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.room_details && assetDetails.room_details.length > 0 ? assetDetails.room_details[0].legal_id ? 'Có sổ' : 'Không có sổ' : 'Không có' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Năm sử dụng:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.project ? assetDetails.project.handover_year : '' }}
                                  </div>
                                </div>
                              </div>
                            </TabItem>
                            <TabItem class="item_2" name="Thông tin căn hộ">
                              <div class="row content_detail_asset">
                                <div class="d-flex justify-content-between w-100 mt-2">
                                  <div class="name_title color_content">Block:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.block ? assetDetails.block.name : '' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Tầng:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.floor ? assetDetails.floor.name : '' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Mã căn hộ:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.apartment_specification ? assetDetails.apartment_specification.apartment_name : '' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Số phòng ngủ:</div>
                                  <div class="content_detail color_content">
                                    {{assetDetails.room_details && assetDetails.room_details.length > 0 ? assetDetails.room_details[0].bedroom_num : '' }}
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Số phòng WC:</div>
                                  <div class="content_detail color_content">
                                    {{assetDetails.room_details && assetDetails.room_details.length > 0 ? assetDetails.room_details[0].wc_num : '' }}
                                  </div>
                                </div>
                              </div>
                            </TabItem>
                            <!-- <TabItem class="item_3" name="Giá trị BĐS">
                              <div class="row content_detail_asset">
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Giá rao bán:</div>
                                  <div class="content_detail color_content">
                                    {{ formatNumberArea(assetDetails.total_amount) }} đ
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Giá trị CTXD:</div>
                                  <div class="content_detail color_content">
                                    {{ assetDetails.total_construction_amount ? formatNumberArea(assetDetails.total_construction_amount) : 0 }}
                                    đ
                                  </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Đơn giá đất:</div>
                                  <div class="content_detail color_content">
                                    {{ formatNumberArea(assetDetails.total_land_unit_price) }} đ
                                  </div>
                                </div>
                              </div>
                            </TabItem> -->
                            <TabItem class="item_4" name="Nguồn">
                              <div class="row content_detail_asset">
                                <div class="d-flex justify-content-between w-100 mt-2">
                                  <div class="name_title color_content">Người liên hệ:</div>
                                  <div class="content_detail color_content">{{ assetDetails.contact_person }}</div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Điện thoại:</div>
                                  <div class="content_detail color_content">{{ assetDetails.contact_phone }}</div>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-1">
                                  <div class="name_title color_content">Ngày đăng tin:</div>
                                  <div class="content_detail color_content">
                                    {{ formatDate(assetDetails.public_date) }}
                                  </div>
                                </div>
                              </div>
                            </TabItem>
                          </Tabs>
                        </div>
                        <div :key="reRender" class="row content_detail_asset justify-content-end mx-0">
                          <button v-if="!assetDetails.isChoosing" class="btn btn-white btn-orange btn-add"
                                  type="button" @click="handleAddProperty(assetDetails ,true)">
                            Chọn
                          </button>
                          <button v-if="!!assetDetails.isChoosing" class="btn btn-white text-nowrap"
                                  type="button" @click="handleDeleteProperty(assetDetails ,false)">
                            Bỏ Chọn
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between w-100">
              <div class="d-flex">
                <button @click="handleChangeRouter" target="_blank" class="btn btn-white text-nowrap index-screen-button btn_create_asset"><img
                  src="@/assets/icons/ic_add_2.svg" style="margin-right: 8px" alt="search">Tạo mới TSSS
                </button>
                <button @click="handleShowFilterMap"
                        class="btn btn-white text-nowrap index-screen-button btn_white_border">
                  <img src="@/assets/icons/ic_filter_2.svg" style="margin-right: 8px" alt="search">Điều kiện lọc
                </button>
              </div>
              <div>
                <button @click="showDetailsSelectedAsset"
                        class="btn btn-white text-nowrap index-screen-button align-items-end">
                  <img height="25px" src="@/assets/icons/ic_fv_location.svg" style="margin-right: 8px">Đã chọn <strong
                  style="color: #FF963D; margin-left: 5px">{{ ` (${assetHasChoose.length})` }}</strong>
                </button>
                <button @click="showMapGetPic" class="btn btn-white text-center align-items-end">
                  <img src="@/assets/icons/ic_search.svg" class="view_map_btn"><span>Chụp bản đồ</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Hình ảnh bản đồ</h3>
          <img class="img-dropdown" :class="!showTookAPhoto ? 'img-dropdown__hide' : ''"
               src="@/assets/images/icon-btn-down.svg" alt="dropdown"
               @click="() => { showTookAPhoto = !showTookAPhoto;  }">
        </div>
      </div>
      <div class="card-body card-info" v-show="showTookAPhoto">
        <div v-if="data.map_img" :key="renderImage" class="container-fluid container_imageMap">
          <img class="w-100" :src="data.map_img" alt="map"/>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Yếu tố so sánh</h3>
          <img class="img-dropdown" :class="!showChoosingComparisonFactor ? 'img-dropdown__hide' : ''"
               src="@/assets/images/icon-btn-down.svg" alt="dropdown"
               @click="() => { showChoosingComparisonFactor = !showChoosingComparisonFactor;  }">
        </div>
      </div>
      <div class="card-body card-info" v-show="showChoosingComparisonFactor">
        <div class="container-fluid">
          <div class="row">
            <div class="col-6 col-lg-4" v-if="itemCompare.visible" v-for="itemCompare in comparison" :key="itemCompare.id">
              <div class="d-flex div_radio">
                <!-- <input
                  class="input"
                  :class="{'disable_input': itemCompare.slug === 'phap_ly'}"
                  type="checkbox" :value="itemCompare.slug"
                  :checked="itemCompare.slug === 'phap_ly' || itemCompare.disable"
                  :disabled="itemCompare.disable || itemCompare.slug === 'phap_ly'"
                  :id="itemCompare.slug"
                  v-model="data.comparison_factor"
                > -->
                <label class="input-checkbox">
                  <input
                  class="input"
                  :class="{'disable_input': itemCompare.slug === 'phap_ly'}"
                  type="checkbox" :value="itemCompare.slug"
                  :checked="itemCompare.slug === 'phap_ly' || itemCompare.disable"
                  :disabled="itemCompare.disable || itemCompare.slug === 'phap_ly'"
                  :id="itemCompare.slug"
                  v-model="data.comparison_factor"
                >
                  <span class="check-mark"/>
                </label>
                <div class="content_economy"><label class="color_content" style="margin-bottom: unset !important" :for="itemCompare.slug">{{itemCompare.name}}</label></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ModalDetailSelectedAsset
      v-if="showDetailAllSelected && assetHasChoose.length > 0"
      :assetHasChoose="showChoosingAssetDetails"
      @cancel="handleCancelShowAllDetail"
    />
    <ModalScreenShotMapAsset
      v-if="showModalTakeAPic && assetHasChoose.length > 0"
      :assets="assetHasChoose"
      :coordinates="coordinates"
      @cancel="showModalTakeAPic = false"
      @saveImageMap="saveImageMap"
    />
    <ModalFilterMap
      v-if="isFilterMap"
      :radius="circle.radius"
      :transaction="transaction"
      :assetType="assetType"
			:year="yearRange"
      @cancel="isFilterMap = false"
      @action="handleActionFilterMap"
    />
  </div>
</template>
<style lang="scss">
@import '../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css';
@import '../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css';
</style>
<script>
import CertificateAsset from '@/models/CertificateAsset'
import AppraiseData from '@/models/AppraiseData'
import {TabItem, Tabs} from 'vue-material-tabs'
import {BCarousel, BCarouselSlide} from 'bootstrap-vue'
import ModalScreenShotMapAsset from './modals/ModalScreenShotMapAsset'
import ModalDetailSelectedAsset from './modals/ModalDetailSelectedAsset'
import ModalFilterMap from './modals/ModalFilterMap'
import moment from 'moment'
import Vue from 'vue'
import Icon from 'buefy'
import {LCircle, LControl, LControlZoom, LIcon, LMap, LMarker, LPopup, LTileLayer, LTooltip} from 'vue2-leaflet'
import Vue2LeafletMarkerCluster from 'vue2-leaflet-markercluster'

Vue.use(Icon)
export default {
	name: 'Step4',
	props: ['data', 'comparison', 'coordinates', 'propertyTypes', 'type_purposes', 'step_active', 'distance_max'],
	components: {
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon,
		LCircle,
		LPopup,
		'v-marker-cluster': Vue2LeafletMarkerCluster,
		BCarousel,
		BCarouselSlide,
		Tabs,
		TabItem,
		ModalScreenShotMapAsset,
		ModalDetailSelectedAsset,
		ModalFilterMap
	},
	computed: {},
	data () {
		return {
      marker_colors: {
				0: 'green',
				51: 'blue',
				52: 'purple',
				53: 'orange',
				54: 'green'
			},
			theme: {
				navItem: '#000000',
				navActiveItem: '#007EC6',
				slider: '#007EC6',
				arrow: '#000000'
			},
			reRenderMap: 4000000,
			reRenderCluster: 4500000,
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
			imageMapScreenShot: '',
			markerLatLng: [10.964112, 106.856461],
			reRender: 321,
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},
			circle: {
				center: [10.964112, 106.856461],
				radius: this.distance_max ? this.distance_max * 1000 : 2000,
				color: 'blue'
			},
			marker_id: '',
			listAssetGeneral: [],
			assetHasChoose: [],
			assetType: [39],
			yearRange: moment().subtract(1, 'year').format('YYYY-MM-DD'),
			assetDetails: '',
			transaction: [51, 52],
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}'
		}
	},
	async mounted () {
		if (this.coordinates) {
			this.circle.center = [this.coordinates.split(',')[0], this.coordinates.split(',')[1]]
			this.markerLatLng = [this.coordinates.split(',')[0], this.coordinates.split(',')[1]]
			this.map.center = [this.coordinates.split(',')[0], this.coordinates.split(',')[1]]
		}
		this.assetHasChoose = this.data.assets_general
		if (this.step_active >= 3) {
			await this.getListAsset()
		}
		if (this.$refs.map_stemap_step6p2) {
			setTimeout(() => {
				this.$refs.map_step6.mapObject.invalidateSize()
			}, 2000)
		}
		await this.data.assets_general.forEach(item => {
			this.listAssetGeneral.forEach(asset => {
				if (item.id === asset.id) {
					asset['isChoosing'] = true
				}
			})
		})
		if (this.data.map_img) {
			this.imageMapScreenShot = this.data.map_img
		}
		this.renderImage += 1
		this.reRenderMap += 1
	},
	beforeUpdate () {
	},
	methods: {
    formatPrice (value) {
			let num = parseFloat(value / 1).toFixed(0).replace('.', ',')
			if (num.length > 3 && num.length <= 6) {
				return parseFloat(num / 1000).toFixed(0) + ' ng'
			} else if (num.length > 6 && num.length <= 9) {
				return parseFloat(num / 1000000).toFixed(0) + ' tr'
			} else if (num.length > 9) {
				return parseFloat(num / 1000000000).toFixed(1) + ' tỷ'
			} else if (num < 900) {
				return num + ' đ' // if value < 1000, nothing to do
			}
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		handleRefeshMap () {
			this.getListAsset()
		},
		handleChangeRouter () {
			const routeData = this.$router.resolve({ name: 'warehouse.create', query: { asset_type_id: 39 } })
			window.open(routeData.href, '_blank')
		},
		handleClickOut () {
			this.reRenderCluster += 1
		},
		formatNumberArea (num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		formatDate (value) {
			return moment(String(value)).format('DD-MM-YYYY')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async getPropertyTypes () {
			await this.propertyTypes.forEach(propertyType => {
				this.assetType.push(
					propertyType.id
				)
			})
		},
		async getListAsset () {
			const distance = parseFloat(this.circle.radius / 1000).toFixed(2)
			const location = this.circle.center
			const transaction = '[' + this.transaction + ']'
			const assetType = '[' + this.assetType + ']'
			const yearRange = this.yearRange
			const getAllAsset = await CertificateAsset.getAllAssetApartment(distance, location, transaction, assetType, yearRange)
			this.listAssetGeneral = [...getAllAsset.data]
			let checkAsset = []
			this.listAssetGeneral.forEach(item => {
				item['center'] = [parseFloat(item.coordinates.split(',')[0]), parseFloat(item.coordinates.split(',')[1])]
				checkAsset = this.data.assets_general.filter(asset => asset.id === item.id)
				if (checkAsset.length > 0) { item['isChoosing'] = true } else { item['isChoosing'] = false }
			})
		},
		handleHidden () {
			this.hiddenList = !this.hiddenList
			setTimeout(() => {
				this.$refs.lmap.mapObject.invalidateSize()
			}, 501)
		},
		async handleMarker (event, asset) {
			const data = [asset]
			const getDetailAsset = await CertificateAsset.getDetailAssetApartment(data)
			if (getDetailAsset.data) {
				this.assetDetails = getDetailAsset.data[0]
				let checkAsset = this.listAssetGeneral.filter(item => item.id === asset.id && item.isChoosing === true)
				if (checkAsset && checkAsset.length > 0) {
					this.assetDetails['isChoosing'] = true
				}
				this.showDetailAsset = true
			}
		},
		hoverDetailToMarker (property) {
			this.map.center = property.center
			this.marker_id = property.id
		},
		handleView () {
			if (this.url === 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}') {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url = 'https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile'
        this.imageMap = false
			} else {
				this.url = 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}'
				this.imageMap = true
			}
		},
		handleShowFilterMap () {
			this.isFilterMap = true
		},
		async handleActionFilterMap (dataFilter) {
			this.$emit('changeDistance', parseFloat(dataFilter.radius / 1000).toFixed(2))
			this.circle.radius = dataFilter.radius
			this.transaction = dataFilter.transaction
			this.assetType = dataFilter.assetType
			this.yearRange = dataFilter.year
			const distance = parseFloat(dataFilter.radius / 1000).toFixed(2)
			const location = this.circle.center
			const transaction = '[' + dataFilter.transaction + ']'
			const assetType = '[' + dataFilter.assetType + ']'
			const yearRange = this.yearRange
			const getAllAsset = await CertificateAsset.getAllAssetApartment(distance, location, transaction, assetType, yearRange)
			this.listAssetGeneral = [...getAllAsset.data]
			this.listAssetGeneral.forEach(item => {
				item['center'] = [parseFloat(item.coordinates.split(',')[0]), parseFloat(item.coordinates.split(',')[1])]
				// item['isChoosing'] = false
				if (this.assetHasChoose && this.assetHasChoose.length > 0) {
					let checkAssetChoose = this.assetHasChoose.filter(asset => asset.id === item.id)
					if (checkAssetChoose && checkAssetChoose.length > 0) {
						item['isChoosing'] = true
					} else { item['isChoosing'] = false }
				} else { item['isChoosing'] = false }
			})
			this.reRenderMap += 1
		},
		zoomUpdated (zoom) {
			this.map.zoom = zoom
		},
		centerUpdated (center) {
			this.center = center
		},

		cancelShowDetailAsset () {
			this.showDetailAsset = false
			this.assetDetails = ''
		},
		handleAddProperty (assetDetail, select) {
			for (let i = 0; i < this.listAssetGeneral.length; i++) {
				if (this.listAssetGeneral[i].id === assetDetail.id) {
					this.listAssetGeneral[i].isChoosing = select
				}
			}
			this.assetHasChoose.push(assetDetail)
			this.assetHasChoose = this.assetHasChoose.sort((a, b) => b.id - a.id)
			this.$emit('choosingAsset', this.assetHasChoose)
			this.reRender += 1
			this.assetDetails.isChoosing = select
		},
		handleDeleteProperty (assetDetail, select) {
			for (let i = 0; i < this.listAssetGeneral.length; i++) {
				if (this.listAssetGeneral[i].id === assetDetail.id) {
					this.listAssetGeneral[i].isChoosing = select
				}
			}
			let isChoose = this.assetHasChoose.filter(item => item.id !== assetDetail.id)
			this.assetHasChoose = isChoose
			this.$emit('choosingAsset', isChoose)
			this.reRender += 1
			this.assetDetails.isChoosing = select
		},
		async handleDetail (asset) {
			this.showDetailAsset = true
			const data = [asset]
			const getDetailAsset = await CertificateAsset.getDetailAssetApartment(data)
			if (getDetailAsset.data) {
				this.assetDetails = getDetailAsset.data[0]
				let checkAsset = this.listAssetGeneral.filter(item => item.id === asset.id && item.isChoosing === true)
				if (checkAsset && checkAsset.length > 0) {
					this.assetDetails['isChoosing'] = true
				}
			}
		},
		async showDetailsSelectedAsset () {
			if (this.assetHasChoose.length === 0) {
				this.showDetailAllSelected = false
				this.$toast.open({
					message: 'Vui lòng chọn tài sản so sánh',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.assetHasChoose.length > 3) {
				this.$toast.open({
					message: 'Chỉ được chọn tối đa 3 tài sản so sánh',
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.assetHasChoose = this.assetHasChoose.sort((a, b) => b.id - a.id)
				const dataDetail = await CertificateAsset.getDetailAssetApartment(this.assetHasChoose)
				if (dataDetail.data) {
					this.showChoosingAssetDetails = dataDetail.data
					this.showDetailAllSelected = true
				}
			}
			// console.log(this.showChoosingAssetDetails, 'showChoosingAssetDetails')
			// console.log(this.assetHasChoose, 'this.assetHasChoose')
		},
		handleCancelShowAllDetail () {
			this.showDetailAllSelected = false
		},
		showMapGetPic () {
			if (this.assetHasChoose.length === 0) {
				this.showModalTakeAPic = false
				this.$toast.open({
					message: 'Vui lòng chọn tài sản so sánh',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.assetHasChoose.length > 3) {
				this.$toast.open({
					message: 'Chỉ được chọn tối đa 3 tài sản so sánh',
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.assetHasChoose = this.assetHasChoose.sort((a, b) => b.id - a.id)
				this.showModalTakeAPic = true
			}
		},
		async saveImageMap (image) {
			if (image) {
				const data = {
					data: image
				}
				const res = await AppraiseData.getImage(data)
				if (res.data) {
					this.imageMapScreenShot = res.data.link
					this.data.map_img = res.data.link
					this.renderImage += 1
				}
			}
		}
	}
}
</script>
<style scoped lang="scss">

.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;

    &__img {
      padding: 8px 20px;
    }

    h3 {
      color: #007EC6;
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
  background: #FFFFFF;
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
  transition: .2s;
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
  right: 0
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
  background: #FFFFFF;
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
    background: #FAA831;
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
    transition: .3s;
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
    border-bottom: 1px solid #DDDDDD;

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
  transition: .5s;

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
    transition: .5s;
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
        transition: .5s;
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
    background: #F28C1C;
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
    color: #FFFFFF;
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
    border-bottom: 2px solid #F28C1C;
    transition: .3s;

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
      border-bottom: 4px solid #F28C1C;
    }

    &__active {
      background: rgba(0, 0, 0, 0.1);
      border-bottom: 4px solid #F28C1C;
    }
  }
}

.btn-map {
  background: #FFFFFF;
  border-radius: 5px;
  border: 3px solid #FFFFFF;
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
    background: #37C3F4;
  }

  &__purple {
    background: #8659FA;
  }

  &__orange {
    background: #FAA831;
  }

  &__green {
    background: #1F8B24;
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
	0%   { transform: scale(1,1)      translateY(0); }
	10%  { transform: scale(1.1,.9)   translateY(0); }
	30%  { transform: scale(.9,1.1)   translateY(-15px); }
	50%  { transform: scale(1.05,.95) translateY(0); }
	57%  { transform: scale(1,1)      translateY(-5px); }
	64%  { transform: scale(1,1)      translateY(0); }
	100% { transform: scale(1,1)      translateY(0); }
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
  background: #E2F3FC;
  border: 1px solid #007EC6;
  font-weight: 600;
  color: #007EC6;
}

.btn_white_border {
  border: 1px solid #617F9E;
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
/deep/ .leaflet-container .leaflet-marker-pane img {
  width: -webkit-fill-available ;
}
.view_map_btn {
  margin-right: 5px;
  height: 1.1rem;
  margin-bottom: 0.2rem
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
        background-color: #DEE6EE;
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
    color: #617F9E;
    // background-color: #617F9E;
    border: 2px solid #617F9E;
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
  width: 30px;
  height: 30px;
  padding: unset !important
}
</style>
