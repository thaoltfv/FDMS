<template>
  <div class="modal-delete d-flex justify-content-center align-items-center">
    <div class="card">
      <div class="card-body">
        <p class="title">
         Đặc điểm tài sản khác
        </p>
            <InputTextarea
                placeholder="Mô tả đặc điểm tài sản khác"
                v-model="other_description"
                vid="other_description"
                :rows="10"
                :maxLength="1000"
                class="col-12 form-group-container"
            />

        <div class="btn__group">
          <button
            class="btn btn-white font-weight-normal font-weight-bold"
            @click.prevent="handleCancel"
          > Trở lại</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
export default {
	name: 'ModalEditDescription',
	props: ['description'],
	components: {
		InputText,
		InputTextarea
	},
	mounted () {
	},
	data () {
		return {
			other_description: this.description ? this.description : ''
		}
	},
	methods: {
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			this.$emit('action', this.other_description)
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
