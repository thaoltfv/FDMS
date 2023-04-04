<template>
  <div>
    <ValidationObserver tag="form"
                        ref="property"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
      >
        <div class="loading" :class="{'loading__true': isSubmit}">
          <a-spin />
        </div>
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Thông tin về quyền sử dụng đất</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="property-detail position-relative">
              <!-- <div class="row justify-content-between">
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
              </div> -->
              <div class="row justify-content-between">
                <div class="col-12 input-contain position-relative">
                  <InputText
                    v-model="form.coordinates"
                    vid="coordinates"
                    label="Tọa độ"
                    rules="required"
                    class="coordinates"
                  />
                  <img class="img-locate" src="../../../../assets/icons/ic_locate.svg" alt="locate" @click.prevent="handleOpenModalMap()">
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputNumberFormat
                    v-model="form.front_side_width"
                    vid="front_side_width"
                    label="Chiều rộng mặt tiền (m)"
                    :max="99999999"
                    :min="-99999999"
                    rules="required"
                    @change="frontSizeWidth($event)"
                    class=""
                  />
                  <span class="text-error" v-if="form.front_side_width !== '' && form.front_side_width !== undefined && form.front_side_width !== null && form.front_side_width <= 0">Vui lòng nhập chiều rộng mặt tiền thích hợp</span>
                  <span class="text-error" v-if="checkValueLongWidth">Chiều rộng mặt tiền đang lớn hơn chiều dài.Xin lưu ý về vấn đề này</span>
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputNumberFormat
                    v-model="form.insight_width"
                    vid="insight_width"
                    label="Chiều dài (m)"
                    :max="99999999"
                    :min="-99999999"
                    rules="required"
                    @change="insightWidth($event)"
                    :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    class=""
                  />
                  <span class="text-error" v-if="form.insight_width !== '' && form.insight_width !== undefined && form.insight_width !== null && form.insight_width <= 0">Vui lòng nhập chiều dài tiền thích hợp</span>
                  <span class="text-error" v-if="checkValueLongWidth">Chiều rộng mặt tiền đang lớn hơn chiều dài.Xin lưu ý về vấn đề này</span>
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.legal_id"
                    vid="legal_id"
                    label="Pháp lý"
                    rules="required"
                    class=""
                    :options="optionsJuridical"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.land_type_id"
                    vid="contact"
                    label="Loại đất"
                    rules="required"
                    class=""
                    :options="optionsLandType"
                    @change="landTypeId"
                  />
                </div>
                <!-- <div class="title">
                  <h4>Quy hoạch</h4>
                  <InputSwitchMDSD
                    v-model="showIsZoning"
                    vid="is_alley_with_connection"
                    label="Cơ sở chuyển MDSD"
                    class="contain-input label-none justify-content-center"
                  />
                </div> -->
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
                      <tr>
                        <th>Mục đích sử dụng</th>
                        <th>Diện tích thẩm định (m²)</th>
                        <th v-if="showIsZoning">Quy Hoạch</th>
                        <th v-if="showIsZoning">Loại quy hoạch</th>
                        <th v-if="showIsZoning">Diện tích quy hoạch</th>
                        <th>Vị trí đất</th>
                        <th>Đơn giá UBND</th>
                        <!-- <th>Hệ số K</th> -->
                        <th v-if="form.land_type_id === 56">Cơ sở chuyển MDSD</th>
                        <th v-if="form.property_detail.length > 1"></th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="(property_detail, index) in form.property_detail" :key="property_detail.id">
                        <td>
                          <InputCategory
                            v-model="property_detail.land_type_purpose_id"
                            :vid="'land_type_purpose' + index"
                            label="Mục đích sử dụng"
                            rules="required"
                            class="contain-input justify-content-center contain-input__scale contain-input__scale-one flexible-input-content"
                            :options="optionsTypePurpose"
                            @change="changeLandTypePurpose(index, form.property_detail)"
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
                            :step="0.01"
                            rules="required"
                            :disabled-input="property_detail.land_type_purpose_id === ''"
                            class="contain-input mr-0 justify-content-center contain-input__number contain-input__scale contain-input__scale-two"
                          />
                          <span v-show="property_detail.total_area !== '' && property_detail.total_area !== undefined && property_detail.total_area !== null && property_detail.total_area <= 0" class="text-error position-absolute">Nhập diện tích hợp lệ</span>
                        </td>
                        <td v-if="showIsZoning">
                          <InputSwitchZone
                            v-model="property_detail.is_zoning"
                            :vid="'is_zoning' + index"
                            label="Quy hoạch"
                            @change="changeZone($event,index)"
                            class="contain-input mr-0 justify-content-center contain-input__number contain-input__scale contain-input__scale-two"/>
                        </td>
                        <td v-if="showIsZoning">
                          <InputText
                            v-model="property_detail.type_zoning"
                            :vid="'type_zoning' + index"
                            class="contain-input justify-content-center contain-input__scale contain-input__scale-one flexible-input-content"
                            :rules="property_detail.is_zoning ? 'required' : ''"
                            :disabledInput="property_detail.is_zoning ? false : true"
                            @change="changeTypeZoning($event, index)"
                          />
                        </td>
                        <td v-if="showIsZoning">
                           <InputNumberFormat
                            v-model="property_detail.planning_area"
                            :vid="'planning_area' + index "
                            label="Diện tích quy hoạch"
                            @change="changePlanningArea($event, index)"
                            :max="99999999"
                            :min="-99999999"
                            :step="0.01"
                            :rules="property_detail.is_zoning ? 'required' : ''"
                            :disabled-input="property_detail.is_zoning ? false : true"
                            class="contain-input mr-0 justify-content-center contain-input__number contain-input__scale contain-input__scale-two"
                          />
                        </td>
                        <td>
                          <InputCategory
                            v-model="property_detail.position_type_id"
                            :vid="'position_type' + index"
                            label="vị trí đất"
                            rules="required"
                            :options="optionsPoints"
                            :disabled="property_detail.land_type_purpose_id === ''"
                            @change="changePositionType(index)"
                            class="contain-input justify-content-center contain-input__number contain-input__scale contain-input__scale-four"
                          />
                        </td>
                        <b-tooltip placement="right" :target="(`circular_unit_price${index}`).toString()">Đơn giá đất UBND sau khi đã nhân các hệ số điều chỉnh theo quy định của từng địa phương như: hệ số đường đất, hệ số vị trí, hệ số k, hệ số d,…</b-tooltip>
                        <td class="position-relative">
                          <InputNumberFormatHasID
                            :id="`circular_unit_price${index}`"
                            v-model="property_detail.circular_unit_price"
                            :vid="'circular_unit_price' + index"
                            label="Đơn giá"
                            :max="99999999999999"
                            :min="-99999999999999"
                            rules="required"
                            :disabled-input="property_detail.land_type_purpose_id === ''"
                            :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            @change="totalUnitPrice($event, index)"
                            class="contain-input mr-0 contain-input__number contain-input__scale contain-input__scale-five d-block"
                          />
                          <span v-show="property_detail.circular_unit_price !== '' && property_detail.circular_unit_price !== undefined && property_detail.circular_unit_price !== null && property_detail.circular_unit_price <= 0" class="text-error position-absolute">Nhập giá trị đơn giá hợp lệ</span>
                        </td>
                        <!-- <td class="position-relative" style="max-width: 145px">
                          <InputNumberFormat
                            v-model="property_detail.k_rate"
                            :vid="'k_rate' + index"
                            label="Hệ số K"
                            :min="-999999999"
                            :max="999999999"
                            :step="0.01"
                            :disabled-input="property_detail.land_type_purpose_id === ''"
                            @change="changeKRate($event, index)"
                            class="contain-input mr-0 justify-content-center contain-input__number contain-input__scale contain-input__scale-six"
                          />
                          <span v-show="property_detail.k_rate < 0 || property_detail.k_rate > 100" class="text-error position-absolute">Nhập hệ số k hợp lệ</span>
                        </td> -->
                        <td v-if="form.land_type_id === 56">
                          <InputSwitchMDSD
                            v-model="property_detail.is_transfer_facility"
                            vid="is_alley_with_connection"
                            label="Cơ sở chuyển MDSD"
                            class="contain-input label-none justify-content-center"
                            @input="changeTransfer(index)"
                          />
                        </td>
                        <td v-if="form.property_detail.length > 1">
                          <div class="btn-delete" @click="removeRowPropertyDetail(index, form.property_detail)">
                            <img src="../../../../assets/icons/ic_delete.svg" alt="delete">
                          </div>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                    <p v-show="duplicateLandTypePurpose" class="text-left text-danger mb-1" style="margin-left: 20px">Trùng mục đích</p>
                  </div>
                  <div class="btn-property" v-if ="form.land_type_id === 56 && form.property_detail.length <2">
                    <button class="btn btn-orange btn-white btn-add" type="button" @click="handleAdd(form.property_detail)">
                      <img src="../../../../assets/icons/ic_add-white.svg" alt="add">
                      Thêm
                    </button>
                  </div>
                </div>
              </div>
              <div class="row justify-content-between">
                <div class="col-12 col-lg-6 input-contain">
                  <label class="name font-weight-bold">Tổng diện tích thẩm định (m<sup>2</sup>)</label>
                  <div class="form-control disabled"><p v-if="form.appraise_land_sum_area" class="mb-0">{{parseFloat(form.appraise_land_sum_area).toFixed(2)}}</p></div>
                </div>
              </div>
            </div>
            <!--      table-->
            <div class="property-detail">
              <div class="row justify-content-between">
              </div>
            </div>
            <div class="property-detail">
              <h3 class="title-property">Đặc điểm chi tiết</h3>
              <h4 class="title-property" style="width: fit-content; background: rgba(252,194,114,0.53)">Giao thông</h4>
              <div class="row">
                <div class="col-12 col-lg-4 input-contain" >
                  <p class="front-side">Mặt tiền tuyến đường chính</p>
                  <div class="d-flex align-items-center">
                    <p class="mb-0 mr-2">Có</p>
                    <ToggleSwitch group="a" :options="frontSideOptions" @change="changeSwitch($event)"/>
                    <p class="mb-0 ml-2">Không</p>
                  </div>
                </div>
                <div class="col-12 col-lg-4 input-contain">
                  <p class="front-side">Căn góc</p>
                  <div class="d-flex align-items-center">
                    <p class="mb-0 mr-2">Có</p>
                    <ToggleSwitch group="c" :options="twoSidesLandOptions" @change="changeSidesLandSwitch($event)"/>
                    <p class="mb-0 ml-2">Không</p>
                  </div>
                </div>
                <div class="col-12 col-lg-4 input-contain" v-if="this.form.front_side !== 1">
                  <p class="front-side">Đường vào thửa đất</p>
                  <div class="d-flex align-items-center">
                    <p class="mb-0 mr-2">Có</p>
                    <ToggleSwitch group="b" :options="individualRoadOptions" @change="changeSwitchRoad($event)"/>
                    <p class="mb-0 ml-2">Không</p>
                  </div>
                </div>
              </div>
              <div class="row" v-if="form.front_side === 1">
                <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.material_id"
                    vid="material_id"
                    label="Chất liệu"
                    class=""
                    :options="optionsMaterial"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputNumberFormat
                    v-model="form.main_road_length"
                    vid="main_road_length"
                    label="Bề rộng đường (m)"
                    :max="999999999"
                    :min="-999999999"
                    :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    @change="mainRoadLength($event)"
                    class=""
                  />
                  <span class="text-error" v-if="form.main_road_length !== '' && form.main_road_length !== undefined && form.main_road_length !== null && form.main_road_length <= 0">Vui lòng nhập bề rộng đường tiền thích hợp</span>
                </div>
                <div class="col-12 col-lg-6 input-contain">
                </div>
              </div>
              <div class="card-table" v-if="form.front_side === 0">
                <div class="card-title">
                  <div class="d-flex justify-content-between align-items-center">
                    <h3 class="title">Thông tin hẻm</h3>
                    <img class="img-dropdown" :class="!showScale? 'img-dropdown__hide' : ''" src="../../../../assets/images/icon-btn-down.svg" alt="dropdown" @click=" showScale = !showScale">
                  </div>
                </div>
                <div class="card-body card-info card-land" v-if="showScale">
                  <div class="contain-table">
                    <table class="table-property">
                      <thead>
                      <tr>
                        <th>Số lần rẽ/quẹo</th>
                        <th>Chất liệu đường </th>
                        <th>Mặt đường (m)</th>
                        <th>Đấu nối đường chính</th>
                        <th>Khoảng cách đến đường chính (m)</th>
                        <th v-if="form.property_turning_time.length > 1"></th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="(turning, index) in form.property_turning_time" :key="turning.id">
                        <td>
                          <InputText
                            v-model="turning.turning"
                            vid="land_type_purpose"
                            rules="required"
                            disabled-input
                            class="contain-input contain-input__scale contain-input__alley "
                          />
                        </td>
                        <td>
                          <InputCategory
                            v-model="turning.material_id"
                            vid="position_type"
                            label="Chất liệu đường"
                            rules="required"
                            class="contain-input justify-content-center contain-input__scale contain-input__alley contain-input__road"
                            :options="optionsMaterial"
                            @change="changeMaterial"
                          />
                        </td>
                        <td class="position-relative">
                          <InputNumberFormat
                            v-model="turning.main_road_length"
                            vid="main_road_length"
                            label="Mặt đường"
                            :max="999999"
                            :min="-999999"
                            @change="roadMain($event, index)"
                            :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            class="contain-input mr-0 contain-input__scale contain-input__alley contain-input__road"
                          />
                          <span v-show="turning.main_road_length !== '' && turning.main_road_length !== undefined && turning.main_road_length !== null && turning.main_road_length <= 0" class="text-error position-absolute">Nhập mặt đường hợp lệ</span>
                        </td>
                        <td>
                          <div class="d-flex justify-content-center">
                            <InputSwitch
                              v-model="turning.is_alley_with_connection"
                              vid="is_alley_with_connection"
                              label="Đấu nối đường chính"
                              class="contain-input contain-input__scale contain-input__alley"
                            />
                          </div>
                        </td>
                        <td class="position-relative">
                          <InputNumberFormat
                            v-model="turning.main_road_distance"
                            vid="main_road_length"
                            label="Khoảng cách"
                            :max="999999"
                            :min="-999999"
                            @change="changDistance($event, index)"
                            :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            class="contain-input justify-content-center mr-0 contain-input__scale contain-input__alley"
                          />
                          <span v-show="turning.main_road_distance !== '' && turning.main_road_distance !== undefined && turning.main_road_distance !== null && turning.main_road_distance <= 0" class="text-error position-absolute">Nhập mặt đường hợp lệ</span>
                        </td>
                        <td v-if="form.property_turning_time.length > 1">
                          <div class="btn-delete" @click="removeTurning(index)">
                            <img src="../../../../assets/icons/ic_delete.svg" alt="delete">
                          </div>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="btn-property">
                    <button class="btn btn-orange btn-white btn-add" type="button" @click="handleAddTurning">
                      <img src="../../../../assets/icons/ic_add-white.svg" alt="add">
                      Thêm
                    </button>
                  </div>
                </div>
              </div>
              <div class="row justify-content-between">
                <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.land_shape_id"
                    vid="land_shape_id"
                    label="Hình dáng"
                    rules="required"
                    class="traffic-input"
                    :options="optionsLandShape"
                  />
                </div>
                <div class="w-100"/>
                <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.business_id"
                    vid="business_id"
                    label="Kinh doanh"
                    rules="required"
                    class="traffic-input"
                    :options="optionsBusiness"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.condition_id"
                    vid="condition_id"
                    label="Cơ sở hạ tầng"
                    rules="required"
                    class="traffic-input"
                    :options="optionsConditions"
                  />
                </div>
              </div>
            </div>
            <div class="row justify-content-between">
              <div class="col-12 col-lg-6 input-contain">
                <InputCategory
                  v-model="form.social_security_id"
                  vid="social_security_id"
                  label="An ninh trật tự, xã hội"
                  rules="required"
                  class="traffic-input"
                  :options="optionsSocialSecurity"
                />
              </div>
              <div class="col-12 col-lg-6 input-contain">
                <InputCategory
                  v-model="form.feng_shui_id"
                  vid="feng_shui_id"
                  label="Phong thủy"
                  rules="required"
                  class="traffic-input"
                  :options="optionsFengShui"
                />
              </div>
              <div class="col-12 col-lg-6 input-contain">
                <InputCategory
                  v-model="form.zoning_id"
                  vid="zoning_id"
                  label="Quy hoạch/hiện trạng"
                  class="traffic-input"
                  rules="required"
                  :options="optionsZoning"
                />
              </div>
              <div class="col-12 col-lg-6 input-contain">
                <InputCategory
                  v-model="form.paymen_method_id"
                  vid="payment_method_id"
                  label="Điều kiện thanh toán"
                  rules="required"
                  class="traffic-input"
                  :options="optionsPaymentMethod"
                />
              </div>
              <div class="col-12 input-contain">
                <InputText
                  v-model="form.description"
                  vid="description"
                  label="Mô tả vị trí"
                  rules="required"
                  class=""
                />
              </div>
            </div>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange btn-action-modal" :class="{'btn-loading disabled': isSubmit}"> <img src="../../../../assets/icons/ic_save.svg"  style="margin-right: 12px" alt="save">Lưu</button>
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"> <img src="../../../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save"> Trở lại</button>
            </div>
          </div>
        </div>
        <ModalMap
          v-if="openModalMap"
          @cancel="openModalMap = false"
          :location="location"
          :address="full_address"
          :center_map="form.coordinates"
          @action="handleCoordinates"
        />
      </div>
    </ValidationObserver>
    <ModalDeleteIndex
      v-if="openModalDelete"
      @cancel="openModalDelete = false"
      :index_delete="this.index_delete"
      @action="handleDeleteTuring"
    />
  </div>
