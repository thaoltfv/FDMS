<template>
  <div>
    <div class="card" >
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Pháp lý tài sản</h3>
          <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
        </div>
      </div>
      <div class="card-body card-info card-land" v-show="showCard">
        <div class="contain-table contain-table__tangible">
          <table class="table-property" >
            <thead>
            <tr v-if="appraise_law.length > 0 ">
              <th>Mã số</th>
              <th>Loại pháp lý</th>
              <th>Số/Ngày</th>
              <th>Người đứng tên pháp lý</th>
              <th>Cơ quan cấp, xác nhận</th>
              <th>Quy hoạch</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(legal_property, index) in appraise_law" :key="'legal_property'+index">
              <td>
                <div class="d-flex justify-content-center align-items-center contain-total contain-total__table">
                  <div class="num num-id text-nowrap d-flex justify-content-center text-center" @click="openModalEditLegal(legal_property, index)"><a>Chỉnh sửa</a></div>
                </div>
              </td>
              <td>
                <InputCategory
                  v-model="legal_property.appraise_law_id"
                  vid="type"
                  label="Loại pháp lý"
                  disabled
                  class="contain-input contain-input__info contain-input__property"
                  :options="optionsJuridicals"
                />
              </td>
              <td>
                <InputText
                  v-model="legal_property.date"
                  vid="legal_property"
                  label="Số/Ngày"
                  disabled-input
                  class="contain-input contain-input__info contain-input__property"
                />
              </td>
              <td>
                <InputText
                  v-model="legal_property.legal_name_holder"
                  vid="person_name"
                  disabled-input
                  label="Người đứng tên pháp lý"
                  class="contain-input contain-input__info contain-input__property"
                />
              </td>
              <td>
                <InputText
                  v-model="legal_property.certifying_agency"
                  vid="organ"
                  label="Cơ quan cấp, xác nhận"
                  disabled-input
                  class="contain-input contain-input__info contain-input__property"
                />
              </td>
              <td>
                <InputSwitch
                  v-model="isZoning[index].is_zoning"
                  vid="is_zoning"
                  label="Quy hoạch"
                  class="contain-input__property"
                  disabled
                />
              </td>
              <td>
                <div class="btn-delete" @click="removePropertyLegal(index)">
                  <img src="../../../assets/icons/ic_delete.svg" alt="delete">
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="btn-property">
          <button class="btn btn-white btn-orange btn-add" type="button" @click="addPropertyLegal">
            <img src="../../../assets/icons/ic_add-white.svg" alt="add">
            Thêm
          </button>
        </div>
      </div>
    </div>
    <ModalLegalProperty
      v-if="openModalLegal"
      @cancel="cancelLegal"
      :legal="legal"
      :juridicals="juridicals"
      :type_purposes="type_purposes"
      :data="data"
      :full_address="full_address"
      :provinceName="provinceName"
      @action="handelSaveLegal"
    />
  </div>
</template>
<script>

