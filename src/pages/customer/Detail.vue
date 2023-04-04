<template>
  <div class="container-fluid">
    <div class="contain-detail">
      <div class="loading" :class="{'loading__true': isSubmit}">
        <a-spin />
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Thông tin đối tác</h3>
          </div>
        </div>
        <div class="card-body card-info">
          <div class="container-filter">
            <div class="row align-items-center">
              <div class="col-12 col-lg-3">
                <div class="card-body__avatar">
                  <div class="img-avatar mb-3">
                    <img v-if="form.customer_picture === '' || form.customer_picture === undefined || form.customer_picture === null" class="w-100 h-100" src="../../assets/icons/ic_user.svg" alt="avatar">
                    <img v-if="form.customer_picture !== '' && form.customer_picture !== undefined && form.customer_picture !== null" class="w-100 h-100 img__avatar" :src="form.customer_picture" alt="img">
                  </div>
                </div>
              </div>
              <div class="col-12 col-lg-9">
                <div class="container-img">
                  <div class="row mr-0 ml-0">
                    <div class="img-empty m-auto text-center" v-if="form.pic.length === 0">
                      <img src="../../assets/images/img_emply.svg" alt="empty">
                      <p class="empty-content">Chưa có hình</p>
                    </div>
                    <div class="contain-img col-4 col-lg-2 contain-img__property" v-for="(images) in form.pic" :key="images.id">
                      <img class="img" :src="images.link" alt="img" @click="showImages">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail content-code">
              <p class="content-title">Mã đối tác:</p>
              <p class="content-name content-name__code">{{'DT_' + form.id }}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail" v-if="form.name != null">
              <p class="content-title">Tên đối tác:</p>
              <p class="content-name" v-if="form.name != null">{{form.name}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Số điện thoại:</p>
              <p class="content-name" v-if="form.phone !== null">{{form.phone}}</p>
            </div>
            <div class="content-detail" v-if="form.tax_code !== null">
              <p class="content-title">Mã số thuế:</p>
              <p class="content-name">{{form.tax_code}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Ngày tạo:</p>
              <p class="content-name"> {{form.created_date}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Người tạo:</p>
              <p class="content-name">{{form.created_by}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Trạng thái:</p>
              <p class="content-name">{{form.status === 'inactive' ? 'Đang vô hiệu hóa' : form.status === 'active' ? 'Đang hoạt động' : ''}}</p>
            </div>
          </div>
          <div class="content-detail">
            <p class="content-title">Địa chỉ đầy đủ:</p>
            <p class="content-name">{{form.address !== '' && form.address !== undefined && form.address !== null ? form.address : 'Không xác định'}}</p>
          </div>
        </div>
      </div>
    </div>
    <ModalImage
      v-if="openImage"
      v-bind:image_detail ="this.image_detail.link"
      @cancel="openImage = false"
    />
    <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
      <div class="d-md-flex d-block button-contain ">
        <button class="btn btn-white" @click="onCancel" type="button">
          <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">
          Trở về
        </button>
        <div v-if="edit" class="mr-15">
          <button class="btn btn-white" @click.prevent="handleEdit(form.id)">
            <img class="img" src="../../assets/icons/ic_edit.svg" alt="edit">
            Chỉnh sửa
          </button>
        </div>
      </div>
    </div>
    <ModalViewImageCustomer
      v-if="showModalImage"
      @cancel="showModalImage = false"
      v-bind:pics="form.pic"
    />
  </div>
</template>
<script>
import InputText from '@/components/Form/InputText'
import Customer from '@/models/Customer'
import ModalPropertyDetail from '@/components/Modal/ModalPropertyDetail'
import ModalImage from '@/components/Modal/ModalImage'
import ModalPrint from '@/components/Modal/ModalPrint'
import ModalTangibleDetail from '@/components/Modal/ModalTangibleDetail'
import {BDropdown, BDropdownItem} from 'bootstrap-vue'
import ModalViewImageCustomer from '../../components/Modal/ModalViewImageCustomer'
import moment from 'moment'

export default {
	name: 'Detail',
	components: {
		ModalViewImageCustomer,
		ModalPrint,
		InputText,
		ModalPropertyDetail,
		ModalTangibleDetail,
		ModalImage,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem
	},

	data () {
		return {
			output: null,
			openImage: false,
			openDetail: false,
			openTangible: false,
			showInfo: true,
			showTable: true,
			showLand: true,
			showOther: true,
			showDeal: true,
			showBlock: true,
			showApartment: true,
			openPrint: false,
			isSubmit: false,
			image: '',
			printDetail: '',
			purpose_use_lands: [],
			basic_utilities: [],
			checkedId: [],
			edit: false,
			form: {},
			showModalImage: false
		}
	},
	created () {
		if ('id' in this.$route.query && this.$route.name === 'customer.detail') {
			if (this.$route.meta['detail']) {
				this.form = Object.assign(this.form, {
					...this.$route.meta['detail']
				})
			} else {
				this.$router.push({name: 'page-not-found'})
			}
		} else {
		}
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'EDIT_CUSTOMER') {
				this.edit = true
			}
		})
	},
	computed: {
		sortedArray: function () {
			function compare (a, b) {
				if (a.price_land > b.price_land) { return -1 }
				if (a.price_land < b.price_land) { return 1 }
				return 0
			}
			// eslint-disable-next-line vue/no-side-effects-in-computed-properties
			return this.purpose_use_lands.sort(compare)
		}
	},
	methods: {
		showImages () {
			this.showModalImage = true
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		openModalImage (data) {
			this.openImage = true
			this.image_detail = data
		},
		async openModalPrint (data) {
			this.isSubmit = true
			await this.getPrint(data)
			this.openPrint = true
			this.isSubmit = false
		},
		openModalDetail (data) {
			this.openDetail = true
			this.property = data
			if (data.front_side === 1) {
				this.frontSideOptions.items.preSelected = 'Yes'
				data.front_side_switch = true
			} else if (data.front_side === 0) {
				data.front_side_switch = false
				this.frontSideOptions.items.preSelected = 'No'
			} else {
				this.frontSideOptions.items.preSelected = ''
			}
			if (data.two_sides_land === true) {
				this.twoSidesLandOptions.items.preSelected = 'Yes'
			} else if (data.two_sides_land === false) {
				this.twoSidesLandOptions.items.preSelected = 'No'
			} else {
				this.twoSidesLandOptions.items.preSelected = ''
			}
			if (data.individual_road === 1) {
				this.individualRoadOptions.items.preSelected = 'Yes'
			} else if (data.individual_road === 0) {
				this.individualRoadOptions.items.preSelected = 'No'
			} else {
				this.individualRoadOptions.items.preSelected = ''
			}
			// if (data.individual_road === 1) {
			//   data.individual_road_switch = true
			// } else {
			//   data.individual_road_switch = false
			// }
			// if (data.front_side === 1) {
			//   data.front_side_switch = true
			// } else {
			//   data.front_side_switch = false
			// }
		},
		openTangibleDetail (data) {
			this.openTangible = true
			this.tangible = data
		},
		onCancel () {
			return this.$router.push({name: 'customer.index'})
		},
		async handleEdit (id) {
			this.$router.push({
				name: 'customer.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		}
	},
	mounted () {
		this.image = process.env.API_URL
	},
	beforeRouteEnter: async (to, from, next) => {
		const customer = await Customer.find(to.query['id'])
		to.meta['detail'] = customer.data
		return next()
	},
	beforeMount () {
	}
}
</script>
<style scoped lang="scss">
  .contain-detail{
    margin-bottom: 80px;
    @media (max-width: 767px) {
      margin-bottom: 145px;
    }
  }
  .pannel {
    background: #FFFFFF;
    box-shadow: 1px 2px 0 #e5eaee;
    border-radius: 5px;
    padding: 25px;
    margin-bottom: 40px;

    &__table {
      padding: 25px 0;
      border-radius: 5px;
    }

    &__input {
      p {
        color: #5a5386;
        font-weight: 600;
      }
    }
  }
  .button-contain {
    @media (max-width: 418px) {
      display: flex !important;
      justify-content: space-between;
      flex-wrap: wrap;
    }
  }
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      @media (max-width: 418px) {
        padding: 15px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 418px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
        &-highlight {
          color: #333333;
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
  .btn{
    &-white{
      max-height: none;

      line-height: 19.07px;
      margin-right: 15px;
      width: 170px;
      @media (max-width: 418px) {
        width: 45%;
        margin-right: 0;
      }
      &:last-child{
        margin-right: 0;
      }
    }
    &-contain{
      margin-bottom: 55px;
      @media (max-width: 418px) {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
      }
    }
  }
  .d-grid{
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-column-gap: 8.9%;
    @media (max-width: 767px) {
      grid-template-columns: 1fr;
    }
  }
  .content{
    &-detail{
      p {
        padding-right: 10px;
        &:nth-last-child(1) {
          padding-right: 0;
        }
      }
    }
    &-title{
      color: #555555;
      margin-bottom: 5px;

      font-weight: 500;
    }
    &-name{
      font-size: 1.125rem;
      color: #000000;
      margin-bottom: 15px;
      font-weight: 600;
      @media (max-width: 767px) {

      }
      &__code{
        color: #FAA831;
      }
    }
  }
  .title{
    margin-bottom: 35px;
  }
  .contain-table{
    overflow-x: auto;
    @media (max-width: 767px) {
      overflow-y: hidden;
    }
    .table-property{
      width: 100%;
    }
  }
  .table-property{
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 500;
        @media (max-width: 418px) {
          padding: 12px;
        }
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          border-left: none;
          width: 180px
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 14px;
      }
    }
  }
  .img-content{
    padding-left: 20px;
    color: rgba(0,0,0,0.85);
    font-size: 1.125rem;
    font-weight: 600;
    span{
      font-weight: 500;
      margin-left: 10px;
    }
  }
  .input-code{
    color: #FAA831;
    cursor: pointer;
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  .img-contain {
    aspect-ratio: 1/1;
    overflow: hidden;
    img{
      cursor: pointer;
      object-fit: cover;
      height: 100%;
    }
    &__table{
      margin: auto;
      max-width: 50px;
      max-height: 50px;
      img{
        object-fit: cover;
        object-position: top;
        cursor: pointer;
        display: flex;
        justify-content: center;
        max-width: 50px;
        max-height: 50px;
      }
    }
  }
  .product-images {
    @media (max-width: 786px) {
      display: block !important;
      .img-contain {
        margin-bottom: 5px;
        max-width: 100%;
        max-height: 100%;
        img {
          width: 100%;
          max-width: 100%;
          max-height: 100%;
        }
      }
    }
  }
  .container-img{
    padding: .75rem 0;
    border: 1px solid #0b0d10;
  }
  .btn-footer {
    background: #FFFFFF;
    padding: 20px 30px;
    position: fixed;
    left: 0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    bottom: 0;
    right: 0;
  }
  .dropdown-container{
    border-radius: 2px;
    background: #FAA831;
    img{
      padding: 7px;
    }
  }
  .loading{
    display: none;
    &__true{
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 100vh;
      background: rgba(0, 0, 0, 0.62);
      z-index: 100000;
      display: flex;
      align-items: center;
      justify-content: center;
      &.btn-loading{
        &:after{
          width: 2rem !important;
          height: 2rem !important;
        }
      }
    }
  }
  .input-checkbox {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    input {
      width: 20px;
      height: 20px;
      position: absolute;
      cursor: pointer;
      opacity: 0;
      &:checked {
        & ~ .check-mark {
          background-color: #FAA831;
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
      width: 20px;
      height: 20px;
      background-color: #FFFFFF;
      border: 1px solid #FAA831;
      border-radius: 4px;
      &:after {
        content: "";
        position: absolute;
        display: none;
        left: 50%;
        top: 50%;
        width: 5px;
        height: 10px;
        border: solid #FFFFFF;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg) translate(-125%, -25%);
        -ms-transform: rotate(45deg) translate(-125%, -25%);
        transform: rotate(45deg) translate(-125%, -25%);
      }
    }
  }
  .mr-15{
    margin-right: 15px;
    @media (max-width: 767px) {
      margin-right: 0;
    }
  }
  .card-body {
    &__avatar {
      display: flex;
      flex-direction: column;
      align-items: center;
      .img-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        img {
          object-fit: cover;
          border-radius: 50%;
        }
      }
    }
  }
  .container{
    &__input{
      position: relative;
      .input{
        &__image{
          position: absolute;
          width: 100%;
          top: 0;
          bottom: 0;
          left: 0;
          right: 0;
          opacity: 0;
          cursor: pointer;
        }
      }
    }
    &__image{
      padding: 10px;
      border: 1px solid #000000;
      border-radius: 10px;
      box-sizing: border-box;
      height: 100%;
    }
  }
  .contain-img{
    aspect-ratio: 1/1;
    overflow: hidden;
    height: auto;
    position: relative;
    text-align: center;
    margin-bottom: 10px;
    .img {
      object-fit: cover;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }
  }
</style>
