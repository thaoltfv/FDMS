<template>
  <div class="container-fluid">
    <div class="contain-detail">
      <div class="loading" :class="{'loading__true': isSubmit}">
        <a-spin />
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Thông tin Đơn giá</h3>
            <img class="img-dropdown" src="../../../assets/images/icon-btn-down.svg" alt="dropdown">
          </div>
        </div>
        <div class="card-body card-info">
          <div class="d-grid">
            <div class="content-detail content-code">
              <p class="content-title">Mã Đơn giá:</p>
              <p class="content-name content-name__code">{{form.id }}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Loại đất:</p>
              <p class="content-name">{{this.form.land_type}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Tỉnh/Thành:</p>
              <p class="content-name">{{this.form.provinces !== undefined && this.form.provinces !== null ? this.form.provinces.name : 'Chưa có Tỉnh/Thành'}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Quận/Huyện:</p>
              <p class="content-name">{{this.form.districts !== undefined && this.form.districts != null ? this.form.districts.name : 'Chưa có Quận/Huyện'}}</p>
            </div>
            <div class="content-detail">
              <p class="content-title">Phường/Xã:</p>
              <p class="content-name">{{this.form.wards !== undefined && this.form.wards !== null ? this.form.wards.name : 'Chưa có Phường/Xã'}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail">
              <p class="content-title">Đường:</p>
              <p class="content-name">{{this.form.streets !== undefined && this.form.streets !== null ? this.form.streets.name: 'Chưa có đường'}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title"> Đoạn:</p>
              <p class="content-name">{{this.form.distances !== null && this.form.distances !== undefined ? this.form.distances.name : 'Chưa có đoạn'}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Vị trí 1:</p>
              <p class="content-name">{{this.form.vt1 ? formatNumber(this.form.vt1) + 'đ' : '0'}}</p>
            </div>
          </div>
          <div class="d-grid">
            <div class="content-detail" >
              <p class="content-title">Vị trí 2:</p>
              <p class="content-name">{{this.form.vt2 ? formatNumber(this.form.vt2) + 'đ' : '0'}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Vị trí 3:</p>
              <p class="content-name">{{this.form.vt3 ? formatNumber(this.form.vt3) + 'đ' : '0'}}</p>
            </div>
            <div class="content-detail" >
              <p class="content-title">Vị trí 4:</p>
              <p class="content-name">{{this.form.vt4 ? formatNumber(this.form.vt4) + 'đ' : '0'}}</p>
            </div>
          </div>
          <div class="content-detail">
            <p class="content-title">Địa chỉ đầy đủ:</p>
            <p class="content-name">{{form.distance? form.distance + ', ' : ''}} {{form.street? form.street + ', ' : ''}} {{form.ward ? form.ward + ', ' : ''}} {{form.district ? form.district + ', ' : ''}} {{form.province? form.province + ', ' : ''}}</p>
          </div>
        </div>
      </div>
    </div>
    <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
      <div class="d-md-flex d-block button-contain ">
        <router-link :to="{name: 'unit_price.index'}" class="btn btn-white" type="button">
          <img class="img" src="../../../assets/icons/ic_cancel.svg" alt="cancel">
          Trở về
        </router-link>
      </div>
    </div>
    </div>
</template>
<script>
import UnitPriceGet from '@/models/UnitPriceGet'

export default {
	name: 'Detail',
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
			form: {
				id: '',
				id_amount: '',
				status: '1',
				input_source: 'DONAVA',
				post_id: '',
				asset_type: {
					type: ''
				},
				created_by: '',
				province: '',
				district: '',
				ward: '',
				street: '',
				doc_num: '',
				ward_id: '',
				street_id: '',
				transaction_type: '',
				land_no: '',
				doc_no: '',
				full_address: '',
				coordinates: '',
				source: '',
				public_date: '',
				property_other: '',
				contact_phone: '',
				total_area: '1',
				total_construction_area: '1',
				total_construction_amount: '',
				total_amount: '1',
				total_area_amount: '1',
				average_land_unit_price: '1',
				contact_person: '',
				properties: [],
				tangible_assets: [],
				other_assets: [],
				pic: [
					{
						link: ''
					}
				]
			},
			edit: false
		}
	},
	created () {
		if ('id' in this.$route.query && this.$route.name === 'unit_price.detail') {
			if (this.$route.meta['detail']) {
				this.form = Object.assign(this.form, {
					...this.$route.meta['detail']
				})
			}
		} else {
		}
	},
	methods: {
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		const unit_price = await UnitPriceGet.find(to.query['id'])
		to.meta['detail'] = unit_price.data
		return next()
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
.img{
  margin-right: 13px;
  max-width: 20px;
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
</style>
