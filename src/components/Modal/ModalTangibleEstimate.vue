<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="loading" :class="{'loading__true': isSubmit}">
          <a-spin />
        </div>
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Công trình xây dựng</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="property-detail">
              <div class="row justify-content-between">
                <div class="col-12 input-contain">
                  <InputCategory
                    v-model="form.building_category"
                    vid="building_category"
                    label="Loại"
                    rules="required"
                    :options="optionsHousingType"
                    @change="changeBuildingType($event)"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ Ở RIÊNG LẺ'">
                  <InputCategory
                    v-model="form.level"
                    vid="level"
                    label="Cấp nhà"
                    rules="required"
                    :options="optionsHousing"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'BIỆT THỰ'">
                  <InputCategory
                    v-model="form.structure"
                    vid="structure_id"
                    label="Cấu trúc"
                    rules="required"
                    :options="optionsStructure"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ Ở RIÊNG LẺ' || building === 'BIỆT THỰ'">
                  <InputCategory
                    v-model="form.rate"
                    vid="rate_id"
                    label="Hạng nhà"
                    rules="required"
                    :options="optionsRate"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ XƯỞNG (KHO)'">
                  <InputCategory
                    v-model="form.crane"
                    vid="crane_id"
                    label="Cẩu trục"
                    rules="required"
                    :options="optionsCrane"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ XƯỞNG (KHO)'">
                  <InputCategory
                    v-model="form.aperture"
                    vid="aperture_id"
                    label="Khẩu độ"
                    rules="required"
                    :options="optionsAperture"
                  />
                </div>
                <div class="col-12 input-contain" v-if="building === 'NHÀ XƯỞNG (KHO)'">
                  <InputCategory
                    v-model="form.factory_type"
                    vid="factory_type_id"
                    label="Loại nhà máy"
                    rules="required"
                    :options="optionsFactionType"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputNumberFormat
                    v-model="form.remaining_quality"
                    vid="remaining_quality"
                    label="Chất lượng còn lại (%)"
                    :max="100"
                    :min="0"
                    rules="required"
                    @change="remainingQuality($event)"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputNumberFormat
                    v-model="form.area"
                    vid="total_construction_base"
                    label="Diện tích tài sản (m²)"
                    :max="99999999"
                    :min="0"
                    rules="required"
                    :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    @change="totalConstructionBase($event)"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange btn-action-modal" :class="{'btn-loading disabled': isSubmit}"> <img src="../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"> Lưu</button>
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"><img src="../../assets/icons/ic_cancel.svg"  style="margin-right: 12px" alt="save">Trở lại</button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import FileUpload from '@/components/file/FileUpload'
import InputNumberFormat from '@/components/Form/InputNumber'
import WareHouse from '@/models/WareHouse'
import File from '@/models/File'

