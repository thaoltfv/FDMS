<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Cơ sở giá trị, cách tiếp cận và phương pháp thẩm định</h3>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard">
      <div class="container-fluid">
        <div class="row justify-content-between">
          <div class="col-12 col-md-6 form-group-container">
            <label class="form-label">Cơ sở giá trị của tài sản thẩm định giá</label>
            <ul>
              <li v-for="item in basis" :key="item.id">{{item.name}}</li>
            </ul>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Nguyên tắc thẩm định</label>
            <ul>
              <li v-for="item in principle" :key="'principle' + item.id">{{item.name}}</li>
            </ul>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Cách tiếp cận</label>
            <ul>
              <li v-for="item in approaches" :key="'approaches' + item.id">{{item.name}}</li>
            </ul>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Phương pháp sử dụng</label>
            <ul>
              <li v-for="item in method" :key="'method' + item.id">{{item.name}}</li>
            </ul>
          </div>
          <InputTextarea
            label="Giả thiết và giả thiết đặc biệt"
            v-model="data.document_description"
            vid="document_description"
            :rows="2"
            class="col-12 form-group-container"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputTextarea from '@/components/Form/InputTextarea'
import moment from 'moment'
export default {
	name: 'AppraiseInfo',
	props: ['data', 'appraisalFacility', 'approach', 'methodsUsed', 'appraisalPrinciples', 'basis', 'principle', 'approaches', 'method'],
	computed: {
		optionsAppraisalPurposes () {
			return {
				data: this.appraisalPurposes,
				id: 'id',
				key: 'name'
			}
		},
		optionsAppraisalFacility () {
			return {
				data: this.appraisalFacility,
				id: 'id',
				key: 'name'
			}
		},
		optionsApproach () {
			return {
				data: this.approach,
				id: 'id',
				key: 'name'
			}
		},
		optionsMethodsUsed () {
			return {
				data: this.methodsUsed,
				id: 'id',
				key: 'name'
			}
		},
		optionsAppraisalPrinciples () {
			return {
				data: this.appraisalPrinciples,
				id: 'id',
				key: 'name'
			}
		}
	},
	components: {
		InputCategory,
		InputText,
		InputDatePicker,
		InputTextarea
	},
	data () {
		return {
			showCard: true,
			directions: [],
			form: {
				appraise_date: '',
				appraisal_time: '',
				appraiser: '',
				manager: '',
				staff: '',
				note: ''
			}
		}
	},
	methods: {
		changeAppraiseDate () {
			if (this.form.appraise_date && this.form.appraise_date !== '') {
				this.data.appraise_date = moment(this.form.appraise_date, 'DD/MM/YYYY').format('YYYY-MM-DD')
			} else {
				this.data.appraise_date = ''
			}
		}
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
