<template>
  <div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Chi tiết về đất</h3>
          <img class="img-dropdown" :class="!showDetailLand ? 'img-dropdown__hide' : ''"
               alt="dropdown" src="@/assets/images/icon-btn-down.svg"
               @click="() => { showDetailLand = !showDetailLand;  }">
        </div>
      </div>
      <div class="card-body card-info" v-show="showDetailLand">
        <div class="container-fluid mb-2">
          <div class="row">
						<div class="col-12 px-0">
              <div class="d-flex align-items-center sub_header_title">
                <span class="label">
                  Kích thước
                </span>
              </div>
            </div>
            <div class="col-12 mt-2 mb-2">
              <div class="row content_form">
                <div class="col-4">
                  <InputLengthArea
                    v-model="data.land_details.front_side_width"
                    :decimal="2"
                    :required="true"
                    :disabled="true"
                    rules="required"
                    label="Chiều rộng mặt tiền"
                    @change="handleChangeWidth"
                  />
                </div>
                <div class="col-4">
                  <InputLengthArea
                    v-model="data.land_details.insight_width"
                    :decimal="2"
                    :required="true"
                    :disabled="true"
                    rules="required"
                    label="Chiều dài"
                    @change="handleChangeInsightWidth"
                  />
                </div>
                <div class="col-4">
                  <InputCategory
                    v-model="data.land_details.land_shape_id"
                    vid="land_shape_id"
                    label="Hình dáng"
                    rules="required"
                    :disabled="true"
                    :options="optionsLandShape"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container-fluid mb-2">
          <div class="row">
            <div class="col-12 px-0">
              <div class="d-flex align-items-center sub_header_title">
                <span class="label">
                  Diện tích theo mục đích sử dụng
                </span>
              </div>
            </div>
            <div class="col-12 mt-2">
              <div class="row content_form color_content">
                <div class="col-5 font-weight-bold">Mục đích sử dụng</div>
                <div class="col-3 font-weight-bold text-center">Diện tích</div>
                <div class="col-4 font-weight-bold">Phân mục đích</div>
              </div>
            </div>
            <div class="col-12 mt-2 mb-2">
              <div v-for="(main_land, index) in data.total_area" :key="index" class="mb-2 row content_form">
                <div class="col-5">
                  <InputCategory
                    v-model="main_land.land_type_purpose_id"
                    vid="land_type_purpose_id"
                    nonLabel="Mục đích sử dụng"
                    rules="required"
                    :disabled="true"
                    :options="optionsTypePurposes"
                    @change="changeLandTypePurpose(index, $event, main_land)"
                  />
                </div>
                <div class="col-3">
                  <InputArea
                    v-model="main_land.total_area"
                    :decimal="2"
                    vid="total_area"
                    :required="true"
                    :disabled="true"
                    :max="9999999999999999"
                    :min="0"
                    rules="required"
                    :sufix="false"
                    :text_center="true"
                    nonLabel="Diện tích phù hợp"
                    @change="handleChangeMainArea($event, index)"
                  />
                </div>
                <div class="col-4">
                  <InputCategoryBoolean
                    v-model="main_land.is_transfer_facility"
                    :disabled="true"
                    vid="is_transfer_facility"
                    nonLabel="Phân mục đích"
                    :options="optionMainOrNot"
                  />
                </div>
                <!-- <div class="col-1 px-3 d-flex align-items-end">
                  <div @click="handleDeleteMainArea(index)" class="btn-delete">
                    <img alt="delete" src="@/assets/icons/ic_delete_2.svg">
                  </div>
                </div> -->

              </div>
            </div>
            <!-- <div class="d-flex">
              <div class="w-100 d-flex justify-content-end">
                <button class="btn text-warning btn-ghost btn-add" type="button" @click="handleAddMainArea">
                  <img alt="add" src="@/assets/icons/ic_add-white.svg">
                  + Thêm
                </button>
              </div>
              <div class="col-1 px-3"></div>
            </div> -->
          </div>
        </div>
        <div class="container-fluid mb-2">
          <div class="row">
            <div class="col-12 px-0">
              <div class="d-flex align-items-center sub_header_title">
                <span class="label">
                  Phần diện tích vi phạm quy hoạch
                </span>
                <label class="input-checkbox">
                  <input
                    :key="updateCheckBox"
                    v-model="checkShowPlanning"
                    :value="checkShowPlanning" disabled
                    type="checkbox" @change="handleChangeStatusPlanning"
                  >
                  <span class="check-mark"/>
                </label>
              </div>
            </div>
            <div v-if="data.planning_area.length > 0" class="col-12 mt-2">
              <div class="row content_form color_content">
                <div class="col-5 font-weight-bold">Mục đích sử dụng</div>
                <div class="col-3 font-weight-bold text-center">Diện tích</div>
                <div class="col-4 font-weight-bold">Loại quy hoạch</div>
              </div>
            </div>
            <div v-if="data.planning_area.length > 0" class="col-12 mt-2 mb-2">
              <div v-for="(planning_area, index) in data.planning_area" :key="index" class="mb-2 row content_form">
                <div class="col-5">
                  <InputCategory
                    v-model="planning_area.land_type_purpose_id"
                    :disabled="true"
                    :options="optionsTypePurposes"
                    nonLabel="Mục đích sử dụng"
                    rules="required"
                    vid="land_type_purpose_id"
                    @change="changeLandPlanningPurpose(index, $event, planning_area)"
                  />
                </div>
                <div class="col-3">
                  <InputArea
                    v-model="planning_area.planning_area"
                    :decimal="2"
                    :disabled="true"
                    :max="9999999999999999"
                    :min="0"
                    :required="true"
                    :sufix="false"
                    :text_center="true"
                    nonLabel="Diện tích vi phạm"
                    rules="required"
                    vid="planning_area"
                    @change="handleChangePlanningArea($event, index)"
                  />
                </div>
                <div class="col-4">
                  <div class="col-12">
                    <InputText
                      v-model="planning_area.type_zoning"
                      :disabledInput="true"
                      nonLabel="Loại quy hoạch"
                      rules="required"
                      vid="appraise_asset"/>
                  </div>
                </div>
                <!-- <div v-if="data.planning_area.length > 1" class="col-1 px-3 d-flex align-items-end">
                  <div class="btn-delete" @click="handleDeletePlanningArea(index)">
                    <img alt="delete" src="@/assets/icons/ic_delete_2.svg">
                  </div>
                </div> -->

              </div>
            </div>
            <!-- <div v-if="this.data.planning_area.length > 0" class="d-flex">
              <div class="w-100 d-flex justify-content-end">
                <button class="btn text-warning btn-ghost btn-add" type="button" @click="handleAddPlanningArea">
                  <img alt="add" src="@/assets/icons/ic_add-white.svg">
                  + Thêm
                </button>
              </div>
              <div class="col-1 px-3"></div>
            </div> -->
          </div>
        </div>
      </div>
      <div style="z-index: 1" class="card-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="row content_form">
                <div class="col-5">
                  <label class="font-weight-bold" style="margin-top: 0.5rem">
                    Tổng diện tích thẩm định (m<sup>2</sup>)
                  </label>
                </div>
                <div class="col-3 result_total_appraise">
                  <strong>{{formatNumber(parseFloat(data.land_details.appraise_land_sum_area).toFixed(2))}}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông số nhà nước</h3>
          <img class="img-dropdown" :class="!showDetailCommitee ? 'img-dropdown__hide' : ''"
               alt="dropdown" src="@/assets/images/icon-btn-down.svg" @click="showDetailCommitee = !showDetailCommitee">
        </div>
      </div>
      <div class="card-body card-info" v-show="showDetailCommitee">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 col-lg-8">
              <div class="row color_content">
                <div class="col-4 font-weight-bold">Mục đích sử dụng</div>
                <div style="text-align: center" class="col-4 font-weight-bold">Vị trí</div>
                <div class="col-4 font-weight-bold">Đơn giá UBND (!)</div>
              </div>
            </div>
            <div class="col-3"></div>
          </div>
          <div class="row mt-3">
            <div class="col-12 col-lg-8">
              <div v-for="(item_UBND, indexUBND) in data.UBND_price" :key="indexUBND" class="row mb-2">
                <div class="col-5">
                  <InputText
                    :value="item_UBND.land_type_purpose ? item_UBND.land_type_purpose.description : ''"
                    class="form-group-container w-100 mt-0"
                    :disabledInput="true"
                  />
                </div>
                <div class="col-3">
                  <InputCategory
                      v-model="item_UBND.position_type_id"
                      :vid="'position_type' + indexUBND"
                      rules="required"
                      nonLabel="Vị trí"
                      :disabled="true"
                      :options="optionPoints"
                      @change="changePositionType(indexUBND)"
                      class="form-group-container w-100 mt-0"
                    />
                </div>
                <div class="col-4">
                  <InputCurrencyUnit
                    :id="`circular_unit_price${indexUBND}`"
                    v-model="item_UBND.circular_unit_price"
                    :vid="'circular_unit_price' + indexUBND"
                    :max="999999999"
                    nonLabel="Đơn giá"
                    rules="required"
                    :required="true"
                    :disabled="true"
                    class="w-100"
                    @change="changeUnitPrice($event, indexUBND)"
                  />
                </div>
              </div>
            </div>
            <div class="col-4 align-items-center d-none d-lg-flex">
              <div>
                <div class="infor-box">
                  <span class="mr-2">
                    <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
                        fill="#007EC6"/>
                    </svg>
                  </span>
                  Đơn giá đất UBND sau khi đã nhân các hệ số điều chỉnh theo quy định của từng địa phương như: hệ số
                  đường đất, hệ số vị trí, hệ số k, hệ số d,...
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
		<div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin quy hoạch</h3>
          <img class="img-dropdown" :class="!showDetailPlanning ? 'img-dropdown__hide' : ''"
               src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showDetailPlanning = !showDetailPlanning">
        </div>
      </div>
      <div class="card-body card-info" v-show="showDetailPlanning">
        <div class="container-fluid">
          <div class="row">
						<div class="col-12 col-lg-6">
							<InputTextarea
								v-model="data.real_estate.planning_info"
								label="Thông tin quy hoạch"
								class="form-group-container"
								:disableInput="true"
                :autosize="true"
							/>
						</div>
						<div class="col-12 col-lg-6">
							<InputTextarea
								v-model="data.real_estate.planning_source"
								label="Nguồn thông tin"
								class="form-group-container"
								:disableInput="true"
                :autosize="true"
							/>
						</div>
						<div class="col-12 col-lg-6">
							<InputText
								v-model="data.real_estate.contact_person"
								label="Người hướng dẫn khảo sát"
								class="form-group-container"
								:disabledInput="true"
							/>
						</div>
						<div class="col-12 col-lg-6">
							<InputText
								v-model="data.real_estate.contact_phone"
								label="Số điện thoại"
								class="form-group-container"
								:disabledInput="true"
							/>
						</div>
          </div>
        </div>
      </div>
    </div>
    <ModalNotificationAppraisal
      v-if="showConfirmPlanning"
      @cancel="handleCancelPlanning"
      :notification="message"
      @action="handleTurnOffPlanning"
    />
    <ModalNotificationAppraisal
      v-if="isChangePurpose"
      @cancel="handleCancelChangePurpose"
      :notification="message"
      @action="handleAcceptChangePurpose"
    />
  </div>
