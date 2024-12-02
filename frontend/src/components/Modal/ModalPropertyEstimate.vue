<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <div class="card" :class="assets.length === 1 ? 'card--one': assets.length === 2 ? 'card--two' : ''">
      <div class="card-header d-flex justify-content-end align-items-center">
        <img @click="handleCancel"
             src="../../assets/icons/ic_cancel-1.svg"
             alt="icon">
      </div>
      <div class="title__property text-center">TÀI SẢN THAM CHIẾU</div>
      <div class="card-body">
        <div class="asset-null" v-if="assets.length === 0">Không tìm thấy tài sản tham chiếu</div>
        <div class="row" v-if="assets.length > 0">
          <div class="col-12 mt-2" :class="assets.length === 1 ? '': assets.length === 2 ? 'col-lg-6' : 'col-lg-4' " v-for="(asset, index) in assets" :key="index">
            <div class="container__property">
<!--              <div class="container__img container__img&#45;&#45;empty" v-if="asset.pic.length === 0"><img src="../../assets/images/img_emply.svg" alt="img_empty"></div>-->
<!--              <div class="container__img" v-if="asset.pic.length > 0"><img :src="asset.pic[0].link" alt="image"></div>-->
              <div class="property__detail d-flex justify-content-between">
                <p class="name">Tài sản:</p>
                <p class="content content__id">{{asset.migrate_status}}_{{asset.id}}</p>
              </div>
              <div class="property__detail d-flex justify-content-between">
                <p class="name">Loại giao dịch:</p>
                <p class="content" :class="asset.transaction_type_id === 51? 'color__blue': asset.transaction_type_id === 53 ? 'color__orange' : asset.transaction_type_id === 52 ? 'color__purple': 'color__green'">{{asset.transaction_type_description}}</p>
              </div>
              <div class="property__detail d-flex justify-content-between" v-if="asset.asset_type === 'CHUNG CƯ'">
                <p class="name">Số phòng ngủ:</p>
                <p class="content">{{asset.bedroom_num}}</p>
              </div>
              <div class="property__detail d-flex justify-content-between" v-if="asset.asset_type !== 'CHUNG CƯ'">
                <p class="name">Vị trí:</p>
                <p class="content">{{asset.front_side === 0 ? 'Hẻm' : asset.front_side === 1? 'Mặt tiền' : '' }}</p>
              </div>
              <div class="property__detail d-flex justify-content-between" v-if="asset.asset_type !== 'CHUNG CƯ' && asset.front_side === 0">
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
              <div v-if="asset.asset_type !== 'CHUNG CƯ'" v-for="(landTypePrice, index) in asset.land_type_purpose_price" :key="'landTypePrice' + index">
                <div class="property__detail d-flex justify-content-between" v-for="(land_type, index) in landTypePurpose" :key="'land_type' + index" v-if="landTypePrice.id === land_type.id">
                  <p class="name">Loại đất:</p>
                  <p class="content">{{land_type.description}}</p>
                </div>
                <div class="property__detail d-flex justify-content-between">
                  <p class="name">Đơn giá:</p>
                  <p class="content">{{format(landTypePrice.price_land)}} đ</p>
                </div>
              </div>
              <div class="property__detail d-flex justify-content-between" v-if="asset.asset_type === 'CHUNG CƯ'">
                <p class="name">Đơn giá giá bình quân:</p>
                <p class="content">{{asset.average_land_unit_price ? format(asset.average_land_unit_price) : 0}} đ</p>
              </div>
              <div class="property__detail d-flex justify-content-between">
                <p class="name">Cách vị trí định giá:</p>
                <p class="content">{{asset.sort !== undefined && asset.sort !== null ? format(asset.sort) + ' m' : 'Không xác định'}}</p>
              </div>
              <div class="property__detail d-flex justify-content-between" v-if="asset.asset_type !== 'CHUNG CƯ'">
                <p class="mb-0 w-100 text-left">Địa chỉ: <span>{{asset.full_address}}</span></p>
              </div>
              <p class="popup-link" @click="handleDetail(asset)">Xem chi tiết</p>
            </div>
          </div>
          <div class="col-12">
            <p class="popup-link mt-3 text-right" @click="handleMap()">Mở bản đồ</p>
          </div>
        </div>
      </div>
    </div>
    <ModalMapDetail
      v-if="open_detail"
      @cancel="open_detail = false"
      :property="this.property"
      :pic="this.pic"
    />
    <ModalMapReferenceProperty
      v-if="openModalMap"
      :location="location"
      :assets="assets"
      @cancel="openModalMap = false"
    />
  </div>
</template>

<script>
import ModalMapDetail from './ModalMapDetail'
import WareHouse from '../../models/WareHouse'
import ModalMapReferenceProperty from '@/components/Modal/ModalMapReferenceProperty'

export default {
	name: 'ModalPropertyEstimate',
	props: ['assets', 'landTypePurpose', 'location'],
	data () {
		return {
			openModalMap: false,
			land_types: [],
			asset_details: [],
			open_detail: false,
			pic: [],
			property: ''
		}
	},
	components: {
		ModalMapReferenceProperty,
		ModalMapDetail
	},
	mounted () {
	},
	methods: {
		handleMap () {
			this.openModalMap = true
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
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},

		handleAction (event) {
			this.$emit('action', event)
			this.$emit('cancel', event)
		}
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
  background: rgba(0,0,0,.6);

  @media (max-width: 787px) {
    padding: 20px;
  }
  .card {
    max-width: 1062px;
    width: 100%;
    margin-bottom: 0;
    &--one{
      max-width: 354px;
    }
    &--two{
      max-width: 708px;
    }
    &-header {
      border-bottom: none;
      h3 {
        color: #333333;
      }
      img {
        cursor: pointer;
      }
    }
    &-body {
      text-align: center;
      padding: 8px 20px 20px;
      max-height: 80vh;
      overflow-y: auto;
      p {
        color: #333333;
        margin-bottom: 40px;
      }
    }
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
        font-size: 14px;
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
    font-size: 14px;
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
</style>
