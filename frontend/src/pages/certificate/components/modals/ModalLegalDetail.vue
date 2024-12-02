<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Pháp lý tài sản</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Loại pháp lý:</p>
                <p class="content-name">{{property.appraise_law_id ? property.law.content : property.description }}</p>
              </div>
              <div class="content-detail">
                <p class="content-title">Số/Ngày:</p>
                <p class="content-name">{{property.date}}</p>
              </div>
            </div>
            <div v-if="property.appraise_law_id" v-for="(land, index) in property.land_details" :key="index" class="d-grid">
              <div class="content-detail">
                <p class="content-title">Số tờ:</p>
                <p class="content-name">{{land.doc_no}}</p>
              </div>
              <div class="content-detail">
                <p class="content-title">Số thửa:</p>
                <p class="content-name">{{land.land_no}}</p>
              </div>
            </div>
            <div>
              <div class="content-detail">
                <p class="content-title">Nội dung:</p>
                <p class="content-name"><pre class="content_legal">{{property.content}}</pre></p>
              </div>
            </div>
            <div v-if="property.appraise_law_id" class="d-grid">
              <div class="content-detail">
                <p class="content-title">Người đứng tên pháp lý:</p>
                <p class="content-name">{{property.legal_name_holder}}</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Cơ quan các cấp xác nhận:</p>
                <p class="content-name"><pre class="content_legal">{{property.certifying_agency}}</pre></p>
              </div>
            </div>
            <div v-if="property.appraise_law_id" class="d-grid">
              <div class="content-detail">
                <p class="content-title">Nguồn gốc sử dụng:</p>
                <p class="content-name"><pre class="content_legal">{{property.origin_of_use}}</pre></p>
              </div>
            </div>
            <div v-if="property.appraise_law_id" class="card-table">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Diện tích theo Giấy CN QSDĐ</h3>
                  <img class="img-dropdown" :class="showScale? '' : 'img-dropdown__hide'" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showScale = !showScale">
                </div>
              </div>
              <div class="card-body card-info card-land" v-if="showScale">
                <div class="contain-table">
                  <table class="table-property">
                    <thead>
                    <tr>
                      <th>Mục đích sử dụng</th>
                      <th>Diện tích (m²)</th>
                      <th>Quy hoạch</th>
                      <th>Lâu dài</th>
                      <th>Thời hạn sử dụng</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(law_detail) in property.law_details" :key="law_detail.id">
                      <td>
                        <div v-if="law_detail.land_type_purpose">
                        {{law_detail.land_type_purpose.description}}
                        </div>
                      </td>
                      <td>
                        {{law_detail.total_area ? formatArea(law_detail.total_area) : 0}}m<sup>2</sup>
                      </td>
                      <td>
                        <InputSwitch
                          v-model="law_detail.is_zoning"
                          vid="is_transfer_facility"
                          label="Quy hoạch"
                          :disabled="true"
                          class="contain-input contain-input__info contain-input__property"/>
                      </td>
                      <td>
                        <InputSwitch
                          v-model="law_detail.expiry_type"
                          vid="is_transfer_facility"
                          label="Lâu dài"
                          :disabled="true"
                          class="contain-input contain-input__info contain-input__property"/>
                      </td>
                      <td>
                        {{ law_detail.expiry_date ? law_detail.expiry_date : '' }}
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
        </div>
          <div class="container-title container-title__footer">
            <div class="d-lg-flex d-block justify-content-end shadow-bottom">
              <button class="btn btn-white" type="button" @click="handleCancel"><img src="../../../../assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel">Trở lại</button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import FileUpload from '@/components/file/FileUpload'
