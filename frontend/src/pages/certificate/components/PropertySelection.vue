<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <h3 class="title">Lựa chọn tài sản so sánh</h3>
          <button type="button" class="btn btn-orange mt-0 ml-2" @click="getAsset">Automatic</button>
          <button type="button" class="btn btn-orange mt-0 ml-2 text-nowrap" @click="handelEditProperty">Chỉnh sửa</button>
        </div>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard && data && data.length > 0">
      <div class="row justify-content-center" v-if="(typeof data !== 'undefined')">
        <div class="col-12 col-lg-3 mt-2" v-for="(asset, index) in data" :key="asset.id">
          <div class="container__property">
            <div class="property__detail d-flex justify-content-between">
              <p class="name">Tài sản {{index + 1}}:</p>
              <p class="content content__id">{{asset.migrate_status + '_' + asset.id}}</p>
            </div>
            <div class="property__detail d-flex justify-content-between">
              <p class="name">Phiên bản:</p>
              <p class="content">{{asset.version && asset.version.length > 0 ? showVersionAsset(asset) : (asset.version ? asset.version : '')}}</p>
            </div>
            <div class="property__detail d-flex justify-content-between">
              <p class="name">Loại giao dịch:</p>
              <p class="content" :class="asset.transaction_type_id === 51 ? 'color__blue': asset.transaction_type_id === 53 ? 'color__orange' : asset.transaction_type_id === 52 ? 'color__purple': 'color__green'">{{asset.transaction_type_description ? asset.transaction_type_description : asset.transaction_type.description}}</p>
            </div>
            <div class="property__detail d-flex justify-content-between">
              <p class="name">Vị trí:</p>
              <p class="content">{{ asset.front_side === 1 || asset.front_side === 0 ? (asset.front_side  === 0 ? 'Hẻm' : asset.front_side  === 1 ? 'Mặt tiền' : '') : asset.properties[0] && asset.properties[0].front_side === 1 ? 'Mặt tiền' : 'Hẻm'}}</p>
            </div>
            <div class="property__detail d-flex justify-content-between" v-if="asset.front_side === 0">
              <p class="name">Bề rộng hẻm:</p>
              <p class="content">{{asset.main_road_length ? formatArea(asset.main_road_length) + ' m' : '' }}</p>
            </div>
            <div class="property__detail d-flex justify-content-between">
              <p class="name">Tổng diện tích:</p>
              <p class="content">{{formatArea(asset.total_area)}} m<sup>2</sup></p>
            </div>
            <div class="property__detail d-flex justify-content-between">
              <p class="name">Giá trị ước tính:</p>
              <p class="content">{{format(asset.total_estimate_amount)}} đ</p>
            </div>
            <div v-for="(landTypePrice, index) in asset.land_type_purpose_price" :key="'landTypePrice' + index">
              <div class="property__detail d-flex justify-content-between" v-for="(land_type, index) in landTypePurposes" :key="'land_type' + index"
                  v-if="(landTypePrice.id === land_type.id)">
                <p class="name">Loại đất:</p>
                <p class="content">{{land_type.description}}</p>
              </div>
              <div class="property__detail d-flex justify-content-between">
                <p class="name">Đơn giá:</p>
                <p class="content">{{format(landTypePrice.price_land)}} đ</p>
              </div>
            </div>
            <div class="property__detail d-flex justify-content-between">
              <p class="mb-0 w-100 text-left">Địa chỉ: <span>{{asset.full_address}}</span></p>
            </div>
            <p class="popup-link" @click="handleDetail(asset, index)">Xem chi tiết</p>
          </div>
        </div>
        <div v-if="imageMap" class="container-imageMap mt-4 col-10">
          <h3 class="title text-center mt-0">Hình ảnh bản đồ</h3>
          <img class="w-100" :src="imageMap" alt="map">
        </div>
        <div class="col-12 d-flex justify-content-center">
          <button class="btn btn-orange" style="width: auto" type="button" @click="handleMap">Sơ đồ vị trí tài sản so sánh.</button>
        </div>
    </div>
  </div>
    <ModalMapProperty
      ref="selection"
      v-if="openModalMap"
      @cancel="openModalMap = false"
      :radius="radius"
      :frontSide="frontSide"
      :isCheckFrontside="formData.is_check_frontside"
      :location="location"
      :landType="landTypePurposes"
      :propertyTypes="propertyTypes"
      :data="data"
      @actionUpdate="updateAssets"
      @actionUpdateCheckFrontSide="updateCheckFrontSides"
      @getRadius="getRadius"
    />
    <ModalInfoDetail
      v-if="open_detail"
      @cancel="open_detail = false"
      :property="property"
      :propertyIndex="propertyIndex"
      :pic="pic"
      @save="saveData"
    />
    <ModalMapAssets
      v-if="openModalMapAsset"
      :assets="data"
      :location="location"
      @cancel="openModalMapAsset = false"
      @saveImageMap="saveMap"
    />
  </div>
</template>

<script>

