<template>
  <div class="h-100">
    <div class="loading" :class="{'loading__true': isSubmit}">
      <a-spin />
    </div>
    <div class="filter" id="filter">
      <FilterProperty
        ref="filter"
        :property_types="propertyTypes"
        :address="this.address"
        @coordinate_search="coordinateFilter"
        @filter-changed="handleFilter"
        @filter-apartment="handleAddressApartment"
        @coordinate_search_advanced="coordinateFilterAdvanced"
        @get-radius="getRadius"
        @getAsset="getAsset"
        @map="handleMap"
      />
    </div>
    <div style="padding: 0 20px">
      <div class="container-map" id="map" v-if="mapContainer">
        <div class="container__btn-printer">
<!--          <b-dropdown class="dropdown-container btn btn-orange btn-printer btn-dropdown" no-caret>-->
<!--            <template #button-content>-->
<!--              <img src="../../../assets/icons/ic_printer_white.svg" alt="">-->
<!--            </template>-->
<!--            <b-dropdown-item @click.prevent="handlePrint">-->
<!--              <div class="dropdown-item-container"><img-->
<!--                src="../../../assets/icons/ic_printer.svg" alt="" style="margin-right: 10px">In mã sơ bộ hiện tại-->
<!--              </div>-->
<!--            </b-dropdown-item>-->
<!--            <b-dropdown-item @click.prevent="handlePrintList">-->
<!--              <div class="dropdown-item-container"><img-->
<!--                src="../../../assets/icons/ic_printer.svg" alt="img" style="margin-right: 10px">In nhiều mã sơ bộ-->
<!--              </div>-->
<!--            </b-dropdown-item>-->
<!--          </b-dropdown>-->
          <button class="btn btn-orange btn-printer" @click="handlePrint"><img src="../../../assets/icons/ic_printer_white.svg" alt="print"></button>
        </div>
        <div v-if="show_map">
          <div class="search">
            <Search @action="getAddressEstimate" :address="this.address" @search="handleSearchFrontSide" @coordinate_search="coordinateSearch" v-if="search_address_detail && address.estimate_type !== 'CHUNG_CU'"/>
            <SearchApartment :address="this.apartment" v-if="address.estimate_type === 'CHUNG_CU'"/>
            <input id="full_address_map" type="text" :value="address.full_address" class="d-none">
          </div>
          <div v-if="mapContainer">
            <p class="note">Các dữ liệu hiển thị trong khoảng thời gian 2 năm gần nhất</p>
            <div class="d-flex all-map">
              <div class="loading" :class="{'loading__map': loading}">
                <a-spin />
              </div>
              <div class="main-map" :class="hiddenList ? 'main-map--hidden' : ''">
                <div id="mapid" class="layer-map">
                  <l-map ref="lmap"
										:zoom="zoom"
										:center="center"
										:options="{zoomControl: false}"
										:maxZoom="20"
										@update:zoom="zoomUpdated"
										@update:bounds="boundsUpdated"
										@click="address.estimate_type !== 'CHUNG_CU' ? choosePoint($event) : ''"
                  >
                    <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                    <l-control-zoom position="bottomright"></l-control-zoom>
                    <l-control position="bottomleft">
                      <button class="btn btn-map" @click="handleView">
                        <img v-if="!imageMap" src="../../../assets/images/im_map.png" alt="">
                        <img v-if="imageMap" src="../../../assets/images/im_satellite.png" alt="">
                      </button>
                    </l-control>
                    <l-control class="control-note" position="topleft">
                      <div class="container-note">
                        <div class="mr-18 d-flex align-items-center">
                          <div class="note-color note-color__blue"/>
                          <p class="note-content">Đã bán</p>
                        </div>
                        <div class="mr-18 d-flex align-items-center">
                          <div class="note-color note-color__orange"/>
                          <p class="note-content">Đã cho thuê</p>
                        </div>
                        <div class="mr-18 d-flex align-items-center">
                          <div class="note-color note-color__purple"/>
                          <p class="note-content">Rao bán</p>
                        </div>
                        <div class="mr-18 d-flex align-items-center">
                          <div class="note-color note-color__green"/>
                          <p class="note-content">Rao cho thuê</p>
                        </div>
                      </div>
                    </l-control>
                    <l-control position="bottomright">
                      <button id="radius" class="btn btn-orange btn-radius" type="button" @click="openPopUp($event)">
                        <img src="../../../assets/icons/ic_radius.svg" alt="filter">
                      </button>
                      <b-tooltip target="radius" >Chọn bán kính</b-tooltip>
                    </l-control>
                    <l-marker :lat-lng="markerLatLng">
                      <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                            <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
                          </l-icon>
                      <l-tooltip>Vị trí của bạn</l-tooltip>
                    </l-marker>
                    <l-circle
                      :lat-lng="circle.center"
                      :radius="circle.radius"
                      :color="circle.color"
                    />
					<l-marker v-for="(location, index) in location" :key="index" :lat-lng="location.center" @click="handleMarker($event)" @mouseover="handleMarkerHover(location.id)">
						<l-icon  class-name="someExtraClass">
							<div class="marker marker__blue" :class="location.center === center ? 'marker__active' : ''" v-if="location.transaction_type_id === 51"/>
							<div class="marker marker__orange" :class="location.center === center ? 'marker__active' : ''" v-if="location.transaction_type_id === 53"/>
							<div class="marker marker__purple" :class="location.center === center ? 'marker__active' : ''" v-if="location.transaction_type_id === 52"/>
							<div class="marker marker__green" :class="location.center === center ? 'marker__active' : ''" v-if="location.transaction_type_id === 54"/>
						</l-icon>
						<l-popup class="sp-custom-popup" ref="popup">
							<img class="popup-img" v-if="location.pic.length > 0" :src="location.pic[0].link" alt="img">
							<div class="d-flex justify-content-between">
							<p class="popup-name">Mã:</p>
							<p class="popup-content popup-content__id">{{location.migrate_status + '_' + location.id}}</p>
							</div>
							<div class="d-flex justify-content-between">
							<p class="popup-name">Loại BĐS:</p>
							<p class="popup-content popup-content__blue" v-if="location.transaction_type_id === 51">{{location.transaction_type}}</p>
							<p class="popup-content popup-content__orange" v-if="location.transaction_type_id === 53">{{location.transaction_type}}</p>
							<p class="popup-content popup-content__purple" v-if="location.transaction_type_id === 52">{{location.transaction_type}}</p>
							<p class="popup-content popup-content__green" v-if="location.transaction_type_id === 54">{{location.transaction_type}}</p>
							</div>
							<div class="d-flex justify-content-between">
							<p class="popup-name">Diện tích:</p>
							<p class="popup-content">{{formatNumber(location.total_area)}} m<sup>2</sup></p>
							</div>
							<div class="d-flex justify-content-between">
							<p class="popup-name">Tổng giá trị:</p>
							<p class="popup-content">{{formatNumber(location.total_amount)}} đ</p>
							</div>
							<p class="popup-link" @click="handleDetail(location)">Xem chi tiết</p>
						</l-popup>
					</l-marker>
                  </l-map>
                </div>
              </div>
              <PropertiesList
                @hiddenList="handleHidden"
                :asset_generals='assetGenerals'
                :location="location"
                :transactions="transactions"
                :transaction_type="transaction_type"
                :marker_id="marker_id"
                :landType="landTypePurpose"
                @action="handleTransactionType"
                @get_center="handleCenter"
              />
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center">
          <button class="btn btn-orange w-100" type="button" @click="handleEstimate">XÁC NHẬN VỊ TRÍ ƯỚC TÍNH </button>
        </div>
      </div>
      <div id="land" v-if="land">
        <Estimate
          ref="estimate"
          :landType="landTypePurpose"
          :buildingCategories="buildingCategories"
          :housingTypes="housingTypes"
          :get_result="get_result"
          :location="markerLatLng"
          :distance="circle.radius"
          :address="address"
          :transaction_type_ids="this.transactions"
          :myOptions="myOptions"
          @result="getResult"
          @get_item="getItemPrint"
          v-if="address.estimate_type !== 'CHUNG_CU'"
          :user="created"
          @warning="getWarning"
        />
        <EstimateApartment
          :landType="landTypePurpose"
          :buildingCategories="buildingCategories"
          :housingTypes="housingTypes"
          :get_result="get_result"
          :location="markerLatLng"
          :distance="circle.radius"
          :address="address"
          :apartment="apartment"
          @result="getResult"
          @get_item="getItemEstimate"
          v-if="address.estimate_type === 'CHUNG_CU'"
          :user="created"
        />
      </div>
    </div>
    <ModalMapDetail
      v-if="open_detail"
      @cancel="open_detail = false"
      :property="this.property"
      :pic="this.pic"
    />
    <ModalRadius
      v-if="open_radius"
      @cancel="open_radius = false"
      :radius="radius"
      @action="handleRadius"
    />
    <ModalValuationPurposes
      v-if="open_valuation_purposes"
      @cancel="open_valuation_purposes = false"
    />
    <ModalPrintEstimate
      v-if="printer"
      @cancel="printer = false"
      :address="address"
      :created ='created'
      :print_item="print_item"
      :warningUpdate="warningUpdate"
      @action="statusPrint"
    />
    <ModalChangeMap
      v-if="changeLocation"
      @cancel="cancelAddress"
      :message="messageMap"
      :location="this.locationMap"
      @action="changeAddress"
    />
    <ModalPrintList
      v-if="openModalPrintList"
      @cancel="openModalPrintList = false"
      :create_by="created"
    />
  </div>
