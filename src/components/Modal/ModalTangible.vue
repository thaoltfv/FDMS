<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center">
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
                    v-model="form.building_type_id"
                    vid="building_type_id"
                    label="Loại"
                    rules="required"
                    :options="optionsHousingType"
                    @change="changeBuildingType($event)"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ Ở RIÊNG LẺ'">
                  <InputCategory
                    v-model="form.building_category_id"
                    vid="building_category_id"
                    label="Cấp nhà"
                    rules="required"
                    :options="optionsHousing"
                    @change="changeCategory"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'BIỆT THỰ'">
                  <InputCategory
                    v-model="form.structure_id"
                    vid="structure_id"
                    label="Cấu trúc"
                    rules="required"
                    :options="optionsStructure"
                    @change="changeStructure"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ Ở RIÊNG LẺ' || building === 'BIỆT THỰ'">
                  <InputCategory
                    v-model="form.rate_id"
                    vid="rate_id"
                    label="Hạng nhà"
                    rules="required"
                    :options="optionsRate"
                    @change="changeRate"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ XƯỞNG (KHO)'">
                  <InputCategory
                    v-model="form.crane_id"
                    vid="crane_id"
                    label="Cẩu trục"
                    rules="required"
                    :options="optionsCrane"
                    @change="changeCrane"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'NHÀ XƯỞNG (KHO)'">
                  <InputCategory
                    v-model="form.aperture_id"
                    vid="aperture_id"
                    label="Khẩu độ"
                    rules="required"
                    :options="optionsAperture"
                    @change="changeAperture"
                  />
                </div>
                <div class="col-12 input-contain" v-if="building === 'NHÀ XƯỞNG (KHO)'">
                  <InputCategory
                    v-model="form.factory_type_id"
                    vid="factory_type_id"
                    label="Loại nhà máy"
                    rules="required"
                    :options="optionsFactionType"
                    @change="changeFactionType"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'CÔNG TRÌNH KHÁC'">
                  <InputText
                    v-model="form.other_building"
                    vid="name_building"
                    label="Tên công trình"
                    rules="required"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building === 'CÔNG TRÌNH KHÁC'">
                  <InputText
                    v-model="form.description"
                    vid="description"
                    label="Mô tả"
                    rules="required"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building !== 'CÔNG TRÌNH KHÁC'">
                  <InputNumberFormat
                    v-model="form.floor"
                    vid="floor"
                    label="Số tầng"
                    rules="required"
                    :max="999"
                    :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    @change="changeFloor($event)"
                    :min="0"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain" v-if="building !== 'CÔNG TRÌNH KHÁC'">
                  <InputCategory
                    v-model="form.start_using_year"
                    vid="start_using_year"
                    label="Năm sử dụng"
                    rules="required"
                    :options="optionYearBuild"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
                  <InputSwitch
                    v-model="form.gpxd"
                    vid="gpxd"
                    rules="required"
                    label="GPXD"
                  />
                </div>
                <div class="w-100"/>
                <div class="col-12 col-lg-6 input-contain">
                  <InputPercent
                    v-model="form.remaining_quality"
                    vid="remaining_quality"
										:key="keyRender"
                    label="Chất lượng còn lại"
                    :max="100"
                    :min="0"
										:decimal="2"
                    rules="required"
                    @change="remainingQuality($event)"
                  />
                </div>
                <div class="col-12 col-lg-6 input-contain">
									<InputArea
                    v-model="form.total_construction_area"
                    vid="total_construction_area"
										:key="keyRender"
                    label="Diện tích xây dựng"
										:decimal="2"
										rules="required"
                    @change="changeArea($event)"
									/>
                </div>
                <div class="col-12 col-lg-6 input-contain">
									<InputArea
                    v-model="form.total_construction_base"
                    vid="total_construction_base"
										:key="keyRender"
                    label="Diện tích sàn"
										:decimal="2"
										rules="required"
                    @change="totalConstructionBase($event)"
									/>
                </div>
                <!-- <div class="col-12 col-lg-6 input-contain">
                  <InputCategory
                    v-model="form.plot_num"
                    vid="build"
                    label="Thửa đất xây dựng"
                    :options="optionsBuild"
                  />
                </div> -->
								<div class="col-12 col-lg-6 input-contain">
									<InputCurrency
										v-model="form.unit_price_m2"
										vid="unit_price_m2"
										:key="keyRender"
										label="Đơn giá xây dựng"
										rules="required"
										@change="changeUnitPriceM2($event)"
									/>
								</div>
                  <!-- :disabled-input="building !== 'CÔNG TRÌNH KHÁC'" -->
