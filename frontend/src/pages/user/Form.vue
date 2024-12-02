<template>
  <div class="user-section">
    <div class="card">
      <div class="card-body">
        <!--Form-->
        <ValidationObserver tag="form"
                            ref="observer"
                            @submit.prevent="validateBeforeSubmit()">
          <div class="row">
            <!--Field User_number ( disable )-->
            <template v-if="$route.name === 'user.edit'">
              <div class="col-md-6 mb-3">
                <label class="form-label">{{ $t('user_number') }}:</label>

                <a-input disabled="" v-model="user_number"/>
              </div>
            </template>

            <!--Company ID-->
            <InputCategory v-if="currentUser.roles.name === ADMIN_TYPE.SUPER_ADMIN"
                           v-model="form.company_id"
                           class="col-md-6 mb-3"
                           rules="required"
                           vid="company_id"
                           label="company_id"
                           :options="optionsCompanyCategory"
                           :required="true"/>

            <!--User name-->
            <InputText v-model="form.nickname"
                       class="col-md-6 mb-3"
                       rules="required|max:100"
                       :max-length="100"
                       vid="user_name"
                       label="user_name"
                       :required="true"/>

            <!--Login ID-->
            <InputText v-model="form.login_id"
                       class="col-md-6 mb-3"
                       rules="required|alpha_num|min:5|max:50"
                       :max-length="50"
                       vid="login_id"
                       label="login_id"
                       :required="true"/>

            <!--CHECK PASSWORD REQUIRED-->
            <template v-if="$route.name === 'user.edit'">
              <!--Login password-->
              <InputText v-model="form.login_password"
                         class="col-md-6 mb-3"
                         rules="half_alphabet|min:8|max:100"
                         :max-length="100"
                         type="password"
                         vid="login_password"
                         label="login_password"/>

              <!--Login password confirm-->
              <InputText v-model="form.login_password_confirmation"
                         class="col-md-6 mb-3"
                         rules="confirmed:login_password"
                         type="password"
                         vid="login_password_confirmation"
                         label="login_password_confirmation"/>
            </template>

            <template v-else>
              <!--Login password (required)-->
              <InputText v-model="form.login_password"
                         class="col-md-6 mb-3"
                         rules="required|half_alphabet|min:8|max:100"
                         :max-length="100"
                         type="password"
                         vid="login_password"
                         label="login_password"
                         :required="true"/>

              <!--Login password confirm-->
              <InputText v-model="form.login_password_confirmation"
                         class="col-md-6 mb-3"
                         rules="required|confirmed:login_password"
                         type="password"
                         vid="login_password_confirmation"
                         label="login_password_confirmation"
                         :max-length="100"
                         :required="true"/>
            </template>

            <!--Mail-->
            <InputText v-model="form.mail"
                       class="col-md-6 mb-3"
                       rules="required|max:100|email"
                       :max-length="100"
                       vid="mail"
                       label="mail"
                       icon="mail"
                       :required="true"/>

            <!--Tel-->
            <InputText v-model="form.tel"
                       class="col-md-6 mb-3"
                       rules="format_tel"
                       vid="tel"
                       label="tel"
                       :placeholder="$t('placeholder_tel')"
                       icon="phone"/>

            <!--Birthday-->
            <InputDatePicker v-model="form.birthday"
                             class="col-md-6 mb-3"
                             vid="birthday"
                             label="birthday"
                             :placeholder="$t('placeholder_birthday')"/>

            <!--Gender-->
            <InputCategoryGender v-model="form.gender"
                                 class="col-md-6 mb-3"
                                 vid="gender"
                                 label="gender"/>

            <!--Prefecture ID-->
            <InputCategory v-model="form.prefecture_id"
                           class="col-md-6 mb-3"
                           vid="prefecture_id"
                           label="prefecture"
                           :options="optionsPrefectureCategory"/>

            <!--Post code-->
            <InputText v-model="form.post_code"
                       class="col-md-6 mb-3"
                       vid="post_code"
                       label="post_code"
                       :maxLength="9"
                       :placeholder="$t('placeholder_postcode')"/>

            <!--Address-->
            <InputText v-model="form.address"
                       class="col-md-6 mb-3"
                       rules="max:50"
                       :max-length="50"
                       vid="address"
                       label="address"/>

            <!--City name-->
            <InputText v-model="form.city_name"
                       class="col-md-6 mb-3"
                       rules="max:50"
                       :max-length="50"
                       vid="city_name"
                       label="district"/>

            <!--Is Active-->
            <InputSwitch v-model="form.is_active"
                         class="col-md-6 mb-3 mb-sm-0"
                         label="text_active"/>

            <!--Is Preview-->
            <InputSwitch v-model="form.is_review"
                         class="col-md-6 mb-3 mb-sm-0"
                         label="text_preview"/>
          </div>

          <!-- Action Section Submit & Cancel -->
          <div class="card-footer card-action fixed-bottom shadow">
            <button type="submit"
                    v-if="$route.params.id ? hasPermissionAction(PERMISSIONS.UPDATE_USER) : hasPermissionAction(PERMISSIONS.CREATE_USER)"
                    class="btn btn-pill btn-success float-right px-4 font-weight-bold"
                    :class="{'btn-loading disabled': isSubmit}">
              {{this.$route.name === 'user.edit' ? $t('btn_update') : $t('btn_save')}}
            </button>

            <button type="button"
                    class="btn btn-pill btn-light float-right px-4 mr-3"
                    @click="onCancel">
              <span>{{ $t('back') }}</span>
            </button>
          </div>
        </ValidationObserver>
      </div>
    </div>
  </div>
