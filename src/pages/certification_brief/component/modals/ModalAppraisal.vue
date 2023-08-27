<template>
  <div class="modal-delete d-flex justify-content-center align-items-center">
    <div class="card">
      <div class="container-title">
        <div class="d-flex justify-content-between">
          <h2 class="title">{{ModalEdit ? 'Tổ thẩm định' : 'Xác nhận lại Tổ thẩm định'}}</h2>
          <img height="35px" @click="handleCancel" class="cancel" src="../../../../assets/icons/ic_cancel_2.svg" alt="">
        </div>
      </div>
      <div class="card-body">
        <ValidationObserver
          tag="form"
          ref="appraisal"
          @submit.prevent="validateAppraisal"
        >
        <div class="row">
          <div v-if="!ModalEdit" class="col-12">
            <InputDatePickerV2
              v-model="form.status_expired_at"
              vid="status_expired_at"
              :showTime="true"
              label="Thời hạn xử lý"
              rules="required"
              class="form-group-container"
              @change="handleChangeDateTime"
            />
          </div>
          <div class="col-12">
              <InputCategory
              v-model="form.appraiser_perform_id"
              vid="appraiser_perform_id"
              label="Chuyên viên thực hiện"
              :rules="requiredAppraiserPerform"
              class="form-group-container"
              @change="handleChangeAppraiserPerform"
              :options="optionsPeformance"
              />
          </div>
		  <div class="col-12">
              <InputCategory
              v-model="form.appraiser_control_id"
              vid="appraiser_control_id"
              label="Kiểm soát viên"
              class="form-group-container"
              @change="handleChangeAppraiserControl"
              :options="optionsAppraiserControl"
              />
          </div>
          <div class="col-12">
              <InputCategory
                v-model="form.appraiser_id"
                vid="appraiser_id"
                label="Thẩm định viên"
                rules="required"
                class="form-group-container"
                @change="handleChangeAppraiser"
                :options="optionsAppraiser"
              />
          </div>
		  
          <div class="col-12">
			<div style="text-align: left !important;" class="form-group-container">
				<!-- <label class="color-black font-weight-bold">Đại diện theo pháp luật</label>
				<div class="form-control border_disable disabled"><p class="mb-0">{{appraisersManager.length > 0 ? appraisersManager[0].name : ''}}</p></div> -->
				<InputCategory
                v-model="form.appraiser_manager_id"
                vid="appraiser_manager_id"
                label="Đại diện theo pháp luật"
                rules="required"
                class="form-group-container"
                @change="handleChangeAppraiserManager"
                :options="optionsAppraiserManager"
              />
			</div>
          </div>
          <div class="col-12">
            <InputCategory
              v-model="form.appraiser_confirm_id"
              vid="appraiser_confirm_id"
              label="Đại diện ủy quyền"
              class="form-group-container"
              :options="optionsSignAppraiser"
            />
          </div>

        </div>
        <div class="btn__group">
          <button
            class="btn btn-white font-weight-normal font-weight-bold"
            @click.prevent="handleCancel"
          > Trở lại</button>
          <button class="btn btn-white btn-orange font-weight-bold ml-2"  type="submit">Lưu</button>
        </div>
      </ValidationObserver>
      </div>
    </div>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import Certificate from '@/models/Certificate'
