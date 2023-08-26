<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    >
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-12">
              <InputCategory
              v-model="data.appraiser_sale_id"
              vid="appraiser"
              label="Nhân viên kinh doanh"
              rules="required"
              class="form-group-container"
              @change="handleChangeAppraiserSale"
              :options="optionsUser"
              />
          </div>
          <div class="col-12">
              <InputCategory
              v-model="data.appraiser_perform_id"
              vid="appraiser"
              label="Chuyên viên thực hiện"
              rules="required"
              class="form-group-container"
              @change="handleChangeAppraiserPerform"
              :options="optionsUser"
              />
          </div>
          <div class="col-12">
              <InputCategory
              v-model="data.appraiser_id"
              vid="appraiser"
              label="Thẩm định viên"
              rules="required"
              class="form-group-container"
              @change="handleChangeAppraiser"
              :options="optionsAppraiser"
              />
          </div>
          <div class="col-12">
              <InputCategory
              v-model="data.appraiser_control_id"
              vid="appraiser"
              label="Kiểm soát viên"
              rules="required"
              class="form-group-container"
              @change="handleChangeAppraiserControl"
              :options="optionsAppraiserControl"
              />
          </div>
          <div class="col-12">
              <div class="form-group-container">
              <label class="color-black font-weight-bold">Tổng giám đốc</label>
              <div class="form-control border_disable disabled"><p class="mb-0">{{appraisersManager.length > 0 ? appraisersManager[0].name : ''}}</p></div>
              </div>
          </div>
          <div class="col-12">
              <InputCategory
              v-model="data.appraiser_confirm_id"
              vid="staff"
              label="KT - Tổng Giám đốc"
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
          <button
            class="btn btn-white btn-orange font-weight-bold ml-2"
            @click.prevent="handleAction"
          >Chọn</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
export default {
	name: 'ModalCustomer',
	props: ['radius', 'frontSide', 'bothSide', 'transaction', 'assetType'],
	components: {
		InputText,
		InputNumberFormat
	},
	mounted () {
	},
	data () {
		return {
			filter_map: {
				radius: '',
				frontSide: '',
				bothSide: '',
				transaction: [],
				assetType: []
			},
			radius_input: ''
		}
	},
	methods: {
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
		handleIsCheckBothSide () {

		},
		handleAction (event) {
			let data = this.filter_map
			data.radius = parseInt(this.filter_map.radius * 1000)
			this.$emit('action', data)
			this.$emit('cancel', event)
		}
	}
}
</script>

<style lang="scss" scoped>
.modal-delete {
  position: fixed;
  z-index: 10002;
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
      text-align: right;
      padding: 15px;
      .btn__group {
        .btn {
          max-width: fit-content;
          width: 100%;
          margin: 14px 0 0;
        }
      }
    }
  }
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
</style>
