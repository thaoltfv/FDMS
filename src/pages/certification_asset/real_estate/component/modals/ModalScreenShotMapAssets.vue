<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <div v-show="isSubmit" class="loading">
      <a-spin />
    </div>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div class="card">
        <div class="d-block d-sm-flex justify-content-between my-3">
          <div class="container-note">
            <div class="mr-18 d-flex align-items-center">
              <img class="icon_marker" style="width: 23px;margin-right: 10px;" src="@/assets/images/marker_empty.png" alt="">
              <p class="note-content">Tài sản tham chiếu</p>
            </div>
            <div class="mr-18 d-flex align-items-center">
              <img class="icon_marker" style="width: 33px;margin-right: 10px;"  src="@/assets/images/home_icon.png" alt="">
              <p class="note-content">Tài sản thẩm định</p>
            </div>
          </div>
          <div class="img-cancel">
            <button class="btn btn-search mr-2" type="button" @click="saveMapImage">Chụp ảnh bản đồ</button>
            <button class="btn btn-white btn-cancel" type="button" @click="handleCancel">Trở lại</button>
          </div>
        </div>
        <div class="main-map">
          <div id="mapid" class="layer-map">
            <l-map ref="lmap"
                   style="height: 100%;"
                   :zoom="map.zoom"
                   :center="map.center"
                   @update:center="updateCenter"
                   :options="{zoomControl: false}"
            >
              <l-tile-layer :url="url"></l-tile-layer>
              <l-control-zoom position="bottomright"></l-control-zoom>
              <l-control position="bottomleft">
                <button class="btn btn-map" @click="handleView" type="button">
                  <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                  <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
                </button>
              </l-control>
              <l-marker :lat-lng="[coordinates.split(',')[0], coordinates.split(',')[1]]">
                <l-icon class-name="someExtraClass" :iconAnchor="[30, 60]">
                  <!-- <div class="marker marker__red"/> -->
                  <img class="icon_marker asset_icon" src="@/assets/images/svg_home.svg" alt="">
                </l-icon>
                <!-- <l-tooltip class="sp-custom-popup" ref="popup" :options="{ permanent: true, interactive: true }">
                  <div class="d-flex justify-content-between">
                    <p class="popup-name">Vị trí tài sản thẩm định</p>
                  </div>
                </l-tooltip> -->
              </l-marker>
              <l-marker v-for="(asset, index) in assets" :key="asset.id" :lat-lng="[asset.coordinates.split(',')[0], asset.coordinates.split(',')[1]]" @click="handleRotate(index + 1)">
                <l-icon :iconAnchor="[30, 60]" >
                  <!-- <div class="marker marker__blue"/> -->
                    <img v-if="index === 0" class="icon_marker asset_icon" :style="{transform: `rotate(${rotation1}deg)`}" src="@/assets/images/svg_marker_1.svg" alt="">
                    <img v-if="index === 1" class="icon_marker asset_icon" :style="{transform: `rotate(${rotation2}deg)`}" src="@/assets/images/svg_marker_2.svg" alt="">
                    <img v-if="index === 2" class="icon_marker asset_icon" :style="{transform: `rotate(${rotation3}deg)`}" src="@/assets/images/svg_marker_3.svg" alt="">
                  <!-- <img class="icon_marker" style="width: 40px" src="../../../../assets/images/iconhouse.png" alt=""> -->
                </l-icon>
                <!-- <l-tooltip class="sp-custom-popup" ref="popup" :options="{ permanent: true, interactive: true }">
                  <p class="popup-content__id mb-0" v-for="(assetId, index) in assets" :key="'id' + assetId.id" v-if="asset.coordinates === assetId.coordinates">{{`TSSS ${index + 1}`}}</p>
                </l-tooltip> -->
              </l-marker>
            </l-map>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>
<style lang="scss">
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import '../../../../../../node_modules/leaflet/dist/leaflet.css';
</style>
<script>
import {LMap, LPopup, LControlZoom, LTileLayer, LMarker, LTooltip, LIcon, LControl} from 'vue2-leaflet'
import Vue2LeafletMarkerCluster from 'vue2-leaflet-markercluster'
import Vue from 'vue'
import Icon from 'buefy'
import { SimpleMapScreenshoter } from 'leaflet-simple-map-screenshoter'

