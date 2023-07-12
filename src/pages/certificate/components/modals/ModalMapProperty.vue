<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div class="card">
        <div class="d-block d-sm-flex justify-content-between my-3">
          <div class="d-flex">
            <div class="mr-18 d-flex align-items-center" style="margin-left:20px">
              <img class="icon_marker" style="width: 23px;margin-right: 10px;" src="../../../../assets/images/marker_none.png" alt="">
              <p class="note-content" style="margin: 3px">Tài sản so sánh đã chọn</p>
            </div>
            <div class="mr-18 d-flex align-items-center" style="margin-left:20px">
              <img class="icon_marker" style="width: 33px;margin-right: 10px;"  src="../../../../assets/images/home_icon.png" alt="">
              <p class="note-content" style="margin: 3px">Tài sản thẩm định</p>
            </div>
            <button class="btn btn-orange" style="width: 300px; margin-left: 50px" type="button" @click="openPopUp($event)">
              <img style="margin-right: 10px" src="../../../../assets/icons/ic_radius.svg" alt="filter">
              Mở rộng tài sản so sánh
            </button>
          </div>
          <div>
            <router-link :to="{name: 'warehouse.create'}" target="_blank" class="btn btn-white text-nowrap index-screen-button"><img
              src="../../../../assets/icons/ic_add.svg" style="margin-right: 8px" alt="search">Tạo mới TSSS
            </router-link>
            <button class="btn btn-search btn-orange" type="button" @click="handleSubmit">Cập nhật</button>
            <button class="btn btn-white btn-cancel" type="button" @click="handleCancel">Trở lại</button>
          </div>
        </div>
        <div class="d-flex all-map">
          <div class="main-map" :class="hiddenList ? 'main-map--hidden' : ''">
            <div id="mapid" class="layer-map">
              <l-map ref="lmap"
                     style="height: 100%;"
                     :zoom="map.zoom"
                     :center="map.center"
                     :options="{zoomControl: false}"
                     :maxZoom="20"
                     @update:zoom="zoomUpdated"
                     @update:center="centerUpdated"
              >
                <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
                <l-control-zoom position="bottomright"></l-control-zoom>
                <l-circle
                  :lat-lng="circle.center"
                  :radius="circle.radius"
                  :color="circle.color"
                />
                <l-control position="bottomleft">
                  <button class="btn btn-map" @click="handleView" type="button">
                    <img v-if="!imageMap" src="../../../../assets/images/im_map.png" alt="">
                    <img v-if="imageMap" src="../../../../assets/images/im_satellite.png" alt="">
                  </button>
                </l-control>
                <!-- <l-control position="bottomright">
                  <button id="radius" class="btn btn-orange btn-radius" type="button" @click="openPopUp($event)">
                    <img src="../../../../assets/icons/ic_radius.svg" alt="filter">
                  </button>
                  <b-tooltip target="radius" >Chọn bán kính</b-tooltip>
                </l-control> -->
                <l-marker :lat-lng="markerLatLng">
                  <l-icon class-name="someExtraClass" :iconAnchor="[20, 40]">
                    <img style="width: 40px !important" src="../../../../assets/images/home_icon.png" alt="">
                  </l-icon>
                  <l-tooltip>Vị trí của bạn</l-tooltip>
                </l-marker>
                  <l-marker v-if="locationArray && locationArray.length > 0" v-for="(location, index) in locationArray" :key="index" :lat-lng="location.center" @click="handleMarker($event)" @mouseover="handleMarkerHover(location.id)">
                    <l-icon v-if="!location.asset"  class-name="someExtraClass">
                      <div class="marker marker__blue" :lat-lng="location.center === map.center ? 'marker__active' : ''" v-if="location.transaction_type_id === 51"/>
                      <div class="marker marker__orange" :lat-lng="location.center === map.center ? 'marker__active' : ''" v-if="location.transaction_type_id === 53"/>
                      <div class="marker marker__purple" :lat-lng="location.center === map.center ? 'marker__active' : ''" v-if="location.transaction_type_id === 52"/>
                      <div class="marker marker__green" :lat-lng="location.center === map.center ? 'marker__active' : ''" v-if="location.transaction_type_id === 54"/>
                    </l-icon>
                    <l-icon v-if="location.asset" :iconAnchor="[15, 40]" >
                     <img style="width:28px !important" src="../../../../assets/images/marker_none.png" alt="">
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
                      <div class="d-flex justify-content-between">
                        <p class="popup-link" @click="handleDetail(location)">Xem chi tiết</p>
                        <button v-if="location.asset ? false : true" class="btn btn-white btn-orange btn-add" type="button" @click="handleAddProperty(location.id,true)">
                        <!-- <img src="../../../assets/icons/ic_add-white.svg" alt="add"> -->
                          Chọn
                        </button>
                        <button v-if="location.asset ? true : false" class="btn btn-white text-nowrap" type="button" @click="handleAddProperty(location.id,false)">
                        <!-- <img src="../../../assets/icons/ic_add-white.svg" alt="add"> -->
                          Bỏ Chọn
                        </button>
                      </div>
                    </l-popup>
                  </l-marker>
              </l-map>
            </div>
          </div>
          <div class="position-relative w-25" :class="hiddenList ? 'property-hidden' : ''">
            <button type="button" class="btn btn__hide" @click="handleHidden">{{hiddenList? 'Hiển thị' : 'Ẩn'}}</button>
            <div class="container-property container-property--hidden">
              <div class="property" :class="hiddenList ? 'property--hidden' : '' " >
                <div class="property-filter">
                  <div class="row">
                    <div class="col-4">
                      <div class="d-flex align-items-center">
                        <label class="input-checkbox">
                          <input class="cursor-pointer" type="checkbox" v-model="all_transaction" @change="handleAllTransaction">
                          <span class="check-mark"></span>
                        </label>
                        <p class="property-content">Tất cả</p>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="d-flex align-items-center">
                        <label class="input-checkbox">
                          <input class="cursor-pointer" type="checkbox"  v-model="transaction_type.sold" @change="handleSold">
                          <span class="check-mark"></span>
                        </label>
                        <p class="property-content">Đã bán</p>
                      </div>
                    </div>
                    <!--
                    <div class="col-4">
                      <div class="d-flex align-items-center">
                        <label class="input-checkbox">
                          <input class="cursor-pointer" type="checkbox" v-model="transaction_type.rented_out" @change="handleRentedOut">
                          <span class="check-mark"></span>
                        </label>
                        <p class="property-content">Đã cho thuê</p>
                      </div>
                    </div>
                    -->
                  </div>
                  <div class="row" style="margin-top: 20px">
                    <div class="col-4">
                      <div class="d-flex align-items-center">
                        <label class="input-checkbox">
                          <input class="cursor-pointer" type="checkbox" v-model="transaction_type.for_sale" @change="handleForSale">
                          <span class="check-mark"></span>
                        </label>
                        <p class="property-content">Rao bán</p>
                      </div>
                    </div>
                    <!--
                    <div class="col">
                      <div class="d-flex align-items-center">
                        <label class="input-checkbox">
                          <input class="cursor-pointer" type="checkbox" v-model="transaction_type.for_rent" @change="handleForRent">
                          <span class="check-mark"></span>
                        </label>
                        <p class="property-content">Rao cho thuê</p>
                      </div>
                    </div>
                    -->
                    <div class="col">
                      <div class="d-flex align-items-center">
                        <label class="input-checkbox">
                          <input class="cursor-pointer" type="checkbox" v-model="isCheckFront" @change="handleIsCheckFrontside">
                          <span class="check-mark"></span>
                        </label>
                        <p class="property-content">Mặt tiền & hẻm</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="property">
                  <div class="property-contain" id="listProperty">
                    <div class="property-info" v-for="(asset_general, index) in locationArray" :key="index" :id="asset_general.id" :class="marker_id === asset_general.id ? 'property-info__active' : ''" @click="handleCenter(asset_general.center, asset_general.id)">
                      <!-- <div class="property-info--img">
                        <label class="input-checkbox input-checkbox--property">
                          <input class="cursor-pointer" type="checkbox" v-model="asset_general.asset" @change="getInput(asset_general.asset)">
                          <span class="check-mark"></span>
                        </label>
                      </div> -->
                      <div class="property-info--content">
                        <div class="d-flex justify-content-between">
                          <p class="info-content">Loại Thông tin:</p>
                          <p class="info-content info-content__detail color__blue" v-if="asset_general.transaction_type_id === 51">{{asset_general.transaction_type}}</p>
                          <p class="info-content info-content__detail color__orange" v-if="asset_general.transaction_type_id === 53">{{asset_general.transaction_type}}</p>
                          <p class="info-content info-content__detail color__purple" v-if="asset_general.transaction_type_id === 52">{{asset_general.transaction_type}}</p>
                          <p class="info-content info-content__detail color__green" v-if="asset_general.transaction_type_id === 54">{{asset_general.transaction_type}}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                          <p class="info-content">Vị trí:</p>
                          <p class="info-content info-content__detail">{{asset_general.front_side === 0 ? 'Trong hẻm' : 'Mặt tiền' }}</p>
                        </div>
                        <div class="d-flex justify-content-between" v-if="asset_general.front_side === 0">
                          <p class="info-content">Bề rộng hẻm</p>
                          <p class="info-content info-content__detail">{{formatFloat(asset_general.main_road_length)}} m</p>
                        </div>
                        <div class="d-flex justify-content-between">
                          <p class="info-content">Tổng diện tích:</p>
                          <p class="info-content info-content__detail">{{formatFloat(asset_general.total_area)}} m <sup>2</sup></p>
                        </div>
                        <div class="d-flex justify-content-between">
                          <p class="info-content">Giá trị ước tính:</p>
                          <span class="info-content info-content__detail">{{format(asset_general.total_estimate_amount)}}đ</span>
                        </div>
                        <div v-if="asset_general.asset_type !== 'CHUNG CƯ' && asset_general.land_type_purpose_price" v-for="(land_type_purpose, index) in asset_general.land_type_purpose_price" :key="'land_type_purpose'+index">
                          <div class="d-flex justify-content-between" v-for="(land_type, index) in landType" :key="'land_type' + index" v-if="land_type_purpose.id === land_type.id">
                            <p class="info-content">Loại đất:</p>
                            <p class="info-content info-content info-content__detail">{{land_type.description}}</p>
                          </div>
                          <div class="d-flex justify-content-between">
                            <p class="info-content">Đơn giá:</p>
                            <p class="info-content info-content info-content__detail">{{format(land_type_purpose.price_land)}}đ</p>
                          </div>
                        </div>
                        <div class="d-flex justify-content-between">
                          <p class="info-content">Ngày xác thực:</p>
                          <p class="info-content info-content__detail">{{formatDate(asset_general.updated_at)}}</p>
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
    </ValidationObserver>
    <ModalRadius
      v-if="open_radius"
      @cancel="open_radius = false"
      :radius="circle.radius"
      @action="handleRadius"
    />
    <ModalMapDetail
      v-if="open_detail"
      @cancel="open_detail = false"
      :property="this.property"
      :pic="this.pic"
    />
  </div>
