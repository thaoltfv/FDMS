<template>
    <div>
      <ValidationObserver tag="form"
                          ref="observer"
                          @submit.prevent="validateBeforeSubmit">
        <div class="modal-detail d-flex justify-content-center align-items-center" >
          <div class="card">
            <div class="container-title">
              <div class="d-lg-flex d-block shadow-bottom">
                <h2 class="title">Thông tin về công trình xây dựng</h2>
              </div>
            </div>
            <div class="contain-detail">
              <div class="row">
                  <div class="col-12 col-lg-6">
                    <InputCategory
                        v-model="form.building_type_id"
                        vid="building_type_id"
                        label="Loại"
                        rules="required"
                        class="form-group-container"
                        :disabled="true"
                        :options="optionsHousingType"
                        @change="changeBuildingType($event)"
                    />
                  </div>
                  <div class="col-12 col-lg-6">
                    <InputCategoryBoolean
                        v-model="form.gpxd"
                        vid="gpxd"
                        :disabled="true"
                        label="Giấy phép xây dựng"
                        class="form-group-container"
                        :options="optionsGPXD"
                    />
                  </div>
                  <div class="col-12 col-lg-12" v-if="form.building_type_id !== 148" >
                    <InputText
                        v-model="form.tangible_name"
                        vid="tangible_name"
                        label="Tên công trình xây dựng"
                        rules="required"
                        :disabledInput="true"
                        class="form-group-container"
                    />
                  </div>
                  <!-- v-if="building === 'NHÀ Ở RIÊNG LẺ'" -->
                  <div class="col-12 col-lg-6"  v-if="form.building_type_id === 41">
                    <InputCategory
                        v-model="form.building_category_id"
                        vid="building_category_id"
                        label="Cấp nhà"

                        :disabled="true"
                        class="form-group-container"
                        :options="optionsHousing"
                        @change="changeCategoryBuilding"
                        hidden
                    />
                  </div>
                  <!-- v-if="building === 'BIỆT THỰ'" -->
                  <div class="col-12 col-lg-6 " v-if="form.building_type_id === 42">
                    <InputCategory
                        v-model="form.structure_id"
                        vid="structure_id"
                        label="Cấu trúc"
                        rules="required"
                        :disabled="true"
                        class="form-group-container"
                        :options="optionsStructure"
                    />
                  </div>
                  <!-- v-if="building === 'NHÀ Ở RIÊNG LẺ' || building === 'BIỆT THỰ'" -->
                  <div class="col-12 col-lg-6" v-if="form.building_type_id === 41 || form.building_type_id === 42" >
                    <InputCategory
                        v-model="form.rate_id"
                        vid="rate_id"
                        label="Hạng nhà"

                        :disabled="true"
                        class="form-group-container"
                        :options="optionsRate"
                        hidden
                    />
                  </div>
                  <!-- v-if="building === 'NHÀ XƯỞNG (KHO)'" -->
                  <div class="col-12 col-lg-6" v-if="form.building_type_id === 43">
                    <InputCategory
                        v-model="form.crane_id"
                        vid="crane_id"
                        label="Cẩu trục"
                        rules="required"
                        :disabled="true"
                        class="form-group-container"
                        :options="optionsCrane"
                    />
                  </div>
                  <!-- v-if="building === 'NHÀ XƯỞNG (KHO)'" -->
                  <div class="col-12 col-lg-6 " v-if="form.building_type_id === 43" >
                    <InputCategory
                        v-model="form.aperture_id"
                        vid="aperture_id"
                        label="Khẩu độ"
                        rules="required"
                        :disabled="true"
                        class="form-group-container"
                        :options="optionsAperture"
                    />
                  </div>
                  <!-- v-if="building === 'NHÀ XƯỞNG (KHO)'" -->
                  <div class="col-12 " v-if="form.building_type_id === 43" >
                    <InputCategory
                        v-model="form.factory_type_id"
                        vid="factory_type_id"
                        label="Loại nhà máy"
                        rules="required"
                        :disabled="true"
                        class="form-group-container"
                        :options="optionsFactionType"
                    />
                  </div>
                  <!-- v-if="building === 'CÔNG TRÌNH KHÁC'" -->
                  <div class="col-12 col-lg-6" v-if="form.building_type_id === 148" >
                    <InputText
                        v-model="form.tangible_name"
                        vid="tangible_name"
                        label="Tên công trình"
                        rules="required"
                        :disabledInput="true"
                        class="form-group-container"
                    />
                  </div>
                  <!-- v-if="building !== 'CÔNG TRÌNH KHÁC'" -->
                  <div class="col-12 col-lg-6" v-if="form.building_type_id !== 148" >
                    <InputNumberNew
                        class="form-group-container"
                        v-model="form.floor"
                        vid="floor_building"
                        label="Số tầng"
                        rules="required"
                        :max="999999"
                        :min="-999999"
                        :decimal="0"
                        :disabled="true"
                        @change="changeFloor($event)"
                    />
                  <span class="text-error" v-if="form.floor !== '' && form.floor <= 0">Vui lòng nhập số tầng thích hợp</span>
                  </div>
                  <!-- v-if="building !== 'CÔNG TRÌNH KHÁC'" -->
                  <div class="col-12 col-lg-6" >
                    <InputCategory
                        v-model="form.start_using_year"
                        class="form-group-container"
                        vid="start_using_year"
                        label="Năm sử dụng"
                        rules="required"
                        @change="changeUsingYear"
                        :disabled="true"
                        :options="optionYearBuild"
                    />
                  </div>
                  <div class="col-12 col-lg-6 input-contain">
                    <InputArea
                        v-model="form.total_construction_area"
                        vid="total_construction_area"
                        class="form-group-container"
                        label="Diện tích xây dựng"
                        :max="99999999"
                        :decimal="2"

                        :disabled="true"
                        @change="changeArea($event)"
                    />
                  </div>
                  <div class="col-12 col-lg-6 input-contain">
                    <InputArea
                        v-model="form.total_construction_base"
                        vid="total_construction_base"
                        class="form-group-container"
                        label="Diện tích sàn"
                        :max="99999999"
                        :decimal="2"
                        rules="required"
                        :disabled="true"
                        @change="totalConstructionBase($event)"
                    />
                  </div>
                  <div class="col-12 col-lg-6 input-contain">
                    <InputNumberNew
                        class="form-group-container"
                        v-model="form.duration"
                        vid="floor"
                        label="Niên hạn"
                        :max="999999"
                        :min="0"
                        :decimal="0"
                        rules="required"
                        :disabled="true"
                        @change="changeDuration($event)"
                    />
                  </div>
                  <div class="col-12 col-lg-6 input-contain">
                    <InputPercent
                        :key="render_key"
                        v-model="form.remaining_quality"
                        vid="remaining_quality"
                        label="Chất lượng còn lại theo tuổi đời"
                        :max="100"
                        :decimal="0"
                        :disabled="true"
                        rules="required"
                        class="form-group-container"
                    />
                  </div>
                  <div class="col-12 col-lg-12 input-contain">
                    <div class="row">
                        <InputTextarea
                        label="Mô tả công trình xây dựng"
                        v-model="form.contruction_description"
                        rules="required"
                        vid="contruction_description"
                        :rows="10"
                        :maxLength="1000"
                        :disableInput="true"
                        class="col-12 form-group-container"
                        />
                    </div>
                  </div>
              </div>
            </div>
            <div class="container-title container-title__footer">
              <div class="d-lg-flex d-block justify-content-end shadow-bottom">
                <button class="btn btn-white" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
              </div>
            </div>
          </div>
        </div>
      </ValidationObserver>
    </div>
  </template>

