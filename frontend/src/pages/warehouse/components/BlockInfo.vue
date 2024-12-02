<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Tiện ích nội khu</h3>
        <img class="img-dropdown" :class="showBlock? '' : 'img-dropdown__hide'" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showBlock = !showBlock">
      </div>
    </div>
    <div class="card-body card-info" v-if="showBlock">
      <div class="container-fluid">
        <!-- <div class="col-12 px-0">
          <div class="d-flex align-items-center sub_header_title">
            <span class="label"> Tiện ích cơ bản </span>
          </div>
        </div> -->
        <p class="title title-highlight">Tiện ích cơ bản</p>
        <div class="row justify-content-flex-start container-utilities">
          <div class="col-12 col-md-6 text-center form-group-container d-flex" v-for="(basic_utility, index) in basic_utilities" :key="index">
            <div class="col d-flex justify-content-flex-start align-items-center">
              <label class="input-checkbox" style="margin-right: 10px;">
                <input type="checkbox" :id="basic_utility.acronym" :value="basic_utility.acronym" v-model="apartment_specification.utilities" >
                <span class="check-mark"/>
              </label>
              <label :for="basic_utility.acronym" style="cursor:pointer" class="color-black font-weight-bold mr-2 mb-2">{{basic_utility.description}}</label>

            </div>
          </div>
          <div class="col-12 col-md-4 text-center"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import WareHouse from '@/models/WareHouse'
import InputNumberFormat from '@/components/Form/InputNumber'
export default {
	name: 'BlockInfo',
	props: ['apartment_specification', 'basic_utilities'],
	components: {
		InputNumberFormat,
		InputCategory,
		InputText
	},
	data () {
		return {
			checkedId: [],
			// basic_utilities: [],
			apartments: [],
			built_years: [],
			showBlock: true,
			form: {
				built_year: '',
				total_floor: 0,
				basement_floor: 0,
				commercial_floor: 0,
				living_floor: 0,
				lift_number: '',
				other_utilities: ''
			}
		}
	},
	computed: {
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
				this.built_years.push(
					{
						year: i
					}
				)
			}
			function compare (a, b) {
				if (a.year > b.year) { return -1 }
				if (a.year < b.year) { return 1 }
				return 0
			}
			return this.built_years.sort(compare)
		},
		handleChecked: function () {
			this.block_list.basic_utilities = []
			this.checkedId.forEach(item => {
				this.block_list.basic_utilities.push({
					basic_utility_id: item
				})
			})
		},
		handleCheck () {
			this.block_list.basic_utilities.forEach(item => {
				this.checkedId.push(
					item.id
				)
			})
		},
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.basic_utilities = [...reps.data.tien_ich_co_ban]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	beforeMount () {
		// this.handleCheck()
		// this.handleChecked()
		this.handleBuiltYear()
		// this.getDictionary()
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
.input-checkbox {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  input {
    width: 20px;
    height: 20px;
    position: absolute;
    cursor: pointer;
    opacity: 0;
    &:checked {
      & ~ .check-mark {
        background-color: #FAA831;
        &:after {
          display: block;
        }
      }
    }
  }
  .check-mark {
    position: absolute;
    top: 0;
    left: 0;
    cursor: pointer;
    width: 20px;
    height: 20px;
    background-color: #FFFFFF;
    border: 1px solid #FAA831;
    border-radius: 4px;
    &:after {
      content: "";
      position: absolute;
      display: none;
      left: 50%;
      top: 50%;
      width: 5px;
      height: 10px;
      border: solid #FFFFFF;
      border-width: 0 3px 3px 0;
      -webkit-transform: rotate(45deg) translate(-125%, -25%);
      -ms-transform: rotate(45deg) translate(-125%, -25%);
      transform: rotate(45deg) translate(-125%, -25%);
    }
  }
}
.sub_header_title {
  background-color: #F6F7FB;
  border: 1px solid #E8E8E8;
  border-radius: 3px;
  padding: 0.5rem 2rem;
  position: relative;
  color: #00507C;
  font-weight: 700;
  font-size: 1.125rem;
  .label {
    margin-right: 15px;
  }
  label {
    margin: 0;
  }
  &::before {
    content: '';
    position: absolute;
    height: calc(100% - 16px);
    width: 3px;
    background-color: #99D161;
    border-radius: 3px;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
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
.justify-content-space-evenly {
  justify-content: space-evenly !important;
}
</style>
