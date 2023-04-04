<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Thông tin về hồ sơ thẩm định</h3>
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
          <div class="col-12 col-md-4 col-lg-3">
            <InputNumberFormat
              v-model="data.ticket_num"
              vid="ticket_num"
              label="Mã nhiệm vụ Base"
              :max="999999999"
              :min="-999999999"
              @change="changeBase($event)"
              class="form-group-container"
            />
            <!-- <span class="text-error" v-if="data.ticket_num < 0">Vui lòng nhập mã nhiệm vụ thích hợp</span> -->
          </div>
          <InputText
            v-model="status"
            vid="status"
            label="Trạng thái"
            disabled-input
            class="col-12 col-md-4 col-lg-4 form-group-container"
          />
          <InputText
            v-model="data.document_num"
            vid="document_num"
            label="Số CVĐ"
            class="col-12 col-md-4 col-lg-3 form-group-container"
          />
          <InputDatePicker
            v-model='form.document_date'
            vid="document_date"
            label="Ngày CVĐ"
            :formatDate="'DD/MM/YYYY'"
            @change="changeDocumentDate"
            class="col-12 col-md-4 col-lg-3 form-group-container"
          />
          <InputDatePicker
            v-model="form.appraise_date"
            vid="appraise_date"
            label="Thời điểm thẩm định"
            rules="required"
            :formatDate="'DD/MM/YYYY'"
            class="col-12 col-md-4 col-lg-4 form-group-container"
            @change="changeAppraiseDate"
          />
          <InputText
            v-model="data.certificate_num"
            vid="certificate_num"
            label="Số chứng thư"
            class="col-12 col-md-4 col-lg-3 form-group-container"
          />
          <InputDatePicker
            v-model='form.certificate_date'
            vid="certificate_date"
            label="Ngày chứng thư"
            class="col-12 col-md-4 col-lg-3 form-group-container"
            :formatDate="'DD/MM/YYYY'"
            :date="disabledDate"
            @change="changeCertificate"
          />
          <InputCategory
            v-model="form.appraise_purpose_id"
            vid="appraise_purpose_id"
            label="Mục đích thẩm định"
            rules="required"
            class="col-12 col-md-4 col-lg-4 form-group-container"
            :options="optionsAppraisalPurposes"
            @change="handleChangeAppraisePurpose"
          />
          <InputText
            v-model="data.petitioner_name"
            vid="base"
            label="Tên KH yêu cầu (trên Chứng thư)"
            :max="9999999"
            class="col-12 col-md-4 col-lg-3 form-group-container"
          />
          <InputText
            v-model="data.petitioner_phone"
            vid="petitioner_phone"
            type="number"
            :max-length="11"
            label="Điện thoại"
            class="col-12 col-md-4 col-lg-3 form-group-container"
          />
          <InputText
            v-model='data.petitioner_address'
            vid="petitioner_address"
            label="Địa chỉ"
            class="col-12 col-md-4 col-lg-4 form-group-container"
          />
          <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-orange" style="width: auto;" type="button" @click="handleOpenModalTableAsset">Chọn Tài sản thẩm định</button>
          </div>
          <div class="col-12" v-if="assets.length > 0">
            <div class="container-asset">
              <h3 class="title-asset">Tài sản thẩm định</h3>
              <div class="row m-0 justify-content-center">
                <div class="col-12 col-lg-3 mt-2" v-for="(asset) in assets" :key="asset.id">
                  <div class="container__property">
                    <!-- <div class="property__detail d-flex justify-content-end">
                      <img style="cursor: pointer" src="../../../assets/icons/ic_cancel-1.svg" alt="" @click="removeAsset(index , asset.id ,asset.appraise_id, asset )">
                    </div> -->
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Mã tài sản:</p>
                      <p class="content content__id">{{`TSTD_${asset.appraise_id ? asset.appraise_id : asset.id}`}}</p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Loại tài sản:</p>
                      <p class="content">{{ asset.asset_type.description }}</p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Loại đất</p>
                      <p class="content">{{asset.properties && asset.properties.length > 0 && asset.properties[0].property_detail && asset.properties[0].property_detail.length > 0 ? asset.properties[0].property_detail[0].land_type_purpose.acronym : ''}} {{asset.properties && asset.properties.length > 0 && asset.properties[0].property_detail && asset.properties[0].property_detail.length > 1 ? ', ' + asset.properties[0].property_detail[1].land_type_purpose.acronym : ''}}</p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Tổng diện tích:</p>
                      <p class="content">{{ asset.properties && asset.properties.length > 0 && asset.properties[0].appraise_land_sum_area ? asset.properties[0].appraise_land_sum_area : 0 }} m <sup>2</sup> </p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="name">Người tạo:</p>
                      <p class="content">{{ asset.created_by.name }} </p>
                    </div>
                    <div class="property__detail d-flex justify-content-between">
                      <p class="mb-0 w-100 text-left">Mô tả: <span>{{asset.asset_type.description ? asset.asset_type.description + ',' : ''}} {{asset.doc_no ? 'Số tờ: ' + asset.doc_no + ',' : ''}} {{asset.land_no ? 'Số thửa: ' + asset.land_no + ',' : ''}} {{asset.street ? asset.street.name + ', ' : ''}} {{asset.ward ? asset.ward.name + ', ' : ''}} {{asset.district ? asset.district.name + ', ' : ''}} {{asset.province ? asset.province.name : ''}}</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ModalTableAssets
      v-if="openModalTableAsset"
      @cancel="cancelData"
      :documents="documents"
      :title="title"
      :certificate_id="certificate_id"
      :isHaveAppraiseId="isHaveAppraiseId"
      @action="handleSaveAssets"
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
import ModalTableAssets from '@/pages/appraisal/components/modals/ModalTableAssets'
import moment from 'moment'
export default {
	name: 'AppraisalInformation',
	props: ['data', 'certificate_id', 'appraisalPurposes', 'propertyTypes', 'topographic', 'provinces', 'districts', 'wards', 'streets', 'distances', 'full_address', 'isHaveAppraiseId'],
	components: {
		InputCategory,
		InputText,
		InputSwitch,
		InputNumberFormat,
		InputDatePicker,
		ModalDeleteIndex,
		ModalTableAssets
	},
	data () {
		return {
			showCard: true,
			openModalDelete: false,
			showApartment: true,
			directions: [],
			furniture_list: [],
			assets: [],
			location: {
				lng: '',
				lat: ''
			},
			status: '',
			documents: [],
			form: {
				appraise_date: '',
				appraise_purpose_id: '',
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
			},
			title: null,
			openModalTableAsset: false
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
			this.status = 'Mới'
		} else if (this.data.status === 3) {
			this.status = 'Đang duyệt'
		} else if (this.data.status === 4) {
			this.status = 'Đã duyệt'
		} else if (this.data.status === 5) {
			this.status = 'Đã hủy'
		}
	},
	methods: {
		handleChangeAppraisePurpose (event) {
			this.form.appraise_purpose_id = event
			this.data.appraise_purpose_id = event
		},
		handleSaveAssets (data) {
			this.assets = data
			this.assets.sort((a, b) => a.id - b.id)
			this.$emit('getAppraisers', data)
		},
		getAssetEdit (data) {
			this.assets = data
			this.assets.sort((a, b) => a.id - b.id)
			this.$emit('getAppraisers', data)
		},
		removeAsset (index, newId, oldId) {
			this.assets.splice(index, 1)
			this.assets.sort((a, b) => a.id - b.id)
			this.$emit('deleteAppraisers', index, this.assets)
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
		changeCertificate () {
			if (this.form.certificate_date && this.form.certificate_date !== '') {
				this.data.certificate_date = moment(this.form.certificate_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
			} else {
				this.data.certificate_date = ''
			}
		},
		async getDataEdit () {
			this.form.document_date = this.data.document_date ? moment(this.data.document_date).format('DD/MM/YYYY') : ''
			this.form.certificate_date = this.data.certificate_date ? moment(this.data.certificate_date).format('DD/MM/YYYY') : ''
			this.form.appraise_date = this.data.appraise_date ? moment(this.data.appraise_date).format('DD/MM/YYYY') : ''
			this.form.appraise_purpose_id = this.data.appraise_purpose_id
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
		handleOpenModalTableAsset () {
			this.title = 'Tài sản thẩm định'
			this.documents = this.assets
			this.openModalTableAsset = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		cancelData () {
			this.openModalTableAsset = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
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
				return current <= moment(this.data.document_date)
			} else {
				return current >= moment().endOf('day')
			}
		}
	},
	beforeMount () {
		this.getDataEdit()
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
  .text-error{
    color: #cd201f;
    font-size: 12px;
  }
  .container {
    &__property {
      height: 100%;
      border: 1px solid #D0D0D0;
      padding: 15px;
      border-radius: 5px;
      @media (max-width: 1023px) {
        margin-bottom: 20px;
      }
      .property {
        &__detail{

          color: #000000;
          margin-bottom: 5px;
          .name, .content{
            margin-bottom: 0;
            padding: 0 !important;
          }
          .name {
            text-align: left;
            width: 50%;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
          }
          .content {
            color: #333333;
            display: block;
            text-align: end;
            &__id{
              color: #FAA831;
            }
          }
        }
      }
    }
    &-asset {
      margin-top: 10px;
      border: 1px solid;
      padding: 20px;
    }
  }
  .title-asset {
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    text-transform: uppercase;
  }
</style>
