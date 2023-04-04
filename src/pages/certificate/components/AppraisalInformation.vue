<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Thông tin chung về tài sản thẩm định</h3>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard">
      <div class="container-fluid">
        <div class="row justify-content-between">
          <InputText
            v-model="data.id"
            vid="id"
            label="Mã tài sản"
            disabled-input
            class="col-12 col-md-4 col-lg-3 form-group-container"
          />
          <InputText
            v-model="status"
            vid="status"
            label="Trạng thái"
            disabled-input
            class="col-12 col-md-4 col-lg-3 form-group-container"
          />
          <div class="col-12 col-md-4 col-lg-4"/>
<!--          <div class="col-12 col-md-4 col-lg-3">-->
<!--            <InputNumberFormat-->
<!--              v-model="data.ticket_num"-->
<!--              vid="ticket_num"-->
<!--              label="Mã nhiệm vụ Base"-->
<!--              rules="required"-->
<!--              :max="999999999"-->
<!--              :min="-999999999"-->
<!--              @change="changeBase($event)"-->
<!--              class="form-group-container"-->
<!--            />-->
<!--            <span class="text-error" v-if="data.ticket_num < 0">Vui lòng nhập mã nhiệm vụ thích hợp</span>-->
<!--          </div>-->
<!--          <InputText-->
<!--            v-model="data.document_num"-->
<!--            vid="document_num"-->
<!--            label="Số CVĐ"-->
<!--            rules="required"-->
<!--            class="col-12 col-md-4 col-lg-3 form-group-container"-->
<!--          />-->
<!--          <InputDatePicker-->
<!--            v-model='form.document_date'-->
<!--            vid="document_date"-->
<!--            label="Ngày CVĐ"-->
<!--            rules="required"-->
<!--            :formatDate="'DD/MM/YYYY'"-->
<!--            @change="changeDocumentDate"-->
<!--            class="col-12 col-md-4 col-lg-4 form-group-container"-->
<!--          />-->
<!--          <InputText-->
<!--            v-model="data.certificate_num"-->
<!--            vid="certificate_num"-->
<!--            label="Số chứng thư"-->
<!--            class="col-12 col-md-4 col-lg-3 form-group-container"-->
<!--          />-->
<!--          <InputDatePicker-->
<!--            v-model='form.certificate_date'-->
<!--            vid="certificate_date"-->
<!--            label="Ngày chứng thư"-->
<!--            class="col-12 col-md-4 col-lg-3 form-group-container"-->
<!--            :formatDate="'DD/MM/YYYY'"-->
<!--            :date="disabledDate"-->
<!--            @change="changeCertificate"-->
<!--          />-->
<!--          <div class="col-12 col-md-4 col-lg-4"/>-->
<!--          <InputText-->
<!--            v-model="data.petitioner_name"-->
<!--            vid="base"-->
<!--            label="Tên KH yêu cầu (trên Chứng thư)"-->
<!--            :max="9999999"-->
<!--            class="col-12 col-md-4 col-lg-3 form-group-container"-->
<!--          />-->
<!--          <InputText-->
<!--            v-model="data.petitioner_phone"-->
<!--            vid="petitioner_phone"-->
<!--            type="number"-->
<!--            :max-length="11"-->
<!--            label="Điện thoại"-->
<!--            class="col-12 col-md-4 col-lg-3 form-group-container"-->
<!--          />-->
<!--          <InputText-->
<!--            v-model='data.petitioner_address'-->
<!--            vid="petitioner_address"-->
<!--            label="Địa chỉ"-->
<!--            class="col-12 col-md-4 col-lg-4 form-group-container"-->
<!--          />-->
          <InputCategory
            v-model="data.asset_type_id"
            vid="asset_type_id"
            label="Loại tài sản"
            rules="required"
            class="col-12 col-md-4 col-lg-3 form-group-container"
            :options="optionsType"
            @change="changeAssetType($event)"
          />
          <div class="col-12 col-md-4 col-lg-3"/>
          <div class="col-12 col-md-4 col-lg-4"/>
