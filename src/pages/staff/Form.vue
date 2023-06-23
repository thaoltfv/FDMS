<template>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
		<div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin chung</h3>
        </div>
      </div>
      <div class="card-body card-info">
        <div class="container-fluid color_content">
          <div class="row">
						<InputText
							v-model="form.name"
							placeholder="Nhập họ tên"
							rules="required|max:200"
							label="Họ tên"
							:max-length="200"
							class="col-12 col-lg-6 input-content"
						/>
						<InputText
							v-model="form.email"
							placeholder="Nhập email"
							rules="required|max:200"
							type="email"
							label="Email"
							:disabled-input="true"
							autocomplete="off"
							class="col-12 col-lg-6 input-content"
							:class="this.$route.name === 'staff.edit' ? '' : 'd-none'"
						/>
						<InputText
							v-model="form.email"
							placeholder="Nhập email"
							rules="required|max:200"
							type="email"
							label="Email"
							autocomplete="off"
							class="col-12 col-lg-6 input-content"
							:class="this.$route.name === 'staff.edit' ? 'd-none' : ''"
						/>
						<InputText
							v-model="form.phone"
							placeholder="Nhập số điện thoại"
							rules="required|max:11"
							type="number"
							label="Số điện thoại"
							autocomplete="off"
							:max-length="11"
							class="col-12 col-lg-6 input-content"
						/>
						<!-- <InputText
							v-model="form.password"
							placeholder="Nhập password"
							:rules="this.$route.name === 'staff.edit'? '' : 'required|max:200'"
							type="password"
							label="Password"
							:max-length="200"
							autocomplete="off"
							class="col-12 col-lg-6 input-content"
							:class="this.$route.name === 'staff.edit' ? 'd-none' : ''"
						/> -->
						<InputCategory
							v-model="form.role_id"
							vid="role"
							label="Phân quyền"
							rules="required"
							:options= optionsRole
							class="col-12 col-lg-6 input-content"
						/>
						<InputText
							v-model="form.address"
							placeholder="Nhập địa chỉ"
							rules="max:200"
							label="Địa chỉ"
							:max-length="300"
							class="col-12 col-lg-6 input-content"
						/>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin nhân viên</h3>
        </div>
      </div>
      <div class="card-body card-info">
        <div class="container-fluid color_content">
          <div class="row">
						<InputCategory
							class="col-12 col-lg-6 input-content"
							v-model="form.appraiser.appraise_position_id"
							:options="optionRoles"
							label="Chức vụ"
							rules="required"
						/>
						<InputText
							v-model="form.appraiser.appraiser_number"
							placeholder="Nhập số thẩm định viên"
							rules="max:200"
							label="Số thẩm định viên"
							:max-length="200"
							class="col-12 col-lg-6 input-content"
						/>
						<InputCategory
							v-model="form.appraiser.branch_id"
							class="col-12 col-lg-6 input-content"
							vid="branch"
							label="Chi nhánh"
							rules="required"
							:options= optionsBranch
						/>
						<InputText
							v-model="form.mailing_address"
							placeholder="Nhập email thông báo"
							rules="max:200"
							type="email"
							label="Email thông báo"
							autocomplete="off"
							:max-length="200"
							class="col-12 col-lg-6 input-content"
						/>
					</div>
				</div>
			</div>
		</div>
		<div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
			<div class="d-md-flex d-block button-contain ">
				<button class="btn btn-white" @click="onCancel" type="button">
					<img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">
					Trở về
				</button>
				<button class="btn btn-white btn-orange text-nowrap" :class="{'btn-loading disabled': isSubmit}" type="submit">
					<img class="img" src="../../assets/icons/ic_save.svg" alt="save">
					Lưu
				</button>
			</div>
		</div>
</ValidationObserver>
</template>
<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import User from '@/models/User'
import WareHouse from '@/models/WareHouse'
import { capitalize } from 'lodash-es'

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
			roles: [],
			branches: [],
			positions: [],
			form: {
				appraiser: {
					appraise_position_id: '',
					appraiser_number: '',
					branch_id: ''
				},
				role_id: '',
				branch_id: '',
				phone: '',
				name: '',
				email: '',
				password: '',
				mailing_address: '',
				address: ''
			},
			isSubmit: false,
			branch: null,
			role_id: ''
		}
	},
	created () {
		console.log('dsadsađâs',this.$route.meta['detail'])
		if ('id' in this.$route.query && this.$route.name === 'staff.edit') {
			this.form = Object.assign(this.form, {
				...this.$route.meta['detail']
			})
			this.branch = this.$route.meta['detail'].branch
			this.role_id = this.$route.meta['detail'].roles[0].id
		} else {
			let year = new Date().getFullYear()
			this.form.password = 'ThamDinh' + capitalize(process.env.CLIENT_ENV) + '@' + year
		}
	},
	computed: {
		optionsRole () {
			return {
				data: this.roles,
				id: 'id',
				key: 'role_name'
			}
		},
		optionsBranch () {
			return {
				data: this.branches,
				id: 'id',
				key: 'name'
			}
		},
		optionRoles () {
			return {
				data: this.positions,
				id: 'id',
				key: 'description'
			}
		}
	},
	methods: {
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.positions = [...reps.data.chuc_vu]
			} catch (err) {
				this.isSubmit = false
				throw err
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
			const role = this.roles.find(role => role.id === data.role_id)
			data.role = role.name
			if (this.$route.name === 'staff.edit') {
				this.updateStaff(data)
			} else {
				this.createStaff(data)
			}
		},
		onCancel () {
			return this.$router.push({name: 'staff.index'})
		},

		async getRoles () {
			try {
				const resp = await User.getRoles()
				this.roles = [...resp.data.data]
				this.form.role_id = this.role_id
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getBranches () {
			try {
				const resp = await User.getBranches()
				this.branches = [...resp.data]
				if (this.branch !== undefined && this.branch !== null) {
					this.form.branch_id = this.branch.id
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async createStaff (data) {
			try {
				const resp = await User.create(data)

				if (resp && Object.keys(resp).length) {
					this.$router.push({name: 'staff.index'}).catch(_ => {
					})
				}
				if (resp.data) {
					this.$toast.open({
						message: 'Thêm mới nhân viên thành công',
						type: 'success',
						position: 'top-right'
					})
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: 'error',
						position: 'top-right'
					})
				}
			} catch (err) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Email đã tồn tại',
					type: 'error',
					position: 'top-right'
				})
				throw err
			}
		},

		async updateStaff (data) {
			try {
				const resp = new User(data)
				await resp.save()
				await this.$router.push({name: 'staff.index'}).catch(_ => {
				})
				if (resp.data) {
					this.$toast.open({
						message: 'Cập nhật nhân viên thành công',
						type: 'success',
						position: 'top-right'
					})
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: 'error',
						position: 'top-right'
					})
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	beforeMount () {
		this.getRoles()
		this.getBranches()
		this.getDictionary()
	},
	mounted () {
		console.log(this.form.password)
	}
}
</script>
<style scoped lang="scss">
.input-content{
    margin-bottom: 10px;
}
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
}
	form {
		padding-bottom: 35px;
	}
</style>
