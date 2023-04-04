<template>
  <div class="container__result">
    <div class="container container__detail">
      <div class="container__total" v-if="result.result.status === 1">
        <div class="total__estimate">
          <p class="title">Đơn giá</p>
          <p class="price">{{format(result.result.unit_price)}}/M<sup>2</sup></p>
        </div>
        <div class="total__estimate">
          <p class="title">Tổng giá trị tài sản</p>
          <p class="price">{{format(result.result.total_price)}}đ</p>
        </div>
        <div class="d-flex justify-content-end align-items-center">
          <div>
            <button class="btn btn-reliability" :class="result.reliability === 1 ? '' : result.reliability === 2 ? 'btn-reliability--orange' : 'btn-reliability--red'" @click="handlePropertyEstimate"> Độ tin cậy
              {{ result.reliability === 1 ? 'cao' : result.reliability === 2 ? 'trung bình' : 'thấp' }}</button>
          </div>
        </div>
      </div>
      <div class="container__total justify-content-center" v-if="result.result.status === 0">
        <div class="total__estimate total__estimate--error">
          <p class="price">{{result.result.error_message}}</p>
        </div>
      </div>
      <ModalPropertyEstimate
        v-if="openModalPropertyEstimate"
        :assets="this.result.assets"
        @cancel="openModalPropertyEstimate = false"
      />
    </div>
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import ModalPropertyEstimate from '@/components/Modal/ModalPropertyEstimate'
export default {
	name: 'ResultEstimate',
	props: ['result'],
	data () {
		return {
			date: '',
			errormessage: '',
			assets: [],
			reliability: null,
			openModalPropertyEstimate: false,
			total_price: '',
			total: {
				area: 0,
				price: 0
			},
			total_building: {
				area: 0,
				price: 0
			}
		}
	},
	created () {

	},
	mounted () {
		this.getNow()
	},
	components: {
		ModalPropertyEstimate,
		InputCategory,
		InputText
	},
	computed: {
	},
	methods: {
		getNow () {
			const today = new Date()
			const date = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear()
			this.date = date
		},
		handlePropertyEstimate () {
			this.openModalPropertyEstimate = true
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		onScrollToTop () {
			window.scrollTo(0, 0)
		}
	}
}
</script>

<style lang="scss" scoped>
.container {
  &__result {
    max-width: 1545px;
    border-radius: 5px;
    margin: 40px auto 0;
    padding: 30px 30px 50px;
    .title{
      text-transform: uppercase;
      text-align: center;
      font-weight: 700;
      font-size: 30px;
      color: #000000;
      margin-bottom: 60px;
    }
    @media (max-width: 767px) {
      padding: 20px;
    }
  }
  &__time{
    margin-top: 10px;
    color: #000000;
    font-weight: 700;
    font-size: 20px;
    .user, .date {
      margin-bottom: 10px;
    }
  }
  &__total{
    .input-total{
      height: 40px;
      width: 306px;
      background: #E5E5E5;
      border-radius: 5px;
    }
  }
}
.land__input{
  margin-top: 18px;
}
.ic{
  &__delete{
    cursor: pointer;
  }
}
.btn{
  &-orange{
    &__result{
      width: 204px;
      margin-bottom: 20px;
    }
  }
}
.total {
  &__estimate {
    display: flex;
    justify-content: space-between;
    align-items: end;
    border-bottom: 1px solid #999999;
    .title {
      margin-bottom: 0;
      font-size: 1.125rem;
      color: #999999;
      text-transform: none;
      font-weight: 500;
      @media (max-width: 767px) {
        text-align: left;
      }
    }
    .price{
      font-size: 40px;
      color: #F28C1C;
      font-weight: 700;
      margin-bottom: 0;
      @media (max-width: 767px) {
        font-size: 30px;
      }
    }
    @media (max-width: 767px) {
      display: block;
      text-align: left;
    }
    &--error {
      justify-content: center;
      border-bottom: none;
    }
  }
}
.btn {
  &-reliability {
    margin-top: 20px;
    background: #1F8B24;
    color: #FFFFFF;
    &--orange{
      background: #FAA831;
    }
    &--red {
      background: #EF3039;
    }
  }
}
.estimate__empty {
  display: flex;
  width: 100%;
  justify-items: center;
  align-items: center;
  text-align: center;
  font-size: 24px;
  height: 35vh;
  p {
    width: 100%;
    text-align: center;
  }
}
.scroll-to-top {
  position: absolute;
  bottom: 30px;
  right: 30px;
  cursor: pointer;
  background: #faa831;
  border-radius: 50%;
  height: 50px;
  width: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  img {
    height: 30px;
  }
}
</style>
