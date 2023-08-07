<template>
  <div>
    <div  class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Công trình xây dựng</h3>
          <img class="img-dropdown" :class="!showDetailListContruction ? 'img-dropdown__hide' : ''"
               alt="dropdown" src="@/assets/images/icon-btn-down.svg"
               @click="showDetailListContruction = !showDetailListContruction">
        </div>
      </div>
      <div class="card-body card-info" v-show="showDetailListContruction">
        <div v-if="isHaveContruction" class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="main-wrapper">
                <div class="responsive-table">
                  <table class="table_contruction color_content">
                    <thead>
                      <tr>
                        <th>Mã số</th>
                        <th>Tên công trình</th>
                        <th>Diện tích sàn (m<sup>2</sup>)</th>
                        <th>Năm xây dựng</th>
                        <th>Thao tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, indexContruction) in data.construction" :key="indexContruction">
                        <td><p class="mb-0">{{ item.id ? `CT_${item.id}` : ''}}</p></td>
                        <td>
                          <p class="name_contruct mb-0">{{ item.tangible_name ? item.tangible_name : '' }}</p>
                        </td>
                        <td>{{ formatNumber(item.total_construction_base) }}</td>
                        <td>{{ item.start_using_year }}</td>
                        <td>
                          <div class="d-flex justify-content-center">
                            <button class="btn-delete" type="button" @click="handleEditContruction(indexContruction)"><img
                              alt="add" src="@/assets/icons/ic_eye.svg"/></button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="col-12">
          <div class="infor-box">
            <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
            </svg>
            Tài sản thẩm định là đất trống không cần khai báo thông tin công trình xây dựng
          </div>
        </div>
      </div>
    </div>
    <ModalStep3ContructionDetail
      v-if="showModalContruction"
      :data="form"
      :housingTypes="housingTypes"
      :buildingCategories="buildingCategories"
      :buildingStructure="buildingStructure"
      :buildingRates="buildingRates"
      :buildingAperture="buildingAperture"
      :buildingFactoryType="buildingFactoryType"
      :buildingCrane="buildingCrane"
      @cancel="showModalContruction = false"
      @action="actionSaveContruction"
    />
    <ModalNotificationAppraisal
      v-if="showConfirmDelete"
      @cancel="showConfirmDelete = false"
      :notification="'Bạn có muốn xóa công trình xây dựng này không?'"
      @action="actionDeleteContruction"
    />
  </div>
</template>

