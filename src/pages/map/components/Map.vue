<template>
  <div>
    <div class="loading" :class="{'loading__true': isSubmit}">
      <a-spin />
    </div>
    <div class="d-flex all-map">
      <div class="main-map" :class="hiddenList ? 'main-map--hidden' : ''">
        <div id="mapid" class="layer-map">
          <l-map ref="lmap"
                 :zoom="zoom"
                 :center="center"
                 :options="{zoomControl: false}"
                 :maxZoom="20"
                 @update:zoom="zoomUpdated"
                 @update:bounds="boundsUpdated"
                 @click="choosePoint($event)"
          >
            <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
            <l-control-zoom position="bottomright"></l-control-zoom>

            <l-control position="bottomright">
              <button class="btn btn-orange mini_btn" type="button" id="filterButton" @click="handleFilter">
                <img src="@/assets/icons/ic_filter.svg" alt="filter" >
              </button>
            </l-control>
            <l-control position="bottomright">
              <button class="btn btn-orange mini_btn" type="button" @click="openPopUp($event)">
                <img src="@/assets/icons/ic_radius.svg" alt="radius">
              </button>
            </l-control>
            <l-control position="bottomright">
              <button class="btn btn-orange mini_btn" type="button" @click="geoLocate">
                <img src="@/assets/icons/ic_locate_white.svg" alt="location">
              </button>
            </l-control>
            <l-control position="bottomleft">
              <button class="btn btn-map" @click="handleView">
                <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
              </button>
            </l-control>
            <l-control class="control-note" position="topleft">
              <div class="container-note">
                <div class="mr-18 d-flex align-items-center">
                  <div class="note-color note-color__blue"/>
                  <p class="note-content">Đã bán</p>
                </div>
                <!-- <div class="mr-18 d-flex align-items-center">
                  <div class="note-color note-color__orange"/>
                  <p class="note-content">Đã cho thuê</p>
                </div> -->
                <div class="mr-18 d-flex align-items-center">
                  <div class="note-color note-color__purple"/>
                  <p class="note-content">Rao bán</p>
                </div>
                <div class="mr-18 d-flex align-items-center">
                  <div class="note-color note-color__green"/>
                  <p class="note-content">Đã thẩm định</p>
                </div>
              </div>
            </l-control>
            <l-marker :lat-lng="markerLatLng">
              <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
              </l-icon>
            </l-marker>
            <l-circle
              :lat-lng="circle.center"
              :radius="circle.radius"
              :color="circle.color"
              :weight="2"
            />
            <v-marker-cluster>
              <l-marker v-for="(apartment, index) in locationApartments" :key="index" :lat-lng="apartment.center" @click="handleMarker($event)" @mouseover="handleMarkerHover(apartment.id)">
                <l-icon  class-name="someExtraClass">

                  <div class="marker marker__blue" :class="apartment.center === center ? 'marker__active' : ''" v-if="apartment.transaction_type_id === 51"/>
                  <div class="marker marker__purple" :class="apartment.center === center ? 'marker__active' : ''" v-if="apartment.transaction_type_id === 52"/>
                  <!-- <div class="marker marker__orange" :class="apartment.center === center ? 'marker__active' : ''" v-if="apartment.transaction_type_id === 53"/>
                  <div class="marker marker__green" :class="apartment.center === center ? 'marker__active' : ''" v-if="apartment.transaction_type_id === 54"/> -->
                  <div class="marker marker__green" :class="apartment.center === center ? 'marker__active' : ''" v-if="apartment.transaction_type_id === 0"/>
                </l-icon>
                <l-popup class="sp-custom-popup" ref="popup">
                  <img class="popup-img" v-if="apartment.pic.length > 0" :src="apartment.pic[0].link" alt="img">
                  <div class="d-flex justify-content-between">
                    <p class="popup-name">Mã:</p>
                    <p class="popup-content popup-content__id">{{apartment.migrate_status + '_' + apartment.id}}</p>
                  </div>
                  <div class="d-flex justify-content-between">
                    <p class="popup-name">Loại BĐS:</p>
                    <p class="popup-content popup-content__blue" v-if="apartment.transaction_type_id === 51">{{apartment.transaction_type}}</p>
                    <p class="popup-content popup-content__purple" v-if="apartment.transaction_type_id === 52">{{apartment.transaction_type}}</p>
                    <!-- <p class="popup-content popup-content__orange" v-if="apartment.transaction_type_id === 53">{{apartment.transaction_type}}</p>
                    <p class="popup-content popup-content__green" v-if="apartment.transaction_type_id === 54">{{apartment.transaction_type}}</p> -->
                    <p class="popup-content popup-content__green" v-if="apartment.transaction_type_id === 0">{{apartment.transaction_type}}</p>
                  </div>
                  <div class="d-flex justify-content-between">
                    <p class="popup-name">Diện tích:</p>
                    <p class="popup-content">{{formatNumber(apartment.total_area)}} m<sup>2</sup></p>
                  </div>
                  <div class="d-flex justify-content-between">
                    <p class="popup-name">Tổng giá trị:</p>
                    <p class="popup-content">{{formatNumber(apartment.total_amount)}} đ</p>
                  </div>
                  <p class="popup-link" @click="handleDetail(apartment)">Xem chi tiết</p>
                </l-popup>
              </l-marker>
            </v-marker-cluster>
			<l-marker v-for="(location, index) in locationLand" :key="index" :lat-lng="location.center" @click="handleMarker($event)" @mouseover="handleMarkerHover(location.id)">
				<l-icon  class-name="someExtraClass">
					<!-- require(`../../assets/icons/${icon}.svg`) marker_colors "@/assets/icons/ic_pin_blue.svg" -->
					<img :id="'img_'+location.id" class="img-location-marker" :src="require(`@/assets/icons/ic_pin_${marker_colors[location.transaction_type_id]}.svg`)" :alt="location.transaction_type_id">
					<div :id="'price_'+location.id" class="price-marker"> {{location.total_amount ? formatPrice(location.total_amount) : '-'}} </div>
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
						<p class="popup-content popup-content__purple" v-if="location.transaction_type_id === 52">{{location.transaction_type}}</p>
						<!-- <p class="popup-content popup-content__orange" v-if="location.transaction_type_id === 53">{{location.transaction_type}}</p>
						<p class="popup-content popup-content__green" v-if="location.transaction_type_id === 54">{{location.transaction_type}}</p> -->
						<p class="popup-content popup-content__green" v-if="location.transaction_type_id === 0">{{location.transaction_type}}</p>
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
        :hiddenFromMap="hiddenList"
        :asset_generals='assetGenerals'
        :location="location"
        :transaction_type="transaction_type"
        :marker_id="marker_id"
        @action="handleTransactionType"
        @get_center="handleCenter"
        @show_marker="handleShowMarker"
      />
    </div>
    <ModalMapDetail
      v-if="open_detail"
      @cancel="open_detail = false"
      :property="this.property"
      :pic="this.pic"
    />
    <ModalMapDetailAppraise
      v-if="open_detail_appraise"
      @cancel="open_detail_appraise = false"
      :property="this.property"
      :pic="this.pic"
    />
    <ModalRadius
      v-if="open_radius"
      @cancel="open_radius = false"
      :radius="radius"
      @action="handleRadius"
    />
    <ModalFilterAdvance
      v-if="showModalFilter"
      @action="handleFilterAsset"
      @cancel="showModalFilter = false"
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
import {LMap, LTileLayer, LMarker, LIcon, LControlZoom, LPopup, LCircle, LTooltip, LLayerGroup, LControl} from 'vue2-leaflet'
import WareHouse from '@/models/WareHouse'
import Icon from 'buefy'
import Vue2LeafletMarkerCluster from 'vue2-leaflet-markercluster'
import ModalMapDetail from '@/components/Modal/ModalMapDetail'
import ModalMapDetailAppraise from '@/components/Modal/ModalMapDetailAppraise'
import PropertiesList from '@/pages/map/components/PropertiesList'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputSwitch from '@/components/Form/InputSwitch'
import ModalRadius from '@/components/Modal/ModalRadius'
import ToggleSwitchSearch from '@/components/Form/ToggleSwitchSearch'
import Search from '@/pages/map/Search'
import VueLeafletMinimap from 'vue-leaflet-minimap'
import ModalFilterAdvance from './modals/ModalFilterAdvance'
import store from '@/store'
import * as types from '@/store/mutation-types'
import moment from 'moment'
import { isEmpty } from 'lodash-es'
Vue.use(Icon)
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
		VueLeafletMinimap,
		ToggleSwitchSearch,
		ModalMapDetailAppraise,
		ModalFilterAdvance
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
	async mounted () {
		await this.$gmapApiPromiseLazy()
		this.initMap()
	},
	data () {
		return {
			frontSideOptions: {
				items: {
					preSelected: 'all',
					labels: [
						{ name: 'yes', color: 'white', backgroundColor: '#FAA831' },
						{ name: 'all', color: 'white', backgroundColor: '#FAA831' },
						{ name: 'no', color: 'white', backgroundColor: '#FAA831' }
					]
				}
			},
			hiddenList: false,
			front_side: '',
			transaction: '',
			pic: [],
			assetGeneralDetail: [],
			provinces: [],
			marker_id: '',
			total_area_from: '',
			total_area_to: '',
			total_amount_from: '',
			total_amount_to: '',
			search_advanced: false,
			transaction_type: {
				sold: false,
				rented_out: false,
				for_sale: false,
				for_rent: false,
				is_appraise: false
			},
			isSubmit: false,
			years: [],
			picList: {
				images: [],
				id: []
			},
			radius: '',
			year: '',
			address: {
				province_id: 34,
				district_id: 411,
				ward_id: '',
				street_id: '',
				full_address: 'Quận Thủ Đức, Thành phố Hồ Chí Minh'
			},
			showModalFilter: false,
			open_radius: false,
			open_detail: false,
			open_detail_appraise: false,
			assetGenerals: [],
			location: [],
			locationApartments: [],
			locationLand: [],
			is_show_popup: false,
			icon_anchor: [15, 31],
			center_icon_anchor: [11, 12],
			show_component: '',
			clusterOptions: {},
			zoom: 15,
			center: [10.851987987311087, 106.74837598976731],
			markerLatLng: [10.851987987311087, 106.74837598976731],
			circle: {
				center: [10.851987987311087, 106.74837598976731],
				radius: 1000,
				color: 'blue'
			},
			marker_colors: {
				0: 'green',
				51: 'blue',
				52: 'purple',
				53: 'orange',
				54: 'green'
			},
			caller: null,
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			bounds: null,
			asset_events: new Vue(),
			imageMap: true,
			property_type: ''
		}
	},
	methods: {
		handleHidden (event) {
			this.hiddenList = event
			setTimeout(() => {
				this.$refs.lmap.mapObject.invalidateSize()
			}, 501)
		},
		handleView () {
			if (this.url === 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png') {
				this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.imageMap = false
			} else {
				this.url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
				this.imageMap = true
			}
		},
		openPopUp () {
			this.open_radius = true
			if (this.circle.radius !== 0) {
				this.radius = this.circle.radius
			}
		},
		storeMapLocation (mapLocation) {
			store.commit(types.SET_MAP_LOCATION, mapLocation)
			localStorage.setItem('mapLocation', JSON.stringify(mapLocation))
		},
		choosePoint (event) {
			const mapLocation = [event.latlng.lat, event.latlng.lng]
			this.storeMapLocation(mapLocation)
			this.setLocation(mapLocation)
		},
		setLocation (mapLocation) {
			this.circle.center = mapLocation
			this.markerLatLng = mapLocation
			this.center = mapLocation
			this.address.full_address = ''
			this.address.province_id = ''
			this.address.district_id = ''
			this.address.ward_id = ''
			this.address.street_id = ''
			this.getAssetGenerals()
		},
		getDefaultLocation() {
			let mapLocation = store.getters.mapLocation
			if (isEmpty(mapLocation)) {
				let local = localStorage.getItem('mapLocation')
				if (!isEmpty(local)) {
					this.storeMapLocation(mapLocation)
				}
			}
			return mapLocation
		},
		initMap () {
			const mapLocation = this.getDefaultLocation()
			if (!isEmpty(mapLocation)) {
				this.setLocation(mapLocation)
			} else {
				this.getAssetGenerals()
			}
		},
		async geocodeAddress () {
				let center = {}
				if(this.address.coordinate && count(this.addres.coordinate) === 2) {
					center = this.addres.coordinate
					this.center = center
					this.markerLatLng = center
					this.circle.center = center
					this.storeMapLocation(center)
					await this.getAssetGenerals()
					this.zoom = 15
				}
		},
		changeSwitchFrontSide (event) {
			if (event.value === 'all') {
				this.front_side = ''
			} else if (event.value === 'yes') {
				this.front_side = 1
			} else if (event.value === 'no') {
				this.front_side = 0
			}
		},
		handleRadius (data) {
			this.circle.radius = data
			this.getAssetGenerals()
		},
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

		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		boundsUpdated (bounds) {
			this.bounds = bounds
		},
		zoomUpdated (zoom) {
			this.zoom = zoom
		},
		async handleDetail (property) {
			if (property.transaction_type_id && property.transaction_type_id !== 0) {
				await this.getAssetGeneralDetail(property.id)
				this.pic = property.pic
				this.open_detail = true
			} else if (property.transaction_type_id === 0) {
				await this.getAppraisersDetail(property.id)
				this.pic = property.pic
				this.open_detail_appraise = true
			}
		},
		async getAppraisersDetail (id) {
			this.isSubmit = true
			const resp = await WareHouse.getAppraiseDetail(id)
			this.property = resp.data
			this.isSubmit = false
		},
		async getAssetGeneralDetail (id) {
			this.isSubmit = true
			const resp = await WareHouse.getAssetGeneralDetail(id)
			this.property = resp.data
			this.isSubmit = false
		},
		handleTotalAreaFrom (event) {
			this.total_area_from = event
			if (this.total_area_from === undefined || this.total_area_from === null) {
				this.total_area_from = ''
			}
		},
		totalAreaTo (event) {
			this.total_area_to = event
			if (this.total_area_to === undefined || this.total_area_to === null) {
				this.total_area_to = ''
			}
		},
		totalAmountFrom (event) {
			this.total_amount_from = event
			if (this.total_amount_from === undefined || this.total_amount_from === null) {
				this.total_amount_from = ''
			}
		},
		totalAmountTo (event) {
			this.total_amount_to = event
			if (this.total_amount_to === undefined || this.total_amount_to === null) {
				this.total_amount_to = ''
			}
		},
		handleFilter () {
			this.showModalFilter = true
		},
		async handleFilterAsset (data) {
			this.address.coordinate = data.coordinate
			this.address.search_address = data.search_address
			this.total_area_from = data.total_area_from
			this.total_area_to = data.total_area_to
			this.total_amount_from = data.total_amount_from
			this.total_amount_to = data.total_amount_to
			this.property_type = data.property_type
			this.year = data.year
			// eslint-disable-next-line no-undef
			let center = {}
			if(this.address.coordinate) {
				center = this.address.coordinate
				this.center = center
				this.markerLatLng = center
				this.circle.center = center
				this.storeMapLocation(center)
				await this.getAssetGenerals()
				this.zoom = 15
			}
			console.log(center)
			this.showModalFilter = false
			store.commit(types.SET_MAP_FILTER, data)
			localStorage.setItem('mapFilter', JSON.stringify(data))
		},
		async handleCenter (center, id) {
			// this.zoom = 18
			// this.center = center
			let locationChoosed = this.locationLand.filter(i => i.isChoosing === true || i.id === id)
			if (locationChoosed && locationChoosed.length > 0) {
				locationChoosed.forEach(location => {
					let element = document.getElementById('img_' + location.id)
					let elementPrice = document.getElementById('price_' + location.id)
					if (location.id !== id) {
						location.isChoosing = false
						element.classList.remove('checking')
						elementPrice.classList.remove('checking')
					} else {
						location.isChoosing = true
						element.classList.add('checking')
						elementPrice.classList.add('checking')
					}
				})
			}
			this.marker_id = id
		},
		handleMarker (event) {
			// this.center = [event.latlng.lat, event.latlng.lng]
		},
		handleMarkerHover (id) {
			// window.location = '#' + id
			this.marker_id = id
		},
		handleShowMarker (center, id) {
			// this.center = center
			this.marker_id = id
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
			this.transaction_type = data
			this.getAssetGenerals()
		},
		async getAssetGenerals () {
			this.isSubmit = true
			if (this.transaction === undefined || this.transaction === null) {
				this.transaction = ''
			}
			const year = this.year
			const province = this.address.province_id
			const district = this.address.district_id
			const ward = this.address.ward_id
			const street = this.address.street_id
			const total_area_from = this.total_area_from
			const total_area_to = this.total_area_to
			const total_amount_from = this.total_amount_from
			const total_amount_to = this.total_amount_to
			const distance = parseFloat(this.circle.radius / 1000).toFixed(2)
			const location = this.circle.center
			const transaction = this.transaction
			const front_side = this.front_side
			const isAppraise = !!this.transaction_type.is_appraise
			const property_type = this.property_type
			const resp = await WareHouse.getSearchAll(year, province, district, ward, street, transaction, total_area_from, total_area_to, total_amount_from, total_amount_to, distance, location, front_side, isAppraise, property_type)
			this.assetGenerals = [...resp.data]
			await this.getLatLng()
			this.isSubmit = false
		},
		handleSearchAdvanced () {
			this.total_area_from = ''
			this.total_area_to = ''
			this.total_amount_from = ''
			this.total_amount_to = ''
			this.front_side = ''
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
		getLatLng () {
			this.location = []
			this.locationApartments = []
			this.locationLand = []
			this.assetGenerals.forEach(assetGeneral => {
				if (assetGeneral.transaction_type === 51 || assetGeneral.transaction_type === 52 || assetGeneral.transaction_type === 0) {
					if (assetGeneral.asset_type_id === 39) {
						this.locationApartments.push({
							id: assetGeneral.id,
							center: [parseFloat(assetGeneral.coordinates.split(',')[0]), parseFloat(assetGeneral.coordinates.split(',')[1])],
							migrate_status: assetGeneral.migrate_status,
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
							public_date: assetGeneral.public_date
						})
					} else {
						this.locationLand.push({
							id: assetGeneral.id,
							center: [parseFloat(assetGeneral.coordinates.split(',')[0]), parseFloat(assetGeneral.coordinates.split(',')[1])],
							migrate_status: assetGeneral.migrate_status,
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
							public_date: assetGeneral.public_date
						})
					}
					this.location.push({
						id: assetGeneral.id,
						center: [parseFloat(assetGeneral.coordinates.split(',')[0]), parseFloat(assetGeneral.coordinates.split(',')[1])],
						migrate_status: assetGeneral.migrate_status,
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
						public_date: assetGeneral.public_date
					})
				}
			})
		},
		Years () {
			const year = new Date().getFullYear()
			for (let i = 2000; i <= year; i++) {
				this.years.push(
					{
						year: i
					}
				)
			}
			function compare (a, b) {
				if (a.year > b.year) { return -1 }
				if (a.year < b.year) { return 1 }
				return 0
			}
			return this.years.sort(compare)
		},
		geoLocate () {
			navigator.geolocation.getCurrentPosition(position => {
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
		async getProvinces () {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
				if (this.address.province_id === '') {
					this.address.province_id = 34
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	beforeMount () {

	},
	created () {
		this.year = moment(new Date(new Date().setFullYear(new Date().getFullYear() - 1))).format('YYYY-MM-DD')
		this.getProvinces()
		this.Years()
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
      background: #26BF7F;
    }
    &__primary {
      background: #206bc4
    }
  }
  &-content{
    margin-bottom: 0;

    color: #000000;
  }
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
.marker {
  width: 15px;
  height: 15px;
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
    background: #26BF7F;
  }
  &__primary {
    background: #206bc4
  }
  &__active{
    width: 35px;
    height: 35px;
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
      color: #26BF7F;
    }
    &__primary {
      color: #206bc4
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
  padding: 0 30px;
  height: 85vh;
  margin-bottom: 20px;
}
.filter{
  padding: 0 30px;
  margin-bottom: 12px;
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
}
.search {
  border: 1px solid rgba(0, 0, 0, 0.3);
  box-sizing: border-box;
  border-radius: 5px;
  padding: 12px 20px;
  margin: auto 30px 15px;
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
    height: 100vh;
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
  padding: 0 30px;
  margin-bottom: 10px;
}
.line{
  border: 1px solid #999999;
  border-radius: 18px;
  width: 12px;
}
//.btn-mobile{
//  display: none;
//  @media (max-width: 1400px) {
//    margin-top: 15px;
//    display: block;
//  }
//}
.btn-pc {
  margin-top: 10px;
  display: flex;
  justify-content: flex-end;
}
.input-number{
  @media (max-width: 1023px) {
    margin-bottom: 5px
  }
}
.input-switch-front-side {
  @media (max-width: 1023px) {
    margin-bottom: 15px;
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
.front-side {
  margin-bottom: 0;
  margin-right: 10px;
  color: #333333;
  font-weight: 700;
}
.mini_btn {
  width: 30px;
  height: 30px;
  padding: unset !important
}
</style>
