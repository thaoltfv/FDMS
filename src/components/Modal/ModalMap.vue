<template>
  <div class="modal-delete d-flex justify-content-center align-items-center">
    <ValidationObserver
      tag="form"
      ref="observer"
      @submit.prevent="validateBeforeSubmit"
    >
      <div class="card">
        <div class="d-block d-sm-flex justify-content-between my-3">
          <div class="search-container w-100">
            <gmap-autocomplete
              :value="search_address"
              :placeholder="strPlaceHolder"
              @place_changed="setPlace"
              @change="changePlace"
              @keyup.enter="changePlace"
              class="input-map"
              :options="{
                fields: ['geometry', 'address_components', 'formatted_address'],
								componentRestrictions:{country: 'vn'}
              }"
            >
            </gmap-autocomplete>
            <div class="icon-container" @click="handleSearch">
              <img src="@/assets/icons/ic_search.svg" alt="" />
            </div>
          </div>
          <input type="text" id="coordinate" :value="address" class="d-none" />
          <button class="btn btn-search" type="button" @click="handleAction">
            Xác nhận
          </button>
          <button
            class="btn btn-white btn-cancel"
            type="button"
            @click="handleCancel"
          >
            Trở lại
          </button>
        </div>
        <div class="main-map">
          <div id="mapid" class="layer-map">
            <l-map
              ref="lmap"
              style="height: 100%"
              :zoom="map.zoom"
              :center="map.center"
              :options="{ zoomControl: false }"
              :maxZoom="20"
              @click="choosePoint($event)"
            >
              <l-tile-layer
                :url="url"
                :options="{ maxNativeZoom: 19, maxZoom: 20 }"
              ></l-tile-layer>
              <l-control-zoom position="bottomright"></l-control-zoom>
              <l-control position="bottomright">
                <button
                  class="btn btn-orange mini_btn"
                  type="button"
                  @click="geoLocate"
                >
                  <img
                    src="@/assets/icons/ic_locate_white.svg"
                    alt="location"
                  />
                </button>
              </l-control>
              <l-control position="bottomleft">
                <button class="btn btn-map" @click="handleView" type="button">
                  <img
                    v-if="!imageMap"
                    src="../../assets/images/im_map.png"
                    alt=""
                  />
                  <img
                    v-if="imageMap"
                    src="../../assets/images/im_satellite.png"
                    alt=""
                  />
                </button>
              </l-control>
              <l-marker :lat-lng="markerLatLng">
                <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                  <img
                    style="width: 60px !important"
                    class="icon_marker"
                    src="@/assets/images/svg_home.svg"
                    alt=""
                  />
                </l-icon>
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
@import "../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import {
	LMap,
	LControlZoom,
	LTileLayer,
	LMarker,
	LTooltip,
	LIcon,
	LControl
} from 'vue2-leaflet'
import Vue from 'vue'
import Icon from 'buefy'