</template>

<script>

import User from '@/models/User'
import store from '@/store'
import Form from '@/mixins/form.mixin'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategoryGender from '@/components/Form/InputCategoryGender'
import InputSwitch from '@/components/Form/InputSwitch'

export default {
	name: 'Form',

	components: {
		InputSwitch,
		InputCategoryGender,
		InputDatePicker,
		InputText,
		InputCategory
	},

	data () {
		return {
			list_companies: [],
			list_prefectures: [],
			form: {
				company_id: '',
				prefecture_id: '',
				nickname: '',
				birthday: '',
				gender: '',
				post_code: '',
				city_name: '',
				address: '',
				tel: '',
				mail: '',
				login_id: '',
				login_password: '',
				login_password_confirmation: '',
				is_active: true,
				is_review: false
			},
			user_number: '',
			isSubmit: false
		}
	},

	mixins: [Form],

	watch: {
		// handle hyphen when post_code = 3
		'form.post_code' (val) {
			const tmpInt = val.replace('-', '')
			if (tmpInt.length > 3) {
				this.form.post_code = tmpInt.substring(0, 3) + '-' + tmpInt.substring(3)
			}
		}
	},

	created () {
		this.list_companies = this.$route.meta['companies']
		this.list_prefectures = this.$route.meta['prefectures']

		// Check permission not super admin
		if (this.currentUser && this.currentUser.roles.name !== this.ADMIN_TYPE.SUPER_ADMIN) {
			this.form.company_id = this.currentUser.company.id
		}

		if ('id' in this.$route.params && this.$route.name === 'user.edit') {
			const detail = this.$route.meta['detail']

			// Show user_number but disable
			this.user_number = detail.user_number

			this.form = Object.assign(this.form, {
				company_id: detail.company.id,
				prefecture_id: detail.prefecture_id || '',
				birthday: [detail.birthday],
				...detail
			})
		}
	},

	computed: {
		currentUser () {
			return store.getters.profile.data
		},

		optionsCompanyCategory () {
			return {
				data: this.list_companies,
				id: 'id',
				key: 'company_name',
				code: 'company_code'
			}
		},

		optionsPrefectureCategory () {
			return {
				data: this.list_prefectures,
				id: 'id',
				key: 'prefecture_name'
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

			if (this.$route.name === 'user.edit') {
				this.updateUser(data)
			} else {
				this.createUser(data)
			}
		},

		async updateUser (data) {
			try {
				const resp = new User(data)
				await resp.save()

				await this.onSuccess(this.$t('message_success'), this.$t('update_message_successfully'))

				await this.$router.push({name: 'user.index'}).catch(_ => {
				})
			} catch (err) {
				throw err
			}
		},

		async createUser (data) {
			try {
				const resp = await User.create(data)

				if (resp && Object.keys(resp).length) {
					await this.onSuccess(this.$t('message_success'), this.$t('create_message_successfully'))

					this.$router.push({name: 'user.index'}).catch(_ => {})
				}
			} catch (err) {
				throw err
			}
		},
		onCancel () {
			return this.$router.push({ name: 'user.index' })
		}
	}
}
</script>

<style lang="scss" scoped>
  .user-section {
    padding: 1.25rem;
  }
</style>
