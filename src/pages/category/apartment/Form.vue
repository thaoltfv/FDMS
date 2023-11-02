<template>
  <div>
    <ValidationObserver class="contain_form" tag="div" ref="observer" @submit.prevent="validateBeforeSubmit">
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin chung dự án</h3>
        </div>
      </div>
      <div class="card-body card-info">
        <div class="container-fluid color_content">
          <div class="row">
            <InputText
              v-model="form.name"
              rules="required"
              :max-length="200"
              class="mb-3 col-12 col-lg-8"
              vid="name"
              label="Tên chung cư"
            />
            <InputText
              v-model="form.rank"
              rules="required"
              :max-length="200"
              class="mb-3 col-12 col-lg-4"
              vid="rank"
              label="Loại chung cư"
			  			hidden
            />
			<InputCategory
				v-model="form.rank"
				class="mb-3 col-12 col-lg-4"
				vid="rank"
				label="Loại chung cư"
				rules="required"
				:options= optionsRank
              
			/>
            <InputCategory
              v-model="form.province_id"
              class="mb-3 col-12 col-lg-4"
              vid="province_id"
              label="Tỉnh/Thành"
              rules="required"
              :options= optionsProvince
              @change="changeProvince($event)"
            />
            <InputCategory
              v-model="form.district_id"
              class="mb-3 col-12 col-lg-4"
              vid="district_id"
              label="Quận/Huyện"
              rules="required"
              :options= optionsDistrict
              @change="changeDistrict($event)"
            />
            <InputCategory
              v-model="form.ward_id"
              rules="required"
              class="mb-3 col-12 col-lg-4"
              :max-length="200"
              vid="ward_id"
              label="Phường/Xã"
              :options="optionsWard"
              @change="changeWard"
            />
            <InputCategory
              v-model="form.street_id"
              class="mb-3 col-12 col-lg-4"
              rules="required|max:200"
              :max-length="200"
              vid="street_id"
              label="Đường"
              :options="optionsStreet"
              @change="changeStreet"
            />
            <div class="position-relative col-12 col-lg-4">
              <InputText
                v-model="form.coordinates"
                rules="required"
                :max-length="200"
                vid="coordinates"
                label="Tọa độ"
                :disabledInput="true"
              />
              <div class="img-locate" >
                <img src="@/assets/icons/ic_locate.svg" alt="" @click.prevent="handleMap()">
              </div>
            </div>

            <InputNumberNoneFormat
              v-model="form.total_blocks"
							:key="key_render_1"
              :vid="'total_blocks'"
              class="mb-3 col-12 col-lg-4"
              label="Tổng số block"
              :max="99999999999999"
              :min="0"
							:decimal="0"
              @change="changeTotalBlock($event)"
			  				hidden
            />
            <InputNumberNoneFormat
              v-model="form.total_apartments"
							:key="key_render_2"
              :vid="'total_apartments'"
              class="mb-3 col-12 col-lg-4"
              label="Tổng số căn hộ"
              :max="99999999999999"
              :min="0"
							:decimal="0"
              @change="changeTotalApartment($event)"
			  			hidden
            />

          </div>
        </div>
      </div>
    </div>
		<div class="card-group">
			<div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin block chung cư</h3>
        </div>
      </div>
      <div class="card-body card-info">
        <div class="container-fluid color_content">
          <div class="row">
            <div class="col-12">
              <a-table
                bordered
                :columns="columnsBlock"
                :data-source="form.block"
                :loading="isLoading"
                class="table__import"
								:customRow="customRowBlock"
								:rowClassName="(record, index) => index === indexBlock ? 'table-row-selected':''"
                :rowKey="(record, index) => index"
                :pagination="false"
              >
                <template slot="name" slot-scope="name">
                    <p class="mb-0 description text-none">{{name}}</p>
                </template>
                <template slot="total_floors" slot-scope="total_floors">
                    <p class="mb-0 total_floors">{{total_floors}}</p>
                </template>
                <template slot="first_floor" slot-scope="first_floor">
                    <p class="mb-0 description">{{first_floor}}</p>
                </template>
                <template slot="last_floor" slot-scope="last_floor">
                    <p class="mb-0 description">{{last_floor}}</p>
                </template>
                <template slot="apartments_per_floor" slot-scope="apartments_per_floor">
                    <p class="mb-0 description">{{apartments_per_floor}}</p>
                </template>
                <template slot="footer" slot-scope="currentPageData">
                  <div class="d-flex w-100">
                    <div class="w-100 d-flex justify-content-end">
                      <button class="btn text-warning btn-ghost btn-add" type="button" @click="handleModalAddBlock">
                        <img alt="add" src="@/assets/icons/ic_add-white.svg">
                        + Thêm
                      </button>
                    </div>
                  </div>
                </template>

                <template slot="action" slot-scope="action, record, index">
                  <div class="d-flex justify-content-center">
                    <button class="btn-delete" type="button" @click="handleEditBlock(action,record,index)"><img
                      alt="add" src="@/assets/icons/ic_edit_2.svg"/></button>
                    <!-- <div>
                      <button class="btn-delete" type="button" @click="handleDeleteBlock(indexLaw)">
                        <img alt="delete_land" src="@/assets/icons/ic_delete_2.svg"></button>
                    </div> -->
                  </div>
                </template>
              </a-table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="(indexBlock || indexBlock === 0) && this.selectedRowKeysBlock && this.selectedRowKeysBlock.length > 0" class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin tầng chung cư</h3>
        </div>
      </div>
      <div class="card-body card-info">
        <div class="container-fluid color_content">
          <div class="row">
            <div class="col-12">
              <a-table
                bordered
                :columns="columnsFloor"
                :data-source="floors"
                :loading="isLoading" class="table__import"
                :rowKey="(record, index) => index"
								:rowClassName="(record, index) => index === indexFloor ? 'table-row-selected':''"
                :customRow="customRowFloor"
								:pagination="false"
              >
                <template slot="name" slot-scope="name">
                    <p class="mb-0 description text-none">{{name}}</p>
                </template>
                <template slot="footer" slot-scope="currentPageData">
                  <div class="d-flex w-100">
                    <div class="w-100 d-flex justify-content-end">
                      <button class="btn text-warning btn-ghost btn-add" type="button" @click="handleModalAddFloor">
                        <img alt="add" src="@/assets/icons/ic_add-white.svg">
                        + Thêm
                      </button>
                    </div>
                  </div>
                </template>
                <template slot="action" slot-scope="action, record, index">
                  <div class="d-flex justify-content-center">
                    <button class="btn-delete" type="button" @click="handleEditFloor(action,record,index)"><img
                      alt="add" src="@/assets/icons/ic_edit_2.svg"/></button>
                    <!-- <div>
                      <button class="btn-delete" type="button" @click="handleDeleteFloor(action,record,index)"><img
                        alt="delete_land" src="@/assets/icons/ic_delete_2.svg"></button>
                    </div> -->
                  </div>
                </template>
              </a-table>
            </div>
          </div>
        </div>
      </div>
    </div>
		</div>
    <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
      <div class="d-md-flex d-block button-contain ">
        <button class="btn btn-white" @click="onCancel" type="button">
          <img class="img" src="@/assets/icons/ic_cancel.svg" alt="cancel">
          Trở về
        </button>
        <button class="btn btn-white btn-orange text-nowrap" :class="{'btn-loading disabled': isSubmit}" @click="validateBeforeSubmit">
          <img class="img" src="@/assets/icons/ic_save.svg" alt="save">
          Lưu
        </button>
      </div>
    </div>
  </ValidationObserver>
      <ModalMap
        v-if="openModalMap"
        @cancel="openModalMap = false"
        :location="this.location"
        :address="full_address"
        :center_map="this.form.coordinates"
        @action = "handleOpenMap"
      />
      <ModalBlock
        v-if="openModalBlock"
        :data="formBlock"
        :isEdit="isEdit"
        @action="handleActionBlock"
        @cancel="openModalBlock = false"
      />
      <ModalFloor
        v-if="openModalFloor"
        :data="formFloor"
        :isEdit="isEdit"
        @action="handleActionFloor"
        @cancel="openModalFloor = false"
      />
  </div>