Vue.use(Icon)
export default {
	name: 'ModalMap',
	props: ['location', 'address', 'center_map'],
	components: {
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon
	},
	data () {
		return {
			search_address: '',
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}',
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
			imageMap: true,
			strPlaceHolder: 'Nhập địa điểm, vị trí hoặc tọa độ'
		}
	},
	async mounted () {
		if (this.address !== undefined && this.address !== null) {
			this.search_address = this.address
			this.$gmapApiPromiseLazy().then(
				await this.initMap(this.search_address, 'address')
			)
		}
		if (
			this.center_map !== '' &&
      this.center_map !== undefined &&
      this.center_map !== null
		) {
			this.map.center = [
				this.center_map.split(',')[0],
				this.center_map.split(',')[1]
			]
			this.markerLatLng = [
				this.center_map.split(',')[0],
				this.center_map.split(',')[1]
			]
			this.markers = [
				[this.center_map.split(',')[0], this.center_map.split(',')[1]]
			]
		}
	},
	methods: {
		changePlace (event) {
			this.search_address = event.target.value
		},
		handleView () {
			if (this.url === 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}') {
				// 		this.url =
				//   'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url = 'https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile'
				this.imageMap = false
			} else {
				this.url = 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}'
				this.imageMap = true
			}
		},
		handleSearch () {
			if (this.search_address) {
				let location = this.search_address
				if (location.split(',') && location.split(',').length === 2 && parseFloat(location.split(',')[0]) && parseFloat(location.split(',')[1])) {
					let lat = parseFloat(location.split(',')[0])
					let lng = parseFloat(location.split(',')[1])
					this.map.center = [lat, lng]
					this.markerLatLng = [lat, lng]
				} else {
					this.initMap(location, 'address')
				}
			} else {
				this.initMap(this.search_address, 'address')
			}
		},
		// centerUpdated (center) {
		//   this.map.center = [center.lat, center.lng]
		//   this.markerLatLng = [center.lat, center.lng]
		// },
		async initMap (address, type) {
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder()
			await this.geocodeAddress(geocoder, address, type)
		},
		async geocodeAddress (geocoder, address, type) {
			let center = {}
			// let address = this.address
			if (!address) {
				address = document.getElementById('coordinate').value
			}
			if (address) {
				let keySearch = {}
				if (type === 'address') {
					keySearch = {
						address: address
					}
				} else if (type === 'location') {
					keySearch = {
						location: address
					}
				}
				await geocoder.geocode(keySearch, function (results, status) {
					if (status === 'OK') {
						const marker = {
							position: results[0].geometry.location
						}
						center = [
							parseFloat(marker.position.lat()),
							parseFloat(marker.position.lng())
						]
					} else {
						// alert('Geocode was not successful for the following reason: ' + status)
					}
				})
				this.map.center = center
				this.markerLatLng = center
			}
		},
		choosePoint (event) {
			this.map.center = [event.latlng.lat, event.latlng.lng]
			this.markerLatLng = [event.latlng.lat, event.latlng.lng]
			this.search_address = event.latlng.lat + ',' + event.latlng.lng
		},
		setPlace (place) {
			if (place.geometry && place.geometry.location) {
				this.search_address = place.formatted_address
				this.currentPlace = place
				this.map.center = [
					place.geometry.location.lat(),
					place.geometry.location.lng()
				]
				this.markerLatLng = [
					place.geometry.location.lat(),
					place.geometry.location.lng()
				]
			} else {
				if (place.name) {
					let location = place.name
					this.search_address = place.name
					if (
						location.split(',') &&
            location.split(',').length === 2 &&
            parseFloat(location.split(',')[0]) &&
            parseFloat(location.split(',')[1])
					) {
						let lat = parseFloat(location.split(',')[0])
						let lng = parseFloat(location.split(',')[1])
						this.map.center = [lat, lng]
						this.markerLatLng = [lat, lng]
					} else {
						this.initMap(location, 'address')
					}
				} else {
					this.initMap(this.search_address, 'address')
				}
			}
			// this.search_address = place.formatted_address
			// this.currentPlace = place
			// this.markers = [
			// 	{
			// 		lat: place.geometry.location.lat(),
			// 		lng: place.geometry.location.lng(),
			// 		label: 'Vị trí'
			// 	}
			// ]
			// this.map.center = [place.geometry.location.lat(), place.geometry.location.lng()]
			// this.markerLatLng = [place.geometry.location.lat(), place.geometry.location.lng()]
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
			navigator.geolocation.getCurrentPosition((position) => {
				this.map.center = [position.coords.latitude, position.coords.longitude]
				this.markerLatLng = [
					position.coords.latitude,
					position.coords.longitude
				]
				// this.map.center = {
				//   lat: position.coords.latitude,
				//   lng: position.coords.longitude
				// }
				// this.map.zoom = 20
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
			this.map.center = [
				parseFloat(this.location.lat),
				parseFloat(this.location.lng)
			]
			this.markerLatLng = [
				parseFloat(this.location.lat),
				parseFloat(this.location.lng)
			]
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
	beforeMount () {}
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
  background: rgba(0, 0, 0, 0.6);
  .card {
    max-width: 90vw;
    width: 100%;
    height: 80vh;
    margin-bottom: 0;
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
    //.g-map{
    //  width: 90vw;
    //  height: 80vh;
    //  border-radius: 5px;
    //  @media (max-width: 1023px) {
    //    width: 100vw;
    //  }
    //  @media (max-width: 767px) {
    //    max-width: 100vw;
    //    height: 100dvh;
    //  }
    //}
  }
}
.input-map {
  box-sizing: border-box;
  margin-right: 10px;
  width: 100%;
  //padding: 0 10px;
}
.btn {
  &-search {
    height: 40px;
    background: #faa831;
    color: #ffffff;
    font-weight: bold;
    white-space: nowrap;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    margin-right: 10px;
    margin-left: 10px;
    &:hover {
      background: #f8b24b;
    }
    span {
      margin-right: 5px;
    }
    @media (max-width: 767px) {
      width: 100%;
      margin-top: 10px;
      margin-left: 0;
    }
  }
  &-cancel {
    height: 40px;
    background: #ffffff;
    font-weight: bold;
    white-space: nowrap;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    margin-right: 10px !important;
    border-radius: 3px;
  }
  &-location {
    min-width: auto;
  }
  &-exit {
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
.icon_marker {
  width: 25px;
}
.search-container {
  position: relative;
  margin-right: 10px;
  .icon-container {
    cursor: pointer;
    position: absolute;
    right: 0;
    top: 50%;
    width: 20px;
    height: auto;
    transform: translateY(-50%);
    img {
      width: 100%;
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
.mini_btn {
  width: 30px;
  height: 30px;
  padding: unset !important;
}
</style>
