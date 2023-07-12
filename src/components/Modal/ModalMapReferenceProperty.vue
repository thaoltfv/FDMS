<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div class="card">
        <div class="d-block d-sm-flex justify-content-between my-3">
          <div class="container-note">
            <div class="mr-18 d-flex align-items-center">
              <div class="note-color note-color__blue"/>
              <p class="note-content">Tài sản tham chiếu</p>
            </div>
            <div class="mr-18 d-flex align-items-center">
              <div class="note-color note-color__red"/>
              <p class="note-content">Vị trí ước tính</p>
            </div>
          </div>
          <div class="img-cancel">
            <img @click="handleCancel"
                 src="../../assets/icons/ic_cancel-1.svg"
                 alt="icon">
          </div>
<!--          <button class="btn btn-white btn-cancel" type="button" @click="handleCancel">Trở lại</button>-->
        </div>
        <div class="main-map">
          <div id="mapid" class="layer-map">
            <l-map ref="lmap"
                   style="height: 100%;"
                   :zoom="map.zoom"
                   :center="location"
                   :options="{zoomControl: false}"
                   :maxZoom="20"
            >
              <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
              <l-control-zoom position="bottomright"></l-control-zoom>
              <l-control position="bottomleft">
                <button class="btn btn-map" @click="handleView" type="button">
                  <img v-if="!imageMap" src="../../assets/images/im_map.png" alt="">
                  <img v-if="imageMap" src="../../assets/images/im_satellite.png" alt="">
                </button>
              </l-control>
              <l-marker :lat-lng="location">

                          <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                            <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
                          </l-icon>
                <l-tooltip>Vị trí ước tính</l-tooltip>
              </l-marker>
              <v-marker-cluster>
                <l-marker v-for="(asset, index) in assets" :key="index" :lat-lng="[asset.coordinates.split(',')[0], asset.coordinates.split(',')[1]]">
                  <l-icon class-name="someExtraClass">
                    <div class="marker marker__blue"/>
                  </l-icon>
                  <l-tooltip class="sp-custom-popup" ref="popup">
                    <div class="d-flex justify-content-between">
                      <p class="popup-name">Mã:</p>
                      <p class="popup-content popup-content__id">{{asset.migrate_status + '_' + asset.id}}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                      <p class="popup-name">Loại BĐS:</p>
                      <p class="popup-content popup-content__blue">{{asset.transaction_type_description}}</p>
                    </div>
                    <div class="d-flex justify-content-between">
                      <p class="popup-name">Diện tích:</p>
                      <p class="popup-content">{{asset.total_area}} m<sup>2</sup></p>
                    </div>
                    <div class="d-flex justify-content-between">
                      <p class="popup-name">Tổng giá trị:</p>
                      <p class="popup-content">{{formatNumber(asset.total_amount)}} đ</p>
                    </div>
                  </l-tooltip>
                </l-marker>
              </v-marker-cluster>
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
import {LMap, LPopup, LControlZoom, LTileLayer, LMarker, LTooltip, LIcon, LControl} from 'vue2-leaflet'
import Vue2LeafletMarkerCluster from 'vue2-leaflet-markercluster'
import Vue from 'vue'
import Icon from 'buefy'

Vue.use(Icon)
export default {
	name: 'ModalMap',
	props: ['location', 'assets', 'center_map'],
	components: {
		'v-marker-cluster': Vue2LeafletMarkerCluster,
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon,
		LPopup
	},
	data () {
		return {
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
				zoom: 16
			},
			imageMap: true
		}
	},
	async mounted () {
		if (this.address !== undefined && this.address !== null) {
			this.search_address = this.address
			await this.$gmapApiPromiseLazy()
			await this.initMap()
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
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		handleView () {
			if (this.url === 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png') {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url = 'https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile'
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
    background-image: url("../../assets/icons/ic_marker_pin.svg");
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
    margin-left: 20px;
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
.container {
  &-note{
    margin-left: 10px;
    display: flex;
    @media (max-width: 767px) {
      display: block;
    }
  }
}
.mr-18 {
  margin-right: 18px;
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

    &__red {
      background: #fa0818;
    }
  }
  &-content {
    margin-bottom: 0;
    font-size: 14px;
    color: #000000;
  }
}
.img-cancel {
  margin-right: 10px;
  cursor: pointer;
}
</style>