</template>
<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import ModalMap from '@/components/Modal/ModalMap'
import Apartment from '@/models/Apartment'
import WareHouse from '@/models/WareHouse'
import store from '@/store'
import * as types from '@/store/mutation-types'
import ModalDelete from '@/components/Modal/ModalDelete'
import ModalNotification from '@/components/Modal/ModalNotification'
import { Form } from 'ant-design-vue'
import ModalCancel from '@/components/Modal/ModalCancel'
import ModalBlock from './modals/ModalBlock'
import ModalFloor from './modals/ModalFloor'

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
		ModalMap,
		InputNumberNoneFormat,
		'a-form': Form,
		'a-form-item': Form.Item,
		ModalDelete,
		ModalNotification,
		ModalCancel,
		ModalBlock,
		ModalFloor
	},

	data () {
		return {
			location: {
				lng: '',
				lat: ''
			},
			key_render_1: 1,
			key_render_2: 1,
			idData: '',
			full_address: '',
			openModalMap: false,
			openModalBlock: false,
			openModalFloor: false,
			openModalApartment: false,
			selectedRows: [],
			selectedRowKeysBlock: [],
			selectedRowKeysFloor: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			ranks: [
					{
						id: 'binh-dan',
						name: 'Bình dân'
					},
					{
						id: 'trung-cap',
						name: 'Trung cấp'
					},
					{
						id: 'cao-cap',
						name: 'Cao cấp'
					},
				],
			form: {
				name: '',
				province_id: '',
				district_id: '',
				ward_id: '',
				street_id: '',
				coordinates: '',
				rank: '',
				total_blocks: 0,
				total_apartments: 0,
				block: []
			},
			formBlock: {
				name: '',
				first_floor: '',
				last_floor: '',
				apartments_per_floor: '',
				total_floors: '',
				floor: [{
					apartment: []
				}]
			},
			formFloor: {
				name: '',
				status: '',
				block_id: '',
				apartment: []
			},
			formApartment: {
				name: '',
				floor_id: ''
			},
			floors: [],
			apartments: [],
			isSubmit: false,
			province: null,
			district: null,
			ward: null,
			street: null,
			add: false,
			isEdit: false,
			isLoading: false,
			isAddNewItem: false,
			editingKey: '',
			indexEdit: '',
			indexBlock: '',
			indexFloor: ''
		}
	},
	async created () {
		// this.provinces = this.$store.getters.provinces
		// if (this.provinces && this.provinces.length === 0) {
		// 	const resp = await WareHouse.getProvinceAll()
		// 	this.provinces = [...resp.data]
		// 	store.commit(types.SET_PROVINCE, [...resp.data])
		// }

		await WareHouse.getProvince()
			.then((resp) => {
				this.provinces = resp.data
				console.log('this.provinces', this.provinces)
			})

		if ('id' in this.$route.query && this.$route.name === 'apartment.edit') {
			this.form = Object.assign(this.form, { ...this.$route.meta['detail'] })
			this.idData = this.$route.meta['detail'].id
			this.getProvinces()
			this.key_render_1 += 1
			this.key_render_2 += 1
		}
	},
	methods: {

		handleActionApartment (data) {
			if (this.isEdit) {
				this.form.block[this.indexBlock].floor[this.indexFloor].apartment[this.indexEdit] = data
			} else {
				this.form.block[this.indexBlock].floor[this.indexFloor].apartment.push(data)
			}
			this.openModalApartment = false
			this.isEdit = false
		},
		handleDeleteApartment () {

		},
		// create a floor
		onSelectChangeFloor (item, selectedRows) {
			this.selectedRowKeysFloor = item
			this.indexFloor = item[0]
			this.apartments = this.form.block[this.indexBlock].floor[item[0]].apartment
		},
		handleModalAddFloor () {
			this.openModalFloor = true
			this.isEdit = false
			this.formFloor = {
				name: '',
				apartment: []
			}
		},
		handleActionFloor (data) {
			if (this.isEdit) {
				this.form.block[this.indexBlock].floor[this.indexEdit] = data
			} else {
				this.form.block[this.indexBlock].floor.push(data)
			}
			this.openModalFloor = false
			this.isEdit = false
		},
		handleEditFloor (data, record, index) {
			this.openModalFloor = true
			this.isEdit = true
			this.indexEdit = index
			this.formFloor = JSON.parse(JSON.stringify(this.form.block[this.indexBlock].floor[index]))
		},
		handleDeleteFloor () {

		},
		// create a block
		handleModalAddBlock () {
			this.openModalBlock = true
			this.isEdit = false
			this.formBlock = {
				name: '',
				first_floor: '',
				last_floor: '',
				apartments_per_floor: '',
				total_floors: '',
				floor: []
			}
		},
		handleActionBlock (data) {
			if (this.isEdit) {
				this.form.block[this.indexEdit] = data
			} else {
				this.form.block.push(data)
			}
			this.openModalBlock = false
			this.isEdit = false
		},
		handleEditBlock (data, record, index) {
			this.openModalBlock = true
			this.isEdit = true
			this.indexEdit = index
			this.formBlock = JSON.parse(JSON.stringify(this.form.block[index]))
		},
		handleDeleteBlock () {

		},
		changeTotalBlock (e) {
			this.form.total_blocks = e
		},
		changeTotalApartment (e) {
			this.form.total_apartments = e
		},
		// edit a block
		handleMap () {
			this.openModalMap = true
		},
		async handleOpenMap (coordinates) {
			this.form.coordinates = coordinates
			this.location.lat = coordinates.split(',')[0]
			this.location.lng = coordinates.split(',')[1]
		},
		changeProvince (provinceId) {
			this.districts = []
			this.wards = []
			this.streets = []
			this.form.district_id = ''
			this.form.ward_id = ''
			this.form.street_id = ''
			this.getDistrictsByProvinceId(+provinceId)
			// if (this.districts && this.districts.length > 0) {
			// 	this.getWardsByDistrictId(+provinceId)
			// 	this.getStreetByDistrictId(+provinceId)
			// }
			const data = this.form
			let provinceName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
				}
			})
			this.full_address = provinceName
		},
		changeDistrict (districtId) {
			this.wards = []
			this.streets = []
			this.form.ward_id = ''
			this.form.street_id = ''
			this.getWardsByDistrictId(+districtId)
			this.getStreetByDistrictId(+districtId)
			const data = this.form
			let provinceName = ''
			let districtName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
						}
					})
				}
			})
			this.full_address = districtName + ',' + provinceName
		},
		changeWard () {
			const data = this.form
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			if (wardName === '') {
				this.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
		},
		changeStreet () {
			const data = this.form
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			if (wardName === '') {
				this.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
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
			if (this.$route.name === 'apartment.edit') {
				this.updateApartment(data)
			} else {
				this.createApartment(data)
			}
		},

		onCancel () {
			return this.$router.push({name: 'apartment.index'})
		},
		customRowBlock (record, index) {
			return {
				on: {
					click: () => {
						this.onClickTableBlock(record, index)
					}
				}
			}
		},
		customRowFloor (record, index) {
			return {
				on: {
					click: () => {
						this.onClickTableFloor(record, index)
					}
				}
			}
		},
		async createApartment (data) {
			try {
				const res = await Apartment.postProject(data)
				if (res.data) {
					this.$router.push({name: 'apartment.index'}).catch(_ => {})
					this.$toast.open({
						message: 'Thêm mới chung cư thành công',
						type: 'success',
						position: 'top-right'
					})
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async updateApartment (data) {
			try {
				console.log('dâta', data)
				
				const res = await Apartment.postProject(data, this.idData)
				if (res.data) {
					await this.$router.push({name: 'apartment.index'}).catch(_ => {})
					this.$toast.open({
						message: 'Cập nhật chung cư thành công',
						type: 'success',
						position: 'top-right'
					})
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		onClickTableBlock (item, index) {
			this.selectedRowKeysBlock = [index]
			this.selectedRowKeysFloor = []
			this.indexBlock = index
			this.floors = this.form.block[index].floor
			if (this.floors && this.floors.length > 0) {
				this.indexFloor = 0
			}
			this.apartments = []
		},
		onClickTableFloor (item, index) {
			this.selectedRowKeysFloor = [index]
			this.indexFloor = index
			this.apartments = this.form.block[this.indexBlock].floor[index].apartment
		},
		getProvinces () {
			if (this.form.province_id) {
				this.getDistrictsByProvinceId(this.form.province_id)
			}
		},

		async getDistrictsByProvinceId (id) {
			await WareHouse.getDistrictsByProvinceId(id)
				.then((resp) => {
					this.districts = resp.data
					if (this.form.district_id) {
						this.getWardsByDistrictId(this.form.district_id)
						this.getStreetByDistrictId(this.form.district_id)
					}
				})
				.catch((err) => {
					this.isSubmit = false
					throw err
				})
		},
		getWardsByDistrictId (id) {
			let wards = this.districts.filter(item => item.id === id)
			this.wards = wards[0].wards
		},
		getStreetByDistrictId (id) {
			let streets = this.districts.filter(item => item.id === id)
			this.streets = streets[0].streets
		}
	},
	beforeMount () {

	},
	computed: {
		columnsBlock () {
			return [
				{
					title: 'Tên Block',
					align: 'left',
					width: '23%',
					scopedSlots: {customRender: 'name'},
					dataIndex: 'name'
				},
				{
					title: 'Tổng số tầng',
					align: 'left',
					width: '15%',
					scopedSlots: {customRender: 'total_floors'},
					dataIndex: 'total_floors'
				},
				{
					title: 'Thao tác',
					scopedSlots: {customRender: 'action'},
					align: 'center',
					width: '10%'
				}
			]
		},
		columnsFloor () {
			return [
				{
					title: 'Tên tầng chung cư',
					align: 'left',
					width: '90%',
					scopedSlots: {customRender: 'name'},
					dataIndex: 'name'
				},
				{
					title: 'Thao tác',
					scopedSlots: {customRender: 'action'},
					align: 'center',
					width: '10%'
				}
			]
		},
		columnsApartment () {
			return [
				{
					title: 'Tên căn hộ',
					align: 'left',
					width: '85%',
					scopedSlots: {customRender: 'name'},
					dataIndex: 'name'
				},
				{
					title: 'Thao tác',
					scopedSlots: {customRender: 'action'},
					align: 'center',
					width: '10%'
				}
			]
		},
		optionsRank(){
			return{
				data: this.ranks,
				id: 'id',
				key: 'name'
			}
		},
		optionsProvince () {
			return {
				data: this.provinces,
				id: 'id',
				key: 'name'
			}

		},
		optionsDistrict () {
			return {
				data: this.districts,
				id: 'id',
				key: 'name'
			}
		},
		optionsWard () {
			return {
				data: this.wards,
				id: 'id',
				key: 'name'
			}
		},
		optionsStreet () {
			return {
				data: this.streets,
				id: 'id',
				key: 'name'
			}
		}
	}
}
</script>
<style scoped lang="scss">
.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin: 0rem 0.75rem 0.75rem;

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
.img-locate {
  cursor: pointer;
  position: absolute;
  right: 14px;
  top: 2.1rem;
  background-color: #f5f5f5;
  height: 2.1rem;
  width: 32px;
  display: grid;
  place-items: center;

  img {
    height: 60%;
  }
}
.contain_form {
  margin-bottom: 8rem;
	display: flex;
  flex-direction: column;
	.card-group{
		@media (max-width: 768px) {
    flex-direction: column;
		justify-content: center;
  }
		.card{
				&-body{
					padding: 15px 10px 25px;
				}
				@media (max-width: 768px) {
    max-width: 100%;
  }
		}
	}
}
/deep/ .ant-table-column-title {
    color: #00507C;
  }
/deep/ .ant-table-row {
			&:nth-child(even) {
				background: none !important;
			}
		}
/deep/ .ant-table-row:active {
			background: #ffe1b7 !important;
			&:nth-child(even) {
			background: #ffe1b7 !important;
			}
		}
/deep/ .table-row-selected {
			background: #ffe1b7 !important;
			&:nth-child(even) {
			background: #ffe1b7 !important;
			}
		}
.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: none;
  border: none;
  img {
    max-width: 1.5rem;
    min-width: 1rem;
    width: 100%;
    height: auto;
  }
}

</style>