<!--          <InputDatePicker-->
<!--            v-model="form.appraise_date"-->
<!--            vid="appraise_date"-->
<!--            label="Thời điểm thẩm định"-->
<!--            rules="required"-->
<!--            :formatDate="'DD/MM/YYYY'"-->
<!--            class="col-12 col-md-4 col-lg-3 form-group-container"-->
<!--            @change="changeAppraiseDate"-->
<!--          />-->
<!--          <InputCategory-->
<!--            v-model="data.appraise_purpose_id"-->
<!--            vid="appraise_purpose_id"-->
<!--            label="Mục đích thẩm định"-->
<!--            rules="required"-->
<!--            class="col-12 col-md-4 col-lg-4 form-group-container"-->
<!--            :options="optionsAppraisalPurposes"-->
<!--          />-->
          <InputCategory
            v-model="data.province_id"
            vid="province_id"
            label="Tỉnh/Thành"
            rules="required"
            class="col-12 col-md-4 col-lg-3 form-group-container"
            :options="optionsProvince"
            @change="changeProvince($event)"
          />
          <InputCategory
            v-model="data.district_id"
            vid="district_id"
            label="Quận/Huyện"
            rules="required"
            class="col-12 col-md-4 col-lg-3 form-group-container"
            @change="changeDistrict($event)"
            :options="optionsDistrict"
          />
          <InputCategory
            v-model="data.ward_id"
            vid="ward_id"
            label="Phường/Xã"
            rules="required"
            class="col-12 col-md-4 col-lg-4 form-group-container"
            :options="optionsWard"
            @change="changeWard($event)"
          />

          <InputCategory
            v-model="data.street_id"
            vid="street_id"
            label="Đường"
            rules="required"
            class="col-12 col-md-6 col-lg-3 form-group-container"
            @change="changeStreet($event)"
            :options="optionsStreet"
          />
          <InputCategory
            v-model="data.distance_id"
            vid="distance_id"
            label="Đoạn"
            class="col-12 col-md-6 col-lg-3 form-group-container"
            :options="optionsDistance"
            @change="changeDistance($event)"
          />
          <div class="col-12 col-md-12 col-lg-4 form-group-container position-relative">
            <InputText
              v-model="data.coordinates"
              vid="coordinates"
              label="Tọa độ"
              rules="required"
              class="coordinates"
            />
            <div class="img-locate">
              <img src="../../../assets/icons/ic_locate.svg" alt="locate" @click="handleOpenModalMap()">
            </div>
          </div>
          <div class="col-7 col-md-7 col-lg-7">
            <InputText
              v-model="data.appraise_asset"
              vid="appraise_asset"
              label="Tài sản thẩm định giá"
              rules="required"
              class="col-12 form-group-container"
            />
          </div>

          <!-- <div class="col-12 col-md-4 col-lg-3">
            <InputNumberFormat
              v-model="data.doc_no_old"
              vid="doc_no_old"
              label="Số tờ cũ"
              :max="999999999"
              :min="-999999999"
              @change="changeDocNoOld($event)"
              class="form-group-container"
            />
            <span class="text-error" v-if="data.doc_no_old < 0">Vui lòng nhập số tờ cũ thích hợp</span>
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <InputNumberFormat
              v-model="data.land_no_old"
              vid="land_no_old"
              label="Số thửa cũ"
              :max="999999999"
              :min="-999999999"
              @change="changeLandNoOld($event)"
              class="form-group-container"
            />
            <span class="text-error" v-if="data.land_no_old < 0">Vui lòng nhập số thửa cũ thích hợp</span>
          </div> -->
          <!-- <div class="col-12 col-md-4 col-lg-3">
            <InputNumberFormat
              v-model="data.doc_no"
              vid="doc_no"
              label="Bản đồ số"
              rules="required"
              :max="999999999"
              :min="-999999999"
              @change="changeDocNo($event)"
              class="form-group-container"
            />
            <span class="text-error" v-if="data.doc_no < 0">Vui lòng nhập bản đồ số thích hợp</span>
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <InputNumberFormat
              v-model="data.land_no"
              vid="land_no"
              label="Thửa đất số"
              rules="required"
              :max="999999999"
              :min="-999999999"
              @change="changeLandNo($event)"
              class="form-group-container"
            />
            <span class="text-error" v-if="data.land_no < 0">Vui lòng nhập thửa đất số thích hợp</span>
          </div> -->
          <InputCategory
            v-model="data.topographic_id"
            vid="topographic_id"
            label="Địa hình"
            class="col-12 col-md-4 col-lg-4 form-group-container"
            :options="optionsTopographic"
          />
          <div class="col-12 col-md-4 col-lg-4 form-group-container"/>

        </div>
      </div>
    </div>
    <ModalMap
      v-if="openModalMap"
      @cancel="openModalMap = false"
      :location="this.location"
      :address="full_address_street"
      :center_map="data.coordinates"
      @action="handleCoordinates"
    />
  </div>
