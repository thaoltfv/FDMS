<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div class="card">
        <div class="d-block d-sm-flex justify-content-between my-3">
          <div class="search-container w-100">
            <gmap-autocomplete
              :value="search_address"
              placeholder="Nhập địa chỉ"
              @place_changed="setPlace"
              class="input-map"
              :options="{fields: ['geometry', 'address_components', 'formatted_address']}"
            >
            </gmap-autocomplete>
            <div class="icon-container" v-if="search_address !== '' && search_address !== undefined && search_address !== null" @click="handleDelete">
              <img src="../../assets/icons/ic_delete_1.svg" alt="">
            </div>
          </div>
          <input type="text" id="coordinate" :value="address" class="d-none">
          <button class="btn btn-search" type="button" @click="handleAction">Thêm tọa độ</button>
          <button class="btn btn-white btn-cancel btn-location"  type="button" @click.prevent="geoLocate"><img src="../../assets/icons/ic_locate.svg" alt="add"></button>
          <button class="btn btn-white btn-cancel" type="button" @click="handleCancel">Trở lại</button>
        </div>
        <div class="main-map">
          <div id="mapid" class="layer-map">
            <l-map ref="lmap"
                   style="height: 100%;"
                   :zoom="map.zoom"
                   :center="map.center"
                   :options="{zoomControl: false}"
                   :maxZoom="20"
                   @click="choosePoint($event)"
            >
              <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
              <l-control-zoom position="bottomright"></l-control-zoom>
              <l-control position="bottomleft">
                <button class="btn btn-map" @click="handleView" type="button">
                  <img v-if="!imageMap" src="../../assets/images/im_map.png" alt="">
                  <img v-if="imageMap" src="../../assets/images/im_satellite.png" alt="">
                </button>
              </l-control>
              <l-marker :lat-lng="markerLatLng">
                <l-tooltip>Vị trí của bạn</l-tooltip>
              </l-marker>
            </l-map>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import '../../../node_modules/leaflet/dist/leaflet.css';
</style>
<script>
import {LMap, LControlZoom, LTileLayer, LMarker, LTooltip, LIcon, LControl} from 'vue2-leaflet'
import PriceEstimate from '@/models/PriceEstimate'
import Vue from 'vue'
import Icon from 'buefy'

Vue.use(Icon)
export default {
	name: 'ModalMapEstimate',
	props: ['location', 'address', 'center_map'],
	components: {
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LIcon,
		LControl
	},
	data () {
		return {
			address_ids: null,
			search_address: '',
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			place: '',
			provinces: [],
			layerType: 'OSM',
			markers: [],
			places: [],
			currentPlace: null,
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 15
			},
			address_components: {
				province: '',
				district: '',
				street: ''
			},
			imageMap: true
		}
	},
	async mounted () {
		if (this.address !== '' && this.address !== undefined && this.address !== null) {
			this.search_address = this.address
			this.$gmapApiPromiseLazy().then(
				await this.initMap()
			)
		}
		if (this.center_map !== '' && this.center_map !== undefined && this.center_map !== null) {
			this.map.center = [this.center_map.split(',')[0], this.center_map.split(',')[1]]
			this.markerLatLng = [this.center_map.split(',')[0], this.center_map.split(',')[1]]
			this.markers = [
				[this.center_map.split(',')[0], this.center_map.split(',')[1]]
			]
		}
	},
	methods: {
		handleView () {
			if (this.url === 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png') {
				this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.imageMap = false
			} else {
				this.url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
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
			// this.map.zoom = 20
		},
		async initMapClick (event) {
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder()
			await this.eventMapClick(geocoder, event)
		},
		async eventMapClick (geocoder, event) {
			const location = event.latlng
			let address = ''
			let address_components = []
			await geocoder.geocode({'location': location}, function (results, status) {
				if (status === 'OK') {
					address = results[0].formatted_address
					address_components = results[0].address_components
				} else {
				}
			})
			this.search_address = address
			this.getAddressDetail(address_components)
		},
		getAddressDetail (address_components) {
			let address_province = address_components.find(address_component_province => address_component_province.types[0] === 'administrative_area_level_1')
			let address_district = address_components.find(address_component_district => address_component_district.types[0] === 'locality' || address_component_district.types[0] === 'administrative_area_level_2')
			let address_street = address_components.find(address_component => address_component.types[0] === 'route')
			if (address_province) {
				this.address_components.province = address_province.long_name.normalize('NFC')
			} else {
				this.address_components.province = ''
			}
			if (address_district) {
				this.address_components.district = address_district.long_name.normalize('NFC')
			} else {
				this.address_components.district = 'UnNamedRoad'
			}
			if (address_street) {
				this.address_components.street = address_street.long_name.normalize('NFC')
			} else {
				this.address_components.street = ''
			}
		},
		async getAddressLocation () {
			const province = this.address_components.province
			const district = this.address_components.district
			const street = this.address_components.street
			const resp = await PriceEstimate.getAddress({province, district, street})
			this.address_ids = resp.data
		},
		choosePoint (event) {
			this.initMapClick(event)
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
		async handleAction (event) {
			const data = this.markerLatLng[0] + ',' + this.markerLatLng[1]
			const address = this.address_components
			const full_address = this.search_address
			this.$emit('cancel', event)
			this.$emit('action', data)
			this.$emit('get_address', address, full_address)
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
			// this.map.zoom = 20
			this.markers = [
				{
					lat: parseFloat(this.location.lat),
					lng: parseFloat(this.location.lng),
					label: 'Vị trí'
				}
			]
		}
	},
	beforeMount () {
	}
}
</script>
<style lang="scss" scoped>
.modal-delete {
  position: fixed;
  z-index: 10002;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  padding-top: 10px;
  background: rgba(0,0,0,.6);
  .card {
    max-width: 90vw;
    width: 100%;
    height: 80vh;
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
    //.g-map{
    //  width: 90vw;
    //  height: 80vh;
    //  border-radius: 5px;
    //  @media (max-width: 1023px) {
    //    width: 100vw;
    //  }
    //  @media (max-width: 767px) {
    //    max-width: 100vw;
    //    height: 100vh;
    //  }
    //}
  }
}
.input-map{
  box-sizing: border-box;
  margin-right: 10px;
  width: 100%;
  //padding: 0 10px;
}
.btn{
  &-search{
    height: 40px;
    background: #FAA831;
    color: #FFFFFF;
    font-weight: bold;
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
    font-weight: bold;
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
}
.main-map {
  position: relative;
  height: 100%;
  width: 90vw;
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
</style>