<script>
import { AlertCircleIcon } from 'vue-feather-icons'
import InputPercent from '@/components/Form/InputPercent'
import InputNumberNew from '@/components/Form/InputNumberNew'
import InputCurrency from '@/components/Form/InputCurrency'
import InputCategoryBoolean from '@/components/Form/InputCategoryBoolean'
import InputArea from '@/components/Form/InputArea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputSwitch from '@/components/Form/InputSwitch'
import InputLengthArea from '@/components/Form/InputLengthArea'
import { BAlert } from 'bootstrap-vue'
import ModalStep3ContructionDetail from './modals/ModalStep3ContructionDetail'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import Vue from 'vue'
import Icon from 'buefy'
Vue.use(Icon)
export default {
	name: 'Step3',
	props: ['data', 'housingTypes', 'buildingCategories', 'buildingStructure', 'buildingRates', 'buildingAperture', 'buildingFactoryType', 'isHaveContruction', 'buildingCrane'],
	components: {
		InputCategory,
		InputText,
		InputTextarea,
		InputSwitch,
		InputLengthArea,
		InputArea,
		InputDatePicker,
		InputCategoryBoolean,
		InputCurrency,
		InputNumberNew,
		InputPercent,
		BAlert,
		AlertCircleIcon,
		ModalStep3ContructionDetail,
		ModalNotificationAppraisal
	},
	computed: {
		optionsHousingType () {
			return {
				data: this.housingTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsGPXD () {
			return {
				data: this.GPXDType,
				id: 'id',
				key: 'description'
			}
		},
		optionsHousing () {
			return {
				data: this.buildingCategories,
				id: 'id',
				key: 'description'
			}
		},
		optionYearBuild () {
			return {
				data: this.built_years,
				id: 'year',
				key: 'year'
			}
		},
		optionsStructure () {
			return {
				data: this.buildingStructure,
				id: 'id',
				key: 'description'
			}
		},
		optionsRate () {
			return {
				data: this.buildingRates,
				id: 'id',
				key: 'description'
			}
		},
		optionsAperture () {
			return {
				data: this.buildingAperture,
				id: 'id',
				key: 'description'
			}
		},
		optionsFactionType () {
			return {
				data: this.buildingFactoryType,
				id: 'id',
				key: 'description'
			}
		},
		optionsCrane () {
			return {
				data: this.buildingCrane,
				id: 'id',
				key: 'description'
			}
		}
	},
	data () {
		return {
			showDetailContruction: true,
			showDetailListContruction: true,
			showModalContruction: false,
			showConfirmDelete: false,
			isEdit: false,
			built_years: [],
			indexEdit: '',
			form: {
				building_type_id: '',
				gpxd: true,
				building_category_id: '',
				floor: '',
				remaining_quality: '',
				tangible_name: '',
				total_construction_base: '',
				total_construction_area: '',
				start_using_year: '',
				duration: '',
				description: '',
				other_building: '',
				rate_id: '',
				structure_id: '',
				crane_id: '',
				aperture_id: '',
				factory_type_id: '',
				created_at: new Date(),
				contruction_description: '+ Móng cột:\n+ Dầm, sàn BTCT chịu lực: \n+ Tường xây: \n+ Mái BTCT: \n+ Nền lát: \n+ Cửa đi, cửa sổ: \n+ Khu vệ sinh: \n+ Khu bếp: \n+ Cầu thang: \n+ Hiện trạng: \n'
			},
			GPXDType: [
				{ id: 1, description: 'Có giấy phép' },
				{ id: 0, description: 'Không có giấy phép' }
			]
		}
	},
	mounted () {
	},
	beforeUpdate () {
	},
	methods: {
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		handleAddContruction () {
			this.showModalContruction = true
			this.isEdit = false
			this.form = {
				building_type_id: '',
				gpxd: true,
				building_category_id: '',
				floor: '',
				remaining_quality: '',
				total_construction_base: '',
				total_construction_area: '',
				start_using_year: '',
				duration: '',
				description: '',
				other_building: '',
				rate_id: '',
				structure_id: '',
				crane_id: '',
				aperture_id: '',
				factory_type_id: '',
				created_at: new Date(),
				contruction_description: '+ Móng cột:\n+ Dầm, sàn BTCT chịu lực: \n+ Tường xây: \n+ Mái BTCT: \n+ Nền lát: \n+ Cửa đi, cửa sổ: \n+ Khu vệ sinh: \n+ Khu bếp: \n+ Cầu thang: \n+ Hiện trạng: \n'
			}
		},
		handleEditContruction (index) {
			this.form = this.data.construction[index]
			this.isEdit = true
			this.indexEdit = index
			this.showModalContruction = true
		},
		handleDeleteContruction (index) {
			this.indexEdit = index
			this.showConfirmDelete = true
		},
		changeFloor (event) {
			this.data.construction.floor = event
		},
		changeArea (event) {
			this.data.construction.total_construction_area = event
		},
		totalConstructionBase (event) {
			this.data.construction.total_construction_base = event
		},
		actionDeleteContruction () {
			this.$emit('deleteContruction', this.indexEdit)
		},
		actionSaveContruction (data) {
			if (this.isEdit) {
				this.$emit('updateContruction', data, this.indexEdit)
				this.isEdit = false
				this.showModalContruction = false
				this.$toast.open({
					message: 'Cập nhập công trình xây dựng thành công',
					type: 'success',
					position: 'top-right'
				})
			} else {
				this.$emit('createContruction', data)
				this.showModalContruction = false
				this.$toast.open({
					message: 'Thêm công trình xây dựng thành công',
					type: 'success',
					position: 'top-right'
				})
			}
		},
		changeDuration (event) {
			this.$emit('changeDuration', event)
		}
	}
}
</script>
<style scoped lang="scss">

.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;
    &__img {
      padding: 8px 20px;
    }
    h3 {
      color: #007EC6;
    }
    @media (max-width: 768px) {
      padding: 12px;
    }

    .title {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }

  &-body {

    @media (max-width: 787px) {
      padding: 15px;
    }
  }

  &-sub_header_title {
    padding: 15px 24px;
  }

  &-info {
    .title {
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

  &-land {
    position: relative;
    padding: 0;
  }
}

.form-group-container {
  margin-top: 10px;
}

.color-black {
  color: #333333;
}

.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: #FFFFFF;
  border: none;
  img {
    max-width: 1.5rem;
    min-width: 1rem;
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

    &:hover {
      border-color: #dc8300;
    }
  }
}

.img-dropdown {
  cursor: pointer;
  width: 18px;

  &__hide {
    transform: rotate(90deg);
    transition: .3s;
  }
}

.img-locate {
  cursor: pointer;
  position: absolute;
  right: 14px;
  top: 30px;
  background: #FFFFFF;
  height: 2.295rem;
  width: 32px;
  display: grid;
  place-items: center;

  img {
    height: 60%;
  }
}

.text-error {
  color: #cd201f;
  font-size: 12px;
}

.select-group {
  background-color: #F6F7FB;
  border: 1px solid #E8E8E8;
  border-radius: 3px;
  padding: 16px 22px;

  .select-title {
    color: #FAA831;
    font-weight: 700;
    white-space: nowrap;
  }
}
  .img_add {
    width: 100%;
    height: 100% !important;
    cursor: pointer;
  }
  .container_input {
    border-radius: 10px;
    border: 2px solid #FAA831;
    width: 100%;
    height: 100%;
    position: relative;
  }
  .input_file_4 {
    left: 0;
    opacity: 0;
    height: 100%;
    width: 100%;
    cursor: pointer;
    position: absolute;
  }
  .sub_header_title {
    background-color: #F6F7FB;
    border: 1px solid #E8E8E8;
    border-radius: 3px;
    padding: 0.5rem 2rem;
    position: relative;
    color: #00507C;
    font-weight: 700;

    .label {
      margin-right: 15px;
    }
    label {
      margin: 0;
    }
    &::before {
      content: '';
      position: absolute;
      height: calc(100% - 16px);
      width: 3px;
      background-color: #99D161;
      border-radius: 3px;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
    }
  }
  .sub_header_title-rows {
    padding-top: 10px;
  }
  .footer-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #00507C;
  }
  /deep/ {
    .form-group-container.disabled {
      background-color: rgba(222, 230, 238, 0.3);
      .ant-input {
        background-color: rgba(222, 230, 238, 0.3) !important;
      }
    }
  }
  .infor-box {
    padding: 1rem;
    border-radius: 12px 15px;
    background-color: #EEF9FF;
    border: 1px solid #007EC6;
    color: #446B92;
    @media (max-height: 660px) {
      font-size: 12px;
    }
    @media (max-height: 970px) and (min-height: 660px) {

    }
  }
  .alertInfo {
    background-color: rgba(0,207,232,.12);
    color: #00CFE8!important;
  }
  .table_contruction {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color:  #DEE6EE;;
        color: #3D4D65;
        border-right: 1px solid white;
        &:first-child{
          border-top-left-radius: 3px;
          border-left: 1px solid #CED4DA;
        }
        &:last-child{
          border-top-right-radius: 3px;
          border-right: 1px solid #CED4DA;
        }
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 7%;
        }
        &:nth-child(2) {
          max-width: 300px
        }
        &:last-child{
          width: 10%;
        }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }

  }
  .main-wrapper {
    width: 100%;
    overflow-x: auto;
    box-sizing: border-box;
  }

  .responsive-table {
    display: inline-block;
    min-width: 100%;
    box-sizing: border-box;
  }

  .responsive-table > table {
    width: 100%;
    border-collapse: collapse;
  }
</style>