</template>

<script>
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputSwitch from '@/components/Form/InputSwitch'
import InputNumberFormat from '@/components/Form/InputNumber'
import WareHouse from '@/models/WareHouse'
import ModalDeleteIndex from '@/components/Modal/ModalDeleteIndex'
import ModalMap from '@/components/Modal/ModalMap'
import moment from 'moment'
export default {
	name: 'AppraisalInformation',
	props: ['data', 'appraisalPurposes', 'propertyTypes', 'topographic', 'provinces', 'districts', 'wards', 'streets', 'distances', 'full_address', 'full_address_street'],
	components: {
		InputCategory,
		InputText,
		InputSwitch,
		InputNumberFormat,
		InputDatePicker,
		ModalMap,
		ModalDeleteIndex
	},
	data () {
		return {
			showCard: true,
			openModalMap: false,
			openModalDelete: false,
			showApartment: true,
			directions: [],
			furniture_list: [],
			location: {
				lng: '',
				lat: ''
			},
			status: '',
			form: {
				appraise_date: '',
				certificate_date: '',
				document_date: '',
				id: '',
				status: '',
				base: '',
				num: '',
				date: '',
				num_certificate: '',
				date_certificate: '',
				customer_request: '',
				phone: '',
				address: '',
				asset_type_id: '',
				full_address: '',
				province_id: '',
				district_id: '',
				ward_id: '',
				street_id: '',
				distance_id: '',
				coordinates: '',
				land_no: '',
				doc_no: '',
				topographic: '',
				land_no_old: '',
				doc_no_old: ''
			}
		}
	},
	computed: {
		optionsAppraisalPurposes () {
			return {
				data: this.appraisalPurposes,
				id: 'id',
				key: 'name'
			}
		},
		optionDirection () {
			return {
				data: this.directions,
				id: 'id',
				key: 'description'
			}
		},
		optionFurniture () {
			return {
				data: this.furniture_list,
				id: 'id',
				key: 'description'
			}
		},
		optionsType () {
			return {
				data: this.propertyTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsTopographic () {
			return {
				data: this.topographic,
				id: 'id',
				key: 'description'
			}
		},
		optionsProvince () {
			return {
				data: this.provinces,
				id: 'id',
				key: 'name'
			}
		},
		optionsDistrict () {
			return {
				data: this.districts,
				id: 'id',
				key: 'name'
			}
		},
		optionsWard () {
			return {
				data: this.wards,
				id: 'id',
				key: 'name'
			}
		},
		optionsStreet () {
			return {
				data: this.streets,
				id: 'id',
				key: 'name'
			}
		},
		optionsDistance () {
			return {
				data: this.distances,
				id: 'id',
				key: 'name'
			}
		}
	},
	mounted () {
		if (this.data.status === 1) {
			this.status = 'Nháp'
		} else if (this.data.status === 2) {
			this.status = 'Đã xác nhận'
		} else if (this.data.status === 3) {
			this.status = 'Đã được chọn'
		} else if (this.data.status === 4) {
			this.status = 'Hoàn thành'
		} else if (this.data.status === 5) {
			this.status = 'Đã hủy'
		}
	},
	methods: {
		changeProvince (provinceId) {
			this.$emit('getDistrict', provinceId)
		},
		changeDistrict (id) {
			this.$emit('getWardStreet', id)
		},
		changeWard (id) {
			this.$emit('getWard', id)
		},
		changeStreet (id) {
			this.$emit('getDistance', id)
		},
		changeDistance (id) {
			this.$emit('getDistanceName', id)
		},
		changeAssetType (id) {
			this.$emit('getAssetType', id)
		},
		changeBase (e) {
			if (e !== undefined && e !== null) {
				this.data.ticket_num = parseFloat(e).toFixed(0)
			} else {
				this.data.ticket_num = ''
			}
		},
		changeLandNo (e) {
			if (e !== undefined && e !== null) {
				this.data.land_no = parseFloat(e).toFixed(0)
			} else {
				this.data.land_no = ''
			}
		},
		changeDocNo (e) {
			if (e !== undefined && e !== null) {
				this.data.doc_no = parseFloat(e).toFixed(0)
			} else {
				this.data.doc_no = ''
			}
		},
		changeLandNoOld (e) {
			if (e !== undefined && e !== null) {
				this.data.land_no_old = parseFloat(e).toFixed(0)
			} else {
				this.data.land_no_old = ''
			}
		},
		changeDocNoOld (e) {
			if (e !== undefined && e !== null) {
				this.data.doc_no_old = parseFloat(e).toFixed(0)
			} else {
				this.data.doc_no_old = ''
			}
		},
		changeCertificate () {
			if (this.form.certificate_date && this.form.certificate_date !== '') {
				this.data.certificate_date = moment(this.form.certificate_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
			} else {
				this.data.certificate_date = ''
			}
		},
		async getDataEdit () {
			this.form.document_date = moment(this.data.document_date).format('DD/MM/YYYY')
			this.form.certificate_date = moment(this.data.certificate_date).format('DD/MM/YYYY')
			this.form.appraise_date = moment(this.data.appraise_date).format('DD/MM/YYYY')
		},
		changeDocumentDate () {
			if (this.form.document_date && this.form.document_date !== '') {
				this.data.document_date = moment(this.form.document_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
				if (moment(this.data.document_date).endOf('day') < moment(this.data.certificate_date)) {
					this.data.certificate_date = ''
					this.form.certificate_date = ''
				}
			} else {
				this.data.document_date = ''
			}
			this.form.appraise_date = this.form.document_date
			this.data.appraise_date = this.data.document_date
		},
		changeAppraiseDate () {
			if (this.form.appraise_date && this.form.appraise_date !== '') {
				this.data.appraise_date = moment(this.form.appraise_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
			} else {
				this.data.appraise_date = ''
			}
		},
		async handleCoordinates (coordinates) {
			this.data.coordinates = coordinates
			this.location.lat = coordinates.split(',')[0]
			this.location.lng = coordinates.split(',')[1]
		},
		async handleOpenModalMap () {
			this.openModalMap = true
		},
		handleRemoveFurniture (index) {
			this.openModalDelete = true
			this.index_delete = index
		},
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.directions = [...reps.data.huong_can_ho]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		disabledDate (current) {
			if (this.data.document_date !== '' && this.data.document_date !== undefined && this.data.document_date !== null) {
				return (moment(this.data.document_date).endOf('day')) < current && current
			} else {
				return current && current > moment().endOf('day')
			}
		}
	},
	beforeMount () {
	}
}
</script>

<style scoped lang="scss">
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 768px) {
      margin-bottom: 20px;
    }
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      @media (max-width: 768px) {
        padding: 12px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 787px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
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
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .form-group-container {
    margin-top: 15px;
  }
  .color-black{
    color: #333333;
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
      &:hover{
        border-color: #dc8300;
      }
    }
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  .img-locate{
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
  .text-error{
    color: #cd201f;
    font-size: 12px;
  }
</style>