import ToggleSwitch from '@/components/Form/ToggleSwitch'
import moment from 'moment'
export default {
	name: 'ModalLandDetail',
	props: ['property', 'img_link', 'frontSideOptions', 'twoSidesLandOptions', 'individualRoadOptions'],
	data () {
		return {
			isSubmit: false,
			showScale: true,
			showAlley: true,
			zoning: [],
			juridicals: [],
			landShapes: [],
			roughes: [],
			images: [],
			socialSecurities: [],
			businesses: [],
			paymentMethods: [],
			fengshuies: [],
			zones: [],
			materials: [],
			landType: [],
			image: null,
			link: '',
			type: '',
			form: {
				property_detail: [
					{
						compare_property_id: '',
						land_type_purpose: '',
						total_area: '',
						estimation_value: '',
						position_type: '',
						circular_unit_price: '',
						k_rate: ''
					}
				]
			}
		}
	},
	components: {
		FileUpload,
		InputCategory,
		InputText,
		InputSwitch,
		ToggleSwitch
	},
	computed: {
	},
	methods: {
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatArea (value) {
			let num = (value / 1).toString().replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleAction()
			}
		}
	},
	beforeMount () {
	}
}
</script>

<style lang="scss" scoped>
.title{
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 25px;
  color: #000000;
}
.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1300px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 35px 95px;
    @media (max-width: 787px) {
      padding: 20px 10px;
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
.card{
  .contain-detail{
    overflow-y: auto;
    overflow-x: hidden;
    margin-top: 20px;
    &::-webkit-scrollbar{
      width: 2px;
    }
  }
  &-title{
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;
    .title{
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-table{
    border-radius: 5px;
    background: #FFFFFF;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    width: 99%;
    margin: 50px auto 50px;
  }
  &-body{
    padding: 35px 30px 40px;
  }
  &-info{
    .title{
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;
    }
  }
  &-land{
    position: relative;
    padding: 0;
  }
}
.img{
  margin-right: 13px;
}
.card{
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);;
  background: #FFFFFF;
  margin-bottom: 75px;
  &-title{
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;
    .title{
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body{
    padding: 35px 30px 40px;
  }
  &-info{
    .title{
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;
    }
  }
  &-land{
    position: relative;
    padding: 0;
  }
}
.card__order{
  max-width: 50%;
  margin-bottom: 1.25rem;
  @media (max-width: 767px) {
    max-width: 100%;
  }
}
.btn{
  &-white{
    max-height: none;

    line-height: 19.07px;
    margin-right: 15px;
    &:last-child{
      margin-right: 0;
    }
  }
  &-contain{
    margin-bottom: 55px;
  }
}
.d-grid{
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-column-gap: 8.9%;
  &:first-child {
    margin-top: 0;
  }
  &__checkbox{
    grid-template-columns: 1fr 1fr;
  }
  @media (max-width: 767px) {
    grid-template-columns: 1fr;
  }
}
.content{
  &-detail{
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
.contain-table{
  @media (max-width: 767px) {
    overflow-y: hidden;
    overflow-x: auto;
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
      padding: 12px 5px;
      font-weight: 500;
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
  color: #000000;

  font-weight: 600;
  span{
    font-weight: 500;
    margin-left: 10px;
  }
}
.input-code{
  color: #000000;
  border-radius: 5px;
  width: 180px;
  border: 1px solid #000000;
  background: #f5f5f5;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
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
    height: 100%;
    cursor: pointer;
    object-fit: cover;
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
.container-title{
  margin: -35px -95px auto;
  padding: 35px 95px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  .title{
    font-size: 1.2rem;
    @media (max-width: 767px) {
      font-size: 1.125rem;
    }
  }
  &__footer{
    margin: auto -95px -35px;
    padding: 20px 95px 20px;
    @media (max-width: 767px) {
      .btn-white{
        margin-bottom: 20px;
      }
    }
  }
}
.container-img{
  padding: .75rem 0;
  border: 1px solid #0b0d10;
}
.traffic-light {
  color: black;
  padding: 0 5px;
  background: rgba(252,194,114,0.53);
  width: fit-content;
}
.input-switch__detail{
  margin-bottom: 25px;
}
.content_legal {
  padding: unset;
  font-family: unset;
}
</style>
