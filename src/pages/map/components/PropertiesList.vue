<template>
  <div class="position-relative" :class="hiddenList ? 'property-hidden' : ''">
    <button type="button" class="btn btn__hide" @click="handleHidden">{{hiddenList? 'Hiển thị' : 'Ẩn'}}</button>
    <div class="container-property container-property--hidden" :class="location.length === 0 ? 'container-property__background' : ''">
      <div class="property" :class="hiddenList ? 'property--hidden' : ''">
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
                  <input class="cursor-pointer" v-model="transaction_type.sold" @change="handleSold" type="checkbox">
                  <span class="check-mark"></span>
                </label>
                <p class="property-content">Đã bán</p>
              </div>
            </div>
            <div class="col-4">
              <div class="d-flex align-items-center">
                <label class="input-checkbox">
                  <input class="cursor-pointer" type="checkbox" v-model="transaction_type.for_sale" @change="handleForSale">
                  <span class="check-mark"></span>
                </label>
                <p class="property-content">Rao bán</p>
              </div>
            </div>
            <!-- <div class="col-4">
              <div class="d-flex align-items-center">
                <label class="input-checkbox">
                  <input class="cursor-pointer" type="checkbox" v-model="transaction_type.rented_out" @change="handleRentedOut">
                  <span class="check-mark"></span>
                </label>
                <p class="property-content">Đã cho thuê</p>
              </div>
            </div> -->
          </div>
          <div class="row" style="margin-top: 20px">
            <!-- <div class="col-4">
              <div class="d-flex align-items-center">
                <label class="input-checkbox">
                  <input class="cursor-pointer" type="checkbox" v-model="transaction_type.for_sale" @change="handleForSale">
                  <span class="check-mark"></span>
                </label>
                <p class="property-content">Rao bán</p>
              </div>
            </div>
            <div class="col">
              <div class="d-flex align-items-center">
                <label class="input-checkbox">
                  <input class="cursor-pointer" type="checkbox" v-model="transaction_type.for_rent" @change="handleForRent">
                  <span class="check-mark"></span>
                </label>
                <p class="property-content">Rao cho thuê</p>
              </div>
            </div> -->
            <div class="col">
              <div class="d-flex align-items-center">
                <label class="input-checkbox">
                  <input class="cursor-pointer" type="checkbox" v-model="transaction_type.is_appraise" @change="handleForAppraise">
                  <span class="check-mark"></span>
                </label>
                <p class="property-content">Đã thẩm định</p>
              </div>
            </div>
          </div>
        </div>
        <div class="property-empty" v-if="location.length === 0">
          Chưa có BĐS nào
        </div>
        <div class="property">
          <div class="property-contain" v-if="location.length > 0">
            <div class="property-info" v-for="(asset_general, index) in location" :key="index" :id="asset_general.id" :class="marker_id === asset_general.id ? 'property-info__active' : ''" @click="handleDetail(asset_general)" @mousemove="handleMarkerDetail(asset_general)">
              <div class="property-info--img" v-if="asset_general.pic.length > 0">
                <img :src="asset_general.pic[0].link" alt="property">
              </div>
              <div class="property-info--img" v-if="asset_general.pic.length === 0">
                <img src="../../../assets/images/img_emply.svg" alt="">
              </div>
              <div class="property-info--content">
                <p class="info-content"> Loại giao dịch:
                  <span class="info-content__detail color__blue" v-if="asset_general.transaction_type_id === 51">{{asset_general.transaction_type}}</span>
                  <span class="info-content__detail color__purple" v-if="asset_general.transaction_type_id === 52">{{asset_general.transaction_type}}</span>
                  <!-- <span class="info-content__detail color__orange" v-if="asset_general.transaction_type_id === 53">{{asset_general.transaction_type}}</span>
                  <span class="info-content__detail color__green" v-if="asset_general.transaction_type_id === 54">{{asset_general.transaction_type}}</span> -->
                  <span class="info-content__detail color__green" v-if="asset_general.transaction_type_id === 0">{{asset_general.transaction_type}}</span>
                </p>
                <p class="info-content"> Địa chỉ: <span class="info-content__detail">{{asset_general.full_address}}</span></p>
                <p class="info-content"> Tổng diện tích: <span class="info-content__detail">{{formatNumber(asset_general.total_area)}} m <sup>2</sup></span></p>
                <p class="info-content">{{asset_general.migrate_status === 'TSS' ? 'Giá trị ước tính:' : 'Giá trị thẩm định' }} <span class="info-content__detail">{{formatNumber(asset_general.total_amount)}}đ</span> </p>
                <p class="info-content">{{asset_general.migrate_status === 'TSS' ? 'Ngày đăng tin:' : 'Ngày thẩm định' }} <span class="info-content__detail">{{formatDate(asset_general.public_date)}}</span> </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <ModalMapDetail
        v-if="open_detail"
        @cancel="open_detail = false"
        :property="this.property"
        :pics="this.picList"
      />
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import ModalMapDetail from '@/components/Modal/ModalMapDetail'
export default {
	name: 'PropertiesList',
	props: ['asset_generals', 'location', 'transaction_type', 'marker_id', 'hiddenFromMap'],
	components: {
		ModalMapDetail
	},
	data () {
		return {
			hiddenList: this.hiddenFromMap,
			active_marker: false,
			all_transaction: true,
			picList: {
				images: [],
				id: []
			},
			open_detail: false
		}
	},
	mounted () {
		this.handleAllTransaction()
	},
	methods: {
		handleHidden () {
			this.hiddenList = !this.hiddenList
			this.$emit('hiddenList', this.hiddenList)
		},
		formatDate (value) {
			return moment(String(value)).format('DD-MM-YYYY')
		},
		// format (value) {
		// 	let num = (value / 1).toFixed(0).replace('.', ',')
		// 	return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		// },
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		handleSold () {
			this.handleCheckAll()
			this.handleAction()
		},
		handleRentedOut () {
			this.handleCheckAll()
			this.handleAction()
		},
		handleForSale () {
			this.handleCheckAll()
			this.handleAction()
		},
		handleForRent () {
			this.handleCheckAll()
			this.handleAction()
		},
		handleForAppraise () {
			this.handleCheckAll()
			this.handleAction()
		},
		handleAllTransaction () {
			if (this.all_transaction === true) {
				this.transaction_type.sold = true
				this.transaction_type.rented_out = true
				this.transaction_type.for_sale = true
				this.transaction_type.for_rent = true
				this.transaction_type.is_appraise = true
			} else {
				this.transaction_type.sold = false
				this.transaction_type.rented_out = false
				this.transaction_type.for_sale = false
				this.transaction_type.for_rent = false
				this.transaction_type.is_appraise = false
			}
			this.handleAction()
		},
		handleCheckAll () {
			this.all_transaction = this.transaction_type.sold === true && this.transaction_type.rented_out === true && this.transaction_type.for_sale === true && this.transaction_type.for_rent === true && this.transaction_type.is_appraise === true
		},
		handleAction () {
			const transaction_type = this.transaction_type
			this.$emit('action', transaction_type)
		},
		handleShowImage (inputId) {
			let picList = []
			this.picList = {
				images: [],
				id: []
			}
			this.asset_generals.filter(item => {
				if (item.id === inputId) {
					let imageList = []
					if (item.pic) {
						item.pic.map((item) => {
							imageList.push(item)
						})
					}
					// eslint-disable-next-line no-unused-vars
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
					// eslint-disable-next-line no-unused-vars
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
					// eslint-disable-next-line no-unused-vars
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
				// eslint-disable-next-line no-return-assign
				picList.map((item) => {
					this.picList.images.push(item)
					this.picList.id.push(inputId)
				})
			}
		},
		handleDetail (property) {
			this.$emit('get_center', property.center, property.id)
			// this.property = property
			// this.handleShowImage(property.id)
			// this.open_detail = true
		},
		handleMarkerDetail (property) {
			this.$emit('show_marker', property.center, property.id)
		}

	}
}
</script>

<style lang="scss" scoped>
.container{
  &-property{
    position: relative;
    background: #FFFFFF;
    height: 100%;
    width: 460px;
    visibility: visible;
    transition: .5s;
    @media (max-width: 1024px) {
      display: none;
    }
    &__background{
      background: #F5F5F5;
    }
  }
}
.property-hidden {
  .container {
    &-property {
      &--hidden {
        /*overflow: hidden;*/
        width: 0;
        visibility: hidden;
        transition: .5s;
      }
    }
  }
}
.property{
  width: 100%;
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
  }
  &-contain{
    height: calc(85vh - 92px);
    overflow-y: auto;
    overflow-x: hidden;
  }
  &-info{
    //margin: 6px 0 0 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    padding: 5px 12px;
    border-bottom: 2px solid #F28C1C;
    transition: .3s;
    &--img{
      width: 130px;
      height: 130px;
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
      width: calc(100% - 110px);
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
    }
    &__active {
      background: rgba(0, 0, 0, 0.1);
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
    color: #26BF7F !important;
  }
  &__primary {
    color: #206bc4
  }
}
.btn__hide{
  width: 80px;
  background: #FAA831;
  color: #ffffff;
  padding: 5px 13px;
  position: absolute;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  left: -80px;
  @media (max-width: 1024px) {
    display: none;
  }
}
</style>
