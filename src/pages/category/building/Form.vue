<template>
			<ValidationObserver tag="form"
													ref="observer"
													@submit.prevent="validateBeforeSubmit">
		<div class="pannel card">
				<div class="row">
					<div class="col-12 col-md-6">
						<div class="row">
						<InputCategory
							v-model="form.building_category"
							placeholder="Loại công trình xây dựng"
							rules="required"
							class="mb-3 col-6"
							vid="building"
							:disabled="$route.name === 'building.edit'"
							label="Loại công trình xây dựng"
							:options="optionBuildingCategory"
							@change="changeBuildingCategory($event)"
						/>
						<InputCategory
							v-model="form.province_id"
							vid="province_id"
							label="Nơi áp dụng"
							class="mb-3 col-6"
							:options="optionsProvince"
						/>
						</div>
					<div class="row justify-content-between">
							<InputCategory
								v-model="form.level"
								placeholder="Cấp nhà"
								rules="required"
								vid="level"
								class="mb-3 col-12 col-md-6"
								label="Cấp nhà"
								:disabled="$route.name === 'building.edit'"
								:options="optionLevel"
								v-if="building === 'NHÀ Ở RIÊNG LẺ'"
							/>
							<InputCategory
								v-model="form.structure"
								placeholder="Cấu trúc"
								rules="required"
								vid="structure"
								class="mb-3 col-12 col-md-6"
								label="Cấu trúc"
								:options="optionStructure"
								:disabled="$route.name === 'building.edit'"
								v-if="building === 'BIỆT THỰ'"
							/>
							<InputCategory
								v-model="form.rate"
								placeholder="Hạng nhà"
								rules="required"
								vid="rate"
								class="mb-3 col-12 col-md-6"
								label="Hạng nhà"
								:disabled="$route.name === 'building.edit'"
								:options="optionRate"
								v-if="building === 'NHÀ Ở RIÊNG LẺ' || building === 'BIỆT THỰ'"
							/>
							<InputCategory
								v-model="form.crane"
								placeholder="Cẩu trục"
								rules="required"
								vid="crane"
								class="mb-3 col-12 col-md-6"
								label="Cẩu trục"
								:disabled="$route.name === 'building.edit'"
								:options="optionCrane"
								v-if="building === 'NHÀ XƯỞNG (KHO)'"
							/>
							<InputCategory
								v-model="form.aperture"
								placeholder="Khẩu độ"
								rules="required"
								vid="aperture"
								class="mb-3 col-12 col-md-6"
								label="Khẩu độ"
								:disabled="$route.name === 'building.edit'"
								:options="optionAperture"
								v-if="building === 'NHÀ XƯỞNG (KHO)'"
							/>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<InputNumberFormat
							v-model="form.unit_price_m2"
							vid="remaining_quality"
							placeholder="Đơn giá xây dựng"
							label="Đơn giá xây dựng"
							rules="required"
							@change="changeUnitPrice($event)"
							:max="99999999999999"
							:formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
							class="mb-3 col-12"
						/>
						<div class="row justify-content-between">
							<InputText
								label="Ngày bắt đầu"
								type="date"
								v-model="form.effect_from"
								rules="required"
								vid="effect_from"
								class="mb-3 col-12 col-md-6"
							/>
							<InputText
								label="Ngày kết thúc"
								type="date"
								v-model="form.effect_to"
								:min="form.effect_from.toString()"
								vid="effect_from"
								class="mb-3 col-12 col-md-6"
							/>
						</div>
					</div>
					<InputCategory
						v-model="form.factory_type"
						placeholder="Loại nhà máy"
						rules="required"
						vid="aperture"
						class="mb-3 col-12"
						label="Loại nhà máy"
						:disabled="$route.name === 'building.edit'"
						:options="optionFactoryType"
						v-if="building === 'NHÀ XƯỞNG (KHO)'"
					/>
				</div>
		</div>

		<div class="pannel card">
			<div class="main-wrapper">
				<div class="responsive-table">
						<table class="table_contruction_pp2 color_content">
								<thead>
									<tr>
										<th colspan="10">Phần kết cấu chính (%)</th>
										<th rowspan="2">CLCL (%)</th>
									</tr>
									<tr>
										<th colspan="2">Móng, khung cột</th>
										<th colspan="2">Tường</th>
										<th colspan="2">Nền, sàn</th>
										<th colspan="2">Kết cấu đỡ mái</th>
										<th colspan="2">Mái</th>
									</tr>
									<tr>
										<th>p</th>
										<th>h</th>
										<th>p</th>
										<th>h</th>
										<th>p</th>
										<th>h</th>
										<th>p</th>
										<th>h</th>
										<th>p</th>
										<th>h</th>
										<th colspan="2">H= Σ ph / Σ p</th>
									</tr>
								</thead>
									<tbody>
									<tr>
										<td>
											<InputNumberNegative
												class="label-none"
												label="test"
												vid="p1"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeP1"
												v-model="form.p1"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="h1"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeH1"
												v-model="form.h1"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="test"
												vid="test"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeP2"
												v-model="form.p2"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="h2"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeH2"
												v-model="form.h2"
												rules="required"
											/>
										</td>
										<td>
												<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="p3"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeP3"
												v-model="form.p3"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="h3"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeH3"
												v-model="form.h3"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="p4"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeP4"
												v-model="form.p4"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="h4"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeH4"
												v-model="form.h4"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="p5"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeP5"
												v-model="form.p5"
												rules="required"
											/>
										</td>
										<td>
											<InputNumberNegative
												class="label-none"
												label="tỷ lệ"
												vid="h5"
												:min="-10000"
												:text_center="true"
												:sufix="false"
												@change="changeH5"
												v-model="form.h5"
												rules="required"
											/>
										</td>
										<td>{{ total ? total : 0 }}</td>
									</tr>
								</tbody>
							</table>
					</div>
				</div>
				<div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
					<div class="d-md-flex d-block button-contain ">
						<button class="btn btn-white" @click="onCancel" type="button">
							<img class="img" src="../../../assets/icons/ic_cancel.svg" alt="cancel">
							Trở về
						</button>
						<button class="btn btn-white btn-orange text-nowrap" :class="{'btn-loading disabled': isSubmit}" type="submit">
							<img class="img" src="../../../assets/icons/ic_save.svg" alt="save">
							Lưu
						</button>
					</div>
				</div>
			</div>
		</ValidationObserver>
