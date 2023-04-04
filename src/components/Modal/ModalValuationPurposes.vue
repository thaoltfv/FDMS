<template>
  <div
    class="modal-purpose d-flex justify-content-center align-items-center">
    <div class="card card__show">
      <div class="card-header">
        <div class="title">
          Chọn mục đích định giá
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <button type="button" class="btn btn-purpose" :class="buy? 'active' : ''" @click="activeBuy">
              Mua bất động sản
            </button>
          </div>
          <div class="col-6">
            <button type="button" class="col-6 btn btn-purpose" :class="sell? 'active' : ''" @click="activeSell">
              Bán bất động sản
            </button>
          </div>
          <div class="col-6">
            <button type="button" class="col-6 btn btn-purpose" :class="loan? 'active' : ''" @click="activeLoan">
              Vay vốn ngân hàng
            </button>
          </div>
          <div class="col-6">
            <button type="button" class="col-6 btn btn-purpose" :class="other? 'active' : ''" @click="activeOther">
              Mục đích khác
            </button>
          </div>
        </div>
        <div class="container__selected">
          <button type="button" class="btn btn-select" :class="!buy && !sell && !loan && !other ? 'disabled' : ''" @click="handleAction">Chọn</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'ModalValuationPurposes',
	data () {
		return {
			buy: false,
			sell: false,
			loan: false,
			other: false
		}
	},
	methods: {
		activeBuy () {
			this.sell = false
			this.loan = false
			this.other = false
			this.buy = true
		},
		activeSell () {
			this.buy = false
			this.loan = false
			this.other = false
			this.sell = true
		},
		activeLoan () {
			this.buy = false
			this.sell = false
			this.other = false
			this.loan = true
		},
		activeOther () {
			this.buy = false
			this.sell = false
			this.loan = false
			this.other = true
		},
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
  .modal-purpose {
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
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
    max-width: 528px;
    width: 100%;
    margin-bottom: 0;
    border-radius: 5px;
    opacity: 0;
    transition: .3s;
    &.card__show{
      opacity: 1;
    }
  &-header {
    padding: 34px 0;
    border-bottom: none;
    display: flex;
    justify-content: center;
    .title{
      font-size: 20px;
      text-transform: uppercase;
      color: #F28C1C;
      font-weight: 700;
    }
  h3 {
    color: #333333;
  }
  img {
    cursor: pointer;
  }
  }
  &-body {
    background-image: url("../../assets/images/im_popup.png");
    background-repeat: no-repeat;
    background-size: cover;
    text-align: center;
    padding: 6px 34px 34px;
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
    &-purpose{
      width: 100%;
      margin-bottom: 15px;
      background: #F5F5F5;
      color: #555555;
      border: 1px solid #E7E7E7;
      box-sizing: border-box;
      transition: .3s;
      &.active {
        color: #FFFFFF !important;
        background: #F28C1C !important;
        &:hover {
          box-shadow: none !important;
        }
      }
      &:hover {
        box-shadow: 0 3px 2px rgba(0, 0, 0, 0.3) !important;
      }
    }
    &-select{
      background: #F28C1C;
      color: #FFFFFF;
      &.disabled {
        background: #FFFFFF;
        color: #000000;
        border: 1px solid #E7E7E7;
        box-sizing: border-box;
        cursor: not-allowed;
        opacity: 1;
      }
    }
  }
  .container{
    &__selected{
      display: flex;
      justify-content: flex-end;
      margin-top: 40px;
    }
  }
</style>
