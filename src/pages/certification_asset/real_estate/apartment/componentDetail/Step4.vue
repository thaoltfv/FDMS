<template>
  <div>
    <div class="card">
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
                  :disabled="true"
                  :id="itemCompare.slug"
                  v-model="data.comparison_factor"
                > -->
                <label class="input-checkbox">
                  <input type="checkbox"
                    :class="{'disable_input': itemCompare.slug === 'phap_ly'}"
                    :value="itemCompare.slug"
                    :checked="itemCompare.slug === 'phap_ly' || itemCompare.disable"
                    :disabled="true"
                    :id="itemCompare.slug"
                    v-model="data.comparison_factor">
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
		// bỏ vì detail không cần loading
		// if (this.step_active >= 3) {
		// 	await this.getListAsset()
		// }
		if (this.$refs.map_stemap_step6p2) {
			await setTimeout(() => {
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
			const getAllAsset = await CertificateAsset.getAllAssetApartment(distance, location, transaction, assetType)
			this.listAssetGeneral = [...getAllAsset.data]
			await this.listAssetGeneral.forEach(item => {
				item['center'] = [parseFloat(item.coordinates.split(',')[0]), parseFloat(item.coordinates.split(',')[1])]
				item['isChoosing'] = false
			})
		},
		handleHidden () {
			this.hiddenList = !this.hiddenList
			setTimeout(() => {
				this.$refs.lmap.mapObject.invalidateSize()
			}, 501)
		},
		async handleMarker (event, asset) {
			const data = await [asset]
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
			const distance = parseFloat(dataFilter.radius / 1000).toFixed(2)
			const location = this.circle.center
			const transaction = '[' + dataFilter.transaction + ']'
			const assetType = '[' + dataFilter.assetType + ']'
			const getAllAsset = await CertificateAsset.getAllAssetApartment(distance, location, transaction, assetType)
			this.listAssetGeneral = [...getAllAsset.data]
			await this.listAssetGeneral.forEach(item => {
				item['center'] = [parseFloat(item.coordinates.split(',')[0]), parseFloat(item.coordinates.split(',')[1])]
				// item['isChoosing'] = false
				if (this.assetHasChoose && this.assetHasChoose.length > 0) {
					let checkAssetChoose = this.assetHasChoose.filter(asset => asset.id === item.id)
					if (checkAssetChoose && checkAssetChoose.length > 0) {
						item['isChoosing'] = true
					} else item['isChoosing'] = false
				} else item['isChoosing'] = false
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
			const data = await [asset]
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
				await this.$toast.open({
					message: 'Vui lòng chọn tài sản so sánh',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.assetHasChoose.length > 3) {
				await this.$toast.open({
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

.img-location-choose {
  width: 50px !important;
  position: absolute;
  bottom: 0px;
  right: -21px;
}

.img-choosing {
  position: absolute;
  width: 18px;
  right: -4px;
  top: -23px;
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
        background-color: #DEE6EE;
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
</style>
