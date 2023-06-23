<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h3 class="font-weight-bold mb-0">
          <!--          {{$t('btn_delete')}}-->
          Thông báo
        </h3>

        <img @click="handleCancel"
             src="../../assets/images/icon-btn-back.svg"
             alt="icon">
      </div>

      <div class="card-body">
        <p class="font-weight-bold" style="font-size: 18px">
          Bạn có chắc muốn gửi email đặt lại mật khẩu cho nhân viên <br/><span style="font-size: 16px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: #0062AF">{{name}}</span> không ?
        </p>

        <div class="btn__group">
          <button
            class="btn btn-white font-weight-normal font-weight-bold"
            @click.prevent="handleCancel"
            v-text="$t('popup_btn_no')"/>
          <button
            class="btn btn-white btn-orange font-weight-bold mt-md-0 mt-2"
            @click.prevent="handleAction"
            v-text="$t('popup_btn_yes')"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'ModalReset',
  props: {
		name: {
			type: String,
			default: ''
		},
  },
	methods: {
		handleCancel (event) {
			this.$emit('cancel', event)
		},

		handleAction (event) {
			this.$emit('action', event)
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
    max-width: 600px;
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
      text-align: center;
      padding: 40px;
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
.btn{
  &-orange {
    background: #FAA831;
    color: #FFFFFF;
    font-weight: bold !important;
  }
}
</style>
