<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center">
        <div class="loading" :class="{'loading__true': isSubmit}">
          <a-spin />
        </div>
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Pháp lý tài sản</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="property-detail">
              <div class="row justify-content-between">
                <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.appraise_law_id"
                    vid="appraise_law_id"
                    label="Loại pháp lý"
                    rules="required"
                    :options="optionsJuridicals"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputText
                    v-model="form.date"
                    vid="date"
                    label="Số/Ngày"
                    rules="required"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputNumberFormat
                    v-model="form.doc_no"
                    vid="doc_num"
                    label="Số tờ"
                    :max="999999999"
                    :min="-999999999"
                    @change="changeDocNo($event)"
                    class=""
                  />
                  <span class="text-error" v-if="form.doc_no !== '' && form.doc_no !== undefined && form.doc_no !== null && form.doc_no < 0">Vui lòng nhập số tờ thích hợp</span>
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputNumberFormat
                    v-model="form.land_no"
                    vid="plot_num"
                    label="Số thửa"
                    :max="999999999"
                    :min="-999999999"
                    @change="changeLandNo($event)"
                    class=""
                  />
                  <span class="text-error" v-if="form.land_no !== '' && form.land_no !== undefined && form.land_no !== null && form.land_no < 0">Vui lòng nhập số thửa thích hợp</span>
                </div>
                <div class="col-12 input-contain">
                  <InputText
                    v-model="form.legal_name_holder"
                    vid="name_building"
                    label="Người đứng tên pháp lý"
                    @change="changeLegal"
                  />
                </div>
                <div class="col-12 input-contain">
                  <InputTextarea
                    v-model="form.content"
                    vid="content"
                    label="Nội dung"
                  />
                </div>
                <div class="col-12 input-contain">
                  <InputTextarea
                    v-model="form.certifying_agency"
                    vid="certifying_agency"
                    label="Cơ quan các cấp xác nhận"
                  />
                </div>
                <div class="col-12 input-contain">
                  <InputTextarea
                    v-model="form.origin_of_use"
                    vid="origin_of_use"
                    label="Nguồn gốc sử dụng"
                  />
                </div>
              </div>
            </div>
            <div class="card-table">
              <div class="card-title">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="title">Quy mô</h3>
                  <img class="img-dropdown" :class="!showScale? 'img-dropdown__hide' : ''" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click=" showScale = !showScale">
                </div>
              </div>
              <div class="card-body card-info card-land" v-if="showScale">
                <div class="contain-table">
                  <table class="table-property">
                    <thead>
                    <tr v-if="form.law_details.length > 0">
                      <th>Mục đích sử dụng</th>
                      <th>Diện tích (m²)</th>
                      <th>Quy hoạch</th>
                      <th>Lâu dài</th>
                      <th>Thời hạn sử dụng</th>
                      <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(property_detail, index) in form.law_details" :key="property_detail.id">
                      <td>
                        <InputCategory
                          v-model="property_detail.land_type_purpose_id"
                          :vid="'land_type_purpose' + index"
                          label="Mục đích sử dụng"
                          rules="required"
                          class="contain-input justify-content-center contain-input__scale contain-input__scale-one flexible-input-content"
                          :options="optionsTypePurpose"
                        />
                      </td>
                      <td class="position-relative">
                        <InputNumberFormat
                          v-model="property_detail.total_area"
                          :vid="'area' + index "
                          label="Diện tích"
                          @change="totalArea($event, index)"
                          :max="99999999"
                          :min="-99999999"
                          rules="required"
                          class="contain-input mr-0 justify-content-center contain-input__number contain-input__scale contain-input__scale-two"
                        />
                        <span v-show="property_detail.total_area !== '' && property_detail.total_area !== undefined && property_detail.total_area !== null && property_detail.total_area <= 0" class="text-error position-absolute">Nhập diện tích hợp lệ</span>
                      </td>
                      <td>
                        <InputSwitch
                          v-model="property_detail.is_zoning"
                          :vid="'is_zoning' + index"
                          label="Quy hoạch"
                          class="contain-input label-none justify-content-center"
                        />
                      </td>
                      <td>
                        <InputSwitch
                          v-model="property_detail.expiry_type"
                          :vid="'expiry_type' + index"
                          label="Lâu dài"
                          class="contain-input label-none justify-content-center"
                          @input="changeExpiryType(index)"
                        />
                      </td>
                      <td class="position-relative" style="max-width: 170px">
                        <InputDatePicker
                          v-model="expiry[index].expiry_date"
                          :vid="'expiry_date' + index"
                          :rules="property_detail.expiry_type ? '' : 'required'"
                          :disabled="property_detail.expiry_type"
                          @change="changeExpiryDate(index)"
                          label="Thời hạn sử dụng"
                          formatDate="DD/MM/YYYY"
                          v-if="expiry.length > 0"
                          :date="disabledDate"
                          class="contain-input mr-0 justify-content-center contain-input__scale contain-input__scale-end"
                        />
                      </td>
                      <td>
                        <div class="btn-delete" @click="removeRowLegalDetail(index)">
                          <img src="../../../../assets/icons/ic_delete.svg" alt="delete">
                        </div>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <div class="btn-property">
                  <button class="btn btn-orange btn-white btn-add" type="button" @click="handleAdd">
                    <img src="../../../../assets/icons/ic_add-white.svg" alt="add">
                    Thêm
                  </button>
                </div>
              </div>
            </div>
            <div class="row justify-content-between">
              <div class="col-12 col-lg-6 input-contain">
                <label class="name font-weight-bold" style="color: #000">Tổng diện tích (m<sup>2</sup>)</label>
                <div class="form-control disabled"><p class="mb-0">{{form.total_area}}</p></div>
              </div>
            </div>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange btn-action-modal" :class="{'btn-loading disabled': isSubmit}"> <img src="../../../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"> Lưu</button>
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"><img src="../../../../assets/icons/ic_cancel.svg"  style="margin-right: 12px" alt="save">Trở lại</button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import InputText from '@/components/Form/InputText'
import FileUpload from '@/components/file/FileUpload'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputTextarea from '@/components/Form/InputTextarea'
import WareHouse from '@/models/WareHouse'
import moment from 'moment'