</template>

<script>
import InputNumberFormatHasID from '@/components/Form/InputNumberFormatHasID'
import InputSwitchMDSD from '@/components/Form/InputSwitchMDSD'
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import InputSwitchZone from '@/components/Form/InputSwitchZone'
import InputRadio from '@/components/Form/InputRadio'
import InputNumberFormat from '@/components/Form/InputNumber'
import ModalDeleteIndex from '@/components/Modal/ModalDeleteIndex'
import WareHouse from '@/models/WareHouse'
import ModalMap from '@/components/Modal/ModalMap'
import ToggleSwitch from '@/components/Form/ToggleSwitch'
import {BTooltip} from 'bootstrap-vue'

export default {
	name: 'ModalLandProperty',
	props: ['property', 'property_index', 'info', 'unit_price', 'street', 'frontSideOptions', 'twoSidesLandOptions', 'individualRoadOptions', 'coordinates', 'full_address', 'doc_no', 'land_no'],
	data () {
		return {
			checkValueLongWidth: false,
			showErrorArea: false,
			main_road_length: '',
			material: '',
			cicular_unit_price: {},
			location: {
				lng: '',
				lat: ''
			},
			type_purpose_name: '',
			checkSubmit: [],
			error_duplicate: [],
			submit: false,
			message_error: [],
			openModalDelete: false,
			openModalMap: false,
			isSubmit: false,
			showScale: true,
			zoning: [],
			juridicals: [],
			landShapes: [],
			roughes: [],
			images: [],
			conditions: [],
			socialSecurities: [],
			businesses: [],
			paymentMethods: [],
			fengshuies: [],
			zones: [],
			type_purposes: [],
			materials: [],
			landType: [],
			points: [],
			image: null,
			link: '',
			type: '',
			file: null,
			count: 2,
			form: {
				coordinates: '',
				legal_id: '',
				appraise_land_sum_area: 0,
				front_side: '',
				individual_road: '',
				zoning_id: '',
				land_type_id: '',
				front_side_width: '',
				insight_width: '',
				land_shape_id: '',
				description: '',
				main_road_length: '',
				material_id: '',
				business_id: '',
				social_security_id: '',
				feng_shui_id: '',
				paymen_method_id: '',
				condition_id: '',
				land_no: '',
				doc_no: '',
				two_sides_land: null,
				front_side_switch: true,
				individual_road_switch: false,
				turning_time: '',
				traffic_strength_weekness_id: '',
				land_type_purpose: '',
				property_detail: [
					{
						convert_fee: '',
						compare_property_id: '',
						land_type_purpose_id: '',
						total_area: '',
						circular_unit_price: '',
						land_type_purpose_data: {
							description: ''
						},
						position_type_id: '',
						description: '',
						k_rate: '',
						is_transfer_facility: false,
						planning_area: 0,
						is_zoning: false,
						type_zoning: ''
					}
				],
				property_turning_time: []
			},
			duplicateLandTypePurpose: false,
			showZone: false,
			showIsZoning: true
		}
	},
	components: {
		InputCategory,
		InputNumberFormat,
		InputText,
		InputSwitch,
		ModalMap,
		ModalDeleteIndex,
		ToggleSwitch,
		InputRadio,
		InputSwitchZone,
		InputSwitchMDSD,
		InputNumberFormatHasID,
		'b-tooltip': BTooltip
	},
	computed: {
		optionsJuridical () {
			return {
				data: this.juridicals,
				id: 'id',
				key: 'description'
			}
		},
		optionsLandType () {
			return {
				data: this.landType,
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
		},
		optionsSocialSecurity () {
			return {
				data: this.socialSecurities,
				id: 'id',
				key: 'description'
			}
		},
		optionsBusiness () {
			return {
				data: this.businesses,
				id: 'id',
				key: 'description'
			}
		},
		optionsPaymentMethod () {
			return {
				data: this.paymentMethods,
				id: 'id',
				key: 'description'
			}
		},
		optionsFengShui () {
			return {
				data: this.fengshuies,
				id: 'id',
				key: 'description'
			}
		},
		optionsZoning () {
			return {
				data: this.zones,
				id: 'id',
				key: 'description'
			}
		},
		optionsMaterial () {
			return {
				data: this.materials,
				id: 'id',
				key: 'description'
			}
		},
		optionsRough () {
			return {
				data: this.roughes,
				id: 'id',
				key: 'description'
			}
		},
		optionsTypePurpose () {
			return {
				data: this.type_purposes,
				id: 'id',
				key: 'description'
			}
		},
		optionsConditions () {
			return {
				data: this.conditions,
				id: 'id',
				key: 'description'
			}
		},
		optionsPoints () {
			return {
				data: this.points,
				id: 'id',
				key: 'description'
			}
		}
	},
	mounted () {
		this.getPropertyEdit()
	},
	methods: {
		changeZone (event, index) {
			if (event) {
				this.showZone = true
				this.form.property_detail[index].is_zoning = event
			} else {
				if (this.form.property_detail) {
					this.form.property_detail[index].is_zoning = false
					this.form.property_detail[index].type_zoning = ''
					this.form.property_detail[index].planning_area = 0
				}

				this.showZone = false
			}
		},
		changeTypeZoning (event, index) {
			if (event) {
				this.form.property_detail[index].type_zoning = event
			} else {
				this.form.property_detail[index].type_zoning = ''
			}
		},
		changePlanningArea (event, index) {
			if (event) {
				this.form.property_detail[index].planning_area = parseFloat(+event).toFixed(2)
				// if(+event > this.form.property_detail[index].total_area){
				//   this.showErrorArea = true
				// } else {
				//   this.showErrorArea = false
				// }
			} else {
				this.form.property_detail[index].planning_area = 0
			}
		},
		async changePositionType (index) {
			if (this.form !== '' && this.form !== undefined && this.form !== null) {
				await this.findLandTypePurpose(index)
			}
			await this.getAddress()
			if (this.cicular_unit_price !== undefined && this.cicular_unit_price !== null) {
				if (this.form.property_detail[index].position_type_id === this.points[0].id) {
					this.form.property_detail[index].circular_unit_price = this.cicular_unit_price.vt1
				} else if (this.form.property_detail[index].position_type_id === this.points[1].id) {
					this.form.property_detail[index].circular_unit_price = this.cicular_unit_price.vt2
				} else if (this.form.property_detail[index].position_type_id === this.points[2].id) {
					this.form.property_detail[index].circular_unit_price = this.cicular_unit_price.vt3
				} else if (this.form.property_detail[index].position_type_id === this.points[3].id) {
					this.form.property_detail[index].circular_unit_price = this.cicular_unit_price.vt4
				}
			} else {
				this.form.property_detail[index].circular_unit_price = ''
			}
		},
		async getAddress () {
			await this.getUnitPrice(this.unit_price.province, this.unit_price.district, this.unit_price.ward, this.unit_price.street, this.unit_price.distance, this.type_purpose_name)
		},
		getPropertyEdit () {
			if (this.property) {
				this.form = this.property
				if (this.form) {
					this.form.property_detail.forEach(property => {
						if (property.is_zoning) {
							this.showZone = true
						}
					})
				}
			} else {
				this.form.coordinates = this.coordinates
				this.form.doc_no = this.doc_no
				this.form.land_no = this.land_no
			}
		},
		findLandTypePurpose (index) {
			const type_purpose = this.type_purposes.find(type_purpose => type_purpose.id === this.form.property_detail[index].land_type_purpose_id)
			this.type_purpose_name = type_purpose.description
		},
		async changeLandTypePurpose (index, array) {
			this.form.property_detail[index].position_type_id = ''
			this.form.property_detail[index].circular_unit_price = ''
			this.duplicateLandTypePurpose = this.checkDuplicateLandType(array)
		},
		async getUnitPrice (province, district, ward, street, distance, land_type) {
			this.isSubmit = true
			const resp = await WareHouse.getUnitPrice(province, district, ward, street, distance, land_type)
			this.cicular_unit_price = resp.data
			this.isSubmit = false
		},
		getProperty () {
			if (this.property !== '' && this.property !== null && this.property !== undefined) {
				this.form = this.property
			}
		},
		landTypeId () {
			if (this.form.property_detail.length > 1) {
				this.form.property_detail = []
				this.form.property_detail.push({
					compare_property_id: '',
					land_type_purpose: '',
					total_area: '',
					position_type: '',
					k_rate: '',
					is_transfer_facility: false
				})
			}
			if (this.form.land_type_id === 56) {
				this.form.property_detail[0].is_transfer_facility = false
			} else {
				this.form.property_detail[0].is_transfer_facility = true
			}
		},
		async handleCoordinates (coordinates) {
			this.form.coordinates = coordinates
			this.location.lat = coordinates.split(',')[0]
			this.location.lng = coordinates.split(',')[1]
		},
		handleOpenModalMap () {
			this.openModalMap = true
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatArea (value) {
			let num = (value / 1).toString().replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		convertFee (event) {
			this.form.convert_fee = event
		},
		changeDocNo (event) {
			if (event) {
				this.form.doc_no = parseInt(event)
			} else {
				this.form.doc_no = ''
			}
		},
		changeLandNo (event) {
			if (event) {
				this.form.land_no = parseInt(event)
			} else {
				this.form.land_no = ''
			}
		},
		frontSizeWidth (event) {
			if (event) {
				this.form.front_side_width = parseFloat(event).toFixed(2)
				if (event > this.form.insight_width ? +this.form.insight_width : 0) {
					this.checkValueLongWidth = true
				} else {
					this.checkValueLongWidth = false
				}
			} else {
				this.form.front_side_width = ''
				this.checkValueLongWidth = false
			}
		},
		insightWidth (event) {
			if (event) {
				this.form.insight_width = parseFloat(event).toFixed(2)
				if (event < this.form.front_side_width ? +this.form.front_side_width : 0) {
					this.checkValueLongWidth = true
				} else {
					this.checkValueLongWidth = false
				}
			} else {
				this.form.insight_width = ''
				this.checkValueLongWidth = false
			}
		},
		mainRoadLength (event) {
			if (event) {
				this.form.main_road_length = parseFloat(event).toFixed(2)
			} else {
				this.form.main_road_length = ''
			}
		},
		totalUnitPrice (event, index) {
			if (event) {
				this.form.property_detail[index].circular_unit_price = +event
			} else {
				this.form.property_detail[index].circular_unit_price = ''
			}
		},
		changeKRate (event, index) {
			if (event) {
				this.form.property_detail[index].k_rate = +event
			} else {
				this.form.property_detail[index].k_rate = ''
			}
		},
		changeTransfer (index) {
			if (this.form.property_detail.length === 2) {
				if (index === 0) {
					this.form.property_detail[1].is_transfer_facility = !this.form.property_detail[index].is_transfer_facility
				} else if (index === 1) {
					this.form.property_detail[0].is_transfer_facility = !this.form.property_detail[index].is_transfer_facility
				}
			}
		},
		titleCase (str) {
			var splitStr = str.toLowerCase().split(' ')
			for (var i = 0; i < splitStr.length; i++) {
				// You do not need to check if i is larger than splitStr length, as your for does that for you
				// Assign it back to the array
				splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1)
			}
			// Directly return the joined string
			return splitStr.join(' ')
		},
		async roadMain (event, index) {
			for (let i = 0; i < this.form.property_turning_time.length; i++) {
				if (i === index) {
					if (event) {
						this.form.property_turning_time[i].main_road_length = +event
					} else {
						this.form.property_turning_time[i].main_road_length = ''
					}
				}
			}
			if (this.property !== '' && this.property !== undefined && this.property !== null) {
				await this.findMaterial()
			}
			let streetName = ''
			streetName = this.unit_price.street.toLowerCase().includes('đường') ? this.titleCase(this.unit_price.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.unit_price.street)}`
			const last_item = this.form.property_turning_time.length - 1
			this.main_road_length = this.form.property_turning_time[last_item].main_road_length
			if (this.form.front_side === 0) {
				this.form.description = 'Tiếp giáp ' + this.material.toLowerCase() + ' rộng khoảng ' + this.formatFloat(this.main_road_length) + 'm ' + 'gần tuyến ' + `${streetName}`
			}
		},
		changDistance (event, index) {
			for (let i = 0; i < this.form.property_turning_time.length; i++) {
				if (i === index) {
					if (event) {
						this.form.property_turning_time[i].main_road_distance = +event
					} else {
						this.form.property_turning_time[i].main_road_distance = ''
					}
				}
			}
		},
		findMaterial () {
			const last_item = this.form.property_turning_time.length - 1
			let streetName = ''
			streetName = this.unit_price.street.toLowerCase().includes('đường') ? this.titleCase(this.unit_price.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.unit_price.street)}`
			this.materials.forEach(material => {
				if (material.id === this.form.property_turning_time[last_item].material_id) {
					this.material = material.description
				}
			})
			if (this.form.front_side === 0) {
				this.form.description = 'Tiếp giáp ' + this.material.toLowerCase() + ' rộng khoảng ' + this.formatFloat(this.main_road_length) + 'm ' + 'gần tuyến ' + `${streetName}`
			}
		},
		roadSide (event, index) {
			for (let i = 0; i < this.form.property_turning_time.length; i++) {
				if (i === index) {
					this.form.property_turning_time[i].roadside_length = +event
				}
			}
		},
		totalArea (event, index) {
			for (let i = 0; i < this.form.property_detail.length; i++) {
				if (i === index) {
					if (event) {
						this.form.property_detail[i].total_area = parseFloat(+event).toFixed(2)
					} else {
						this.form.property_detail[i].total_area = ''
					}
				}
			}
			const totalArea = this.form.property_detail
			let total = 0
			totalArea.forEach(item => {
				total = total + +item.total_area
			})
			this.form.appraise_land_sum_area = parseFloat(total)
		},
		handleAddTurning () {
			this.form.property_turning_time.push({
				main_road_length: '',
				turning: 'Hẻm số ' + (this.form.property_turning_time.length + 1),
				roadside_length: '',
				main_road_distance: '',
				is_alley_with_connection: false,
				material_id: '',
				description: ''
			})
			this.count++
		},
		handleAdd (array) {
			let isDuplicate = this.checkDuplicateLandType(array)
			if (!isDuplicate) {
				this.counter++
				this.form.property_detail.push({
					description: '',
					land_type_purpose_data: {
						description: ''
					},
					circular_unit_price: '',
					convert_fee: '',
					compare_property_id: '',
					land_type_purpose_id: '',
					total_area: '',
					position_type_id: '',
					k_rate: '',
					is_zoning: false,
					type_zoning: '',
					planning_area: 0,
					is_transfer_facility: false
				})
			}
		},
		checkDuplicateLandType (array) {
			this.duplicateLandTypePurpose = false
			const valueArr = array.map((item) => { return item.land_type_purpose_id })
			return valueArr.some((item, idx) => {
				return valueArr.indexOf(item) !== idx
			})
		},
		removeTurning (index) {
			this.openModalDelete = true
			this.index_delete = index
		},
		handleDeleteTuring (event, index) {
			if (this.form.property_turning_time.length > 1) {
				this.form.property_turning_time.splice(index, 1)
				this.form.property_turning_time.forEach((property_turning_time, i) => {
					property_turning_time.turning = 'Hẻm số ' + (i + 1)
				})
			}
			const last_item = this.form.property_turning_time.length - 1
			this.main_road_length = this.form.property_turning_time[last_item].main_road_length
			this.materials.forEach(material => {
				if (material.id === this.form.property_turning_time[last_item].material_id) {
					this.material = material.description
				}
			})
			let streetName = ''
			streetName = this.unit_price.street.toLowerCase().includes('đường') ? this.titleCase(this.unit_price.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.unit_price.street)}`
			if (this.form.front_side === 0) {
				this.form.description = 'Tiếp giáp ' + this.material.toLowerCase() + ' rộng khoảng ' + this.formatFloat(this.main_road_length) + 'm ' + 'gần tuyến ' + `${streetName}`
			}
		},
		removeRowPropertyDetail (index, array) {
			this.form.property_detail.splice(index, 1)
			const total = this.form.property_detail
			let totalArea = 0
			total.forEach(item => {
				totalArea = totalArea + parseFloat(item.total_area)
			})
			this.form.appraise_land_sum_area = totalArea
			this.duplicateLandTypePurpose = this.checkDuplicateLandType(array)
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async handleAction (event) {
			this.isSubmit = true
			try {
				this.isSubmit = false
				const data = this.form
				const property_index = this.property_index
				this.landShapes.forEach(landShape => {
					if (landShape.id === data.land_shape_id) {
						data.land_shape = landShape
					}
				})
				if (this.form.doc_no < 0 || this.form.land_no < 0 || this.form.front_side_width <= 0 || this.form.insight_width <= 0 || (this.form.front_side === 1 && this.form.main_road_length <= 0)) {
					this.$toast.open({
						message: 'Vui lòng nhập giá trị thích hợp trước khi lưu',
						type: 'error',
						position: 'top-right'
					})
				} else {
					this.$emit('cancel', event)
					this.$emit('action', data, property_index)
					if (this.property !== '' && this.property !== null && this.property !== undefined) {
						this.$toast.open({
							message: 'Cập nhật thông tin thửa đất thành công',
							type: 'success',
							position: 'top-right'
						})
					} else {
						this.$toast.open({
							message: 'Thêm mới thông tin thửa đất thành công',
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
		async changeMaterial () {
			if (this.property !== '' && this.property !== undefined && this.property !== null) {
				await this.findRoadMain()
			}
			const last_item = this.form.property_turning_time.length - 1
			this.materials.forEach(material => {
				if (material.id === this.form.property_turning_time[last_item].material_id) {
					this.material = material.description
				}
			})
			let streetName = ''
			streetName = this.unit_price.street.toLowerCase().includes('đường') ? this.titleCase(this.unit_price.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.unit_price.street)}`
			if (this.form.front_side === 0) {
				this.form.description = 'Tiếp giáp ' + this.material.toLowerCase() + ' rộng khoảng ' + this.formatFloat(this.main_road_length) + 'm ' + 'gần tuyến ' + `${streetName}`
			}
		},
		findRoadMain () {
			const last_item = this.form.property_turning_time.length - 1
			this.main_road_length = this.form.property_turning_time[last_item].main_road_length
			let streetName = ''
			streetName = this.unit_price.street.toLowerCase().includes('đường') ? this.titleCase(this.unit_price.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.unit_price.street)}`
			if (this.form.front_side === 0) {
				this.form.description = 'Tiếp giáp ' + this.material.toLowerCase() + ' rộng khoảng ' + this.formatFloat(this.main_road_length) + 'm ' + 'gần tuyến ' + `${streetName}`
			}
		},
		changeSwitch (event) {
			let streetName = ''
			streetName = this.unit_price.street.toLowerCase().includes('đường') ? this.titleCase(this.unit_price.street).replace('Đường', 'đường') : `đường ${this.titleCase(this.unit_price.street)}`
			if (event.value === 'Yes') {
				this.form.front_side = 1
				this.form.individual_road_switch = false
				this.form.main_road_distance = ''
				this.form.turning_time = ''
				this.form.property_turning_time = []
				this.form.description = 'Tiếp giáp mặt tiền ' + `${streetName}`
			} else if (event.value === 'No') {
				this.form.front_side = 0
				this.form.description = 'Tiếp giáp ' + this.material.toLowerCase() + ' rộng khoảng ' + this.formatFloat(this.main_road_length) + 'm ' + 'gần tuyến ' + `${streetName}`
				if (this.form.property_turning_time.length === 0) {
					this.form.property_turning_time.push({
						main_road_length: '',
						turning: 'Hẻm số 1',
						roadside_length: '',
						main_road_distance: '',
						is_alley_with_connection: false,
						material_id: '',
						description: ''
					})
				}
				this.form.material_id = ''
				this.form.roadside_length = ''
				this.form.main_road_length = ''
			} else {
				this.form.front_side = null
				this.form.description = ''
				this.form.property_turning_time = []
			}
		},
		changeSidesLandSwitch (event) {
			if (event.value === 'Yes') {
				this.form.two_sides_land = true
			} else if (event.value === 'No') {
				this.form.two_sides_land = false
			} else {
				this.form.two_sides_land = null
			}
		},
		changeSwitchRoad (event) {
			if (event.value === 'Yes') {
				this.form.individual_road = 1
			} else if (event.value === 'No') {
				this.form.individual_road = 0
			} else {
				this.form.individual_road = ''
			}
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.property.validate()
			if (this.form.front_side === '' || this.form.front_side === undefined || this.form.front_side === null) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng chọn mặt tiền',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.form.two_sides_land === '' || this.form.two_sides_land === undefined || this.form.two_sides_land === null) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng chọn căn góc',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.form.front_side === 0 && (this.form.individual_road === '' || this.form.individual_road === undefined || this.form.individual_road === null)) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng chọn đường vào thửa đất ',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.form.land_type_id === 56 && this.form.property_detail.find(property_detail => property_detail.is_transfer_facility === true) === undefined) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng chọn cơ sở chuyển MDSD',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.form.property_detail.find(property_detail => +property_detail.total_area <= 0 || property_detail.circular_unit_price <= 0 || property_detail.k_rate < 0 || property_detail.k_rate > 100)) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập giá trị quy mô hợp lệ trước khi lưu',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.form.property_turning_time.find(property_turning => property_turning.main_road_length <= 0 || property_turning.main_road_distance <= 0)) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập hẻm hợp lệ trước khi lưu',
					type: 'error',
					position: 'top-right'
				})
			} else if (this.form.property_detail.find(property_detail => +property_detail.total_area < +property_detail.planning_area)) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Diện tích quy hoạch không được lớn hơn diện tích đất gốc. Vui lòng kiểm tra lại.',
					type: 'error',
					position: 'top-right'
				})
			} else if (isValid) {
				this.isSubmit = true
				await this.handleAction()
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async getRough () {
			try {
				const resp = await WareHouse.getRough()
				this.roughes = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDictionary () {
			try {
				const resp = await WareHouse.getDictionaries()
				this.juridicals = [...resp.data.phap_ly]
				this.landType = [...resp.data.loai_dat]
				this.landShapes = [...resp.data.hinh_dang_dat]
				this.socialSecurities = [...resp.data.an_ninh_moi_truong_song]
				this.businesses = [...resp.data.kinh_doanh]
				this.paymentMethods = [...resp.data.dieu_kien_thanh_toan]
				this.conditions = [...resp.data.dieu_kien_ha_tang]
				this.fengshuies = [...resp.data.phong_thuy]
				this.zones = [...resp.data.quy_hoach_hien_trang]
				this.type_purposes = [...resp.data.loai_dat_chi_tiet]
				this.materials = [...resp.data.giao_thong_chat_lieu]
				this.roughes = [...resp.data.giao_thong]
				this.points = [...resp.data.vi_tri_dat]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	async beforeMount () {
		this.isSubmit = true
		await this.getDictionary()
		await this.getRough()
		this.isSubmit = false
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
    max-width: 1350px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 35px 60px;
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
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);;
  background: #FFFFFF;
  width: 99%;
  margin: 50px auto 75px ;
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
      @media (max-width: 767px) {

      }
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
.btn-orange {
  background: #FAA831;
  text-align: center;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
  height: 35px;
  width: 100px;
  color: #fff;
  margin-right: 15px;
  box-sizing: border-box;
  &:hover{
    border-color: #dc8300;
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
      color: #000000;
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
.errors-messages {
  color: #cd201f;
  position: absolute;
  font-size: 12px;
}
.front-side{
  font-weight: 700;
}
.text-error{
  left: 14px;
  color: #cd201f;
  font-size: 12px;
}
</style>
