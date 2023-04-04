<template>
  <div>
      <ValidationObserver tag="form"
                          ref="formLegal"
                          @submit.prevent="validateLegal">
      <div class="modal-detail d-flex justify-content-center align-items-center" >
          <div class="card">
          <div class="container-title">
              <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Thông tin về pháp lý tài sản</h2>
              </div>
          </div>
          <div class="contain-detail mb-2">
            <div class="row">
              <InputText
                v-model="form.description"
                vid="description"
                class="form-group-container col-12 col-lg-6"
                label="Tên pháp lý"
                rules="required"
              />
              <InputText
                v-model="form.certifying_agency"
                vid="certifying_agency"
                label="Cơ quan các cấp xác nhận"
                rules="required"
                class="form-group-container col-12 col-lg-6"
              />
              <InputText
                v-model="form.document_num"
                vid="document_num"
                class="form-group-container col-12 col-lg-3"
                label="Số pháp lý"
                rules="required"
              />
              <InputDatePicker
                v-model="form.document_date"
                vid="document_date"
                label="Ngày pháp lý"
                placeholder="Ngày/tháng/năm"
                rules="required"
                class="form-group-container col-12 col-lg-3"
                formatDate='DD/MM/YYYY'
              />
              <InputText
                v-model="form.origin_of_use"
                vid="origin_of_use"
                label="Nguồn gốc sử dụng"
                class="form-group-container col-12 col-lg-6"
              />
              <InputTextarea
                v-model="form.content"
                vid="content"
                label="Nội dung"
                class="form-group-container col-12"
                :rows="3"
              />
            </div>
          </div>
          <div class="container-title container-title__footer">
              <div class="d-lg-flex d-block justify-content-end shadow-bottom">
              <button class="btn btn-white" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
              <button class="btn btn-white btn-orange text-nowrap" type="button" @click.prevent="validateLegal"><img src="@/assets/icons/ic_save.svg" style="margin-right: 5px" alt="save"> Lưu</button>
              </div>
          </div>
          </div>
      </div>
      </ValidationObserver>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputDatePicker from '@/components/Form/InputDatePicker'
// import moment from 'moment'
export default {
	name: 'ModalStep2MachineLegal',
	props: ['data'],
	components: {
		InputText,
		InputTextarea,
		InputDatePicker
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
		async validateLegal () {
			const valid = await this.$refs.formLegal.validate()
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
  .btn-delete {
    cursor: pointer;
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border: none;
    margin-bottom: 0.6rem;
    img {
      width: 100%;
      height: auto;
    }
  }
  .title{
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 25px;
  color: #000000;
  }
  .modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);
  .card {
      border-radius: 5px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
      max-width: 1300px;
      width: 100%;
      max-height: 90vh;
      margin-bottom: 0;
      padding: 35px 95px;
      @media (max-width: 787px) {
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
      .title{
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
      }
  }
  &-table{
      border-radius: 5px;
      background: #FFFFFF;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
      width: 99%;
      margin: 50px auto 50px;
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
  .img{
  margin-right: 13px;
  }
  .card{
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);;
  background: #FFFFFF;
  margin-bottom: 75px;
  &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
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
  .card__order{
  max-width: 50%;
  margin-bottom: 1.25rem;
  @media (max-width: 767px) {
      max-width: 100%;
  }
  }
  .btn{
  &-white{
      max-height: none;

      line-height: 19.07px;
      margin-right: 15px;
      &:last-child{
      margin-right: 0;
      }
  }
  &-contain{
      margin-bottom: 55px;
  }
  }
  .d-grid{
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-column-gap: 8.9%;
  &:first-child {
      margin-top: 0;
  }
  &__checkbox{
      grid-template-columns: 1fr 1fr;
  }
  @media (max-width: 767px) {
      grid-template-columns: 1fr;
  }
  }
  .content{
    &-title{
        color: #555555;
        margin-bottom: 5px;

        font-weight: 500;
    }
    &-name{
        font-size: 1.125rem;
        color: #000000;
        margin-bottom: 15px;
        font-weight: 600;
        &__code{
        color: #FAA831;
        }
    }
  }
  .contain-table{
  @media (max-width: 767px) {
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
      }
  }
  tbody{
      td{
      border: 1px solid #E5E5E5;
      &:first-child{
          border-left: none;
          width: 180px
      }
      &:last-child{
          border-right: none;
      }
      box-sizing: border-box;
      padding: 14px;
      }
  }
  }
  .img-content{
  color: #000000;

  font-weight: 600;
  span{
      font-weight: 500;
      margin-left: 10px;
  }
  }
  .input-code{
  color: #000000;
  border-radius: 5px;
  width: 180px;
  border: 1px solid #000000;
  background: #f5f5f5;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  }
  .img-dropdown{
  cursor: pointer;
  width: 18px;
  &__hide{
      transform: rotate(90deg);
      transition: .3s;
  }
  }
  //.img-contain {
  //  max-width: 200px;
  //  max-height: 200px;
  //  height: auto;
  //  margin-left: 20px;
  //  &__table{
  //    margin: auto;
  //    max-width: 50px;
  //    img{
  //      cursor: pointer;
  //      display: flex;
  //      justify-content: center;
  //    }
  //  }
  //  img{
  //    object-fit: contain;
  //    width: 100%;
  //    height: auto;
  //    max-width: 200px;
  //    max-height: 200px;
  //  }
  //}
  .img-contain {
  aspect-ratio: 1/1;
  overflow: hidden;
  img{
      height: 100%;
      cursor: pointer;
      object-fit: cover;
  }
  &__table{
      margin: auto;
      max-width: 50px;
      max-height: 50px;
      img{
      object-fit: cover;
      object-position: top;
      cursor: pointer;
      display: flex;
      justify-content: center;
      max-width: 50px;
      max-height: 50px;
      }
  }
  }
  .container-title{
  margin: -35px -95px auto;
  padding: 35px 95px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  .title{
      font-size: 1.2rem;
      @media (max-width: 767px) {
      font-size: 1.125rem;
      }
  }
  &__footer{
      margin: auto -95px -35px;
      padding: 20px 95px 20px;
      @media (max-width: 767px) {
      .btn-white{
          margin-bottom: 20px;
      }
      }
  }
  }
  .container-img{
  padding: .75rem 0;
  border: 1px solid #0b0d10;
  }
  .m-h-100 {
    min-height: 100px;
    max-height: 100px;
  }
</style>
