<template>
  <div class="position-relative" :class="hiddenList ? 'property-hidden' : ''" >
    <!-- <button type="button" class="btn btn__hide" @click="handleHidden">{{hiddenList? 'Hiện' : 'Ẩn'}}</button> -->
    <div class="container-property container-property--hidden" :class="listTSSS.length === 0 ? 'container-property__background' : ''">
      <div class="property" :class="hiddenList ? 'property--hidden' : ''">
        <div class="property">
          <form class="position-relative" @submit.prevent="search">
            <InputText
              v-model="search_code"
              class="input-flash"
              placeholder="Tìm kiếm nhanh"
              vid="search"
              label="Tìm kiếm"
               />
            <button class="btn-img btn-img__search">
              <img class="img" src="@/assets/icons/ic_search.svg" alt="search">
            </button>
          </form>
        </div>
        <div class="property-empty" v-if="listTSSS.length === 0">
          Chưa có TSSS nào
        </div>
        <div class="property">
          <div class="property-contain" v-if="listTSSS.length > 0">
            <div class="property-info" v-for="(asset_general, index) in listTSSS" :key="index" :id="asset_general.id" :class="marker_id === asset_general.id ? 'property-info__active' : ''" @click="handleDetail(asset_general)" @mousemove="handleMarkerDetail(asset_general)">
              <div class="property-info--content">
                <p class="info-content"> Mã: <span class="info-content color__green">{{`TSS_${asset_general.id}`}}</span></p>
                <p class="info-content"> Loại giao dịch:
                  <span class="info-content__detail color__blue" v-if="asset_general.transaction_type_id === 51">{{asset_general.transaction_type_description}}</span>
                  <span class="info-content__detail color__purple" v-if="asset_general.transaction_type_id === 52">{{asset_general.transaction_type_description}}</span>
                  <span class="info-content__detail color__orange" v-if="asset_general.transaction_type_id === 53">{{asset_general.transaction_type_description}}</span>
                  <span class="info-content__detail color__green" v-if="asset_general.transaction_type_id === 54">{{asset_general.transaction_type_description}}</span>
                  <span class="info-content__detail color__green" v-if="asset_general.transaction_type_id === 0">{{asset_general.transaction_type_description}}</span>
                </p>
                <p class="info-content" :id="`address_`+asset_general.id"> Địa chỉ: <span class="info-content__detail">{{asset_general.full_address}}</span></p>
                <b-tooltip :target="('address_' + asset_general.id).toString()">{{ asset_general.full_address}}</b-tooltip>
                <p class="info-content">{{asset_general.migrate_status === 'TSS' ? 'Giá trị ước tính:' : 'Giá trị thẩm định' }} <span class="info-content color__orange">{{formatPrice(asset_general.total_amount)}}</span> </p>
                <p class="info-content">{{asset_general.migrate_status === 'TSS' ? 'Ngày đăng tin:' : 'Ngày thẩm định' }} <span class="info-content__detail">{{formatDate(asset_general.public_date)}}</span> </p>                
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import moment from 'moment'
import { BTooltip } from 'bootstrap-vue'
import InputText from '@/components/Form/InputText'

export default {
	name: 'PropertiesList',
	props: ['max_listTSSS','listTSSS','hiddenFromMapTSSS','marker_id'],
	components: {
    'b-tooltip': BTooltip,
    InputText
	},
	data () {
		return {
      hiddenList: this.hiddenFromMapTSSS,
      search_code: '',
      // filer_list: this.max_listTSSS
		}
	},
	async mounted () {
    // if (this.max_listTSSS){
    //   await this.search()
    // }
    // console.log('this.max_listTSSS',this.max_listTSSS)
	},
	methods: {
    async search () {
      console.log('vô search',this.search_code)
      if (this.search_code !== ''){
        let filteredList = [];
        for (let i= 0; i<this.max_listTSSS.length; i++) {
          const e = this.max_listTSSS[i]
          if (e['id'].toString().includes(this.search_code) === true
          || e['full_address'].toString().includes(this.search_code) === true) {
            filteredList = [...filteredList, e];
          }
        }
        this.listTSSS = filteredList
        this.$emit('changeList', this.listTSSS)
      } else {
        this.listTSSS = this.max_listTSSS
        this.$emit('changeList', this.listTSSS)
      }
      
    },
    handleHidden () {
			this.hiddenList = !this.hiddenList
			this.$emit('hiddenListTSSS', this.hiddenList)
		},
		formatDate (value) {
			return moment(String(value)).format('DD-MM-YYYY')
		},
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
    formatPrice (value) {
			let num = parseFloat(value / 1).toFixed(0).replace('.', ',')
			if (num.length > 3 && num.length <= 6) {
				return parseFloat(num / 1000).toFixed(0) + ' ng'
			} else if (num.length > 6 && num.length <= 9) {
				return parseFloat(num / 1000000).toFixed(0) + ' tr'
			} else if (num.length > 9) {
				return parseFloat(num / 1000000000).toFixed(1) + ' tỷ'
			} else if (num < 900) {
				return num + ' đ' // if value < 1000, nothing to do
			}
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
    handleDetail (property) {
			this.$emit('get_center', property)
			// this.property = property
			// this.handleShowImage(property.id)
			// this.open_detail = true
		},
		handleMarkerDetail (property) {
			this.$emit('show_marker', property)
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
    width: 20vw;
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
    height: calc(74vh - 92px);
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
      // width: calc(100% - 110px);
      width: 100%;
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