<!--                  <div class="form-control disabled"><p class="mb-0">{{formatNumber(form.unit_price_m2)}}</p></div>-->
                <div class="col-12 col-lg-6 input-contain">
                  <label class="name font-weight-bold">Giá trị ước tính (VND)</label>
                  <div class="d-flex justify-content-end form-control input-disabled disabled"><p class="mb-0">{{formatNumber(form.estimation_value)}}</p><p class="mb-0 ml-2">đ</p></div>
                </div>
              </div>
            </div>
            <div class="card-table">
              <div class="card-title card-title__img">
                <form enctype="multipart/form-data" class="d-flex align-items-center">
                  <h3 class="title">Hình ảnh</h3>
                  <div class="img-upload">
                    <img src="../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
                    Tải ảnh lên
                    <input type="file" id="img" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event)" />
                  </div>
                </form>
              </div>
              <div class="card-body">
                <div class="container-img row ml-0 mr-0" >
                  <div class="img-empty text-center" v-if="form.pic.length === 0">
                    <img src="../../assets/images/img_emply.svg" alt="empty">
                    <p class="empty-content">Chưa có hình</p>
                  </div>
                  <div class="contain-img col-4 col-lg-2 " v-for="(images, index) in form.pic" :key="images.id">
                    <div class="delete" @click="removeImage(index)">X</div>
                    <img class="img" :src="images.link" alt="img">
                  </div>
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
import InputPercent from '@/components/Form/InputPercent'
import InputCurrency from '@/components/Form/InputCurrency'
import InputArea from '@/components/Form/InputArea'
import InputLengthArea from '@/components/Form/InputLengthArea'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import FileUpload from '@/components/file/FileUpload'
import InputNumberFormat from '@/components/Form/InputNumber'
import WareHouse from '@/models/WareHouse'
import File from '@/models/File'

