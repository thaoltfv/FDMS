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
              :options="{
								fields: ['geometry', 'address_components', 'formatted_address'],
								componentRestrictions:{country: 'vn'}
							}"
            >
            </gmap-autocomplete>
            <div class="icon-container" v-if="search_address !== '' && search_address !== undefined && search_address !== null" @click="handleDelete">
              <img src="@/assets/icons/ic_delete_1.svg" alt="">
            </div>
          </div>
          <input type="text" id="coordinate" :value="address" class="d-none">
          <button class="btn btn-search" type="button" @click="handleAction">Thêm tọa độ</button>
          <button class="btn btn-white btn-cancel btn-location"  type="button" @click.prevent="geoLocate"><img src="@/assets/icons/ic_locate.svg" alt="add"></button>
          <button class="btn btn-white btn-cancel" type="button" @click="handleCancel">Trở lại</button>
        </div>
        <div class="main-map">
          <div id="mapid" class="layer-map">
            <l-map ref="lmap"
              style="height: 100%;"
              :zoom="map.zoom"
              :center="map.center"
              :maxZoom="20"
              :options="{zoomControl: false}"
              @click="choosePoint($event)"
            >
              <l-tile-layer :url="url" :options="{ maxNativeZoom: 19, maxZoom: 20}"></l-tile-layer>
              <l-control-zoom position="bottomright"></l-control-zoom>
              <l-control position="bottomleft">
                <button class="btn btn-map" @click="handleView" type="button">
                  <img v-if="!imageMap" src="@/assets/images/im_map.png" alt="">
                  <img v-if="imageMap" src="@/assets/images/im_satellite.png" alt="">
                </button>
              </l-control>
              <l-marker :lat-lng="markerLatLng">
                <l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
                  <img style="width: 60px !important" class="icon_marker" src="@/assets/images/svg_home.svg" alt="">
                </l-icon>
              <l-tooltip>Vị trí tài sản</l-tooltip>
              </l-marker>
            </l-map>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>
<style lang="scss">
@import "../../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import {LMap, LControlZoom, LTileLayer, LMarker, LTooltip, LIcon, LControl} from 'vue2-leaflet'
import Vue from 'vue'
import Icon from 'buefy'

Vue.use(Icon)
export default {
	name: 'ModalMapAsset',
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
			places: [],
			currentPlace: null,
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 15
			},
			imageMap: true,
			render_map: 3232102
		}
	},
	async mounted () {
		this.search_address = this.address
		if (this.center_map !== '' && this.center_map) {
			this.map.center = [this.center_map.split(',')[0], this.center_map.split(',')[1]]
			this.markerLatLng = [this.center_map.split(',')[0], this.center_map.split(',')[1]]
		}
		await this.$gmapApiPromiseLazy()
		await this.initMap()
	},
	methods: {
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
			let address = this.address
			if (!address) {
				address = document.getElementById('coordinate').value
			}
			if (address) {
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
			}
		},
		choosePoint (event) {
			this.map.center = [event.latlng.lat, event.latlng.lng]
			this.markerLatLng = [event.latlng.lat, event.latlng.lng]
		},
		setPlace (place) {
			this.search_address = place.formatted_address
			this.currentPlace = place
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
		geoLocate () {
			navigator.geolocation.getCurrentPosition(position => {
				this.map.center = [position.coords.latitude, position.coords.longitude]
				this.markerLatLng = [position.coords.latitude, position.coords.longitude]
			})
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