<script>
import InputPercent from '@/components/Form/InputPercent'
import InputNumberNew from '@/components/Form/InputNumberNew'
import InputCategoryBoolean from '@/components/Form/InputCategoryBoolean'
import InputArea from '@/components/Form/InputArea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputSwitch from '@/components/Form/InputSwitch'
import InputLengthArea from '@/components/Form/InputLengthArea'
import moment from 'moment'
export default {
	name: 'ModalBuildingDetail',
	props: ['data', 'housingTypes', 'buildingCategories', 'buildingStructure', 'buildingRates', 'buildingAperture', 'buildingFactoryType', 'isHaveContruction', 'buildingCrane'],
	components: {
		InputCategory,
		InputText,
		InputSwitch,
		InputTextarea,
		InputLengthArea,
		InputDatePicker,
		InputArea,
		InputCategoryBoolean,
		InputNumberNew,
		InputPercent
	},
	data () {
		return {
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			built_years: [],
			GPXDType: [
				{ id: 1, description: 'Có giấy phép' },
				{ id: 0, description: 'Không có giấy phép' }
			],
			render_key: 1231555
		}
	},

	computed: {
		optionsHousingType () {
			return {
				data: this.housingTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsGPXD () {
			return {
				data: this.GPXDType,
				id: 'id',
				key: 'description'
			}
		},
		optionsHousing () {
			return {
				data: this.buildingCategories,
				id: 'id',
				key: 'description'
			}
		},
		optionYearBuild () {
			return {
				data: this.built_years,
				id: 'year',
				key: 'year'
			}
		},
		optionsStructure () {
			return {
				data: this.buildingStructure,
				id: 'id',
				key: 'description'
			}
		},
		optionsRate () {
			return {
				data: this.buildingRates,
				id: 'id',
				key: 'description'
			}
		},
		optionsAperture () {
			return {
				data: this.buildingAperture,
				id: 'id',
				key: 'description'
			}
		},
		optionsFactionType () {
			return {
				data: this.buildingFactoryType,
				id: 'id',
				key: 'description'
			}
		},
		optionsCrane () {
			return {
				data: this.buildingCrane,
				id: 'id',
				key: 'description'
			}
		}
	},
	mounted () {
		this.handleBuiltYear()
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
		changeFloor (event) {
			this.form.floor = event
		},
		totalConstructionBase (event) {
			this.form.total_construction_base = event
		},
		changeArea (event) {
			this.form.total_construction_area = event
		},
		changeBuildingType (event) {
			this.form.building_type = this.housingTypes.filter(item => item.id === event)[0]
		},
		changeCategoryBuilding () {

		},
		changeUsingYear (event) {
			if (event) {
				if (this.form.created_at) {
					if ((+moment(this.form.created_at).format('YYYY') - this.form.start_using_year) > this.form.duration) {
						this.form.remaining_quality = 0
					} else this.form.remaining_quality = parseFloat((1 - (+moment(this.form.created_at).format('YYYY') - this.form.start_using_year) / this.form.duration) * 100).toFixed(0)
				} else {
					if (((new Date()).getFullYear() - this.form.start_using_year) > this.form.duration) {
						this.form.remaining_quality = 0
					} else this.form.remaining_quality = parseFloat((1 - ((new Date()).getFullYear() - this.form.start_using_year) / this.form.duration) * 100).toFixed(0)
				}
			}
			this.render_key += 1
		},
		changeDuration (event) {
			if (event) {
				this.form.duration = parseFloat(event).toFixed(0)
				if (this.form.created_at) {
					if ((+moment(this.form.created_at).format('YYYY') - this.form.start_using_year) > this.form.duration) {
						this.form.remaining_quality = 0
					} else this.form.remaining_quality = +parseFloat((1 - (+moment(this.form.created_at).format('YYYY') - this.form.start_using_year) / this.form.duration) * 100).toFixed(0)
				} else {
					if (((new Date()).getFullYear() - this.form.start_using_year) > this.form.duration) {
						this.form.remaining_quality = 0
					} else this.form.remaining_quality = +parseFloat((1 - ((new Date()).getFullYear() - this.form.start_using_year) / this.form.duration) * 100).toFixed(0)
				}
			} else {
				this.form.duration = ''
				this.form.remaining_quality = ''
			}
			this.render_key += 1
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleAction()
			}
		},
		handleAction () {
			this.$emit('action', this.form)
		}
	},
	beforeMount () {
	}
}
</script>

  <style lang="scss" scoped>
  .title{
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 25px;
    color: #000000;
  }
  .modal-detail {
    position: fixed;
    z-index: 1031;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.6);
    .card {
      border-radius: 5px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
      max-width: 1300px;
      width: 100%;
      max-height: 90vh;
      margin-bottom: 0;
      padding: 35px 95px;
      @media (max-width: 787px) {
        padding: 20px 10px;
      }
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
  .card{
    .contain-detail{
      overflow-y: auto;
      overflow-x: hidden;
      margin-top: 20px;
      &::-webkit-scrollbar{
        width: 2px;
      }
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-table{
      border-radius: 5px;
      background: #FFFFFF;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
      width: 99%;
      margin: 50px auto 50px;
    }
    &-body{
      padding: 35px 30px 40px;
    }
    &-info{
      .title{
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .img{
    margin-right: 13px;
  }
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);;
    background: #FFFFFF;
    margin-bottom: 75px;
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
    }
    &-info{
      .title{
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .card__order{
    max-width: 50%;
    margin-bottom: 1.25rem;
    @media (max-width: 767px) {
      max-width: 100%;
    }
  }
  .btn{
    &-white{
      max-height: none;

      line-height: 19.07px;
      margin-right: 15px;
      &:last-child{
        margin-right: 0;
      }
    }
    &-contain{
      margin-bottom: 55px;
    }
  }
  .d-grid{
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-column-gap: 8.9%;
    &:first-child {
      margin-top: 0;
    }
    &__checkbox{
      grid-template-columns: 1fr 1fr;
    }
    @media (max-width: 767px) {
      grid-template-columns: 1fr;
    }
  }
  .content{
    &-detail{
    }
    &-title{
      color: #555555;
      margin-bottom: 5px;

      font-weight: 500;
    }
    &-name{
      font-size: 1.125rem;
      color: #000000;
      margin-bottom: 15px;
      font-weight: 600;
      &__code{
        color: #FAA831;
      }
    }
  }
  .contain-table{
    @media (max-width: 767px) {
      overflow-y: hidden;
      overflow-x: auto;
    }
    .table-property{
      width: 100%;
    }
  }
  .table-property{
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 500;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          border-left: none;
          width: 180px
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 14px;
      }
    }
  }
  .img-content{
    color: #000000;

    font-weight: 600;
    span{
      font-weight: 500;
      margin-left: 10px;
    }
  }
  .input-code{
    color: #000000;
    border-radius: 5px;
    width: 180px;
    border: 1px solid #000000;
    background: #f5f5f5;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  //.img-contain {
  //  max-width: 200px;
  //  max-height: 200px;
  //  height: auto;
  //  margin-left: 20px;
  //  &__table{
  //    margin: auto;
  //    max-width: 50px;
  //    img{
  //      cursor: pointer;
  //      display: flex;
  //      justify-content: center;
  //    }
  //  }
  //  img{
  //    object-fit: contain;
  //    width: 100%;
  //    height: auto;
  //    max-width: 200px;
  //    max-height: 200px;
  //  }
  //}
  .img-contain {
    aspect-ratio: 1/1;
    overflow: hidden;
    img{
      height: 100%;
      cursor: pointer;
      object-fit: cover;
    }
    &__table{
      margin: auto;
      max-width: 50px;
      max-height: 50px;
      img{
        object-fit: cover;
        object-position: top;
        cursor: pointer;
        display: flex;
        justify-content: center;
        max-width: 50px;
        max-height: 50px;
      }
    }
  }
  .container-title{
    margin: -35px -95px auto;
    padding: 35px 95px 0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    .title{
      font-size: 1.2rem;
      @media (max-width: 767px) {
        font-size: 1.125rem;
      }
    }
    &__footer{
      margin: auto -95px -35px;
      padding: 20px 95px 20px;
      @media (max-width: 767px) {
        .btn-white{
          margin-bottom: 20px;
        }
      }
    }
  }
  .container-img{
    padding: .75rem 0;
    border: 1px solid #0b0d10;
  }
  </style>
