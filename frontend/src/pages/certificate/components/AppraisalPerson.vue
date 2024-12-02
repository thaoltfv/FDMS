<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Tổ thẩm định</h3>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard">
      <div class="container-fluid">
        <div class="row justify-content-between">
          <InputCategory
            v-model="data.appraiser_id"
            vid="appraiser"
            label="Thẩm định viên"
            rules="required"
            class="col-12 col-md-4 col-lg-3 form-group-container"
            :options="optionsAppraiser"
          />
          <div class="col-12 col-md-4 col-lg-3 form-group-container">
            <label class="color-black font-weight-bold">Giám đốc</label>
            <div class="form-control disabled"><p class="mb-0">{{appraisersManager.length > 0 ? appraisersManager[0].name : name}}</p></div>
          </div>
          <InputCategory
            v-model="data.appraiser_confirm_id"
            vid="staff"
            label="KT - Giám đốc"
            class="col-12 col-md-4 col-lg-4 form-group-container"
            :options="optionsAppraiser"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
export default {
	name: 'AppraisalPerson',
	props: ['data', 'appraisersManager', 'appraisers'],
	computed: {
		optionsAppraiser () {
			return {
				data: this.appraisers,
				id: 'id',
				key: 'name'
			}
		}
	},
	components: {
		InputCategory,
		InputText
	},
	data () {
		return {
			showCard: true,
			name: '',
			directions: [],
			form: {
				appraiser: '',
				manager: '',
				staff: ''
			}
		}
	},
	created () {
	}
}
</script>

<style scoped lang="scss">
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 768px) {
      margin-bottom: 20px;
    }
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      @media (max-width: 768px) {
        padding: 12px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 787px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
        &-highlight {
          background: rgba(252, 194, 114, 0.53);
          text-align: center;
          padding: 10px 0;
          border-radius: 2px;
        }
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .form-group-container {
    margin-top: 15px;
  }
  .color-black{
    color: #333333;
  }
  .btn-delete{
    cursor: pointer;
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border: 0.777778px solid #000000;
    border-radius: 5.88235px;
    padding: 10px;
    margin: auto;
    width: 36px;
    height: 36px;
    img{
      width: 100%;
      height: auto;
    }
  }
  .btn {
    &-orange {
      background: #FAA831;
      text-align: center;
      border-radius: 5px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
      height: 35px;
      width: 100px;
      color: #fff;
      margin: 15px 0 0;
      box-sizing: border-box;
      &:hover{
        border-color: #dc8300;
      }
    }
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
</style>
