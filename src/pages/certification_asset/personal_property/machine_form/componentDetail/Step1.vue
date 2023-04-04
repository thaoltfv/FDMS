<template>
  <div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin chung</h3>
          <img class="img-dropdown" :class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
            src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailAppraise = !showCardDetailAppraise">
        </div>
      </div>
      <div class="card-body card-info" v-show="showCardDetailAppraise">
        <div class="container-fluid color_content">
          <div class="row">
            <InputCategory
              v-model="data.asset_type_id"
              vid="asset_type_id"
              label="Loại tài sản"
              rules="required"
              class="form-group-container col-12 col-lg-6"
              :options="optionsType"
              :disabled="true"
              @change="changeAssetType($event)"
            />
            <InputText
              v-model="data.name"
              vid="name"
              class="form-group-container col-12 col-lg-6"
              label="Tên tài sản"
              rules="required"
              :disabledInput="true"
            />
            <InputText
              v-model="data.model"
              vid="model"
              class="form-group-container col-12 col-lg-6"
              label="Mẫu mã"
              rules="required"
              :disabledInput="true"
            />
            <InputCategory
              v-model="data.manufacturer_id"
              vid="manufacturer_id"
              label="Nhà sản xuất"
              rules="required"
              class="form-group-container col-12 col-lg-3"
              :options="optionsManufacturer"
              :disabled="true"
              @change="changeAssetType($event)"
            />
            <InputCategory
              v-model="data.manufacturer_country_id"
              vid="manufacturer_country_id"
              label="Nơi sản xuất"
              rules="required"
              class="form-group-container col-12 col-lg-3"
              :options="optionsManufacturerCountryType"
              :disabled="true"
              @change="changeAssetType($event)"
            />
            <InputCategory
              v-model="data.fuel_id"
              vid="fuel_id"
              label="Nhiên liệu"
              rules="required"
              class="form-group-container col-12 col-lg-6"
              :options="optionsFuelTypes"
              :disabled="true"
              @change="changeAssetType($event)"
            />
            <InputCategory
              v-model="data.manufacturer_year"
              vid="manufacturer_year"
              label="Năm sản xuất"
              rules="required"
              class="form-group-container col-12 col-lg-3"
              :options="optionYears"
              :disabled="true"
              @change="changeAssetType($event)"
            />

            <InputCategory
              v-model="data.using_year"
              vid="using_year"
              label="Năm sử dụng"
              rules="required"
              class="form-group-container col-12 col-lg-3"
              :options="optionYears"
              :disabled="true"
              @change="changeAssetType($event)"
            />
            <InputTextarea
              v-model="data.description"
              vid="description"
              class="form-group-container col-12"
              label="Mô tả"
              rules="required"
              rows="5"
              :disableInput="true"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputTextarea from '@/components/Form/InputTextarea'
export default {
	components: {
		InputText,
		InputCategory,
		InputTextarea
	},
	props: ['data', 'propertyTypes', 'manufacturer', 'manufacturerCountryType', 'fuelTypes'],
	data () {
		return {
			form: {
				name: ''
			},
			showCardDetailAppraise: true
		}
	},
	computed: {
		optionsType () {
			return {
				data: this.propertyTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsManufacturer () {
			return {
				data: this.manufacturer,
				id: 'id',
				key: 'description'
			}
		},
		optionsManufacturerCountryType () {
			return {
				data: this.manufacturerCountryType,
				id: 'id',
				key: 'description'
			}
		},
		optionsFuelTypes () {
			return {
				data: this.fuelTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionYears () {
			return {
				data: this.years,
				id: 'year',
				key: 'year'
			}
		}
	},
	methods: {
		changeAssetType (event) {

		}
	}
}
</script>
<style lang="scss" scoped>
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
      color: #007EC6 !important;
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
  margin-top: 10px;
}
</style>
