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
              <h2 class="title">Thông tin thửa đất</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="d-grid" v-for="(property_doc) in property.compare_property_doc" :key="property_doc.id">
                <div class="content-detail">
                  <p class="content-title">Số tờ:</p>
                  <p class="content-name">{{property_doc.doc_num}}</p>
                </div>
                <div class="content-detail">
                  <p class="content-title">Số thửa:</p>
                  <p class="content-name">{{property_doc.plot_num}}</p>
                </div>
            </div>
            <!-- <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Tọa độ:</p>
                <p class="content-name">{{property.coordinates}}</p>
              </div>
            </div> -->
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Chiều rộng mặt tiền:</p>
                <p class="content-name">{{ formatNumber(property.front_side_width) }}m</p>
              </div>
              <div class="content-detail">
                <p class="content-title">Chiều dài</p>
                <p class="content-name">{{ formatNumber(property.insight_width) }}m</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail" v-if="property.legal !== null">
                <p class="content-title">Pháp lý</p>
                <p class="content-name">{{property.legal.description}}</p>
              </div>
              <div class="content-detail" v-if="property.land_type !== null">
                <p class="content-title">Loại đất</p>
                <p class="content-name">{{property.land_type.description}}</p>
              </div>
            </div>
            <div class="card-table">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Quy mô</h3>
                  <img class="img-dropdown" :class="showScale? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showScale = !showScale">
                </div>
              </div>
              <div class="card-body card-info card-land" v-if="showScale">
                <div class="contain-table">
                  <table class="table-property">
                    <thead>
                    <tr>
                      <th>Mục đích sử dụng</th>
                      <th>Diện tích</th>
                      <th>Vị trí đất</th>
                      <th>Đơn giá đất theo QĐ của UBND</th>
                      <!-- <th>Hệ số K</th> -->
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(propertyDetail) in property.property_detail" :key="propertyDetail.id">
                      <td>
                        <div v-if="propertyDetail.land_type_purpose_data !== null">
                        {{propertyDetail.land_type_purpose_data.description}}
                        </div>
                      </td>
                      <td>
                        {{ formatNumber(propertyDetail.total_area) }}m<sup>2</sup>
                      </td>
                      <td>
                        <div v-if="propertyDetail.position_type !== null">
                          {{propertyDetail.position_type.description}}
                        </div>
                      </td>
                      <td>
                        {{ formatCurrency(propertyDetail.circular_unit_price) }}đ
                      </td>
                      <!-- <td>
                        {{ propertyDetail.k_rate }}
                      </td> -->
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title">Tổng diện tích:</p>
                <p class="content-name">{{ formatNumber(property.asset_general_land_sum_area) }}m<sup>2</sup></p>
              </div>
            </div>
            <p class="title mb-3">Đặc điểm chi tiết</p>
            <p class="title traffic-light"> Giao thông</p>
              <div class="row">
                <div class="col-12 col-lg-4 input-switch__detail">
                  <p class="front-side">Mặt tiền tuyến đường chính</p>
                  <div class="d-flex align-items-center">
                    <p class="mb-0 mr-2">Có</p>
                    <ToggleSwitch group="a" :options="frontSideOptions" :disabled="true"/>
                    <p class="mb-0 ml-2">Không</p>
                  </div>
                </div>
                <div class="col-12 col-lg-4 input-switch__detail">
                  <p class="front-side">Căn góc</p>
                  <div class="d-flex align-items-center">
                    <p class="mb-0 mr-2">Có</p>
                    <ToggleSwitch group="c" :options="twoSidesLandOptions" :disabled="true"/>
                    <p class="mb-0 ml-2">Không</p>
                  </div>
                </div>
                <div class="col-12 col-lg-4 input-switch__detail" v-if="property.front_side === 0">
                  <p class="front-side">Đường vào thửa đất</p>
                  <div class="d-flex align-items-center">
                    <p class="mb-0 mr-2">Có</p>
                    <ToggleSwitch group="b" :options="individualRoadOptions" :disabled="true"/>
                    <p class="mb-0 ml-2">Không</p>
                  </div>
                </div>
              </div>
            <div class="d-grid">
              <div class="content-detail" v-if="property.material !== null">
                <p class="content-title">Chất liệu đường</p>
                <p class="content-name">{{ property.material.description }}</p>
              </div>
              <div class="content-detail" v-if="property.main_road_length !== '' && property.main_road_length !== undefined && property.main_road_length !== null">
                <p class="content-title">Bề rộng đường</p>
                <p class="content-name">{{ formatNumber(property.main_road_length) }}m</p>
              </div>
            </div>
            <div class="card-table" v-if="property.front_side === 0">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Thông tin hẻm</h3>
                  <img class="img-dropdown" :class="showAlley? '' : 'img-dropdown__hide'" src="../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showAlley = !showAlley">
                </div>
              </div>
              <div class="card-body card-info card-land" v-if="showAlley">
                <div class="contain-table">
                  <table class="table-property">
                    <thead>
                    <tr>
                      <th>Số lần rẽ</th>
                      <th>Chất liệu đường</th>
                      <th>Mặt đường</th>
                      <th>Đấu nối đường chính</th>
                      <th>Gần trục đường chính</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(alley) in property.compare_property_turning_time" :key="alley.id">
                      <td>
                        {{alley.turning}}
                      </td>
                      <td>
                        <div v-if="alley.material == null">
                          Chưa có chất liệu đường
                        </div>
                        <div v-if="alley.material !== null">
                          {{alley.material.description}}
                        </div>
                      </td>
                      <td>
                        {{ formatNumber(alley.main_road_length) }} m
                      </td>
                      <td>
                        <InputSwitch
                        v-model="alley.is_alley_with_connection"
                        vid="front_side_switch"
                        label="Hẻm đầu nối"
                        :disabled="true"
                        class="contain-input contain-input__info contain-input__property"
                      />
                      </td>
                      <td>
                        <InputSwitch
                          v-model="alley.is_near_main_road"
                          vid="front_side_switch"
                          label="Gần trục đường chính"
                          class="contain-input contain-input__info contain-input__property"
                          :disabled="true"
                        />
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title traffic-light">Hình dáng</p>
                <p class="content-name">{{property.land_shape !== undefined && property.land_shape !== null ? property.land_shape.description : ''}}</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title traffic-light">Kinh doanh</p>
                <p class="content-name">{{property.business !== undefined && property.business !== null ? property.business.description : ''}}</p>
              </div>
              <div class="content-detail">
                <p class="content-title traffic-light">Cơ sở hạ tầng</p>
                <p class="content-name">{{property.conditions !== undefined && property.conditions !== null? property.conditions.description : ''}}</p>
              </div>
              </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title traffic-light">An ninh, trật tự xã hội</p>
                <p class="content-name">{{property.social_security !== undefined && property.social_security !== null? property.social_security.description : ''}}</p>
              </div>
              <div class="content-detail">
                <p class="content-title traffic-light">Phong thủy</p>
                <p class="content-name">{{property.feng_shui !== undefined && property.feng_shui !== null ?  property.feng_shui.description : ''}}</p>
              </div>
            </div>
            <div class="d-grid">
              <div class="content-detail">
                <p class="content-title traffic-light">Quy hoạch/ hiện trạng</p>
                <p class="content-name">{{property.zoning !== undefined && property.zoning !== null ? property.zoning.description : ''}}</p>
              </div>
              <div class="content-detail">
                <p class="content-title traffic-light">Điều kiện thanh toán</p>
                <p class="content-name">{{property.paymen_method !== undefined && property.paymen_method !== null ? property.paymen_method.description : ''}}</p>
              </div>
            </div>
            <div class="content-detail" v-if="property.description !== null">
              <p class="content-title">Mô tả vị trí</p>
              <p class="content-name">{{property.description}}</p>
            </div>
            <div class="card-table">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Hình ảnh</h3>
                </div>
              </div>
              <div class="card-body">
                <div class="container-img row mr-0 ml-0">
                  <div class="img-empty text-center" v-if="property.pic.length === 0">
                    <img src="../../assets/images/img_emply.svg" alt="empty">
                    <p class="empty-content"> Chưa có hình</p>
                  </div>
                  <div class="img-contain col-4 col-lg-2" v-for="images in property.pic" :key="images.id">
                    <img :src="images.link" alt="img">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-lg-flex d-block justify-content-end shadow-bottom">
              <button class="btn btn-white" type="button" @click="handleCancel"><img src="../../assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
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
export default {
	name: 'ModalPropertyDetail',
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
		formatCurrency (value) {
			if (value) {
				let num = (value / 1).toFixed(0).replace('.', ',')
				return num.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
			return value
		},
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
			return num
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
  font-size: 16px;
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
      font-size: 16px;
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
      font-size: 16px;
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
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body{
    padding: 35px 30px 40px;
  }
  &-info{
    .title{
      font-size: 16px;
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
    font-size: 14px;
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
    font-size: 14px;
    font-weight: 400;
  }
  &-name{
    font-size: 16px;
    color: #000000;
    margin-bottom: 15px;
    font-weight: 600;
    @media (max-width: 767px) {
      font-size: 14px;
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
  font-weight: 400;
  color: #000000;
  text-align: center;
  thead{
    th{
      padding: 12px 5px;
      font-weight: 400;
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
  font-size: 14px;
  font-weight: 600;
  span{
    font-weight: 400;
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
    font-size: 18px;
    @media (max-width: 767px) {
      font-size: 16px;
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
</style>