export default {
	name: 'ModalBuildingProperty',
	props: ['data', 'compare_properties', 'legal', 'juridicals', 'type_purposes', 'full_address', 'provinceName'],
	data () {
		return {
			date: '',
			isSubmit: false,
			showScale: true,
			form: {
				appraise_law_id: '',
				date: '',
				doc_no: '',
				land_no: '',
				legal_name_holder: '',
				content: '',
				certifying_agency: '',
				origin_of_use: '',
				total_area: 0,
				law_details: []
			},
			expiry: []
		}
	},
	components: {
		FileUpload,
		InputCategory,
		InputText,
		InputNumberFormat,
		InputSwitch,
		InputDatePicker,
		InputTextarea
	},
	computed: {
		optionsJuridicals () {
			return {
				data: this.juridicals,
				id: 'id',
				key: 'content'
			}
		},
		optionsTypePurpose () {
			return {
				data: this.type_purposes,
				id: 'id',
				key: 'description'
			}
		}
	},
	async mounted () {
		await this.getLegal()
		if (this.legal === undefined || this.legal === null) {
			this.getData()
			this.getProvince()
		}
	},
	methods: {
		changeExpiryDate (index) {
			if (this.expiry[index].expiry_date && this.expiry[index].expiry_date !== '') {
				this.form.law_details[index].expiry_date = moment(this.expiry[index].expiry_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
			} else {
				this.form.law_details[index].expiry_date = ''
			}
		},
		changeExpiryType (index) {
			if (this.form.law_details[index].expiry_type) {
				this.form.law_details[index].expiry_date = ''
				this.expiry[index].expiry_date = ''
			}
		},
		getData () {
			this.form.land_no = this.data.land_no
			this.form.doc_no = this.data.doc_no
			this.getContent()
			if (this.data.properties && this.data.properties.length > 0) {
				if (this.data.properties[0].property_detail) {
					this.data.properties[0].property_detail.forEach(property_detail => {
						this.form.law_details.push({
							land_type_purpose_id: property_detail.land_type_purpose_id,
							total_area: property_detail.total_area,
							is_zoning: false,
							expiry_type: true,
							expiry_date: ''
						})
						this.expiry.push({
							expiry_date: ''
						})
					})
					this.getTotal()
				}
			}
		},
		getProvince () {
			this.form.certifying_agency = `Sở Tài nguyên và Môi trường ${this.provinceName.toLowerCase().includes('thành phố') ? this.provinceName : this.provinceName ? 'Tỉnh ' + this.provinceName : ''}`
		},
		getContent () {
			this.form.content = 'Chứng nhận ' + `${this.form.legal_name_holder ? this.form.legal_name_holder + ' ' : ''}` + 'được quyền sử dụng đất thuộc thửa đất số ' + `${this.form.land_no ? this.form.land_no + ' ' : ''}` + 'tờ bản đồ số ' + `${this.form.doc_no ? this.form.doc_no + ' ' : ''}` + `${this.full_address ? this.full_address : ''}.`
		},
		changeLegal () {
			this.getContent()
		},
		changeDocNo (e) {
			if (e !== undefined && e !== null) {
				this.form.doc_no = parseInt(e)
			} else {
				this.form.doc_no = ''
			}
			this.getContent()
		},
		changeLandNo (e) {
			if (e !== undefined && e !== null) {
				this.form.land_no = parseInt(e)
			} else {
				this.form.land_no = ''
			}
			this.getContent()
		},
		totalArea (event, index) {
			for (let i = 0; i < this.form.law_details.length; i++) {
				if (i === index) {
					if (event) {
						this.form.law_details[i].total_area = +event
					} else {
						this.form.law_details[i].total_area = ''
					}
				}
			}
			this.getTotal()
		},
		getTotal () {
			this.form.total_area = 0
			this.form.law_details.forEach(item => {
				this.form.total_area = this.form.total_area + item.total_area
			})
		},
		getLegal () {
			if (this.legal) {
				this.form = this.legal
				this.legal.law_details.forEach(law_detail => {
					if (law_detail.expiry_date && law_detail.expiry_date !== '') {
						this.expiry.push({
							expiry_date: moment(law_detail.expiry_date).format('DD/MM/YYYY')
						})
					} else {
						this.expiry.push({
							expiry_date: ''
						})
					}
				})
			}
		},
		changeFloor (event) {
			if (event !== undefined && event !== null) {
				this.form.floor = parseInt(event)
			} else {
				this.form.floor = ''
			}
		},
		remainingQuality (event) {
			this.form.remaining_quality = event
			this.form.estimation_value = this.form.total_construction_base * this.form.unit_price_m2 * (this.form.remaining_quality / 100)
		},
		totalConstructionBase (event) {
			if (event !== undefined && event !== null) {
				this.form.total_construction_base = parseFloat(event).toFixed(2)
			} else {
				this.form.total_construction_base = ''
			}
		},
		changeUnitPriceM2 (event) {
			this.form.unit_price_m2 = event
			this.form.estimation_value = this.form.total_construction_base * this.form.unit_price_m2 * (this.form.remaining_quality / 100)
		},
		changeArea (event) {
			if (event !== undefined && event !== null) {
				this.form.total_construction_area = parseFloat(event).toFixed(2)
			} else {
				this.form.total_construction_area = ''
			}
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		handleAdd () {
			this.form.law_details.push({
				land_type_purpose_id: '',
				total_area: '',
				is_zoning: false,
				expiry_type: true,
				expiry_date: ''
			})
			this.expiry.push({
				expiry_date: ''
			})
		},
		removeRowLegalDetail (index) {
			this.form.law_details.splice(index, 1)
			this.expiry.splice(index, 1)
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async handleAction (event) {
			this.isSubmit = true
			try {
				this.isSubmit = true
				const data = this.form
				if ((this.form.total_area !== '' && this.form.total_area !== undefined && this.form.total_area !== null && this.form.total_area <= 0) || this.form.doc_no < 0 || this.form.land_no < 0) {
					this.$toast.open({
						message: 'Vui lòng nhập giá trị thích hợp trước khi lưu',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else {
					this.$emit('action', data)
					this.$emit('cancel', event)
					if (this.legal !== '' && this.legal !== null && this.legal !== undefined) {
						this.$toast.open({
							message: 'Cập nhật pháp lý tài sản thành công',
							type: 'success',
							position: 'top-right'
						})
					} else {
						this.$toast.open({
							message: 'Thêm mới pháp lý tài sản thành công',
							type: 'success',
							position: 'top-right'
						})
					}
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		changeSwitch () {
			if (this.form.individual_road_switch === true) {
				this.form.individual_road = 1
			} else {
				this.form.individual_road = 0
			}
			if (this.form.front_side_switch === true) {
				this.form.front_side = 1
			} else {
				this.form.front_side = 0
			}
		},
		async validateBeforeSubmit (event) {
			const isValid = await this.$refs.observer.validate()
			this.isSubmit = true
			if (isValid) {
				await this.handleAction(event)
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async getBuildingPrices () {
			const building_category = this.form.building_type_id
			const level = this.form.building_category_id
			const rate = this.form.rate_id
			const structure = this.form.structure_id
			const crane = this.form.crane_id
			const aperture = this.form.aperture_id
			const factory_type = this.form.factory_type_id
			if ((this.form.building_category_id !== '' && this.form.building_category_id !== undefined && this.form.building_category_id !== null && this.form.rate_id !== '' && this.form.rate_id !== undefined && this.form.rate_id !== null) || (this.form.structure_id !== '' && this.form.structure_id !== undefined && this.form.structure_id !== null && this.form.rate_id !== '' && this.form.rate_id !== undefined && this.form.rate_id !== null) || (this.form.crane_id !== '' && this.form.crane_id !== undefined && this.form.crane_id !== null && this.form.aperture_id !== '' && this.form.aperture_id !== undefined && this.form.aperture_id !== null && this.form.factory_type_id !== '' && this.form.factory_type_id !== undefined && this.form.factory_type_id !== null)) {
				const resp = await WareHouse.getBuildingPrices(building_category, level, rate, structure, crane, aperture, factory_type)
				this.form.unit_price_m2 = parseInt(resp.data)
			} else {
				this.form.unit_price_m2 = 0
			}
			this.form.estimation_value = this.form.total_construction_base * this.form.unit_price_m2 * (this.form.remaining_quality / 100)
		},
		async getCrane () {
			try {
				const resp = await WareHouse.getCrane()
				this.buildingCrane = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		disabledDate (current) {
			return current && current < moment().endOf('day')
		}
	},
	async beforeMount () {
		await this.getCrane()
	}
}
</script>

<style lang="scss" scoped>
.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);
  @media (max-width: 768px) {
    padding: 20px;
  }
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1300px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 35px 95px;
    @media (max-width: 768px) {
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
.title-property{
  font-weight: 700;
  font-size: 1.2rem;
  margin-bottom: 18px;
}
.input-contain{
  margin-bottom: 25px;
}
.card-table{
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  max-width: 99%;
  margin: 50px auto 75px;
}
.card-table tbody tr:last-child td, .card-table tbody tr:last-child th{
  border-bottom: 1px solid #E5E5E5 ;
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
    &__img{
      padding: 8px 20px;
    }
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
.table-property{
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead{
    th{
      padding: 12px;
      font-weight: 500;
    }
  }
  tbody{
    td{
      border: 1px solid #E5E5E5;
      &:first-child{
        border-left: none;
        border-right: none;
      }
      &:last-child{
        border-right: none;
      }
      box-sizing: border-box;
      padding: 20px 14px;
    }
  }
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
.contain-table{
  overflow-x: auto;
  @media (max-width: 1024px) {
    overflow-y: hidden;
    overflow-x: auto;
  }
  .table-property{
    width: 100%;
  }
}
.contain-file{
  display: flex;
  align-items: center;
  h3{
    margin-top: 8px;
    margin-bottom: 0;
  }
}
.btn-upload{
  background: #FFFFFF;
  white-space: nowrap;
  border: 1px solid #555555;
  box-sizing: border-box;
  border-radius: 5px;
  padding: 5px 19px;
  font-size: 10px;
}
.btn-property{
  padding: 10px;
}
.img-dropdown{
  cursor: pointer;
  width: 18px;
  &__hide{
    transform: rotate(90deg);
    transition: .3s;
  }
}
.img-upload{
  margin-left: 20px;
  position: relative;
  width: 123px;
  height: 35px;
  color: #fff;
  background: #FAA831;

  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
  cursor: pointer;
  input{
    cursor: pointer !important;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 100%;
    opacity: 0;
  }
}
.contain-img{
  height: auto;
  position: relative;
  .img{
    width: 100%;
  }
  .delete{
    position: absolute;
    top: 0;
    right: 0;
    background: #000000;
    color: #FFFFFF;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: 700;
    border-radius: 5px;
  }
}
.contain-total{
  &__left{
    color: #000000;
    .num{
      padding: 0 11px 0 24px;
      width: 340px;
      height: 35px;
      line-height: 1.5;
      border-radius: 5px;
      border: 1px solid #555555;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      background: #f1f1f1 !important;
      cursor: not-allowed;
      user-select: none;
      p{
        margin-bottom: 0;
      }
    }
    .name{
      min-width: 175px;
      margin-bottom: 0;
      margin-right: 20px;
    }
  }
}
.img-locate{
  cursor: pointer;
  position: absolute;
  right: 15px;
  top: 35px;
}
.form-control {
  width: 100%;
}
.btn-orange {
  background: #FAA831;
  text-align: center;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
  height: 35px;
  width: 146px;
  color: #fff;
  margin-right: 15px;
  box-sizing: border-box;
  &:hover{
    border-color: #dc8300;
  }
}
.container-title{
  margin: -35px -95px auto;
  padding: 35px 95px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  @media (max-width: 767px) {
    margin: -20px -10px auto;
    padding: 20px 10px 0;
  }
  .title{
    font-size: 1.2rem;
    margin-bottom: 25px;
    font-weight: 700;
    @media (max-width: 767px) {
      font-size: 1.125rem;
    }
  }
  &__footer{
    margin: auto -95px -35px;
    padding: 20px 95px 20px;
    @media (max-width: 767px) {
      margin: auto -10px -20px;
      padding: 20px 10px 0;
      .btn-white{
        margin-bottom: 20px;
      }
    }
  }
}
.contain-img{
  aspect-ratio:1/1;
  overflow: hidden;
  height: auto;
  position: relative;
  text-align: center;
  margin-bottom: 10px;
  .img{
    object-fit: cover;
    margin-right: 0 ;
    width: 100%;
    height: 100%;
    cursor: pointer;
    &-table{
      margin: auto;
      min-width: 50px;
      min-height: 50px;
      width: 50px;
      height: 50px;
      object-fit: cover;
    }
  }
  &__table{
    width: auto;
  }
  .delete{
    position: absolute;
    top: 0;
    right: 0.75rem;
    background: #000000;
    color: #FFFFFF;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: 700;
    border-radius: 5px;
  }
}
.container-img{
  padding: .75rem 0;
  border: 1px solid #0b0d10;
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
.input-disabled {
  min-height: 30px;
  height: 33px;
}
.text-error{
  color: #cd201f;
  font-size: 12px;
  width: 100%;
  left: 0;
}
</style>
