<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Thông tin chi tiết căn hộ</h3>
        <img class="img-dropdown" :class="showApartment? '' : 'img-dropdown__hide'" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showApartment = !showApartment">
      </div>
    </div>
    <div class="card-body card-info" v-if="showApartment">
      <div class="container-fluid">
        <div class="row justify-content-between">
          <InputText
            v-model='apartment_specification.apartment_name'
            vid="apartment_specification.apartment_name"
            label="Mã căn hộ"
            rules="required"
            class="col-12 col-lg-4 form-group-container"
          />
          <InputCategory
              v-model='room_details.loai_can_ho_id'
              vid="loai_can_ho_id"
              label="Loại căn hộ"
              rules="required"
              class="col-12 col-lg-4 form-group-container"
              :options="optionsLoaiCanHo"
            />
          <InputArea
            v-model="room_details.area"
            vid="area"
            label="Diện tích (m²)"
            rules="required"
            :max="99999999"
            @change="handleArea($event)"
            class="col-12 col-lg-4 form-group-container"
          />
          <InputNumberNoneFormat
            v-model="room_details.bedroom_num"
            vid="bedroom_num"
            label="Số phòng ngủ"
            rules="required"
            :max="9999"
            @change="handleBedroomNum($event)"
            class="col-12 col-lg-4 form-group-container"
          />
          <InputNumberNoneFormat
            v-model="room_details.wc_num"
            vid="wc_num"
            label="Số phòng WC"
            rules="required"
            :max="9999"
            @change="handleWCNum($event)"
            class="col-12 col-lg-4 form-group-container"
          />
          <InputCategory
            v-model="apartment_specification.handover_year"
            class="col-12 col-lg-4 form-group-container"
            vid="handover_year"
            label="Năm sử dụng"
            rules="required"
            @change="changeUsingYear"
            :options="optionYearBuild"
          />
          <InputCategory
            v-model='room_details.direction_id'
            vid="direction_id"
            label="Hướng chính"
            rules="required"
            class="col-12 col-lg-4 form-group-container"
            :options="optionDirection"
          />
          <InputCategory
            v-model='room_details.furniture_quality_id'
            vid="furniture_quality_id"
            label="Tình trạng nội thất"
            rules="required"
            class="col-12 col-lg-4 form-group-container"
            :options="optionFurniture"
          />
          <!-- <InputCategory
            v-model='room_details.furniture_quality_id'
            vid="furniture_quality_id"
            label="Tình trạng nội thất"
            rules="required"
            class="col-12 col-lg-4 form-group-container"
            :options="optionFurniture"
          /> -->
          <div class="col-12 col-lg-4 form-group-container"></div>
          <InputTextarea
            label="Mô tả"
            v-model="room_details.description"
            vid="description"
            :rows="4"
            :maxLength="1000"
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
import InputSwitch from '@/components/Form/InputSwitch'
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import InputArea from '@/components/Form/InputArea'
import InputTextarea from '@/components/Form/InputTextarea'
import ModalDeleteIndex from '@/components/Modal/ModalDeleteIndex'
export default {
	name: 'ApartmentInfoDetail',
	props: ['room_details', 'apartment_id', 'apartments', 'directions', 'furniture_list', 'apartment_specification', 'loai_can_ho'],
	components: {
		InputCategory,
		InputText,
		InputSwitch,
		InputNumberNoneFormat,
		ModalDeleteIndex,
		InputTextarea,
		InputArea
	},
	data () {
		return {
			openModalDelete: false,
			showApartment: true,
			built_years: [],
			form: {
				apartment_id: this.apartment_id ? this.apartment_id : '',
				two_sides_room: false,
				area: '',
				bedroom_num: '',
				wc_num: '',
				direction_id: '',
				room_furniture_details: [
					{
						name: '',
						number: '',
						description: ''
					}
				],
				furniture_quality: ''
			}
		}
	},
	computed: {
    optionsLoaiCanHo () {
			return {
				data: this.loai_can_ho,
				id: 'id',
				key: 'description'
			}
		},
		optionDirection () {
			return {
				data: this.directions,
				id: 'id',
				key: 'description'
			}
		},
		optionFurniture () {
			return {
				data: this.furniture_list,
				id: 'id',
				key: 'description'
			}
		},
		optionsApartments () {
			return {
				data: this.apartments,
				id: 'id',
				key: 'name'
			}
		},
		optionYearBuild () {
			return {
				data: this.built_years,
				id: 'year',
				key: 'year'
			}
		}
	},
	methods: {
		handleBuiltYear () {
			const year = new Date().getFullYear()
			for (let i = 1970; i <= year; i++) {
				this.built_years.push({ year: i })
			}
			function compare (a, b) {
				if (a.year > b.year) { return -1 }
				if (a.year < b.year) { return 1 }
				return 0
			}
			return this.built_years.sort(compare)
		},
		changeUsingYear (event) {

		},
		handleChangeApartment (event) {
			this.$emit('changeAparment', event)
		},
		handleArea (event) {
			this.room_details.area = event
			this.$emit('changeAreaApartment', event)
		},
		handleBedroomNum (event) {
			this.room_details.bedroom_num = event
			this.$emit('changeBedroomNum', event)
		},
		handleWCNum (event) {
			this.room_details.wc_num = event
			this.$emit('changeWCNum', event)
		}
	},
	beforeMount () {
		this.handleBuiltYear()
	}
}
</script>

<style scoped lang="scss">
.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;
    &__img {
      padding: 8px 20px;
    }
    h3 {
      color: #007EC6;
    }
    @media (max-width: 768px) {
      padding: 12px;
    }

    .title {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body {

    @media (max-width: 787px) {
      padding: 15px;
    }
  }
  &-sub_header_title {
    padding: 15px 24px;
  }
  &-info {
    .title {
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
  &-land {
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
