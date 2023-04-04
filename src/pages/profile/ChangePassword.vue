<template>
  <div class="card px-4 py-2">
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div class="card-body">
        <div class="card-body__info">
          <InputText
            v-model="form.new_password"
            label="Mật khẩu mới"
            vid="new_password"
            type="password"
            rules="required|min:8"
            class="information-item"
            autocomplete="off"
          />
          <InputText
            v-model="form.confirm_new_password"
            label="Nhập lại mật khẩu"
            vid="new_password"
            type="password"
            rules="required|min:8"
            class="information-item"
            autocomplete="off"
          />
        </div>
      </div>
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
        <div class="d-md-flex d-block button-contain ">
          <button class="btn btn-white" type="button" @click="handleCancel">
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
  </div>
</template>

<script>
import UploadDragDrop from '@/components/file/UploadDragDrop'
import InputText from '@/components/Form/InputText'
import User from '@/models/User'

export default {
	name: 'change_password',
	components: {
		UploadDragDrop,
		InputText
	},
	data () {
		return {
			viewImage: false,
			isSubmit: false,
			file: null,
			form: {
				new_password: '',
				confirm_new_password: ''
			}
		}
	},
	computed: {
	},
	mounted () {
	},
	methods: {
		handleCancel () {
			this.$router.push({name: 'profile.index'})
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleSubmit()
			}
		},
		handleSubmit () {
			this.isSubmit = true
			this.changePassword()
		},
		async changePassword () {
			try {
				const resp = await User.changePassword(this.form)
				if (resp.data) {
					this.$toast.open({
						message: 'Cập nhật mật khẩu thành công',
						type: 'success',
						position: 'top-right'
					})
					await this.$router.push({name: 'profile.index'})
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: 'error',
						position: 'top-right'
					})
				}
				this.isSubmit = false
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	}
}
</script>

<style lang="scss" scoped>
.card {
  padding-bottom: 47px !important;
  box-shadow: none;
  border: none;
  &-title {
    font-weight: 700;
    padding-bottom: 47px;
  }
  &-body {
    align-items: center;
    @media (max-width: 766px) {
      grid-column-gap: 20px;
    }
    &__avatar {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    &__info {
      .information-item {
        padding: 5px 0;
        &__title {
        }
      }
    }
  }
}
.name {
  font-weight: 700;
  font-size: 20px;
}
.img-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  img {
    object-fit: cover;
    border-radius: 50%;
  }
}
.container{
  &__input{
    position: relative;
    .input{
      &__image{
        position: absolute;
        width: 100%;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        opacity: 0;
        cursor: pointer;
      }
    }
  }
}
.btn{
  &__change-password{
    margin-right: 10px;
  }
}
.information {
  display: flex;
  flex-direction: row;
  margin: auto;
  @media (max-width: 766px) {
    display: block;
  }
  &-item {
    font-size: 1.125rem;
    margin: 0 45px;
    @media (max-width: 766px) {
      padding: 7.5px 0;
    }
    &__title {
      min-width: 147px;
      font-size: 1.125rem;
      font-weight: 600;
      color: #555555;
      display: block;
    }
  }
}
.action {
  margin-bottom: 47px;
}
.img__avatar{
  cursor: pointer;
}
.name{
  &__user {
    font-size: 20px;
    font-weight: 700;
    color: #000000;
  }
}
</style>
