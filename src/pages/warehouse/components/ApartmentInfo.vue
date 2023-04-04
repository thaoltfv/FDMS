<template>
  <div class="row justify-content-between">
    <InputCategory
      v-model="apartment.apartment_id"
      vid="apartment_id"
      label="Tên chung cư/dự án"
      rules="required"
      class="col-12 col-md-4 col-lg-3 form-group-container"
      :options="optionApartment"
      @change="handleApartment($event)"
    />
    <InputCategory
      v-model="apartment_info.block_list_id"
      vid="block_list_id"
      label="Block (khu)"
      class="col-12 col-md-4 col-lg-3 form-group-container"
      :options="optionBlock"
      @change="handleBlockList($event)"
    />
    <InputText
      v-model="apartment_info.room_num"
      vid="room_num"
      label="Mã căn hộ"
      class="col-12 col-md-4 col-lg-4 form-group-container"
    />
    <InputNumberFormat
      v-model="apartment_info.floor"
      vid="floor"
      label="Tầng"
      rules="required"
      :max="9999"
      :min="0"
      @change="handleFloor($event)"
      :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
      class="col-12 col-md-4 col-lg-3 form-group-container"
    />
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputText from '@/components/Form/InputText'
import WareHouse from '@/models/WareHouse'
export default {
	name: 'ApartmentInfo',
	props: ['apartment_info', 'apartment', 'block_info'],
	components: {
		InputNumberFormat,
		InputCategory,
		InputText
	},
	mounted () {
		if (this.apartment_info.block_list_id !== '') {
			this.block_info.block_list_id = this.apartment_info.block_list_id
		}
	},
	data () {
		return {
			apartments: [],
			block_lists: [],
			form: {
				apartment_id: '',
				apartment_info: [
					{
						block_list_id: '',
						room_num: '',
						floor: ''
					}
				]
			}
		}
	},
	computed: {
		optionApartment () {
			return {
				data: this.apartments,
				id: 'id',
				key: 'name'
			}
		},
		optionBlock () {
			return {
				data: this.block_lists,
				id: 'id',
				key: 'name'
			}
		}
	},
	methods: {
		handleFloor (event) {
			if (event !== undefined && event !== null) {
				this.apartment_info.floor = parseInt(event)
			} else {
				this.apartment_info.floor = 0
			}
		},
		handleApartment (ApartmentId) {
			this.blocks = []
			this.apartment_info.block_list_id = ''
			this.getBlockListsByApartmentId(+ApartmentId)
			this.apartments.forEach(apartment => {
				if (apartment.id === this.apartment.apartment_id) {
					if (apartment.address !== null && apartment.address !== undefined) {
						this.apartment.full_address = apartment.address
					} else {
						this.apartment.full_address = ''
					}
					if (apartment.coordinates !== null && apartment.coordinates !== undefined) {
						this.apartment.coordinates = apartment.coordinates
					} else {
						this.apartment.coordinates = ''
					}
				}
			})
		},
		handleBlockList (event) {
			this.block_info.block_list_id = event
		},
		async getApartments () {
			try {
				const resp = await WareHouse.getApartment()
				this.apartments = [...resp.data]
				await this.getBlockListsByApartmentId(this.block_info.apartment_id)
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getBlockListsByApartmentId (id) {
			try {
				const resp = await WareHouse.getBlock(id)
				this.block_lists = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	beforeMount () {
		// this.getApartments()
	}
}
</script>

<style scoped lang="scss">
.form-group-container {
  margin-top: 15px;
}
</style>
