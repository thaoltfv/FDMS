<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleAction">
    <div class="card position-relative">
      <div class="done">
        <img src="../../assets/icons/ic_done.svg" alt="">
      </div>
      <div class="card-header py-3 justify-content-center">
        <h3 class="font-weight-bold">
          <!--          {{$t('btn_delete')}}-->
          Hoàn thành
        </h3>
      </div>

      <div class="card-body">
        <p style="font-size: 18px" v-html="notification"/>
<!--          {{notification}}-->

        <div class="btn__group">
          <button
            class="btn btn-white btn-orange btn-pill font-weight-normal"
            @click.prevent="handleAction">
            OK
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'ModalNotification',
	props: ['notification'],
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
  .card {
    max-width: 600px;
    width: 100%;
    margin-bottom: 0;
    &-header {
      border-bottom: 1px solid #DDDDDD;
      padding-top: 50px !important;
      h3 {
        color: #333333;
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
    .done{
      width: 70px;
      height: 70px;
      position: absolute;
      border-radius: 50%;
      left: 50%;
      top: -35px;
      transform: translate(-50%);
      background: #FAA831 ;
      display: flex;
      align-items: center;
      justify-content: center;
      img {
        width: 70%;
        height: auto;
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