import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import InputDatePicker from '@/components/Form/InputDatePicker'
import moment from 'moment'
import ModalLegalProperty from '@/pages/certificate/components/modals/ModalLegalProperty'
export default {
	name: 'PropertyLegal',
	props: ['data', 'juridicals', 'appraise_law', 'type_purposes', 'full_address', 'provinceName'],
	data () {
		return {
			showCard: true,
			openModalLegal: false,
			housingTypes: [],
			form: [],
			isEditLegal: false,
			indexLegal: '',
			legal: null,
			isZoning: []
		}
	},
	computed: {
		optionsJuridicals () {
			return {
				data: this.juridicals,
				id: 'id',
				key: 'content'
			}
		}
	},
	components: {
		InputText,
		InputNumberFormat,
		InputCategory,
		InputSwitch,
		InputDatePicker,
		ModalLegalProperty
	},
	methods: {
		addPropertyLegal () {
			this.isEditLegal = false
			this.indexLegal = ''
			this.legal = null
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openModalLegal = true
		},
		openModalEditLegal (data, index) {
			this.openModalLegal = true
			this.legal = data
			this.indexLegal = index
			this.isEditLegal = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handelSaveLegal (data) {
			let zoning = data.law_details.find(appraise_law_detail => appraise_law_detail.is_zoning === true)
			if (this.isEditLegal === false) {
				this.appraise_law.push(data)
				if (zoning) {
					this.isZoning.push({
						is_zoning: true
					})
				} else {
					this.isZoning.push({
						is_zoning: false
					})
				}
			} else {
				this.isZoning[this.indexLegal].is_zoning = !!zoning
				this.appraise_law[this.indexLegal] = data
			}
		},
		cancelLegal () {
			this.openModalLegal = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		removePropertyLegal (index) {
			this.appraise_law.splice(index, 1)
		},
		getExpiryDate () {
			this.appraise_law.forEach(appraise => {
				if (appraise.expiry_date && appraise.expiry_date !== '') {
					this.form.push({
						expiry_date: moment(appraise.expiry_date).format('DD/MM/YYYY')
					})
				} else {
					this.form.push({
						expiry_date: ''
					})
				}
			})
		},
		changeExpiryDate (index) {
			if (this.form[index].expiry_date && this.form[index].expiry_date !== '') {
				this.appraise_law[index].expiry_date = moment(this.form[index].expiry_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
			} else {
				this.appraise_law[index].expiry_date = ''
			}
		},
		changeCastle (index) {
			if (this.appraise_law[index].expiry_type) {
				this.appraise_law[index].expiry_date = ''
				this.form[index].expiry_date = ''
			}
		},
		disabledDate (current) {
			return current && current < moment().endOf('day')
		}
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
  }
  }
  &-land{
     position: relative;
     padding: 0;
   }
  }
  .contain-table{
  &__tangible{
     overflow-y: hidden;
     overflow-x: auto;
   }
  @media (max-width: 1440px) {
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
  @media (max-width: 787px) {
    padding: 12px;
  }
  }
  }
  tbody{
  td{
    border: 1px solid #E5E5E5;
  &:first-child{
     border-left: none;
     border-right: none;
   }
  padding: 20px 14px;
  }
  }
  &__order {
  tbody{
  td{
  &:first-child{
     width: 40%;
   }
  &:last-child{
     width: 70px;
   }
  padding: 20px 70px;
  @media (max-width: 1023px) {
    padding: 20px 30px;
  }
  }
  }
  }
  }
  .contain-total{
    display: grid !important;
    margin-right: 0;
    grid-template-columns: 1fr 1fr;
    color: #333333;
  @media (max-width: 1440px) {
    display: block !important;
  }
  .num{
    padding: 0 11px 0 24px;
    height: 35px;
    line-height: 1.5;
    width: 180px;
    border-radius: 5px;
    border: 1px solid #555555;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    background: #f1f1f1 !important;
    cursor: not-allowed;
    user-select: none;
  @media (max-width: 787px) {
    width: 100% !important;
  }
  p{
    margin-bottom: 0;
  }
  &-id{
     color: #FAA831;
     text-align: center !important;
     background: #FFFFFF !important;
     border: none;
     width: 100%;
     padding: 0;
   }
  }
  .name{
    margin-bottom: 0;
    font-size: 1.125rem;
    font-weight: 500;
    margin-right: 20px;
  @media (max-width: 1440px) {
    margin-bottom: 10px;
    font-weight: 700;
  }
  }
  &__last{
  .num{
    width: 315px;
  @media (max-width: 767px){
    width: calc(100vw - 120px) ;
  }
  }
  }
  &__table{
     grid-template-columns: 1fr;
  .num{
    text-align: left;
    margin: auto;
  &-id{
     cursor: pointer;
     color: #FAA831;
     text-align: center !important;
   }
  }
  }
  }
  .coordinate{
    color: #000000;
    background: #f1f1f1;
    padding: 0 11px 0 24px;
    display: flex;
    align-items: center;
    height: 35px;
    border-radius: 5px;
    border: 1px solid #555555;
  .num{
  p{
    margin-bottom: 0;
  }
  }
  }
  .btn{
  &-white{
     max-height: none;

     line-height: 19.07px;
     min-width: 153px;
     margin-right: 15px;
  &:last-child{
     margin-right: 0;
   }
  }
  &-orange {
     max-height: none;

     line-height: 19.07px;
     min-width: 153px;
     margin-right: 15px;
     color: #FFFFFF;
     background: #FAA831;
   }
  &-contain{
     margin-bottom: 55px;
  @media (max-width: 768px) {
    margin-bottom: 30px;
  }
  }
  }
  .btn-delete{
    display: flex;
    align-items: center;
    cursor: pointer;
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
</style>