</template>

<script>
import InputCurrency from '@/components/Form/InputCurrency'
import InputCurrencyUnit from '@/components/Form/InputCurrencyUnit'
import InputCategoryBoolean from '@/components/Form/InputCategoryBoolean'
import InputArea from '@/components/Form/InputArea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputSwitch from '@/components/Form/InputSwitch'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputLengthArea from '@/components/Form/InputLengthArea'
import ModalDeleteIndex from '@/components/Modal/ModalDeleteIndex'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import {TabItem, Tabs} from 'vue-material-tabs'
import {LControl, LControlZoom, LIcon, LMap, LMarker, LTileLayer, LTooltip} from 'vue2-leaflet'
import Vue from 'vue'
import Icon from 'buefy'

Vue.use(Icon)
export default {
	name: 'Step2',
	props: ['data', 'coordinates', 'topographic', 'landShapes', 'points', 'type_purposes'],
	components: {
		InputCategory,
		InputText,
		InputTextarea,
		InputSwitch,
		InputNumberFormat,
		InputLengthArea,
		InputArea,
		InputDatePicker,
		ModalDeleteIndex,
		Tabs,
		TabItem,
		InputCategoryBoolean,
		InputCurrency,
    InputCurrencyUnit,
		ModalNotificationAppraisal,
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon
	},
	computed: {
		optionsTopographic () {
			return {
				data: this.topographic,
				id: 'id',
				key: 'description'
			}
		},

		optionMainOrNot () {
			return {
				data: this.optionMainChoose,
				id: 'id',
				key: 'description'
			}
		},
		optionPoints () {
			return {
				data: this.points,
				id: 'id',
				key: 'description'
			}
		},
		optionsTypePurposes () {
			return {
				data: this.type_purposes,
				id: 'id',
				key: 'description'
			}
		},
		optionsLandShape () {
			return {
				data: this.landShapes,
				id: 'id',
				key: 'description'
			}
		}
	},
	data () {
		return {
			componentKey: 1000,
			updateCheckBox: 1200,
			showDetailCommitee: true,
			showDetailPlanning: true,
			showDetailLand: true,
			showDetailPurpose: true,
			imageMap: true,
			showConfirmPlanning: false,
			isChangePurpose: false,
			land_type_purpose_id: '',
			indexPurpose: '',
			message: '',
			location: {
				lng: '',
				lat: ''
			},
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},
			url: 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}',
			optionMainChoose: [
				{
					id: 1,
					description: 'Chính'
				},
				{
					id: 0,
					description: 'Phụ'
				}
			],
			checkShowPlanning: false
		}
	},
	mounted () {
		if (this.data.planning_area.length > 0) {
			this.checkShowPlanning = true
      console.log('xử lý planning mới',this.data.planning_area)
      let new_planning = []
      for (let i = 0; i < this.data.planning_area.length; i++) {
        const e = this.data.planning_area[i];
        if (e.extra_planning) {
          // console.log('e.extra_planning',e.extra_planning)
          // e.extra_planning = JSON.parse(e.extra_planning)
          // console.log('đã parse', e.extra_planning)
          for (let index = 0; index < e.extra_planning.length; index++) {
            const element = e.extra_planning[index];
            new_planning.push({
              appraise_property_id: element.appraise_property_id,
              planning_area: element.planning_area,
              land_type_purpose_id: element.land_type_purpose_id,
              type_zoning: element.type_zoning
            })
            console.log('new_planning',new_planning)
          }
        }
      }
      if (new_planning.length > 0) {
        this.data.planning_area = new_planning
      }
		}
		if (this.coordinates) {
			this.map.center = [this.coordinates.split(',')[0], this.coordinates.split(',')[1]]
			this.markerLatLng = [this.coordinates.split(',')[0], this.coordinates.split(',')[1]]
			this.map.zoom = 17
		}
		if (this.$refs.map_step2) {
			setTimeout(() => {
				this.$refs.map_step2.mapObject.invalidateSize()
			}, 1000)
		}
	},
	beforeUpdate () {
	},
	methods: {
		formatNumber (num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		handleView () {
			if (this.url === 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}') {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				this.url = 'https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile'
        this.imageMap = false
			} else {
				this.url = 'https://mt0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}'
				this.imageMap = true
			}
		},
		handleChangeWidth (value) {
			this.data.land_details.front_side_width = value
		},
		handleChangeInsightWidth (value) {
			this.data.land_details.insight_width = value
		},
		handleDeleteMainArea (index) {
			this.$emit('deleteMainArea', index)
		},
		handleAddMainArea () {
			this.$emit('addMainArea')
		},
		handleChangeStatusPlanning (event) {
			if (event.target.checked) {
				this.$emit('changeStatusPlanning', true)
			} else {
				this.showConfirmPlanning = true
				this.message = 'Tất cả thông tin diện tích vi phạm quy hoạch sẽ bị xóa, bạn có chắc muôn bỏ phần diện tích quy hoạch đã nhập'
			}
		},
		handleCancelPlanning () {
			if (this.showConfirmPlanning) {
				this.checkShowPlanning = true
				this.showConfirmPlanning = false
			}
		},
		handleTurnOffPlanning () {
			this.showConfirmPlanning = false
			this.checkShowPlanning = false
			this.$emit('changeStatusPlanning', false)
		},
		handleAddPlanningArea () {
			this.$emit('addPlanningArea')
		},
		handleDeletePlanningArea (index) {
			this.$emit('deletePlanningArea', index)
		},
		changePositionType (index) {
			this.$emit('changePositionType', index)
		},
		changeUnitPrice (value, index) {
			this.$emit('changeUnitPrice', value, index)
		},
		changeLandPlanningPurpose (index, land_type_purpose_id) {
			this.$emit('changeLandPlanningPurpose', index, land_type_purpose_id)
		},
		changeLandTypePurpose (index, land_type_purpose_id, dataMainArea) {
			// let oldMainArea = JSON.parse(JSON.stringify(this.data.total_area))
			// let oldPlanningArea = JSON.parse(JSON.stringify(this.data.planning_area))
			// this.indexPurpose = index
			// this.land_type_purpose_id = land_type_purpose_id
			// let checkChangePurposeExist = this.checkPurposeChange(land_type_purpose_id, oldMainArea, oldPlanningArea)
			// if (checkChangePurposeExist) {
			//   this.isChangePurpose = true
			//   this.message = 'Bạn có muốn thay đổi mục đích sử dụng không'
			// } else {
			// }
			this.$emit('changeLandTypePurpose', index, land_type_purpose_id)
		},
		checkPurposeChange (id_wanna_change, oldMainArea, oldPlanningArea) {
			let checkArray = []
			oldMainArea.forEach(itemMainArea => {
				if (itemMainArea.land_type_purpose_id) {
					checkArray.push(itemMainArea.land_type_purpose_id)
				}
			})
			oldPlanningArea.forEach(itemPlanningArea => {
				if (itemPlanningArea.land_type_purpose_id) {
					checkArray.push(itemPlanningArea.land_type_purpose_id)
				}
			})
			let isNotChangeExist = checkArray.filter(item => item === id_wanna_change)
			if (isNotChangeExist && isNotChangeExist.length === 0) {
				return true
			} else return false
		},
		handleAcceptChangePurpose () {
			this.$emit('changeLandTypePurpose', this.indexPurpose, this.land_type_purpose_id)
		},
		handleCancelChangePurpose () {
			if (this.isChangePurpose) {
				this.checkShowPlanning = true
				this.isChangePurpose = false
			}
		},

		handleChangeMainArea (value, index) {
			this.$emit('handleChangeMainArea', value, index)
		},
		handleChangePlanningArea (value, index) {
			this.$emit('handleChangePlanningArea', value, index)
		}
	}
}
</script>
<style scoped lang="scss">
.form-map {
  height: 100%;
  flex: 1;
}

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
  margin-top: 15px;
}

