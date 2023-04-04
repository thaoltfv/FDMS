<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    >
    <div class="card">
      <div class="card-body">
        <p class="title">
         Nhập bán kính (km)
        </p>
        <InputNumberFormat
          v-model="radius_input"
          label=""
          :max="10"
          :min="0"
          @change="changeRadius($event)"
          class=""
        />
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
	name: 'ModalRadius',
	props: ['radius'],
	components: {
		InputText,
		InputNumberFormat
	},
	mounted () {
		this.radius_input = parseFloat(this.radius / 1000).toFixed(1)
	},
	data () {
		return {
			radius_input: ''
		}
	},
	methods: {
		changeRadius (event) {
			this.radius_input = parseFloat(event).toFixed(2)
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},

		handleAction (event) {
			const data = parseInt(this.radius_input * 1000)
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
    max-width: 417px;
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
  font-weight: 400;
  font-size: 16px;
  text-align: left;
  margin-bottom: 7px;
}
.btn{
  &-orange {
    background: #FAA831;
    color: #FFFFFF;
    font-weight: bold !important;
  }
  &-white{
    min-width: auto;
  }
}
</style>
