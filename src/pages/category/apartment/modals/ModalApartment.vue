<template>
  <div class="modal-delete d-flex justify-content-center align-items-center" >
    <div class="card">
      <div class="container-title">
        <div class="d-flex justify-content-between">
          <h2 class="title">{{isEdit ? 'Chỉnh sửa thông tin căn hộ' : 'Thêm mới thông tin căn hộ'}}</h2>
          <img height="35px" @click="handleCancel" class="cancel" src="@/assets/icons/ic_cancel_2.svg" alt="">
        </div>
      </div>
      <div class="card-body">
          <ValidationObserver tag="form" ref="formApartment" @submit.prevent="validateApartment">
            <InputText
              v-model="form.name"
              rules="required"
              :max-length="200"
              class="form-group-container col-12"
              vid="name_block"
              label="Tên căn hộ"
            />
          </ValidationObserver>
        <div class="btn__group">
          <button class="btn btn-white" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
          <button class="btn btn-white btn-orange text-nowrap" type="button" @click.prevent="validateApartment"><img src="@/assets/icons/ic_save.svg" style="margin-right: 5px" alt="save"> Lưu</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
// import moment from 'moment'
export default {
	name: 'ModalStep2OtherLegal',
	props: ['data', 'isEdit'],
	components: {
		InputText,
		InputNumberNoneFormat
	},
	data () {
		return {
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {}
		}
	},
	mounted () {
	},
	methods: {
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		changeTotalFloor (e) {
			this.form.total_floors = e
		},
		changeFirstFloor (e) {
			this.form.first_floor = e
		},
		changeLastFloor (e) {
			this.form.last_floor = e
		},
		changApartmentPerFloor (e) {
			this.form.apartments_per_floor = e
		},
		async validateApartment () {
			const valid = await this.$refs.formApartment.validate()
			if (valid) {
				await this.handleAction()
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		handleAction () {
			this.$emit('action', this.form)
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
        max-width: 100px;
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