Vue.use(Icon)
export default {
	name: 'ModalMap',
	props: ['coordinates', 'assets', 'center_map', 'assetArray'],
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
			isSubmit: false,
			mapScreen: {},
			search_address: '',
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			place: '',
			provinces: [],
			layerType: 'OSM',
			markers: [],
			places: [],
			currentPlace: null,
			rotation1: 0,
			rotation2: 0,
			rotation3: 0,
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				// center: [300.964112, 89.856461],
				zoom: 16
			},
			imageMap: true
		}
	},
	async mounted () {
		this.map.center = [this.coordinates.split(',')[0], this.coordinates.split(',')[1]]
		this.$nextTick(() => {
			this.mapScreen = this.$refs.lmap.mapObject
		})
	},
	methods: {
		handleRotate (numberImage) {
			if (numberImage === 1) {
				this.rotation1 += 45
			} else if (numberImage === 2) {
				this.rotation2 += 45
			} else if (numberImage === 3) {
				this.rotation3 += 45
			}
		},
		saveMapImage () {
			let pluginOptions = {
				cropImageByInnerWH: true, // crop blank opacity from image borders
				hidden: true, // hide screen icon
				domtoimageOptions: {
					cacheBust: true,
					crossOrigin: 'anonymous'
				}, // see options for dom-to-image
				position: 'topleft', // position of take screen icon
				screenName: 'screen', // string or function
				hideElementsWithSelectors: ['.leaflet-control-container'], // by default hide map controls All els must be child of _map._container
				// mimeType: "image/png", // used if format == image,
				caption: null // streeng or function, added caption to bottom of screen
				// captionFontSize: 15,
				// captionFont: "Arial",
				// captionColor: "black",
				// captionBgColor: "white",
				// captionOffset: 5
				// onPixelDataFail: async function({ node, plugin, error, mapPane, domtoimageOptions }) {
				// // Solutions:
				// // decrease size of map
				// // or decrease zoom level
				// // or remove elements with big distanses
				// // and after that return image in Promise - plugin._getPixelDataOfNormalMap
				//   return plugin._getPixelDataOfNormalMap(domtoimageOptions)
				// }
			}

			this.simpleMapScreenshoter = new SimpleMapScreenshoter(
				pluginOptions
			).addTo(this.mapScreen)

			const format = 'image' // 'image' - return base64, 'canvas' - return canvas

			this.mapScreen.on('simpleMapScreenshoter.takeScreen', this.beforeScreenshot)
			this.simpleMapScreenshoter
				.takeScreen(format, pluginOptions)
				.then(image => {
					this.$emit('saveImageMap', image)
					this.$emit('cancel')
				})
		},
		beforeScreenshot (e) {
			this.isSubmit = true
			Array.from(document.styleSheets).forEach(sheet => {
				const node = sheet.ownerNode
				if (
					node &&
          node.href &&
          node.href.indexOf('fonts.googleapis.com') > -1
				) {
					node.parentElement.removeChild(node)
				}
			})
		},
		getSubmit () {
			this.isSubmit = true
		},
		updateCenter (e) {
			this.map.center = e
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
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
			this.map.center = [parseFloat(this.coordinates.lat), parseFloat(this.coordinates.lng)]
			this.markerLatLng = [parseFloat(this.coordinates.lat), parseFloat(this.coordinates.lng)]
			// this.map.zoom = 20
			this.markers = [
				{
					lat: parseFloat(this.coordinates.lat),
					lng: parseFloat(this.coordinates.lng),
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
.asset_icon {
  width: 60px !important;
  transform-origin: bottom;
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
    background-image: url("../../../../../assets/icons/ic_marker_pin.svg");
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
    &__red {
      color: #de1616;
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

    color: #000000;
  }
}
.img-cancel {
  margin-right: 10px;
  cursor: pointer;
}
.loading{
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.62);
  position: fixed;
  z-index: 10003;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  &.btn-loading {
    &:after{
      width: 2rem !important;
      height: 2rem !important;
    }
  }
}
</style>