</template>
<style lang="scss">
@import '../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css';
@import '../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css';
@import '../../../../../node_modules/leaflet/dist/leaflet.css';
</style>
<script>
import {LCircle,
	LControl,
	LControlZoom,
	LIcon,
	LMap,
	LMarker,
	LPopup,
	LTileLayer,
	LTooltip} from 'vue2-leaflet'
import {BTooltip} from 'bootstrap-vue'
import ModalRadius from '@/components/Modal/ModalRadius'
import Vue from 'vue'
import Icon from 'buefy'
import moment from 'moment'
import AppraiseData from '../../../../models/AppraiseData'
import Vue2LeafletMarkerCluster from 'vue2-leaflet-markercluster'
import ModalMapDetail from '@/components/Modal/ModalMapDetail'
import WareHouse from '@/models/WareHouse'
Vue.use(Icon)
export default {
	name: 'ModalMap',
	props: ['data', 'location', 'address', 'center_map', 'radius', 'frontSide', 'landType', 'propertyTypes', 'isCheckFrontside'],
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
		'b-tooltip': BTooltip,
		'v-marker-cluster': Vue2LeafletMarkerCluster,
		ModalRadius,
		ModalMapDetail
	},
	data () {
		return {
			// icon: icon({
			//   iconUrl: "static/images/baseball-marker.png",
			//   iconSize: [32, 37],
			//   iconAnchor: [16, 37]
			// }),
			open_detail: false,
			assetGenerals: [],
			locationArray: [],
			search_address: '',
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}',
			place: '',
			provinces: [],
			layerType: 'OSM',
			markers: [],
			places: [],
			currentPlace: null,
			markerLatLng: [10.964112, 106.856461],
			center: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 16
			},
			circle: {
				center: [10.964112, 106.856461],
				radius: 1000,
				color: 'blue'
			},
			imageMap: true,
			hiddenList: false,
			open_radius: false,
			marker_id: '',
			locationArrayPopup: [],
			isCheckFront: this.isCheckFrontside,
			pic: [],
			property: null,
			transaction_type: {
				sold: true,
				rented_out: false,
				for_sale: true,
				for_rent: false
			},
			all_transaction: false,
			transaction: [],
			assetType: [],
			propertySelected: true
		}
	},
	async mounted () {
		if (this.location && this.location !== '') {
			this.circle.center = [this.location.split(',')[0], this.location.split(',')[1]]
			this.markerLatLng = [this.location.split(',')[0], this.location.split(',')[1]]
			this.map.center = [this.location.split(',')[0], this.location.split(',')[1]]
			this.circle.radius = this.radius
		}
		await this.getTransaction()
		await this.getPropertyTypes()
		await this.getAssetGenerals()
		this.findData()
	},
	methods: {
		handleAddProperty (id, select) {
			for (let i = 0; i < this.locationArray.length; i++) {
				if (this.locationArray[i].id === id) {
					this.locationArray[i].asset = select
				}
			}
		},
		getTransaction () {
			this.transaction = '[' + [51, 52] + ']'
		},
		getPropertyTypes () {
			this.propertyTypes.forEach(propertyType => {
				this.assetType.push(
					propertyType.id
				)
			})
		},
		handleTransactionType () {
			let data = this.transaction_type
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
		handleIsCheckFrontside () {
			this.handleCheckAll()
			this.getAssetGenerals()
			this.$emit('actionUpdateCheckFrontSide', this.isCheckFront)
		},
		handleSold () {
			this.handleCheckAll()
			this.handleTransactionType()
		},
		handleRentedOut () {
			this.handleCheckAll()
			this.handleTransactionType()
		},
		handleForSale () {
			this.handleCheckAll()
			this.handleTransactionType()
		},
		handleForRent () {
			this.handleCheckAll()
			this.handleTransactionType()
		},
		handleCheckAll () {
			this.all_transaction = this.transaction_type.sold === true && this.transaction_type.rented_out === true && this.transaction_type.for_sale === true && this.transaction_type.for_rent === true && this.isCheckFront === true
		},
		handleAllTransaction () {
			if (this.all_transaction === true) {
				this.transaction_type.sold = true
				this.transaction_type.rented_out = true
				this.transaction_type.for_sale = true
				this.transaction_type.for_rent = true
				this.isCheckFront = true
			} else {
				this.transaction_type.sold = false
				this.transaction_type.rented_out = false
				this.transaction_type.for_sale = false
				this.transaction_type.for_rent = false
				this.isCheckFront = false
			}
			this.handleTransactionType()
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
		updateAsset () {
			this.locationArrayPopup = []
			this.locationArray.forEach(data => {
				if (data.asset === true) {
					const dataFilter = this.assetGenerals.filter(item => item.id === data.id)
					if (dataFilter) {
						this.locationArrayPopup.push(dataFilter[0])
					}
				}
			})
			// await this.locationArray.forEach((location, index) => {
			//   if (location.asset === true) {
			//     const select = this.assetGenerals.find(item =>{location.id === item.id})
			//     console.log(select,'select')
			//     if(select){
			//       this.locationArrayPopup.push(this.assetGenerals[index])
			//       // this.locationArrayPopup.push(select)
			//     }
			//   }
			// })
		},
		async handleSubmit () {
			await this.updateAsset()
			let checkPrice = false
			await this.locationArrayPopup.forEach(item => {
				if (item.land_type_purpose_price.length > 0) {
					item.land_type_purpose_price.forEach(itemPriceLand => {
						if (itemPriceLand.price_land <= 10000) {
							checkPrice = true
						}
					})
				} else if (item.total_estimate_amount <= 10000) {
					checkPrice = true
				}
			})
			if (checkPrice) {
				this.$toast.open({
					message: 'Tài sản so sánh không hợp lệ',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.locationArrayPopup.length > 3) {
				this.$toast.open({
					message: 'Chỉ được chọn nhiều nhất 3 tài sản',
					type: 'error',
					position: 'top-right'
				})
			} else {
				await this.locationArrayPopup.sort((a, b) => b.id - a.id)
				this.$emit('actionUpdate', this.locationArrayPopup)
				this.$emit('cancel')
				this.$toast.open({
					message: 'Cập nhật thành công',
					type: 'success',
					position: 'top-right'
				})
			}
		},
		zoomUpdated (zoom) {
			this.map.zoom = zoom
		},
		async handleRadius (data) {
			this.circle.radius = data
			this.$emit('getRadius', data)
			await this.getAssetGenerals()
			this.findData()
		},
		findData () {
			if (this.data && this.data.length > 0) {
				this.data.forEach(id => {
					this.locationArray.forEach(idArray => {
						if (id.id === idArray.id) {
							idArray.asset = true
						}
					})
				})
				this.getArraySort()
			}
		},
		getArraySort () {
			this.locationArray.sort((a, b) => b.asset - a.asset)
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
		async initMap () {
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder()
			await this.geocodeAddress(geocoder)
		},
		async geocodeAddress (geocoder) {
			let center = {}
			const address = document.getElementById('coordinate').value
			await geocoder.geocode({'address': address}, function (results, status) {
				if (status === 'OK') {
					const marker = {
						position: results[0].geometry.location
					}
					center = [parseFloat(marker.position.lat()), parseFloat(marker.position.lng())]
				} else {
					// alert('Geocode was not successful for the following reason: ' + status)
				}
			})
			this.map.center = center
			this.markerLatLng = center
			this.markers = [
				center
			]
		},
		choosePoint (event) {
			this.map.center = [event.latlng.lat, event.latlng.lng]
			this.markerLatLng = [event.latlng.lat, event.latlng.lng]
		},
		setPlace (place) {
			this.search_address = place.formatted_address
			this.currentPlace = place
			this.markers = [
				{
					lat: place.geometry.location.lat(),
					lng: place.geometry.location.lng(),
					label: 'Vị trí'
				}
			]
			this.map.center = [place.geometry.location.lat(), place.geometry.location.lng()]
			this.markerLatLng = [place.geometry.location.lat(), place.geometry.location.lng()]
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			const data = this.markerLatLng[0] + ',' + this.markerLatLng[1]
			this.$emit('cancel', event)
			this.$emit('action', data)
		},
		handleDelete () {
			this.search_address = ''
		},
		validateBeforeSubmit () {
			const isValid = this.$refs.observer.validate()
			if (isValid) {
				this.handleAction()
			}
		},
		handleGmap (event) {
			this.markers = [
				{
					lat: event.latLng.lat(),
					lng: event.latLng.lng(),
					label: 'Vị trí'
				}
			]
			this.map.center = {
				lat: event.latLng.lat(),
				lng: event.latLng.lng()
			}
		},
		geoLocate () {
			navigator.geolocation.getCurrentPosition(position => {
				this.map.center = [position.coords.latitude, position.coords.longitude]
				this.markerLatLng = [position.coords.latitude, position.coords.longitude]
				this.markers = [
					{
						lat: position.coords.latitude,
						lng: position.coords.longitude,
						label: 'Vị trí'
					}
				]
			})
		},
		getLocate () {
			this.map.center = [parseFloat(this.location.lat), parseFloat(this.location.lng)]
			this.markerLatLng = [parseFloat(this.location.lat), parseFloat(this.location.lng)]
			this.markers = [
				{
					lat: parseFloat(this.location.lat),
					lng: parseFloat(this.location.lng),
					label: 'Vị trí'
				}
			]
		},
		async getAssetGenerals () {
			const distance = parseFloat(this.circle.radius / 1000).toFixed(2)
			const location = this.circle.center
			const frontSide = this.frontSide
			const isCheckFront = this.isCheckFront
			const transaction = this.transaction
			const assetType = '[' + this.assetType + ']'
			const resp = await AppraiseData.getSearchAll(distance, location, frontSide, transaction, assetType, isCheckFront)
			this.assetGenerals = [...resp.data]
			await this.getLatLng()
		},
		getLatLng () {
			this.locationArray = []
			this.assetGenerals.forEach(assetGeneral => {
				this.locationArray.push({
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
					main_road_length: assetGeneral.main_road_length,
					asset: false
				})
			})
		},
		openPopUp () {
			this.open_radius = true
		},
		async handleCenter (center, id) {
			this.map.zoom = 18
			this.map.center = center
			this.marker_id = id
		},
		centerUpdated (center) {
			this.center = center
		},
		handleMarkerHover (id) {
			this.marker_id = id
			const listTG = document.getElementById('listProperty')
			let activeItem = document.getElementById(id)
			listTG.scrollTop = activeItem.offsetTop - activeItem.offsetHeight / 1.5
		},
		handleMarker (event) {
			this.map.center = [event.latlng.lat, event.latlng.lng]
		}
	},
	beforeMount () {
	}
}
</script>
<style lang="scss" scoped>
.modal-delete {
  position: fixed;
  z-index: 1030;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  padding-top: 10px;
  background: rgba(0,0,0,.6);
  .card {
    max-width: 95vw;
    width: 100%;
    height: 90vh;
    margin-bottom: 0;
    @media (max-width: 767px) {
      max-width: 100vh;
      height: 100vh;
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
}
.input-map{
  box-sizing: border-box;
  margin-right: 10px;
  width: 100%;
}
.btn{
  &-search{
    height: 40px;
    background: #FAA831;
    color: #FFFFFF;
    font-weight: 700;
    white-space: nowrap;
    box-shadow:  0 1px 4px rgba(0, 0, 0, .25) !important;
    margin-right: 10px ;
    margin-left: 10px;
    &:hover {
      background: #f8b24b;
    }
    span{
      margin-right: 5px;
    }
    @media (max-width: 767px) {
      width: 100%;
      margin-top: 10px;
      margin-left: 0;
    }
  }
  &-cancel{
    height: 40px;
    background: #FFFFFF;
    font-weight: 700;
    white-space: nowrap;
    box-shadow:  0 1px 4px rgba(0, 0, 0, .25) !important;
    margin-right: 10px !important;
    border-radius: 3px;
  }
  &-location {
    min-width: auto;
  }
  &-exit{
    cursor: pointer;
    margin: 10px;
    width: 20px;
    height: auto;
  }
  &-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 146px;
    color: #fff;
    margin-right: 15px;
    box-sizing: border-box;
    &:hover{
      border-color: #dc8300;
    }
  }
}
.all-map{
  position: relative;
  height: 91.9%;
  width: 95vw;
  .loading{
    display: none;
    &__map{
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
      &.btn-loading{
        &:after{
          width: 2rem !important;
          height: 2rem !important;
        }
      }
    }
  }
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
  &--hidden {
    transition: .5s;
    width: 100%;
  }
}
.icon_marker{
  width: 25px;
}
.search-container {
  position: relative;
  margin-right: 10px;
  .icon-container{
    cursor: pointer;
    position: absolute;
    right: 0;
    top: 50%;
    width: 20px;
    height: auto;
    transform: translateY(-50%);
    img{
      width: 100%;
    }
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
      width: 80px;
      background: #FAA831;
      color: #ffffff;
      padding: 5px 13px;
      position: absolute;
      white-space: nowrap;
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
      left: -80px;
      @media (max-width: 1024px) {
        display: none;
      }
    }
  }
.input-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 15px;
  height: 15px;
  margin-bottom: 0;
  input {
    width: 15px;
    height: 15px;
    position: absolute;
    cursor: pointer;
    opacity: 0;
    &:checked {
      & ~ .check-mark {
        background-color: #FFFFFF;
        &:after {
          display: block;
        }
      }
    }
  }
  .check-mark {
    position: absolute;
    top: 0;
    left: 0;
    cursor: pointer;
    width: 15px;
    height: 15px;
    background-color: #FFFFFF;
    border: 1px solid #FFFFFF;
    border-radius: 3px;
    &:after {
      content: "";
      position: absolute;
      display: none;
      left: 50%;
      top: 50%;
      width: 5px;
      height: 10px;
      border: solid #000000;
      border-width: 0 1px 1px 0;
      -webkit-transform: rotate(45deg) translate(-125%, -25%);
      -ms-transform: rotate(45deg) translate(-125%, -25%);
      transform: rotate(45deg) translate(-125%, -25%);
    }
  }
  &--property {
    width: 100px;
    height: 100px;
    input {
      width: 100px;
      height: 100px;
      &:checked {
        & ~ .check-mark {
          background-color: #f29003;
          border: 1px solid;
          &:after {
            display: block;
          }
        }
      }
    }
    .check-mark {
      width: 100px;
      height: 100px;
      border: 1px solid #2d2d2d;
      border-radius: 12px;
      &:after {
        width: 36px;
        height: 76px;
        border: solid #ffffff;
        border-width: 0 12px 12px 0;
      }
    }
  }
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
  &-empty{
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
  &-contain{
    height: calc(100% - 92px);
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
      img{
        border-radius: 5px;
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .img__empty{
        width: 60%;
        height: 60%;
        margin: auto;
      }
    }
    &--content{
      width: calc(100%);
      .info{
        &-content{
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
    width: 35px;
    height: 35px;
    background-image: url("../../../../assets/icons/ic_marker_pin.svg");
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
  .container_detail {
    display: flex;
  }
}
</style>