</template>

<style lang="scss">
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import '../../../../node_modules/leaflet/dist/leaflet.css';
</style>
<script>
import Vue from 'vue'
import {
	LCircle,
	LControl,
	LControlZoom,
	LIcon,
	LLayerGroup,
	LMap,
	LMarker,
	LPopup,
	LTileLayer,
	LTooltip
} from 'vue2-leaflet'
import WareHouse from '@/models/WareHouse'
import PriceEstimate from '@/models/PriceEstimate'
import Icon from 'buefy'
import Vue2LeafletMarkerCluster from 'vue2-leaflet-markercluster'
import ModalMapDetail from '@/components/Modal/ModalMapDetail'
import PropertiesList from '@/pages/price_estimate/components/PropertiesList'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputSwitch from '@/components/Form/InputSwitch'
import ModalRadius from '@/components/Modal/ModalRadius'
import Search from '@/pages/price_estimate/Search'
import SearchApartment from './SearchApartment'
import FilterProperty from '@/pages/price_estimate/components/FilterProperty'
import ModalValuationPurposes from '@/components/Modal/ModalValuationPurposes'
import ModalPrintEstimate from '@/components/Modal/ModalPrintEstimate'
import Estimate from '@/pages/price_estimate/components/Estimate'
import EstimateApartment from '@/pages/price_estimate/components/EstimateApartment'
import Result from '@/pages/price_estimate/components/Result'
import ModalPrintList from '@/components/Modal/ModalPrintList'
import Print from 'vue-print-nb'
import {BDropdown, BDropdownItem, BTooltip} from 'bootstrap-vue'
import ModalChangeMap from '../../../components/Modal/ModalChangeMap'
import streetJson from '../../../assets/json/street_compare'

