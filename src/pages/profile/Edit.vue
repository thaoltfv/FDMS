<template>
  <div class="card px-4 py-2" :style="isMobile() ? {'margin':'0', 'padding':'0!important'} : {}">
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
    <div class="card-body">
      <div class="card-body__avatar">
        <div class="img-avatar mb-3">
          <img v-if="form.image === '' || form.image === undefined || form.image === null" class="w-100 h-100" src="../../assets/icons/ic_user.svg" alt="avatar">
          <img v-if="form.image !== '' && form.image !== undefined && form.image !== null" class="w-100 h-100 img__avatar" :src="form.image" alt="img" @click="handleViewImage">
        </div>
        <div class="container__input">
          <button type="button" class="btn btn-orange">Thay đổi avatar</button>
          <input type="file" id="img" accept="image/png, image/jpeg, image/jpeg, image/jpg" class="input__image" @change="onImageChange($event)"/>
        </div>

      </div>
      <div class="card-body__info">
        <InputText
          v-model="form.name"
          label="Họ và tên"
          vid="name"
          rules="required"
          class="information-item"
        />
        <InputText
          v-model="form.email"
          label="Email"
          vid="email"
          rules="required"
          disabled-input
          class="information-item"
        />
        <InputText
          v-model="form.phone"
          label="Số điện thoại"
          vid="phone"
          rules="required"
          type="number"
          :max-length="11"
          class="information-item"
        />
        <InputText
          v-model="form.address"
          label="Địa chỉ"
          vid="address"
          class="information-item"
        />
        <InputText
          v-model="currentUser.roles[0].role_name"
          label="Quyền hạn"
          vid="role"
          rules="required"
          disabled-input
          class="information-item"
        />
        <InputText
          v-model="branch_name"
          label="Chi nhánh"
          vid="branch"
          disabled-input
          class="information-item"
        />
      </div>
    </div>
    <div v-if="!isMobile()"  class="btn-footer d-md-flex d-block justify-content-end align-items-center">
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
      <div v-else class="btn-footer d-md-flex d-block"  style="bottom: 60px;">
        <div class="d-md-flex d-block button-contain row" style="justify-content: space-around;display: flex!important;">
          <button class="btn btn-white col-6" type="button" @click="handleCancel" style="width: unset;margin: 0;padding: 0;">
            <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">
            Trở về
          </button>
          <button class="btn btn-white btn-orange text-nowrap col-6" :class="{'btn-loading disabled': isSubmit}" type="submit" style="width: unset;margin: 0;padding: 0;">
            <img class="img" src="../../assets/icons/ic_save.svg" alt="save">
            Lưu
          </button>
        </div>
      </div>
    </ValidationObserver>
    <ModalImage
      v-if="viewImage"
      :image_detail ="this.form.image"
      @cancel="viewImage = false"
    />
  </div>
</template>

<script>
import UploadDragDrop from '@/components/file/UploadDragDrop'
import store from '@/store'
import File from '@/models/File'
import InputText from '@/components/Form/InputText'
import User from '@/models/User'
import ModalImage from '@/components/Modal/ModalImage'

export default {
	name: 'Create',
	components: {
		UploadDragDrop,
		InputText,
		ModalImage
	},
	data () {
		return {
			viewImage: false,
			isSubmit: false,
			file: null,
			branch_name: '',
			form: {
				image: null,
				name: '',
				email: '',
				phone: '',
				role: '',
				role_name: '',
				branch_id: '',
				address: ''
			}
		}
	},
	computed: {
		currentUser () {
			if (store.getters.profile !== null) {
				return store.getters.profile.data.user
			}
		}
	},
	mounted () {
		this.form.image = this.currentUser.image
		this.form.name = this.currentUser.name
		this.form.phone = this.currentUser.phone
		this.form.email = this.currentUser.email
		this.form.address = this.currentUser.address
		this.form.role = this.currentUser.roles[0].name
		if (this.currentUser.branch) {
			this.form.branch_id = this.currentUser.branch.id
			this.branch_name = this.currentUser.branch.name
		}
	},
	methods: {
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		},
		handleCancel () {
			this.$router.push({name: 'profile.index'})
		},
		handleViewImage () {
			this.viewImage = true
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
		uploadImage () {
			this.isSubmit = true
			const formData = new FormData()
			formData.append('image', this.file)
			return File.uploadImageProfile({data: formData}).then(response => {
				if (response && response.data && response.data.data) {
					const item = {
						link: response.data.data.link,
						picture_type: response.data.data.picture_type
					}
					this.form.image = item.link
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
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleSubmit()
			}
		},
		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			this.updateUser(data)
		},
		async updateUser (data) {
			const id = this.currentUser.id
			try {
				const resp = await User.updateUser(id, data)
				if (resp.data) {
					this.$toast.open({
						message: 'Cập nhật tài khoản thành công',
						type: 'success',
						position: 'top-right'
					})
					await this.$router.push({name: 'profile.index'})
					location.reload()
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