</template>
<script>
import InputText from '@/components/Form/InputText'
import InputNumberNegative from '@/components/Form/InputNumberNegative'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputDatePickerRange from '@/components/Form/InputDatePickerRange'
import Building from '@/models/Building'
import WareHouse from '@/models/WareHouse'

export default {
	props: {
		detail: {
			type: Object,
			default: () => {
			}
		}
	},
	name: 'Form',
	components: {
		InputText,
		InputCategory,
		InputNumberFormat,
		InputDatePickerRange,
		InputNumberNegative
	},
	data () {
		return {
			buildingCategory: [],
			buildingLevel: [],
			buildingRate: [],
			buildingStructure: [],
			buildingCrane: [],
			buildingAperture: [],
			buildingFactoryType: [],
			provinces: [],
			building: '',
			total: 0,
			form: {
				building_category: '',
				level: '',
				rate: '',
				structure: '',
				crane: '',
				aperture: '',
				factory_type: '',
				unit_price_m2: '',
				effect_from: '',
				effect_to: '',
				p1: 0,
				p2: 0,
				p3: 0,
				p4: 0,
				p5: 0,
				h1: 0,
				h2: 0,
				h3: 0,
				h4: 0,
				h5: 0
			},
			isSubmit: false
		}
	},
	created () {
		if ('id' in this.$route.query && this.$route.name === 'building.edit') {
			this.form = Object.assign(this.form, {
				...this.$route.meta['detail']
			})
		} else {
		}
	},
	computed: {
		optionsProvince () {
			return {
				data: this.provinces,
				key: 'name',
				id: 'id'
			}
		},
		optionBuildingCategory () {
			return {
				data: this.buildingCategory,
				key: 'description',
				id: 'id'
			}
		},
		optionLevel () {
			return {
				data: this.buildingLevel,
				key: 'description',
				id: 'id'
			}
		},
		optionRate () {
			return {
				data: this.buildingRate,
				key: 'description',
				id: 'id'
			}
		},
		optionStructure () {
			return {
				data: this.buildingStructure,
				key: 'description',
				id: 'id'
			}
		},
		optionCrane () {
			return {
				data: this.buildingCrane,
				key: 'description',
				id: 'id'
			}
		},
		optionAperture () {
			return {
				data: this.buildingAperture,
				key: 'description',
				id: 'id'
			}
		},
		optionFactoryType () {
			return {
				data: this.buildingFactoryType,
				key: 'description',
				id: 'id'
			}
		}
	},
	methods: {
		getDate () {
			const today = new Date()
			const dd = String(today.getDate()).padStart(2, '0')
			const mm = String(today.getMonth() + 1).padStart(2, '0')
			const yyyy = today.getFullYear()
			this.form.effect_from = yyyy + '-' + mm + '-' + dd
		},
		changeUnitPrice (event) {
			this.form.unit_price_m2 = event
		},
		changeBuildingCategory (event) {
			this.form.level = ''
			this.form.rate = ''
			this.form.structure = ''
			this.form.crane = ''
			this.form.aperture = ''
			this.form.factory_type = ''
			this.buildingCategory.forEach(buildingCategory => {
				if (buildingCategory.id === event) {
					this.building = buildingCategory.description
				}
			})
		},
		buildingCategories () {
			const building = parseInt(this.form.building_category)
			this.buildingCategory.forEach(buildingCategory => {
				if (buildingCategory.id === building) {
					this.building = buildingCategory.description
				}
			})
		},
		async getProvinces () {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDictionaries () {
			try {
				const resp = await Building.getDictionaries()
				this.buildingCategory = [...resp.data.loai_nha]
				this.buildingLevel = [...resp.data.cap_nha]
				this.buildingRate = [...resp.data.hang_nha]
				this.buildingStructure = [...resp.data.cau_truc_biet_thu]
				this.buildingCrane = [...resp.data.cau_truc_nha_xuong]
				this.buildingAperture = [...resp.data.khau_do]
				this.buildingFactoryType = [...resp.data.loai_nha_may]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		changeP1 (e) {
			if (e) {
				this.form.p1 = e
			} else {
				this.form.p1 = 0
			}
			this.getTotal()
		},
		changeP2 (e) {
			if (e) {
				this.form.p2 = e
			} else {
				this.form.p2 = 0
			}
			this.getTotal()
		},
		changeP3 (e) {
			if (e) {
				this.form.p3 = e
			} else {
				this.form.p3 = 0
			}
			this.getTotal()
		},
		changeP4 (e) {
			if (e) {
				this.form.p4 = e
			} else {
				this.form.p4 = 0
			}
			this.getTotal()
		},
		changeP5 (e) {
			if (e) {
				this.form.p5 = e
			} else {
				this.form.p5 = 0
			}
			this.getTotal()
		},
		changeH1 (e) {
			if (e) {
				this.form.h1 = e
			} else {
				this.form.h1 = 0
			}
			this.getTotal()
		},
		changeH2 (e) {
			if (e) {
				this.form.h2 = e
			} else {
				this.form.h2 = 0
			}
			this.getTotal()
		},
		changeH3 (e) {
			if (e) {
				this.form.h3 = e
			} else {
				this.form.h3 = 0
			}
			this.getTotal()
		},
		changeH4 (e) {
			if (e) {
				this.form.h4 = e
			} else {
				this.form.h4 = 0
			}
			this.getTotal()
		},
		changeH5 (e) {
			if (e) {
				this.form.h5 = e
			} else {
				this.form.h5 = 0
			}
			this.getTotal()
		},
		getTotal () {
			let p1 = this.form.p1
			let p2 = this.form.p2
			let p3 = this.form.p3
			let p4 = this.form.p4
			let p5 = this.form.p5
			let h1 = this.form.h1
			let h2 = this.form.h2
			let h3 = this.form.h3
			let h4 = this.form.h4
			let h5 = this.form.h5
			if (p1 + p2 + p3 + p4 + p5 !== 0) {
				this.total = Math.round(parseFloat((p1 * h1 + p2 * h2 + p3 * h3 + p4 * h4 + p5 * h5) / (p1 + p2 + p3 + p4 + p5)).toFixed(1))
			} else {
				this.total = 0
			}
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleSubmit()
			}
		},

		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			console.log('data',data)
			// return
			if (this.$route.name === 'building.edit') {
				this.updateBuilding(data)
			} else {
				this.createBuilding(data)
			}
		},

		onCancel () {
			return this.$router.push({name: 'building.index'})
		},

		async createBuilding (data) {
			try {
				const resp = await Building.create(data)

				if (resp && Object.keys(resp).length) {
					this.$router.push({name: 'building.index'}).catch(_ => {
					})
				}
				this.$toast.open({
					message: 'Thêm mới thành công',
					type: 'success',
					position: 'top-right'
				})
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async updateBuilding (data) {
			try {
				const resp = new Building(data)
				await resp.save()
				await this.$router.push({name: 'building.index'}).catch(_ => {
				})
				this.$toast.open({
					message: 'Cập nhật thành công',
					type: 'success',
					position: 'top-right'
				})
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	async beforeMount () {
		await this.getDictionaries()
		await this.getProvinces()
		if (this.$route.name === 'building.create') {
			this.getDate()
		}
		if (this.$route.name === 'building.edit') {
			this.buildingCategories()
			this.getTotal()
		}
	}
}
</script>
<style scoped lang="scss">
	.pannel{
		background: #FFFFFF;
		box-shadow: 1px 2px 0 #e5eaee;
		border-radius: 5px;
		padding: 25px;
		margin: 1rem;
		&__table{
			padding: 25px 0;
			border-radius: 5px;
			//box-shadow: 0 0 5px rgba(0,0,0,.1);
		}
		&__input{
			p{
				color: #5a5386;
				font-weight: 600;
			}
		}
	}
	.form-control{
		width: 100%;
		margin-right: 5px;
		color: #555555;
		border-radius: 5px;

		@media (max-width: 1023px) {
			width: 100%;
		}
		&:focus{
			border-color: #CCCCCC;
			box-shadow: none;
		}
	}
	.main-wrapper {
		margin-top:1rem;
		width: 100%;
		overflow-x: auto;
		box-sizing: border-box;
	}
	.responsive-table {
		display: inline-block;
		min-width: 100%;
		box-sizing: border-box;
	}

	.responsive-table > table {
		width: 100%;
		border-collapse: collapse;
	}
	.table_contruction_pp2 {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color:  #DEE6EE;;
        color: #3D4D65;
        border: 1px solid white;
        &:first-child{
          border-top-left-radius: 3px;
          border-left: 1px solid #CED4DA;
        }
        &:last-child{
          border-top-right-radius: 3px;
          border-right: 1px solid #CED4DA;
					width: 15%;
        }
      }
    }
    tbody{
      td{
				min-width: 100px;
        border: 1px solid #CED4DA;
        // &:first-child{
        //   width: 16%;
        // }
        // &:nth-child(2) {
        //   width: 15%;
        // }
        // &:last-child{
        //   width: 5%;
        // }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }
  }
</style>
