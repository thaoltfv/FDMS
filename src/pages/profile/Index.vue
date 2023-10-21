<template>
  <div class="card px-4 py-2">
    <div class="card-body">
      <div class="card-body__avatar">
        <div class="img-avatar mb-3">
          <img v-if="currentUser.image === '' || currentUser.image === undefined || currentUser.image === null" class="w-100 h-100" src="../../assets/icons/ic_user.svg" alt="avatar">
          <img v-if="currentUser.image !== '' && currentUser.image !== undefined && currentUser.image !== null" class="w-100 h-100 img__avatar" :src="currentUser.image" alt="img">
        </div>
        <p class="name__user" >{{currentUser.name !== undefined && currentUser.name !== null ? currentUser.name : '' }}</p>
        <div v-if="!isMobile()" class="d-flex">
          <router-link :to="{name: 'profile.password'}" class="btn btn-orange btn__change-password">Thay đổi mật khẩu</router-link>
          <div class="container__input">
            <router-link type="button" class="btn btn-orange" :to="{name: 'profile.edit'}">Chỉnh sửa thông tin</router-link>
          </div>
        </div>
        <div v-else class="d-flex">
          <router-link :to="{name: 'profile.password'}" class="btn btn-orange btn__change-password">Đổi mật khẩu</router-link>
          <div class="container__input">
            <router-link type="button" class="btn btn-orange" :to="{name: 'profile.edit'}">Chỉnh sửa</router-link>
          </div>
        </div>
      </div>
      <div class="card-body__info">
        <div class="container container__info">
          <div class="card-title"> Thông tin cá nhân</div>
          <div class="row">
            <div class="col-12 col-lg-6">
              <p class="information-item"><span class="information-item__title">Email:</span> {{ currentUser ? currentUser.email : '' }}</p>
            </div>
            <div class="col-12 col-lg-6">
              <p class="information-item"><span class="information-item__title">Số điện thoại:</span> {{ currentUser ? currentUser.phone : ''}}</p>
            </div>
            <div class="col-12 col-lg-6">
              <p class="information-item"><span class="information-item__title">Quyền hạn:</span> {{ currentUser ? currentUser.roles[0].role_name : ''}}</p>
            </div>
            <div class="col-12 col-lg-6">
              <p class="information-item"><span class="information-item__title">Chi nhánh:</span> {{ currentUser && currentUser.branch ? currentUser.branch.name : '' }}</p>
            </div>
            <div class="col-12 col-lg-6">
              <p class="information-item"><span class="information-item__title">Địa chỉ:</span> {{ currentUser ? currentUser.address : '' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
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
import InputText from '@/components/Form/InputText'
import ModalImage from '@/components/Modal/ModalImage'

export default {
	name: 'password',
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
	methods: {
    isMobile() {
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		},
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
    background: #faa831;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    padding: 10px;
    margin-right: -0.75rem;
    margin-left: -0.75rem;
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
        padding: 8px 0;
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
  &__info {
    margin-top: 30px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    border-radius: 10px;
    padding-bottom: 20px;
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
    color: #000000;
    font-weight: 700;
    @media (max-width: 766px) {
      padding: 7.5px 0;
      margin: 0 25px;
    }
    &__title {
      min-width: 147px;
      font-size: 1.125rem;
      font-weight: 500;
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