Vue.use(Icon)
Vue.use(Print)
export default {
	name: 'Map',
	components: {
		Search,
		LMap,
		LTileLayer,
		LMarker,
		LIcon,
		LPopup,
		LCircle,
		LControlZoom,
		LTooltip,
		LLayerGroup,
		LControl,
		'v-marker-cluster': Vue2LeafletMarkerCluster,
		ModalMapDetail,
		PropertiesList,
		InputCategory,
		InputSwitch,
		InputNumberFormat,
		ModalRadius,
		FilterProperty,
		Estimate,
		Result,
		ModalValuationPurposes,
		EstimateApartment,
		ModalPrintEstimate,
		SearchApartment,
		'b-tooltip': BTooltip,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		ModalChangeMap,
		ModalPrintList
	},
	computed: {
		optionsYear () {
			return {
				data: this.years,
				key: 'year',
				id: 'year'
			}
		}
	},
	data () {
		return {
			myOptions: {
				items: {
					preSelected: 'Tất cả',
					labels: [
						{ name: 'Có', color: 'white', backgroundColor: '#FAA831' },
						{ name: 'Tất cả', color: 'white', backgroundColor: '#FAA831' },
						{ name: 'Không', color: 'white', backgroundColor: '#FAA831' }
					]
				}
			},
			openModalPrintList: false,
			messageMap: '',
			statusStreet: false,
			modalChangeAddress: false,
			locationMap: '',
			changeLocation: false,
			hiddenList: false,
			print_btn: false,
			print_item: {
				location: [10.964112, 106.856461]
			},
			printer: false,
			search_address_detail: true,
			transaction: [],
			transactions: [],
			open_valuation_purposes: true,
			show_map: true,
			loading: false,
			get_result: '',
			search_form: true,
			result: false,
			mapContainer: false,
			map: false,
			land: false,
			provinces: [],
			propertyTypes: [],
			landTypePurpose: [],
			pic: [],
			assetGeneralDetail: [],
			marker_id: '',
			search_advanced: false,
			transaction_type: {
				sold: false,
				rented_out: false,
				for_sale: false,
				for_rent: false
			},
			is_print: false,
			isSubmit: false,
			years: [],
			picList: {
				images: [],
				id: []
			},
			radius: '',
			address: {
				province_id: '',
				province: '',
				district_id: '',
				district: '',
				ward_id: '',
				ward: '',
				street_id: '',
				street: '',
				front_side: false,
				full_address: '',
				estimate_type: '',
				apartment_id: ''
			},
			search: {
				estimate_type: '',
				front_side: ''
			},
			open_radius: false,
			open_detail: false,
			assetGenerals: [],
			housingTypes: [],
			buildingCategories: [],
			location: [],
			is_show_popup: false,
			icon_anchor: [15, 31],
			center_icon_anchor: [11, 12],
			show_component: '',
			clusterOptions: {},
			zoom: 15,
			center: [10.964112, 106.856461],
			markerLatLng: [10.964112, 106.856461],
			markerLatLngSave: [10.964112, 106.856461],
			radiusSave: 1000,
			circle: {
				center: [10.964112, 106.856461],
				radius: 1000,
				color: 'blue'
			},
			caller: null,
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}',
			bounds: null,
			asset_events: new Vue(),
			asset_type_ids: [],
			address_components: {
				province: '',
				district: '',
				wards: [],
				street: ''
			},
			apartment: {},
			created: '',
			imageMap: true,
			warningUpdate: false
		}
	},

	async created () {
		if ('id' in this.$route.query && this.$route.name === 'price_estimate.log') {
			let data = {}
			if (this.$route.meta['detail']) {
				this.isSubmit = true
				this.open_valuation_purposes = false
				this.mapContainer = true
				data = JSON.parse(this.$route.meta['detail'].input)
				this.address.province_id = data.province_id
				this.address.district_id = data.district_id
				this.address.ward_id = data.ward_id
				this.address.street_id = data.street_id
				this.markerLatLng = [data.location.split(',')[0], data.location.split(',')[1]]
				this.center = [data.location.split(',')[0], data.location.split(',')[1]]
				this.circle.center = [data.location.split(',')[0], data.location.split(',')[1]]
				this.print_item.location = [data.location.split(',')[0], data.location.split(',')[1]]
				this.transactions = data.transaction_type_ids
				this.transaction = '[' + data.transaction_type_ids + ']'
				if (data.front_side === 1) {
					this.myOptions.items.preSelected = 'Có'
				} else if (data.front_side === 0) {
					this.myOptions.items.preSelected = 'Không'
				} else {
					this.myOptions.items.preSelected = ''
				}
				await this.openEstimate()
				await this.$refs.filter.getAssetTypes()
				await this.getAssetGenerals()
				this.$refs.estimate.getDataLog(data)
				this.isSubmit = false
				window.location = '#land'
			} else {
				await this.$router.push({name: 'page-not-found'})
			}
		} else {
		}
	},

	methods: {
		getWarning (e) {
			this.warningUpdate = e
		},
		getRadius (radius) {
			this.circle.radius = radius
		},
		handleHidden () {
			this.hiddenList = !this.hiddenList
			setTimeout(() => {
				this.$refs.lmap.mapObject.invalidateSize()
			}, 501)
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
		handlePrint () {
			this.printer = true
		},
		handlePrintList () {
			this.openModalPrintList = true
		},
		statusPrint () {
			this.$refs.estimate.getIsPrint()
		},
		async getResult (event) {
			this.print_btn = true
			await this.closeResult()
			this.get_result = event
			await this.openResult()
			window.location = '#result'
		},
		getItemPrint (event) {
			this.print_item = event
		},
		getItemEstimate (event) {
			this.print_item = event
		},
		closeResult () {
			this.result = false
		},
		openResult () {
			this.result = true
		},
		handleSearchFrontSide () {
			this.getAssetGenerals()
		},
		async handleSearch (event) {
			await this.getDataSearch(event)
			await this.getAssetGenerals()
		},
		async handleFilter (event) {
			this.myOptions.items.preSelected = ''
			await this.getDataFilter(event)
			this.asset_type_ids = event.asset_type_ids
			// await this.getAssetGenerals()
		},
		getAsset (event) {
			this.asset_type_ids = event
		},
		coordinateFilter (geocoder) {
			if (this.address.full_address !== '' && this.address.full_address !== undefined && this.address.full_address !== null) {
				this.print_item.position = this.address.full_address
				this.geocodeAddress(geocoder)
			}
		},
		coordinateFilterAdvanced (geocoder, search) {
			this.address.full_address = search
			if (this.address.full_address !== '' && this.address.full_address !== undefined && this.address.full_address !== null) {
				this.print_item.position = this.address.full_address
				this.geocodeAddress(geocoder)
			}
		},
		async coordinateSearch (geocoder) {
			if (this.address.full_address !== '' && this.address.full_address !== undefined && this.address.full_address !== null) {
				this.print_item.position = this.address.full_address
				this.geocodeAddress(geocoder)
			}
			await this.getAddressEstimate()
		},
		handleAddressApartment (apartment) {
			this.apartment = apartment
		},
		getDataSearch (event) {
			this.address.province_id = event.province_id
			this.address.district_id = event.district_id
			this.address.ward_id = event.ward_id
			this.address.street_id = event.street_id
			if (event.full_address !== '' && event.full_address !== undefined && event.full_address !== null) {
				this.print_item.position = this.address.full_address
				this.address.full_address = event.full_address
			}
			if (event.coordinates !== '' && event.coordinates !== undefined && event.coordinates !== null) {
				this.print_item.location = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
				this.circle.center = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
				this.center = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
				this.markerLatLng = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
			}
		},
		getDataFilter (event) {
			this.transactions = event.transaction
			this.transaction = '[' + event.transaction + ']'
			this.address.apartment_id = event.apartment_id
			this.print_item.apartment_id = event.apartment_id
			this.address.estimate_type = event.estimate_type
			this.print_item.estimate_type = event.estimate_type
			this.address.province_id = event.province_id
			this.address.district_id = event.district_id
			this.address.ward_id = event.ward_id
			this.address.street_id = event.street_id
			if (event.full_address !== '' && event.full_address !== undefined && event.full_address !== null) {
				this.print_item.position = event.full_address
				this.address.full_address = event.full_address
			}
			if (event.coordinates !== '' && event.coordinates !== undefined && event.coordinates !== null) {
				this.print_item.location = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
				this.circle.center = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
				this.center = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
				this.markerLatLng = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
				this.markerLatLngSave = [event.coordinates.split(',')[0], event.coordinates.split(',')[1]]
			}
		},
		async handleShowLand () {
			await this.openLand()
			window.location = '#land'
			this.mapContainer = false
		},
		openLand () {
			this.land = true
		},
		changeFilter () {
			setTimeout(function () {
				window.location = '#filter'
			})
		},
		async handleMap (map) {
			await this.openMap(map)
			window.location = '#map'
		},
		filterAddress () {
			this.mapContainer = false
			this.land = false
		},
		async openMap (map) {
			await this.filterAddress()
			this.mapContainer = map
		},
		getAddressEstimate () {
			if (this.address.estimate_type !== 'CHUNG_CU' && this.land === true) {
				this.$refs.estimate.getAddressEstimate()
			}
		},
		async handleEstimate () {
			if (this.address.estimate_type !== 'CHUNG_CU') {
				if (this.address.street_id && this.address.district_id && this.address.ward_id && this.address.province_id) {
					await this.openEstimate()
					await this.getAddressEstimate()
					window.location = '#land'
				} else {
					this.$toast.open({
						message: 'Vui lòng điền đầy đủ thông tin: Tỉnh/Thành, Quận/Huyện, Phường/Xã, Đường trước khi thực hiện ước tính giá',
						type: 'error',
						position: 'top-right'
					})
				}
			} else {
				await this.openEstimate()
				window.location = '#land'
			}
		},
		async openEstimate () {
			this.land = true
		},
		openPopUp () {
			this.open_radius = true
			if (this.circle.radius !== 0) {
				this.radius = this.circle.radius
			}
		},
		convertString (address) {
			const arrayAddress = address.split(', ')
			if (arrayAddress.length > 2) {
				this.address_components.wards = [arrayAddress[1], arrayAddress[2]]
			} else if (arrayAddress.length > 1) {
				this.address_components.wards = [arrayAddress[1]]
			} else {
				this.address_components.wards = []
			}
		},
		async choosePoint (event) {
			this.locationMap = event
			this.markerLatLng = [event.latlng.lat, event.latlng.lng]
			this.circle.center = [event.latlng.lat, event.latlng.lng]
			this.center = [event.latlng.lat, event.latlng.lng]
			this.print_item.location = [event.latlng.lat, event.latlng.lng]
			const R = 6371
			const dLat = this.deg2rad(this.markerLatLng[0] - this.markerLatLngSave[0])
			const dLng = this.deg2rad(this.markerLatLng[1] - this.markerLatLngSave[1])
			const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(this.deg2rad(this.markerLatLng[0])) * Math.cos(this.deg2rad(this.markerLatLngSave[0])) * Math.sin(dLng / 2) * Math.sin(dLng / 2)
			const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
			const result = R * c
			this.changePosition(event, result)
		},
		deg2rad (deg) {
			return deg * (Math.PI / 180)
		},
		async changePosition (event, result) {
			this.modalChangeAddress = true
			this.search_address_detail = false
			await this.initMapClick(event, result)
			await this.getAssetGenerals()
			this.search_address_detail = true
		},
		async changeAddress (event) {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.isSubmit = true
			this.search_address_detail = false
			this.markerLatLngSave = [event.latlng.lat, event.latlng.lng]
			this.statusStreet = true
			this.modalChangeAddress = false
			await this.initMapClick(event)
			await this.getLocationStreet()
			await this.getAssetGenerals()
			this.search_address_detail = true
			this.isSubmit = false
		},
		async cancelAddress () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.changeLocation = false
		},

		async geocodeAddress (geocoder) {
			let center = {}
			const address = document.getElementById('full_address_map').value
			if (address && address.split(',') && address.split(',').length === 2 && parseFloat(address.split(',')[0]) && parseFloat(address.split(',')[1])) {
				center[parseFloat(address.split(',')[0]), parseFloat(address.split(',')[1])]
			} else {
				await geocoder.geocode({'address': address}, function (results, status) {
					if (status === 'OK') {
						const marker = {
							position: results[0].geometry.location
						}
						center = [parseFloat(marker.position.lat()), parseFloat(marker.position.lng())]
					}
				})
			}
			if (center) {
				this.print_item.location = center
				this.center = center
				this.markerLatLng = center
				this.markerLatLngSave = center
				this.circle.center = center
				await this.getAssetGenerals()
			}
		},
		handleRadius (data) {
			this.circle.radius = data
			this.getAssetGenerals()
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		centerUpdated (center) {
			this.center = center
		},
		boundsUpdated (bounds) {
			this.bounds = bounds
		},
		zoomUpdated (zoom) {
			this.zoom = zoom
		},
		async handleDetail (property) {
			await this.getAssetGeneralDetail(property.id)
			this.pic = property.pic
			this.open_detail = true
		},
		async getAssetGeneralDetail (id) {
			this.isSubmit = true
			const resp = await WareHouse.getAssetGeneralDetail(id)
			this.property = resp.data
			this.isSubmit = false
		},
		async handleCenter (center, id) {
			this.zoom = 18
			this.center = center
			this.marker_id = id
		},
		handleMarker (event) {
			this.center = [event.latlng.lat, event.latlng.lng]
		},
		handleMarkerHover (id) {
			this.marker_id = id
			const listTG = document.getElementById('listTG')
			let activeItem = document.getElementById(id)
			listTG.scrollTop = activeItem.offsetTop - activeItem.offsetHeight / 1.7
		},
		handleTransactionType (data) {
			const transactions = []
			let transaction = ''
			if (data.sold === true) {
				transactions.push(51)
			}
			if (data.rented_out === true) {
				transactions.push(53)
			}
			if (data.for_sale === true) {
				transactions.push(52)
			}
			if (data.for_rent === true) {
				transactions.push(54)
			}
			if (transactions.length > 0) {
				transaction = '[' + transactions + ']'
			} else {
				transaction = '[' + 0 + ']'
			}
			this.transaction = transaction
			this.getAssetGenerals()
		},
		async getAssetGenerals () {
			this.loading = true
			const distance = parseFloat(this.circle.radius / 1000).toFixed(2)
			const location = this.circle.center
			const transaction = this.transaction
			const asset_type_ids = '[' + this.asset_type_ids + ']'
			const resp = await PriceEstimate.getSearchAll(transaction, distance, location, asset_type_ids)
			this.assetGenerals = [...resp.data]
			await this.getLatLng()
			this.loading = false
		},
		handleSearchAdvanced () {
			this.total_area_from = ''
			this.total_area_to = ''
		},
		handleShowImage (inputId) {
			let picList = []
			this.picList = {
				images: [],
				id: []
			}
			this.assetGenerals.filter(item => {
				if (item.id === inputId) {
					let imageList = []
					if (item.pic) {
						item.pic.map((item) => {
							imageList.push(item)
						})
					}
					let propertyPics = []
					item.properties.map((prop) => {
						if (prop.pic.length > 0) {
							propertyPics.push(prop.pic)
						}
					})
					if (propertyPics[0]) {
						propertyPics[0].map((item) => {
							imageList.push(item)
						})
					}
					let tangiblePics = []
					item.tangible_assets.map((prop) => {
						if (prop.pic.length > 0) {
							tangiblePics.push(prop.pic)
						}
					})
					if (tangiblePics[0]) {
						tangiblePics[0].map((item) => {
							imageList.push(item)
						})
					}
					let otherPics = []
					item.other_assets.map((prop) => {
						if (prop.pic.length > 0) {
							otherPics.push(prop.pic)
						}
					})
					if (otherPics[0]) {
						otherPics[0].map((item) => {
							imageList.push(item)
						})
					}
					picList = imageList
				}
			})
			if (picList && picList.length > 0) {
				picList.map((item) => {
					this.picList.images.push(item)
					this.picList.id.push(inputId)
				})
			}
		},
		handlePrinter () {
			window.print()
		},
		async initMapClick (event, result) {
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder()
			await this.eventMapClick(geocoder, event, result)
		},
		async eventMapClick (geocoder, event, result) {
			const location = event.latlng
			let address = ''
			let address_components = []
			let addressLog = []
			await geocoder.geocode({'location': location}, function (results, status) {
				if (status === 'OK') {
					addressLog = results[0]
					address = results[0].formatted_address
					address_components = results[0].address_components
				} else {
				}
			})
			this.search_address = address
			if (this.modalChangeAddress) {
				await this.getAddressDetailStreet(address_components)
			}
			if ((result > (this.radiusSave / 1000) && this.address.ward_id !== '' && this.address.ward_id) || (!this.statusStreet && this.address.street_id !== '' && this.address.street_id)) {
				this.messageMap = 'Bạn có muốn thay đổi địa chỉ trên thanh tìm kiếm khi thay đổi vị trí của bản đồ không?'
				this.changeLocation = true
				document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			} else {
				await this.getAddressLog(addressLog)
				await this.convertString(address)
				await this.getAddressDetail(address_components)
				await this.getLocationStreet()
			}
		},
		async getAddressLog (input) {
			await PriceEstimate.logAddressEstimates(JSON.stringify(input))
		},
		async getAddressDetail (address_components) {
			let address_province = address_components.find(address_component_province => address_component_province.types[0] === 'administrative_area_level_1')
			let address_district = address_components.find(address_component_district => address_component_district.types[0] === 'locality' || address_component_district.types[0] === 'administrative_area_level_2')
			if (address_province) {
				this.address_components.province = address_province.long_name.normalize('NFC')
			} else {
				this.address_components.province = 'UnNamedProvince'
			}
			if (address_district) {
				this.address_components.district = address_district.long_name.normalize('NFC')
			} else {
				this.address_components.district = 'UnNamedRoad'
			}
			await this.getAddressLocation()
		},
		async getAddressDetailStreet (address_components) {
			let address_street = address_components.find(address_component => address_component.types[0] === 'route')
			if (address_street) {
				this.address_components.street = address_street.long_name.normalize('NFC')
			} else {
				this.address_components.street = ''
			}
			await this.compareStreet(address_street)
		},
		async getAddressLocation () {
			const province = this.address_components.province
			const district = this.address_components.district
			const wards = this.address_components.wards
			const resp = await PriceEstimate.getAddress({province, district, wards})
			if (resp.data.province) {
				this.address.province_id = resp.data.province.id
			} else {
				this.address.province_id = ''
			}
			if (resp.data.district) {
				this.address.district_id = resp.data.district.id
			} else {
				this.address.district_id = ''
			}
			if (resp.data.ward) {
				this.address.ward_id = resp.data.ward.id
			} else {
				this.address.ward_id = ''
			}
		},
		compareStreet (address_street) {
			if (address_street) {
				const streetReplace = address_street.long_name.replace('Đường ', '')
				this.statusStreet = this.address.street.includes(streetReplace.normalize('NFC'))
			} else if (this.address.street !== '' && this.address.street && !this.address.street.includes()) {
				this.statusStreet = false
			}
		},
		async getLocationStreet () {
			if (this.address.district_id !== '' && this.address.district_id) {
				const district_id = this.address.district_id
				let street = this.address_components.street
				const resp = await PriceEstimate.getStreet({district_id, street})
				if (resp.data.street) {
					this.address.street_id = resp.data.street.id
					this.address.street = resp.data.street.name
				} else {
					streetJson.data.forEach(streetDistrict => {
						if (streetDistrict.district.toLowerCase() === this.address.district.toLowerCase() && streetDistrict.street === this.address_components.street) {
							street = streetDistrict.street_compare
						}
					})
					const respAgain = await PriceEstimate.getStreet({district_id, street})
					if (respAgain.data.street) {
						this.address.street_id = respAgain.data.street.id
						this.address.street = respAgain.data.street.name
					} else {
						this.address.street_id = ''
						this.address.street = ''
					}
				}
			} else {
				this.address.street_id = ''
				this.address.street = ''
			}
		},
		getLatLng () {
			this.location = []
			this.assetGenerals.forEach(assetGeneral => {
				this.location.push({
					id: assetGeneral.id,
					migrate_status: assetGeneral.migrate_status,
					center: [parseFloat(assetGeneral.coordinates.split(',')[0]), parseFloat(assetGeneral.coordinates.split(',')[1])],
					transaction_type_id: assetGeneral.transaction_type,
					transaction_type: assetGeneral.transaction_type_description,
					total_area: assetGeneral.total_area,
					total_amount: assetGeneral.total_amount,
					pic: assetGeneral.pic,
					contact_person: assetGeneral.contact_person,
					contact_phone: assetGeneral.contact_phone,
					full_address: assetGeneral.full_address,
					tangible_assets: assetGeneral.tangible_assets,
					properties: assetGeneral.properties,
					total_estimate_amount: assetGeneral.total_estimate_amount,
					total_construction_amount: assetGeneral.total_construction_amount,
					land_type_purpose: assetGeneral.land_type_purpose,
					land_type_purpose_price: assetGeneral.land_type_purpose_price,
					public_date: assetGeneral.public_date,
					front_side: assetGeneral.front_side,
					average_land_unit_price: assetGeneral.average_land_unit_price,
					asset_type: assetGeneral.asset_type,
					updated_at: assetGeneral.updated_at,
					main_road_length: assetGeneral.main_road_length
				})
			})
		},
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.propertyTypes = [...reps.data.loai_tai_san]
				this.landTypePurpose = [...reps.data.loai_dat_chi_tiet]
				this.buildingCategories = [...reps.data.cap_nha]
				this.housingTypes = [...reps.data.loai_nha]
				if (this.land) {
					this.$refs.estimate.getLandTypePurpose(this.landTypePurpose)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async geoLocate () {
			await navigator.geolocation.getCurrentPosition(position => {
				this.circle.center = [position.coords.latitude, position.coords.longitude]
				this.center = [position.coords.latitude, position.coords.longitude]
				this.markerLatLng = [position.coords.latitude, position.coords.longitude]
				this.address.province_id = ''
				this.address.district_id = ''
				this.address.ward_id = ''
				this.address.street_id = ''
				this.getAssetGenerals()
			})
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			this.created = profile.data.user.name
		}
	},
	beforeMount () {
		this.getProfiles()
		this.getAssetGenerals()
		this.getDictionary()
	}
}
</script>

<style lang="scss" scoped>
.leaflet-popup-content {
  min-width: 300px;
}
.mr-18 {
  margin-right: 18px;
}
.main-map {
  position: relative;
  height: 100%;
  width: 80%;
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
  &--hidden{
    transition: .5s;
    width: 100%;
  }
}
.btn-group {
  .button {
    box-shadow: -3px 2px 3px rgba(51, 51, 51, 0.5);
    border-radius: 4px;
    margin: 3px;
    border: 1px solid transparent;
  }
  position: absolute;
  top: 0;
  right: 0;
  width: 70px;
}
.container {
  &-note{
    display: flex;
    margin-bottom: 10px;
    @media (max-width: 767px) {
      display: block;
    }
  }
  &__btn {
    &-printer {
      position: fixed;
      right: 0;
      top: 100px;
      z-index: 100;
    }
  }
}
.note{
  &-color {
    width: 20px;
    height: 20px;
    border-radius: 2px;
    margin-right: 10px;
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
  }
  &-content{
    margin-bottom: 0;

    color: #000000;
  }
}
.marker {
  width: 15px;
  height: 15px;
  border-radius: 50%;
  &:hover{
    border: 2px solid;
  }
  &__red{
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
  &__active{
    width: 25px;
    height: 25px;
    background-image: url("../../../assets/icons/ic_marker_pin.svg");
    background-repeat: no-repeat;
    background-size: cover;
    background-color: transparent;
    &:hover {
      border: none;
    }
  }
}
.popup {
  &-name, &-content{
    margin-bottom: 0;
    font-size: 12px;
    color: #000000;
  }
  &-name{
    font-weight: 600;
  }
  &-content{
    text-transform: lowercase;
    &:first-letter{
      text-transform: uppercase;
    }
    &__id{
      text-transform: none;
      color: #FAA831;
    }
    &__blue {
      color: #37C3F4;
    }

    &__purple {
      color: #8659FA;
    }

    &__orange {
      color: #FAA831;
    }

    &__green {
      color: #1F8B24;
    }
  }
  &-link {
    color: #F28C1C;
    font-weight: 600;
    text-decoration-line: underline;
    cursor: pointer;
  }
}
.icon_marker{
  width: 25px;
}
.all-map{
  position: relative;
  height: 75vh;
  margin-bottom: 20px;
  .loading{
    display: none;
    &__map{
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.62);
      z-index: 100000;
      display: flex;
      align-items: center;
      justify-content: center;
      &.btn-loading{
        &:after{
          width: 2rem !important;
          height: 2rem !important;
        }
      }
    }
  }
}
.filter{
  padding: 0 30px;
  background-image: url("../../../assets/images/img_background.png");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: calc(100dvh - 80px);
  max-height: calc(100dvh - 80px);
  &-timer {
    margin-right: 15px;
    @media (max-width: 767px) {
      padding: 0;
      margin-bottom: 10px;
      margin-right: 0;
    }
  }
}
.btn{
  &-orange{
    font-weight: 600;

    @media (max-width: 767px) {
      margin-bottom: 10px;
    }
  }
  &-cancel{
    color: #999999;
    box-shadow: none !important;
    padding: 0;
  }
  &-printer {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    img {
      width: 22px;
    }
  }
  &-radius {
    width: 34px;
    height: 2.295rem;
    padding: 2px;
    .img {
      width: 100%;
      height: 100%;
    }
  }
}
.search {
  box-sizing: border-box;
  border-radius: 5px;
  margin: auto 0 15px;
  @media (max-width: 767px) {
    margin: auto 30px 15px;
  }
}
.loading{
  display: none;
  &__true{
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
    &.btn-loading{
      &:after{
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
.popup-img{
  object-fit: cover;
  width: 100%;
  max-height: 103px;
}
.switch-container{
  margin-bottom: 10px;
}
.line{
  border: 1px solid #999999;
  border-radius: 18px;
  width: 12px;
}
.btn-mobile{
  display: none;
  @media (max-width: 1400px) {
    margin-top: 15px;
    display: block;
  }
}
.btn-pc {
  display: block;
  @media (max-width: 1400px) {
    display: none;
  }
}
.input-number{
  @media (max-width: 1023px) {
    margin-bottom: 15px
  }
}
.note {
  color: #EF3039;
}
.container{
  &-map{
    max-width: 1710px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    margin: 40px auto;
    padding: 30px 30px 20px;
    @media (max-width: 767px) {
      margin: 0 10px 20px;
      padding: 10px;
    }
  }
  &-estimate{
    display: flex;
    justify-content: center;
    .btn{
      &-orange{
        text-transform: uppercase;
        font-weight: 700;
        width: 277px;
      }
    }
  }
  &__cancel{
    padding: 10px 0;
    border-bottom: 1px solid #D0D0D0;
    margin-bottom: 30px;
  }
}
.btn-map {
  background: #FFFFFF;
  border-radius: 5px;
  border: 3px solid #FFFFFF;
  padding: 0;
  box-sizing: border-box;
  img{
    max-width: 50px;
    height: auto;
  }
}
.btn-dropdown {
  padding: 0;
  img{
    margin: 5px auto;
  }
}
</style>
