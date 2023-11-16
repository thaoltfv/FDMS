<!--<script>-->
<!--export default {-->
<!--  name: 'ModalPrint'-->
<!--}-->
<!--</script>-->

<!--<style scoped>-->

<!--</style>-->
<template>
  <div>
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="card">
          <iframe style="height: 100dvh;" :src="print_detail" frameborder="0"></iframe>
        </div>
      </div>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import FileUpload from '@/components/file/FileUpload'
import moment from 'moment'
import VuePdfApp from 'vue-pdf-app'
import 'vue-pdf-app/dist/icons/main.css'

export default {
	name: 'ModalPrint',
	props: ['print_detail', 'img_link'],
	components: {
		VuePdfApp,
		FileUpload,
		InputCategory,
		InputText,
		InputSwitch
	},
	mounted () {
	},
	methods: {
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		}
	},
	beforeMount () {
	}
}
</script>

<style lang="scss" scoped>
.title{
  font-size: 16px;
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
  @media (max-width: 787px) {
    padding: 20px;
  }
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1300px;
    width: 100%;
    max-height: 100dvh;
    background: transparent;
    overflow-y: auto;
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

.img-content{
  color: #000000;
  font-size: 14px;
  font-weight: 600;
  span{
    font-weight: 400;
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
.img-contain {
  max-width: 200px;
  max-height: 200px;
  height: auto;
  margin-left: 20px;
  &__table{
    margin: auto;
    max-width: 50px;
    img{
      cursor: pointer;
      display: flex;
      justify-content: center;
    }
  }
  img{
    object-fit: contain;
    width: 100%;
    height: auto;
    max-width: 200px;
    max-height: 200px;
  }
}
.btn{
  &-white{
    max-height: none;
    font-size: 14px;
    line-height: 19.07px;
    min-width: 153px;
    margin-right: 15px;
    &:last-child{
      margin-right: 0;
    }
  }
  &-contain{
    margin-bottom: 55px;
  }
}
</style>
