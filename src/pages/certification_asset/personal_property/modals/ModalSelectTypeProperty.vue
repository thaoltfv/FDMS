<template>
  <div v-if="!isMobile()" @click.self="handleCancel" class="modal-purpose d-flex justify-content-center align-items-center">
    <div class="card card__show">
      <div class="card-header">
        <div class="title">
          Chọn loại tài sản thẩm định
        </div>
      </div>
      <div class="card-body">
        <!-- <div class="row">
          <lord-icon
            src="https://cdn.lordicon.com/uetqnvvg.json"
            trigger="hover"
            style="width:250px;height:250px">
        </lord-icon>
        </div> -->

        <div class="row">
          <div class="col">
            <button type="button" class="btn btn-purpose h-100" :class="machine? 'active' : ''" @click="selectMachine">
              Máy móc thiết bị
            </button>
          </div>
          <div class="col">
            <button type="button" class="btn btn-purpose h-100" :class="vehicle? 'active' : ''" @click="selectVehicle">
              Phương tiện vận tải
            </button>
          </div>
          <div class="col">
            <button type="button" class="btn btn-purpose h-100" :class="other? 'active' : ''" @click="activeOther">
              Động sản khác
            </button>
          </div>
        </div>
        <div class="container__selected">
          <button type="button" class="btn btn-select"
            :class="!other && !machine && !vehicle ? 'disabled' : ''"
            @click="handleAction">
            Chọn
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-else @click.self="handleCancel" class="modal-purpose d-flex justify-content-center align-items-center">
    <div class="card card__show">
      <div class="card-header" style="padding: 10px;">
        <div class="title">
          Chọn loại tài sản thẩm định
        </div>
      </div>
      <div class="card-body" style="    padding: 0;">
        <!-- <div class="row">
          <lord-icon
            src="https://cdn.lordicon.com/uetqnvvg.json"
            trigger="hover"
            style="width:250px;height:250px">
        </lord-icon>
        </div> -->

        <div class="row">
          <div class="col">
            <button type="button" class="btn btn-purpose h-100" :class="machine? 'active' : ''" @click="selectMachine" style="    padding: 0;">
              Máy móc thiết bị
            </button>
          </div>
          <div class="col">
            <button type="button" class="btn btn-purpose h-100" :class="vehicle? 'active' : ''" @click="selectVehicle" style="    padding: 0;">
              Phương tiện vận tải
            </button>
          </div>
          <div class="col">
            <button type="button" class="btn btn-purpose h-100" :class="other? 'active' : ''" @click="activeOther" style="    padding: 0;">
              Động sản khác
            </button>
          </div>
        </div>
        <div class="container__selected" style="margin-top: 10px;">
          <button type="button" class="btn btn-select"
            :class="!other && !machine && !vehicle ? 'disabled' : ''"
            @click="handleAction">
            Chọn
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'ModalSelectTypeProperty',
	data () {
		return {
			other: false,
			machine: false,
			vehicle: false
		}
	},
	methods: {
    isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
		activeOther () {
			this.machine = false
			this.vehicle = false
			this.other = true
		},
		selectMachine () {
			this.other = false
			this.vehicle = false
			this.machine = true
		},
		selectVehicle () {
			this.other = false
			this.machine = false
			this.vehicle = true
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			if (this.other) {
				return this.$router.push({ name: 'certification_asset.other_purpose.create' })
			} else if (this.vehicle) {
				return this.$router.push({ name: 'certification_asset.vehicle.create' })
			} else if (this.machine) {
				return this.$router.push({ name: 'certification_asset.machine.create' })
			} else {
				return this.$toast.open({
					message: 'Hiện tại chức năng này chưa được mở ở phiên bản dùng thử',
					type: 'error',
					position: 'top-right',
					duration: 5000
				})
			}
		}
	}
}
</script>

<style lang="scss" scoped>
  .modal-purpose {
    position: fixed;
    z-index: 1005;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.6);

  @media (max-width: 787px) {
    padding: 20px;
  }
  .card {
    background-image: url("../../../../assets/images/personal_property.png");
    background-image: linear-gradient(to bottom, rgba(255, 245, 245, 0.616) 0%,rgba(255, 255, 255, 0.678) 100%), url("../../../../assets/images/personal_property.png");
    background-repeat: no-repeat;
    background-size: cover;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
    max-width: 50rem;
    width: 100%;
    margin-bottom: 0;
    border-radius: 5px;
    opacity: 0;
    transition: .3s;
    &.card__show{
      opacity: 1;
    }
  &-header {
    padding: 1.5rem 0;
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
  .title_content {
    text-align: left;
  }
</style>