.color-black {
  color: #333333;
}

.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: #FFFFFF;
  border-radius: 5.88235px;
  padding: 10px;
  // margin: auto;
  // width: 36px;
  // height: 36px;

  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
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
  top: 2.1rem;
  background: #FFFFFF;
  height: 2.1rem;
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
    margin-top: -2.85rem;
    background-color: #EEF9FF;
    border: 1px solid #007EC6;
    color: #446B92;
    display: inline-flex;
    @media (max-height: 660px) {
      font-size: 12px;
    }
    @media (max-height: 970px) and (min-height: 660px) {

    }
  }
  .main-map {
  position: relative;
  height: 100%;
  width: 100%;
  transition-timing-function: ease;
  transition-duration: 0.25s;
  overflow-x: hidden;
  @media (max-width: 1023px) {
    width: 100%;
  }
  .layer-map {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 0;
    transition-timing-function: ease;
    transition-duration: 0.25s;

  }
}
.btn-map {
  background: #FFFFFF;
  border-radius: 5px;
  border: 3px solid #FFFFFF;
  padding: 0;
  box-sizing: border-box;
  img{
    max-width: 50px;
    height: auto;
  }
}

.icon_marker{
  width: 25px;
}
.container_map {
  height: 300px;
}
.result_total_appraise {
  text-align: center;
  background: #EEF9FF;
  border: 1px solid #007EC6;
  border-radius: 3px;
  padding-top: 0.5rem;
  padding-bottom: 0.4rem;
}
.content_form {
  padding-left: 1.5rem;
}
.input-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  input {
    width: 22px;
    height: 22px;
    position: absolute;
    cursor: pointer;
    opacity: 0;
    &:checked {
      & ~ .check-mark {
        background-color: #DEE6EE;
        &:after {
          display: block;
        }
      }
    }
    &:disabled {
      & ~ .check-mark {
        background-color: #DEE6EE;
      }
    }
  }
  .check-mark {
    position: absolute;
    top: 0px;
    left: 0;
    cursor: pointer;
    width: 22px;
    height: 22px;
    font-size: 18px;
    font-weight: bold;
    color: #617F9E;
    // background-color: #617F9E;
    border: 2px solid #617F9E;
    border-radius: 4px;
    &:after {
      content: "\2713";
      position: absolute;
      display: none;
      left: 50%;
      top: -3px;
      width: 5px;
      height: 10px;
      // border: solid #FFFFFF;
      // border-width: 0 3px 3px 0;
      -webkit-transform: rotate(0deg) translate(-125%, -25%);
      -ms-transform: rotate(0deg) translate(-125%, -25%);
      transform: rotate(0deg) translate(-125%, -25%);
    }
  }
}
</style>
