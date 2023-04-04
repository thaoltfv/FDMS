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
              <h2 class="title">Thông tin về công trình xây dựng</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="d-grid">
              <div class="content-detail" v-if="tangible.building_type !== null">
                <p class="content-title">Loại</p>
                <p class="content-name">{{tangible.building_type.description}}</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail" v-if="tangible.building_category !== undefined && tangible.building_category !== null">
                <p class="content-title">Cấp nhà</p>
                <p class="content-name">{{tangible.building_category.description}}</p>
              </div>
              <div class="content-detail" v-if="tangible.structure !== undefined && tangible.structure !== null">
                <p class="content-title">Cấu trúc</p>
                <p class="content-name">{{tangible.structure.description}}</p>
              </div>
              <div class="content-detail" v-if="tangible.rate !== undefined && tangible.rate !== null">
                <p class="content-title">Hạng nhà</p>
                <p class="content-name">{{tangible.rate.description}}</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail" v-if="tangible.crane !== undefined && tangible.crane !== null">
                <p class="content-title">Cẩu trục</p>
                <p class="content-name">{{tangible.crane.description}}</p>
              </div>
              <div class="content-detail" v-if="tangible.aperture !== undefined && tangible.aperture !== null">
                <p class="content-title">Khẩu độ</p>
                <p class="content-name">{{tangible.aperture.description}}</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail" v-if="tangible.factory_type !== undefined && tangible.factory_type !== null">
                <p class="content-title">Loại nhà máy</p>
                <p class="content-name">{{tangible.factory_type.description}}</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail" v-if="tangible.other_building !== '' && tangible.other_building !== undefined && tangible.other_building !== null">
                <p class="content-title">Tên công trình</p>
                <p class="content-name">{{tangible.other_building}}</p>
              </div>
              <div class="content-detail" v-if="tangible.description !== '' && tangible.description !== undefined && tangible.description !== null">
                <p class="content-title">Mô tả</p>
                <p class="content-name">{{tangible.description}}</p>
              </div>
              <div class="content-detail" v-if="tangible.floor !== '' && tangible.floor !== undefined && tangible.floor !== null">
                <p class="content-title">Số tầng</p>
                <p class="content-name">{{tangible.floor}}</p>
              </div>
              <div class="content-detail" v-if="tangible.start_using_year !== '' && tangible.start_using_year !== undefined && tangible.start_using_year !== null">
                <p class="content-title">Số năm sử dụng</p>
                <p class="content-name">{{tangible.start_using_year}}</p>
              </div>
            </div>
            <div class="d-grid" style="margin-bottom: 15px">
              <InputSwitch
                v-model="tangible.gpxd"
                vid="gpxd"
                label="GPXD"
                :disabled="true"
                class="input-switch"
              />
            </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Chất lượng còn lại</p>
                <p class="content-name">{{tangible.remaining_quality}}%</p>
              </div>
              <div class="content-detail">
                <p class="content-title">Diện tích xây dựng</p>
                <p class="content-name">{{tangible.total_construction_area}}m<sup>2</sup></p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Diện tích sàn</p>
                <p class="content-name">{{tangible.total_construction_base}}m<sup>2</sup></p>
              </div>
              <div class="content-detail">
                <p class="content-title">Thửa đất xây dựng</p>
                <p class="content-name">{{tangible.plot_num}}</p>
              </div>
              <div class="content-detail">
                <p class="content-title">Niên hạn</p>
                <p class="content-name">{{tangible.duration ? tangible.duration : ''}}</p>
              </div>
            </div>
            <div class="col-12 col-lg-12">
              <div class="row">
                  <InputTextarea
                    label="Mô tả công trình xây dựng"
                    :value="tangible.contruction_description"
                    rules="required"
                    vid="contruction_description"
                    :rows="10"
                    :maxLength="1000"
                    :disableInput="true"
                    class="col-12 form-group-container"
                  />
              </div>
            </div>

          </div>
          <div class="container-title container-title__footer">
            <div class="d-lg-flex d-block justify-content-end shadow-bottom">
              <button class="btn btn-white" type="button" @click="handleCancel"><img src="../../../../assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
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
import InputTextarea from '@/components/Form/InputTextarea'
export default {
	name: 'ModalBuildingDetail',
	props: ['tangible', 'img_link'],
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
			},
			show_detail: ''
		}
	},
	components: {
		FileUpload,
		InputCategory,
		InputText,
		InputSwitch,
		InputTextarea
	},
	computed: {
	},
	created () {
		if (this.tangible.contruction_description) {
			this.show_detail = this.tangible.contruction_description
			this.show_detail = this.show_detail.split('\n')
			for (let i = 0; i < this.show_detail.length; i++) {
				this.show_detail[i] = this.show_detail[i] + '<br>'
			}
			this.show_detail = this.show_detail.join('')
		}
	},
	methods: {
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
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
      padding: 12px 0;
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
//.img-contain {
//  max-width: 200px;
//  max-height: 200px;
//  height: auto;
//  margin-left: 20px;
//  &__table{
//    margin: auto;
//    max-width: 50px;
//    img{
//      cursor: pointer;
//      display: flex;
//      justify-content: center;
//    }
//  }
//  img{
//    object-fit: contain;
//    width: 100%;
//    height: auto;
//    max-width: 200px;
//    max-height: 200px;
//  }
//}
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
</style>
