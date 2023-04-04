<template>
    <div class="pannel card">
      <ValidationObserver tag="form"
                          ref="observer"
                          @submit.prevent="validateBeforeSubmit">

        <InputText
        v-model="form.name"
        placeholder="Nhập tên chi nhánh"
        rules="required|max:200"
        :max-length="200"
        vid="branch"
        label="Tên chi nhánh"
        />

        <InputText
          v-model="form.address"
          placeholder="Nhập địa chỉ"
          rules="required|max:200"
          :max-length="200"
          vid="address"
          label="Địa chỉ"
          class="mt-3"
        />

        <InputText
          v-model="form.acronym"
          placeholder="Nhập tên viết tắt"
          rules="required|max:200"
          :max-length="200"
          vid="acronym"
          label="Tên viết tắt"
          class="mt-3"
        />
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
      </ValidationObserver>
    </div>
</template>
<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import Branch from '@/models/Branch'

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
		InputCategory
	},

	data () {
		return {
			provinces: [],
			form: {
				name: '',
				acronym: '',
				address: ''
			},
			isSubmit: false
		}
	},
	created () {
		if ('id' in this.$route.query && this.$route.name === 'branch.edit') {
			this.form = Object.assign(this.form, {
				...this.$route.meta['detail']
			})
			this.form.province_id = this.$route.meta['detail'].province_id
			this.form.district_id = this.$route.meta['detail'].district_id
		} else {
		}
	},
	computed: {
		optionsProvince () {
			return {
				data: this.provinces,
				id: 'id',
				key: 'name'
			}
		}
	},
	methods: {
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleSubmit()
			}
		},

		handleSubmit () {
			this.isSubmit = true
			let data = this.form

			if (this.$route.name === 'branch.edit') {
				this.updateBranch(data)
			} else {
				this.createBranch(data)
			}
		},
		onCancel () {
			return this.$router.push({name: 'branch.index'})
		},

		async getProvinces () {
			try {
				const resp = await Branch.getProvince()
				this.provinces = [...resp.data]
				if (this.form.province_id === '') {
					this.form.province_id = 45
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async createBranch (data) {
			try {
				const resp = await Branch.create(data)

				if (resp && Object.keys(resp).length) {
					this.$router.push({name: 'branch.index'}).catch(_ => {
					})
				}
				this.$toast.open({
					message: 'Thêm mới chi nhánh thành công',
					type: 'success',
					position: 'top-right'
				})
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async updateBranch (data) {
			try {
				const resp = new Branch(data)
				await resp.save()
				await this.$router.push({name: 'branch.index'}).catch(_ => {
				})
				this.$toast.open({
					message: 'Cập nhật phân quyên thành công',
					type: 'success',
					position: 'top-right'
				})
			} catch (err) {
				this.isSubmit = false
			}
		}
	},
	beforeMount () {
		this.getProvinces()
	}
}
</script>
<style scoped lang="scss">
  .pannel{
    background: #FFFFFF;
    box-shadow: 1px 2px 0 #e5eaee;
    border-radius: 5px;
    padding: 25px;
    margin-bottom: 40px;
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
</style>