export default {
	name: 'ModalTangibleEstimate',
	props: ['info', 'img_link', 'compare_properties', 'tangible', 'tangible_index'],
	data () {
		return {
			building: '',
			isSubmit: false,
			showScale: true,
			buildingStructure: [],
			buildingRates: [],
			building_categories: [],
			housingTypes: [],
			zoning: [],
			juridicals: [],
			landShapes: [],
			roughes: [],
			images: [],
			conditions: [],
			socialSecurities: [],
			businesses: [],
			paymentMethods: [],
			fengshuies: [],
			zones: [],
			type_purposes: [],
			materials: [],
			landType: [],
			image: null,
			link: '',
			type: '',
			file: null,
			form: {
				building_category: '',
				level: '',
				rate: '',
				structure: '',
				crane: '',
				aperture: '',
				factory_type: '',
				remaining_quality: '',
				area: ''
			}
		}
	},
	components: {
		FileUpload,
		InputCategory,
		InputNumberFormat,
		InputText,
		InputSwitch
	},
	computed: {
		optionsHousingType () {
			return {
				data: this.housingTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsHousing () {
			return {
				data: this.building_categories,
				id: 'id',
				key: 'description'
			}
		},
		optionsBuild () {
			return {
				data: this.compare_properties,
				id: 'id',
				key: 'plot_num'
			}
		},
		optionsRate () {
			return {
				data: this.buildingRates,
				id: 'id',
				key: 'description'
			}
		},
		optionsStructure () {
			return {
				data: this.buildingStructure,
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
		}
	},
	async mounted () {
		if (this.tangible !== '' && this.tangible !== null && this.tangible !== undefined) {
			await this.getTangible()
			await this.getDictionary()
			this.buildingCategories()
		}
	},
	methods: {
		buildingCategories () {
			const building = parseInt(this.form.building_category)
			this.housingTypes.forEach(housingType => {
				if (housingType.id === building) {
					this.building = housingType.description
				}
			})
		},
		async getTangible () {
			this.form.building_category = this.tangible.building_category
			this.form.level = this.tangible.level
			this.form.rate = this.tangible.rate
			this.form.structure = this.tangible.structure
			this.form.crane = this.tangible.crane
			this.form.aperture = this.tangible.aperture
			this.form.factory_type = this.tangible.factory_type
			this.form.remaining_quality = this.tangible.remaining_quality
			this.form.area = this.tangible.area
		},
		changeBuildingType (event) {
			this.form.level = ''
			this.form.rate = ''
			this.form.structure = ''
			this.form.crane = ''
			this.form.aperture = ''
			this.form.factory_type = ''
			this.housingTypes.forEach(buildingCategory => {
				if (buildingCategory.id === event) {
					this.building = buildingCategory.description
				}
			})
		},
		buildingType (event) {
			this.housingTypes.forEach(buildingCategory => {
				if (buildingCategory.id === event) {
					this.building = buildingCategory.description
				}
			})
		},
		remainingQuality (event) {
			this.form.remaining_quality = event
		},
		totalConstructionBase (event) {
			this.form.area = event
		},
		changeArea (event) {
			this.form.total_construction_area = event
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		onImageChange (e) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				this.createImage()
				this.uploadImage()
			}
		},
		createImage () {
			let reader = new FileReader()
			let v = this
			reader.onload = (e) => {
				v.image = e.target.result
			}
			reader.readAsDataURL(this.file)
		},
		removeImage (index) {
			this.form.pic.splice(index, 1)
			document.getElementById('img').value = ''
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		uploadImage () {
			this.isSubmit = true
			const formData = new FormData()
			formData.append('image', this.file)
			return File.upload({data: formData}).then(response => {
				if (response && response.data && response.data.data) {
					const item = {
						link: response.data.data.link,
						picture_type: response.data.data.picture_type
					}
					this.form.pic.push(item)
					this.isSubmit = false
				} else if (response.data.error) {
					this.isSubmit = false
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		async handleAction (event) {
			this.isSubmit = true
			try {
				this.isSubmit = false
				this.isSubmit = true
				const data = this.form
				const tangible_index = this.tangible_index
				this.$emit('action', data, tangible_index)
				this.$emit('cancel', event)
				if (this.tangible !== '' && this.tangible !== null && this.tangible !== undefined) {
					this.$toast.open({
						message: 'Cập nhật công trình xây dựng thành công',
						type: 'success',
						position: 'top-right'
					})
				} else {
					this.$toast.open({
						message: 'Thêm mới công trình xây dựng thành công',
						type: 'success',
						position: 'top-right'
					})
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async validateBeforeSubmit (event) {
			const isValid = await this.$refs.observer.validate()
			this.isSubmit = true
			if (isValid) {
				await this.handleAction(event)
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async getDictionary () {
			try {
				const resp = await WareHouse.getDictionaries()
				this.building_categories = [...resp.data.cap_nha]
				this.housingTypes = [...resp.data.loai_nha]
				this.buildingRates = [...resp.data.hang_nha]
				this.buildingStructure = [...resp.data.cau_truc_biet_thu]
				this.buildingAperture = [...resp.data.khau_do]
				this.buildingFactoryType = [...resp.data.loai_nha_may]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getCrane () {
			try {
				const resp = await WareHouse.getCrane()
				this.buildingCrane = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	async beforeMount () {
		await this.getDictionary()
		await this.getCrane()
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
  @media (max-width: 768px) {
    padding: 20px;
  }
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1300px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 35px 95px;
    @media (max-width: 768px) {
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
.title-property{
  font-weight: 700;
  font-size: 18px;
  margin-bottom: 18px;
}
.input-contain{
  margin-bottom: 25px;
}
.card-table{
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  max-width: 99%;
  margin: 50px auto 75px;
}
.card-table tbody tr:last-child td, .card-table tbody tr:last-child th{
  border-bottom: 1px solid #E5E5E5 ;
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
    &__img{
      padding: 8px 20px;
    }
    .title{
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body{
    padding: 35px 30px 40px;
  }
  &-info{
    .title{
      font-size: 16px;
      font-weight: 700;
      margin-top: 28px;
    }
  }
  &-land{
    position: relative;
    padding: 0;
  }
}
.table-property{
  width: 100%;
  font-weight: 400;
  color: #000000;
  text-align: center;
  thead{
    th{
      padding: 12px;
      font-weight: 400;
    }
  }
  tbody{
    td{
      border: 1px solid #E5E5E5;
      &:first-child{
        border-left: none;
        border-right: none;
      }
      &:last-child{
        border-right: none;
      }
      box-sizing: border-box;
      padding: 20px 14px;
    }
  }
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
.contain-table{
  overflow-x: auto;
  @media (max-width: 1024px) {
    overflow-y: hidden;
    overflow-x: auto;
  }
  .table-property{
    width: 100%;
  }
}
.contain-file{
  display: flex;
  align-items: center;
  h3{
    margin-top: 8px;
    margin-bottom: 0;
  }
}
.btn-upload{
  background: #FFFFFF;
  white-space: nowrap;
  border: 1px solid #555555;
  box-sizing: border-box;
  border-radius: 5px;
  padding: 5px 19px;
  font-size: 10px;
}
.btn-property{
  padding: 10px;
}
.img-dropdown{
  cursor: pointer;
  width: 18px;
  &__hide{
    transform: rotate(90deg);
    transition: .3s;
  }
}
.img-upload{
  margin-left: 20px;
  position: relative;
  width: 123px;
  height: 35px;
  color: #fff;
  background: #FAA831;
  font-size: 14px;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
  cursor: pointer;
  input{
    cursor: pointer !important;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 100%;
    opacity: 0;
  }
}
.contain-img{
  height: auto;
  position: relative;
  .img{
    width: 100%;
  }
  .delete{
    position: absolute;
    top: 0;
    right: 0;
    background: #000000;
    color: #FFFFFF;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
  }
}
.contain-total{
  &__left{
    color: #000000;
    .num{
      padding: 0 11px 0 24px;
      width: 340px;
      height: 35px;
      line-height: 1.5;
      border-radius: 5px;
      border: 1px solid #555555;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      background: #f1f1f1 !important;
      cursor: not-allowed;
      user-select: none;
      p{
        margin-bottom: 0;
      }
    }
    .name{
      min-width: 175px;
      margin-bottom: 0;
      margin-right: 20px;
    }
  }
}
.img-locate{
  cursor: pointer;
  position: absolute;
  right: 15px;
  top: 35px;
}
.form-control {
  width: 100%;
}
.btn-orange {
  background: #FAA831;
  text-align: center;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
  height: 35px;
  width: 146px;
  color: #fff;
  margin-right: 15px;
  box-sizing: border-box;
  &:hover{
    border-color: #dc8300;
  }
}
.container-title{
  margin: -35px -95px auto;
  padding: 35px 95px 0;
  @media (max-width: 767px) {
    margin: -20px -10px auto;
    padding: 20px 10px 0;
  }
  .title{
    font-size: 18px;
    margin-bottom: 25px;
    font-weight: bold;
    @media (max-width: 767px) {
      font-size: 16px;
    }
  }
  &__footer{
    margin: auto -95px -35px;
    padding: 20px 95px 20px;
    @media (max-width: 767px) {
      margin: auto -10px -20px;
      padding: 20px 10px 0;
      .btn-white{
        margin-bottom: 20px;
      }
    }
  }
}
.contain-img{
  aspect-ratio:1/1;
  overflow: hidden;
  height: auto;
  position: relative;
  text-align: center;
  margin-bottom: 10px;
  .img{
    object-fit: cover;
    margin-right: 0 ;
    width: 100%;
    height: 100%;
    cursor: pointer;
    &-table{
      margin: auto;
      min-width: 50px;
      min-height: 50px;
      width: 50px;
      height: 50px;
      object-fit: cover;
    }
  }
  &__table{
    width: auto;
  }
  .delete{
    position: absolute;
    top: 0;
    right: 0.75rem;
    background: #000000;
    color: #FFFFFF;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
  }
}
.container-img{
  padding: .75rem 0;
  border: 1px solid #0b0d10;
}
.loading{
  display: none;
  &__true{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100dvh;
    background: rgba(0, 0, 0, 0.62);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    &.btn-loading{
      &:after{
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
</style>
