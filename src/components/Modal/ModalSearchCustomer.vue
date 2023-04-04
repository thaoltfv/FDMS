<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="card">
          <div class="btn--contain">
            <div class="btn--cancel" @click="handleCancel">
              <img src="../../assets/icons/ic_cancel-1.svg" alt="cancel">
            </div>
          </div>
          <div class="content-detail">
          <div class="input--search">
              <InputText
                v-model="filter.search"
                label="VC bất kỳ"
                class="contain-input contain-input__any contain-input__customer"
              />
            <div class="input-grid">
              <InputText
                v-model="filter.id"
                vid="id"
                label="Mã khách hàng"
                class="contain-input contain-input__info"
              />
              <InputText
                v-model="filter.name"
                vid="name"
                label="Tên khách hàng"
                class="contain-input contain-input__info"
              />
              <InputCategory
                v-model="filter.status"
                vid="info"
                label="Trạng thái"
                class="contain-input contain-input__info"
                :options="opstionStatus"
              />

              <InputText
                v-model="filter.tax_code"
                vid="tax_code"
                label="Mã số code"
                class="contain-input contain-input__info"
              />
              <InputText
                v-model="created_date"
                vid="province_id"
                label="Ngày tạo"
                type="date"
                class="contain-input contain-input__info"
                @change="changeDate(created_date)"
              />
              <InputText
                v-model="filter.created_by"
                vid="created_by"
                label="Người tạo"
                class="contain-input contain-input__info"
              />
            </div>
            <InputText
              v-model="filter.address"
              vid="address"
              label="Địa chỉ"
              class="contain-input contain-input__any contain-input__customer" style="margin-top: 28px"
            />
          </div>
          </div>
          <div>
            <button class="btn btn--search" @click.prevent="search(filter)">Tìm kiếm</button>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputCategory from '@/components/Form/InputCategory'