import CertificationBrief from '@/models/CertificationBrief'
import InputDatePickerV2 from '@/components/Form/InputDatePickerV2'
import moment from 'moment'
import AppraiserCompany from '@/models/AppraiserCompany'
export default {
	name: 'ModalAppraisal',
	props: ['data', 'idData', 'status', 'requiredAppraiserPerform', 'requiredAppraiser', 'ModalEdit'],
	components: {
		InputText,
		InputNumberFormat,
		InputCategory,
		InputDatePickerV2
	},
	created () {
		console.log('vô đây không')
		this.getAppraisers()
		this.getTimeStamp()
	},
	mounted () {
	},
	data () {
		return {
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			appraisers: [],
			signAppraisers: [],
			appraisersManager: [],
			appraisersControl: [],
			users: [],
			positions: [],
			employeePerformance: [],
			employeeBusiness: [],
			minutes: 0
		}
	},
	methods: {
		handleChangeDateTime (date) {
			if (date) {

			}
		},
		addMinutes () {
			let dateConvert = new Date()
			let minutes = this.minutes
			return new Date(dateConvert.getTime() + minutes * 60000)
		},
		async getTimeStamp () {
			let res = await CertificationBrief.getTimeStamp()
			if (res.data) {
				let statusObject = []
				if (this.data.status === 1) {
					statusObject = res.data.filter(item => item.acronym === 'MOI')
					this.minutes = statusObject[0].value
				} else if (this.data.status === 2) {
					statusObject = res.data.filter(item => item.acronym === 'DANG-THAM-DINH')
					this.minutes = statusObject[0].value
				} else if (this.data.status === 3) {
					statusObject = res.data.filter(item => item.acronym === 'DANG-DUYET')
					this.minutes = statusObject[0].value
				} else if (this.data.status === 4) {
					statusObject = res.data.filter(item => item.acronym === 'HOAN-THANH')
					this.minutes = statusObject[0].value
				}
			} else {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			}
			let dateConverted = await this.addMinutes(480)
			this.form.status_expired_at = await moment(dateConverted).format('DD-MM-YYYY HH:mm')
		},
		async getAppraisers () {
			const resp = await Certificate.getAppraisers()
			const appraiserCompany = await AppraiserCompany.detail()
			let dataAppraise = await [...resp.data]
			let idManager = await appraiserCompany.data.data[0].appraiser.id
			this.employeePerformance = await dataAppraise
			this.employeeBusiness = await dataAppraise
			this.appraisersControl = await dataAppraise
			this.appraisersManager = await dataAppraise.filter(item => item.is_legal_representative === 1)
			console.log('dsadấdsad', this.appraisersManager)
			this.form.appraiser_manager_id = await this.appraisersManager[0].id
			let appraiser = dataAppraise.filter(item => item.appraiser_number !== '')
			if (this.form && this.form.appraiser_manager_id) {
				this.appraisers = await appraiser.filter(item => item.id !== this.form.appraiser_manager_id)
			}
			if (this.form && this.form.appraiser_id) {
				const filterData = await appraiser.filter(item => item.id !== this.form.appraiser_id && item.id !== this.form.appraiser_manager_id)
				this.signAppraisers = await filterData
			} else {
				this.signAppraisers = await this.appraisers
			}
		},
		changeRadius (event) {
			this.filter_map.radius = parseFloat(event).toFixed(2)
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAllTransaction () {

		},
		handleSold () {

		},
		handleForSale () {

		},
		handleChangeAppraiser (event) {
			if (event) {
				this.form.appraiser_confirm_id = ''
				if (event === this.form.appraiser_manager_id) {
					this.signAppraisers = this.appraisers
				} else {
					const filterData = this.appraisers.filter(item => item.id !== event)
					this.signAppraisers = filterData
				}
			} else {
				this.form.appraiser_confirm_id = ''
				this.signAppraisers = this.appraisers
			}
		},
		handleChangeAppraiserManager (event) {
			// if (event) {
			// 	this.form.appraiser_confirm_id = ''
			// 	if (event === this.form.appraiser_manager_id) {
			// 		this.signAppraisers = this.appraisers
			// 	} else {
			// 		const filterData = this.appraisers.filter(item => item.id !== event)
			// 		this.signAppraisers = filterData
			// 	}
			// } else {
			// 	this.form.appraiser_confirm_id = ''
			// 	this.signAppraisers = this.appraisers
			// }
		},
		handleChangeAppraiserControl () {},
		handleChangeAppraiserPerform () {

		},
		async validateAppraisal () {
			const isValid = await this.$refs.appraisal.validate()
			if (isValid) {
				this.handleAction()
			}
		},
		async handleAction (event) {
			let dateConverted = moment(this.form.status_expired_at).format('YYYY-MM-DD HH:mm:ss')
			const data = {
				status: this.status,
				appraiser_perform_id: this.form.appraiser_perform_id,
				appraiser_id: this.form.appraiser_id,
				appraiser_confirm_id: this.form.appraiser_confirm_id,
				appraiser_manager_id: this.form.appraiser_manager_id,
				appraiser_control_id: this.form.appraiser_control_id,
				status_expired_at: this.form.status_expired_at
			}
			if (this.ModalEdit) {
				const res = await CertificationBrief.updateAppraisers(this.idData, data)
				if (res.data) {
					await this.$toast.open({
						message: 'Lưu thông tin tổ thẩm định thành công',
						type: 'success',
						position: 'top-right'
					})
					await this.$emit('updateAppraisal', res.data, this.idData, dateConverted)
					await this.$emit('cancel', event)
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				} else {
					await this.$toast.open({
						message: 'Lưu thất bại',
						type: 'error',
						position: 'top-right'
					})
				}
			} else {
				const res = await CertificationBrief.updateStatusCertificate(this.idData, data)
				if (res.data) {
					await this.$toast.open({
						message: 'Lưu thông tin tổ thẩm định thành công',
						type: 'success',
						position: 'top-right'
					})
					await this.$emit('updateAppraisal', res.data, this.idData, dateConverted)
					await this.$emit('cancel', event)
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				} else {
					await this.$toast.open({
						message: 'Lưu thất bại',
						type: 'error',
						position: 'top-right'
					})
				}
			}
		}

	},
	computed: {
		optionsAppraiserManager () {
			return {
				data: this.appraisersManager,
				id: 'id',
				key: 'name'
			}
		},
		optionsAppraiserControl () {
			return {
				data: this.appraisersControl,
				id: 'id',
				key: 'name'
			}
		},
		optionsAppraiser () {
			return {
				data: this.appraisers,
				id: 'id',
				key: 'name'
			}
		},
		optionsSignAppraiser () {
			return {
				data: this.signAppraisers,
				id: 'id',
				key: 'name'
			}
		},
		optionsPeformance () {
			return {
				data: this.employeePerformance,
				id: 'id',
				key: 'name'
			}
		},
		optionsBusiness () {
			return {
				data: this.employeeBusiness,
				id: 'id',
				key: 'name'
			}
		}
	}
}
</script>

<style lang="scss" scoped>
.modal-delete {
  position: fixed;
  z-index: 300;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);

  @media (max-width: 787px) {
    padding: 20px;
  }
  .card {
    max-width: 500px;
    width: 100%;
    margin-bottom: 0;
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
      padding: 0px 15px 15px;
      border-top: 1px solid #E8E8E8;
      .btn__group {
        text-align: right;
        .btn {
          max-width: fit-content;
          width: 100%;
          margin: 14px 0 0;
        }
      }
    }
  }
}
.container-title{
  // margin: -35px -95px auto;
  // padding: 35px 95px 0;
  // padding: 15px 50px 10px 95px;
  // box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  padding-left: 1rem;

  .title{
    color:#007EC6;
    font-weight: 600;
    margin-top:20px;
    margin-bottom: 15px;
    font-size: 1.2rem;
    @media (max-width: 767px) {
      font-size: 1.125rem;
    }
  }
  // &__footer{
  //   margin: auto -95px -35px;
  //   padding: 20px 95px 20px;
  //   @media (max-width: 767px) {
  //     .btn-white{
  //       margin-bottom: 20px;
  //     }
  //   }
  // }
}
.title{
  font-weight: 500;
  font-size: 1.125rem;
  text-align: left;
  margin-bottom: 7px;
}
.btn{
  &-orange {
    background: #FAA831;
    color: #FFFFFF;
    font-weight: 700 !important;
  }
  &-white{
    min-width: auto;
  }
}
.form-group-container {
  margin-top: 10px;
}
.property_content {
  font-weight: 600
}
.input_checkbox {
  margin-right: 10px;
}
.color-black {
  color: #333333;

}
.border_disable {
  border-color: #d9d9d9 !important;
}
.form-group-container {
  margin-top: 10px;
}

</style>