import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import ModalMapProperty from './modals/ModalMapProperty'
import ModalInfoDetail from './modals/ModalInfoDetail'
import WareHouse from '@/models/WareHouse'
import ModalMapAssets from '@/pages/certificate/components/modals/ModalMapAssets'
export default {
	name: 'AppraisalPerson',
	props: ['data', 'landTypePurposes', 'radius', 'location', 'frontSide', 'appraisers', 'formData', 'propertyTypes', 'imageMap'],
	computed: {
		optionsAppraiser () {
			return {
				data: this.appraisers,
				id: 'id',
				key: 'name'
			}
		}
	},
	components: {
		InputCategory,
		InputText,
		ModalMapProperty,
		ModalInfoDetail,
		ModalMapAssets
	},
	data () {
		return {
			openModalMapAsset: false,
			open_detail: false,
			showCard: true,
			name: '',
			openModalMap: false,
			directions: [],
			form: {
				appraiser: '',
				manager: '',
				staff: ''
			},
			pic: [],
			property: null,
			propertyIndex: null,
			index: null,
			id_choose: '',
			asset_id: null
		}
	},
	methods: {
		showVersionAsset (asset) {
			let versionTitle = ''
			this.formData.asset_general.forEach(item => {
				if (item.asset_general_id === asset.id) {
					versionTitle = item.version
				}
			})
			return versionTitle
		},
		saveMap (image) {
			this.$emit('saveImageMap', image)
		},
		handleMap () {
			if (this.data.length > 0) {
				this.openModalMapAsset = true
			} else {
				this.$toast.open({
					message: 'Hiện tại không chưa có danh sách tài sản so sánh',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		getRadius (data) {
			this.$emit('getRadius', data)
		},
		async handleDetail (property, index) {
			await this.getAssetGeneralDetail(property.id)
			this.pic = property.pic
			this.formData.asset_general.forEach(item => {
				if (item.asset_general_id === property.id) {
					this.propertyIndex = item.asset_property_detail_id
				}
			})
			// this.propertyIndex = this.formData.asset_general[index].asset_property_detail_id
			this.index = index
			this.asset_id = property.id
			this.open_detail = true
		},
		saveData (id, property_data) {
			this.id_choose = id
			this.$emit('savePropertyId', id, this.index, this.asset_id, property_data)
		},
		async getAssetGeneralDetail (id) {
			this.isSubmit = true
			const resp = await WareHouse.getAssetGeneralDetail(id)
			this.property = resp.data
			this.isSubmit = false
		},
		updateAssets (data) {
			this.$emit('updateAsset', data)
		},
		updateCheckFrontSides (data) {
			this.$emit('updateCheckFrontSide', data)
		},
		handelEditProperty () {
			this.openModalMap = true
		},
		getAsset () {
			this.$emit('action')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		}
	}
}
</script>

<style scoped lang="scss">
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 768px) {
      margin-bottom: 20px;
    }
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      @media (max-width: 768px) {
        padding: 12px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 787px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
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
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .form-group-container {
    margin-top: 15px;
  }
  .color-black{
    color: #333333;
  }
  .btn-delete{
    cursor: pointer;
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border: 0.777778px solid #000000;
    border-radius: 5.88235px;
    padding: 10px;
    margin: auto;
    width: 36px;
    height: 36px;
    img{
      width: 100%;
      height: auto;
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
      &:hover{
        border-color: #dc8300;
      }
    }
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  .title__property {
    text-align: center;
    font-size: 25px;
    font-weight: 600;
    color: #000000;
    margin-bottom: 30px;
  }
  .container {
    &__property {
      height: 100%;
      border: 1px solid #D0D0D0;
      padding: 15px;
      border-radius: 5px;
      @media (max-width: 1023px) {
        margin-bottom: 20px;
      }
      .property {
        &__detail{

          color: #000000;
          margin-bottom: 5px;
          .name, .content{
            margin-bottom: 0;
            padding: 0 !important;
          }
          .name {
            text-align: left;
            width: 50%;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
          }
          .content {
            color: #333333;
            display: block;
            text-align: end;
            &__id{
              color: #FAA831;
            }
          }
        }
      }
    }
  }
  .property {
    &__title {
      color: #333333;

      text-decoration: underline;
      margin-bottom: 10px !important;
      text-align: left;
    }
  }
  .container {
    &__img{
      width: calc(100% + 30px);
      height: 135px;
      margin: -15px -15px 5px;
      img{
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      &--empty{
        width: auto;
        background: #eeeeee;
        img{
          object-fit: contain;
        }
      }
    }
  }
  .color {
    &__blue{
      color: #37C3F4 !important;
    }
    &__purple {
      color: #8659FA !important;
    }

    &__orange {
      color: #FAA831 !important;
    }

    &__green {
      color: #1F8B24 !important;
    }
  }
  .asset-null {
    color: #333333;
    font-size: 24px;
    line-height: 12;
  }
  .popup{
    &-link {
      text-align: right;
      color: #F28C1C !important;
      font-weight: 600;
      text-decoration-line: underline;
      cursor: pointer;
      margin-bottom: 0 !important;
    }
  }
  .container-imageMap {
    border: 1px solid;
    padding: 10px;
  }
</style>