export default {
	name: 'ModalTangible',
	props: ['info', 'img_link', 'compare_properties', 'tangible', 'tangible_index'],
	data () {
		return {
			keyRender: 0,
			built_years: [],
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
				id: '',
				remaining_quality: '',
				building_category_id: '',
				total_construction_base: '',
				estimation_value: '',
				unit_price_m2: '',
				building_type_id: '',
				floor: '',
				start_using_year: '',
				gpxd: true,
				total_construction_area: '',
				plot_num: '',
				rate_id: '',
				structure_id: '',
				crane_id: '',
				aperture_id: '',
				factory_type_id: '',
				other_building: '',
				description: '',
				pic: []
			}
		}
	},
	components: {
		FileUpload,
		InputCurrency,
		InputPercent,
		InputArea,
		InputLengthArea,
		InputCategory,
		InputText,
		InputNumberFormat
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
		optionYearBuild () {
			return {
				data: this.built_years,
				id: 'year',
				key: 'year'
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
	mounted () {
		this.getTangible()
		this.handleBuiltYear()
		this.keyRender += 1
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
		buildingCategories () {
			const building = parseInt(this.form.building_type_id)
			this.housingTypes.forEach(housingType => {
				if (housingType.id === building) {
					this.building = housingType.description
				}
			})
		},
		getTangible () {
			if (this.tangible !== '' && this.tangible !== null && this.tangible !== undefined) {
				this.form.other_building = this.tangible.other_building
				this.form.description = this.tangible.description
				this.form.remaining_quality = this.tangible.remaining_quality
				this.form.building_category_id = this.tangible.building_category_id
				this.form.total_construction_base = this.tangible.total_construction_base
				this.form.estimation_value = this.tangible.estimation_value
				this.form.unit_price_m2 = this.tangible.unit_price_m2
				this.form.building_type_id = this.tangible.building_type_id
				this.form.floor = this.tangible.floor
				this.form.start_using_year = this.tangible.start_using_year
				this.form.gpxd = this.tangible.gpxd
				this.form.total_construction_area = this.tangible.total_construction_area
				// this.form.plot_num = this.tangible.plot_num
        this.form.plot_num = ''
				this.form.pic = this.tangible.pic
				this.form.rate_id = this.tangible.rate_id
				this.form.structure_id = this.tangible.structure_id
				this.form.crane_id = this.tangible.crane_id
				this.form.aperture_id = this.tangible.aperture_id
				this.form.factory_type_id = this.tangible.factory_type_id
				if (this.$route.name === 'warehouse.edit') {
					this.form.id = this.tangible.id
				}
			}
		},
		changeBuildingType (event) {
			this.form.building_category_id = ''
			this.form.rate_id = ''
			this.form.structure_id = ''
			this.form.crane_id = ''
			this.form.aperture_id = ''
			this.form.factory_type_id = ''
			this.housingTypes.forEach(buildingCategory => {
				if (buildingCategory.id === event) {
					this.building = buildingCategory.description
				}
			})
			if (this.building === 'CÔNG TRÌNH KHÁC') {
				this.form.floor = ''
				this.form.start_using_year = ''
			} else {
				this.form.other_building = ''
				this.form.description = ''
			}
			this.getBuildingPrices()
		},
		changeCategory () {
			this.getBuildingPrices()
		},
		changeRate () {
			this.getBuildingPrices()
		},
		changeStructure () {
			this.getBuildingPrices()
		},
		changeCrane () {
			this.getBuildingPrices()
		},
		changeAperture () {
			this.getBuildingPrices()
		},
		changeFactionType () {
			this.getBuildingPrices()
		},
		changeFloor (event) {
			if (event !== undefined && event !== null) {
				this.form.floor = parseInt(event)
			} else {
				this.form.floor = 0
			}
		},
		remainingQuality (event) {
			this.form.remaining_quality = event
			this.form.estimation_value = this.form.total_construction_base * this.form.unit_price_m2 * (this.form.remaining_quality / 100)
		},
		totalConstructionBase (event) {
			this.form.total_construction_base = event
			this.form.estimation_value = this.form.total_construction_base * this.form.unit_price_m2 * (this.form.remaining_quality / 100)
		},
		changeUnitPriceM2 (event) {
			this.form.unit_price_m2 = event
			this.form.estimation_value = this.form.total_construction_base * this.form.unit_price_m2 * (this.form.remaining_quality / 100)
		},
		changeArea (event) {
			this.form.total_construction_area = event
		},
		formatNumber (value) {
			if (value !== '') {
				return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
			} else return value
		},
		onImageChange (e) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				if (this.file.type === 'image/png' || this.file.type === 'image/jpeg' || this.file.type === 'image/jpg' || this.file.type === 'image/gif') {
					this.createImage()
					this.uploadImage()
				} else {
					this.$toast.open({
						message: 'Hình không đúng định dạng vui lòng kiểm tra lại',
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
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
				// if (this.file !== null) {
				//   await this.uploadImage()
				// }
				this.isSubmit = true
				const data = this.form
				const tangible_index = this.tangible_index
				this.building_categories.forEach(building_category => {
					if (building_category.id === data.building_type_id) {
						data.building_type = building_category
					}
				})
				this.housingTypes.forEach(housingType => {
					if (housingType.id === data.building_category_id) {
						data.building_category = housingType
					}
				})
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
		changeSwitch () {
			if (this.form.individual_road_switch === true) {
				this.form.individual_road = 1
			} else {
				this.form.individual_road = 0
			}
			if (this.form.front_side_switch === true) {
				this.form.front_side = 1
			} else {
				this.form.front_side = 0
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
		async getBuildingPrices () {
			const building_category = this.form.building_type_id
			const level = this.form.building_category_id
			const rate = this.form.rate_id
			const structure = this.form.structure_id
			const crane = this.form.crane_id
			const aperture = this.form.aperture_id
			const factory_type = this.form.factory_type_id
      const province_id = this.info.province_id
			if ((this.form.building_category_id !== '' && this.form.building_category_id !== undefined && this.form.building_category_id !== null && this.form.rate_id !== '' && this.form.rate_id !== undefined && this.form.rate_id !== null) || (this.form.structure_id !== '' && this.form.structure_id !== undefined && this.form.structure_id !== null && this.form.rate_id !== '' && this.form.rate_id !== undefined && this.form.rate_id !== null) || (this.form.crane_id !== '' && this.form.crane_id !== undefined && this.form.crane_id !== null && this.form.aperture_id !== '' && this.form.aperture_id !== undefined && this.form.aperture_id !== null && this.form.factory_type_id !== '' && this.form.factory_type_id !== undefined && this.form.factory_type_id !== null)) {
				const resp = await WareHouse.getBuildingPrices(building_category, level, rate, structure, crane, aperture, factory_type, province_id)
				this.form.unit_price_m2 = parseInt(resp.data)
			} else {
				this.form.unit_price_m2 = 0
			}
			this.form.estimation_value = this.form.total_construction_base * this.form.unit_price_m2 * (this.form.remaining_quality / 100)
			this.keyRender += 1
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
		this.isSubmit = true
		await this.getDictionary()
		await this.getCrane()
		if (this.$route.name === 'warehouse.edit' || (this.tangible !== '' && this.tangible !== null && this.tangible !== undefined)) {
			this.buildingCategories()
		}
		this.isSubmit = false
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
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
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
.input-disabled {
  min-height: 30px;
  height: 33px;
}
</style>