import InputDatePickerRange from '@/components/Form/InputDatePickerRange'
import WareHouse from '@/models/WareHouse'
import moment from 'moment'
export default {
	name: 'ModalSearchAdvanced',
	components: {
		InputCategory,
		InputNumberFormat,
		InputDatePickerRange,
		InputText
	},
	props: ['roles', 'filter_search'],
	data () {
		return {
			showSearch: false,
			propertyTypes: [],
			infoSources: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			users: [],
			status: [
				{
					id: 'active',
					name: 'Đang hoạt động'
				},
				{
					id: 'inactive',
					name: 'Đang vô hiệu hóa'
				}
			],
			created_date: '',
			filter: {
				id: '',
				name: '',
				status: '',
				tax_code: '',
				created_date: '',
				created_by: '',
				search: '',
				address: ''
			}
		}
	},
	mounted () {
		if (this.filter_search !== undefined && this.filter_search !== null) {
			this.filter.search = this.filter_search.search
			this.filter.id = this.filter_search.id
			this.filter.name = this.filter_search.name
			this.filter.status = this.filter_search.status
			this.filter.tax_code = this.filter_search.tax_code
			this.filter.created_date = this.filter_search.created_date
			this.filter.created_by = this.filter_search.created_by
		}
	},
	computed: {
		optionCreateBy () {
			return {
				data: this.users,
				id: 'name',
				key: 'name'
			}
		},
		opstionStatus () {
			return {
				data: this.status,
				id: 'id',
				key: 'name'
			}
		},
		optionsType () {
			return {
				data: this.propertyTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsInfo () {
			return {
				data: this.infoSources,
				id: 'id',
				key: 'description'
			}
		}
	},
	methods: {
		search (filter) {
			this.$emit('filter-changed', this.filter)
			this.$emit('cancel', event)
			this.$router.push({
				name: 'customer.index',
				query: {
					search: filter.search,
					id: filter.id,
					name: filter.name,
					status: filter.status,
					tax_code: filter.tax_code,
					create_date: filter.created_date,
					created_by: filter.created_by
				}
			}).catch(_ => {})
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async getPropertyType () {
			try {
				const resp = await WareHouse.getPropertyType()
				this.propertyTypes = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
			}
		},
		changeDate (value) {
			this.filter.created_date = moment(String(value)).format('DD/MM/YYYY')
		},
		reset () {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = ''
				}
			}
			this.$emit('filter-changed', this.filter)
		}
	},
	beforeMount () {
	}
}
</script>

<style lang="scss" scoped>

.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);
  @media (max-width: 787px) {
    padding: 20px;
  }
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1200px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 20px 50px;
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
.content-detail{
  overflow-y: auto;
  overflow-x: hidden;
}
.btn {
  &--contain {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 18px;
  }
  &--cancel{
    width: 13px;
    height: auto;
    cursor: pointer;
    img{
      width: 100%;
    }
  }
  &--more{
    padding: 9px;
    color: #FFFFFF;
    background: #FAA831;
    box-shadow: 0 1px 4px rgba(250, 168, 49, 0.25);
    border-radius: 5px;
    margin-top: 26px;
    font-weight: 600;
    font-size: 12px;
  }
  &--search {
    padding: 10px 48px;
    color: #FFFFFF;
    background: #FAA831;
    border-radius: 5px;
    margin-top: 40px;
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 18px;
    float: right;
  }
  .icon{
    font-weight: bold;
    margin-left: 10px;
    transition: .3s;
    &__hide{
      transform: rotate(180deg) !important;
      transition: .3s;
    }
  }
}
.input{
  &-grid{
    margin-top: 28px;
    display: grid;
    grid-template-columns: 1fr 341px 1fr;
    grid-column-gap: 33px;
    grid-row-gap: 25px;
    &__info{
      white-space: nowrap !important;
      grid-template-columns: 1fr 1fr 1fr;
      margin-bottom: 27px;
    }
    @media (max-width: 768px) {
      grid-template-columns: 1fr;
    }
  }
}
.price-range{
  .line{
    margin: auto 11px;
  }
  .input-price{
    font-size: 12px;
    height: 25px;
    border-radius: 5px;
    border: 1px solid #555555;
    box-sizing: border-box;
    width: 48%;
    &:hover, &:active, &:focus{
      border: 1px solid #555555;
    }
  }
}
.hr {
  border: 1px solid #D9D9D9;
  background: #D9D9D9;
}
.slider{
  margin-top: 24px;
  position: relative;
  width: 80%;
  height: 5px;
  background: #D9D9D9;
  &-range{
    position: absolute;
    top: 0;
    height: 100%;
    width: 80%;
    background: #FAA831;
  }
  &-handle{
    height: 14px;
    width: 14px;
    top: -6px;
    border-radius: 2px;
    border: solid 2px #c5a37d;
    background: #c5a37d;
  }
}
.range-slider {
  width: 100%;
  margin: auto;
  text-align: center;
  position: relative;
}

.range-slider input[type=range] {
  position: absolute;
  left: 0;
  bottom: -24px;
  @media (max-width: 768px) {
    bottom: -55px;
  }
}

input[type=number] {
  border: 1px solid #ddd;
  text-align: center;
  font-size: 1.6em;
  -moz-appearance: textfield;
}

input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
  -webkit-appearance: none;
}

input[type=number]:invalid,
input[type=number]:out-of-range {
  border: 2px solid #ff6347;
}

input[type=range] {
  -webkit-appearance: none;
  width: 100%;
}

input[type=range]:focus {
  outline: none;
}

input[type=range]:focus::-webkit-slider-runnable-track {
  background: #FAA831;
}

input[type=range]:focus::-ms-fill-lower {
  background: #FAA831;
}

input[type=range]:focus::-ms-fill-upper {
  background: #FAA831;
}

input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 5px;
  cursor: pointer;
  animate: 0.2s;
  background: #FAA831;
  border-radius: 1px;
  box-shadow: none;
  border: 0;
}

input[type=range]::-webkit-slider-thumb {
  z-index: 2;
  position: relative;
  box-shadow: 0 0 0 #000;
  border: 1px solid #FAA831;
  height: 18px;
  width: 18px;
  border-radius: 25px;
  background: #f8bd6e;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -7px;
}
.row__total{
  margin-top: 44px;
}
.total-title{
  color: #000000;
  font-size: 14px;
  font-weight: 600;
  @media (max-width: 768px) {
    padding: 20px 0;
  }
}
.row{
  margin-right: -26px;
  margin-left: -26px;
}
.row>*{
  padding-right: 26px;
  padding-left: 26px;
}
</style>
