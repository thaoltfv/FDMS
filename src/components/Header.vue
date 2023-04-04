<template>
  <header class="navbar navbar-expand-md d-none d-lg-flex">
    <div class="container-fluid">
      <button class="navbar-toggler" data-target="#navbar-menu" data-toggle="collapse" type="button">
        <span class="navbar-toggler-icon"></span>
      </button>
      <keep-alive>
        <breadcrumb />
      </keep-alive>
      <div class="navbar-nav flex-row order-md-last ml-auto">
        <div class="nav-item dropdown notification">
          <notification />
        </div>
        <div class="nav-item dropdown ml-2">
          <b-dropdown class="dropdown-container" no-caret right>
            <template #button-content>
              <div v-if="currentUser">
                <font-awesome-icon icon="user-circle" class="avatar" v-if="currentUser.image === '' || currentUser.image === undefined || currentUser.image === null" />
                <img class="avatar" v-if="currentUser.image !== '' && currentUser.image !== undefined && currentUser.image !== null" :src="currentUser.image" alt="image">
              </div>
              <div v-if="currentUser" class="text-left">
                <p class="name mb-0">{{ currentUser.name }}</p>
                <p class="type">{{ formatSentenceCase(currentUser.appraiser.appraise_position.description) }}</p>
              </div>
            </template>
            <b-dropdown-item @click="navigatorToInfo()">
              <div class="dropdown-item-container">Thông tin</div>
            </b-dropdown-item>
            <b-dropdown-item @click="navigatorToPassword()">
              <div class="dropdown-item-container">Đổi mật khẩu</div>
            </b-dropdown-item>
            <b-dropdown-item>
              <div class="dropdown-item-container" @click="logout()">Đăng xuất</div>
            </b-dropdown-item>
          </b-dropdown>
          <!--          <a class="nav-link d-flex lh-1 text-reset p-0" href="#">-->
          <!--            <b-dropdown class="dropdown-container" no-caret>-->
          <!--              <template #button-content>-->
          <!--                <i class="fas fa-user-circle avatar"></i>-->
          <!--              </template>-->
          <!--              <b-dropdown-item>-->
          <!--                <div class="dropdown-item-container">Thông tin</div>-->
          <!--              </b-dropdown-item>-->
          <!--              <b-dropdown-item>-->
          <!--                <div class="dropdown-item-container" @click="logout()">Đăng xuất</div>-->
          <!--              </b-dropdown-item>-->
          <!--            </b-dropdown>-->
          <!--          </a>-->
        </div>
      </div>
    </div>
    <broadcasting ref="broadcasting" v-if="currentUser !== undefined"/>
  </header>
</template>

<script>
import 'firebase/auth'
import * as types from '@/store/mutation-types'
import Admin from '@/models/Admin'
import Breadcrumb from '@/components/Breadcrumb'
import Broadcasting from '@/components/Broadcasting'
import Notification from '@/components/Notification'
import firebase from 'firebase/app'
import store from '@/store'
import { BDropdown, BDropdownItem } from 'bootstrap-vue'
import { BellIcon, LogOutIcon } from 'vue-feather-icons'
import { LOCALE } from '@/enum/locale.enum'

export default {
	name: 'Header',

	components: {
		LogOutIcon,
		BellIcon,
		Breadcrumb,
		Broadcasting,
		Notification,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem
	},

	data () {
		return {
			languages: [LOCALE.ja, LOCALE.en, LOCALE.vi]
		}
	},

	computed: {
		currentUser () {
			if (store.getters.profile !== null) {
				return store.getters.profile.data.user
			}
			return null
		}
	},

	methods: {
		formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},
		async logout () {
			try {
				await Admin.logout()
				await firebase.auth().signOut()
			} catch (e) { }
			store.commit(types.LOG_OUT)
			await this.$router.push({ name: 'login' })
		},
		navigatorToInfo () {
			if (this.$route.path !== '/profile') {
				this.$router.push({ name: 'profile.index' })
			}
		},
		navigatorToPassword () {
			if (this.$route.path !== '/profile') {
				this.$router.push({ name: 'profile.password' })
			}
		},
		initBroadcasting () {
			if (this.currentUser !== null) {
				this.$refs.broadcasting.connect()
			} else {
				this.$refs.broadcasting.disconnect()
			}
		}
	},

	watch: {
		currentUser (value) {
			this.initBroadcasting()
		}
	},

	mounted () {
		this.initBroadcasting()
	}
}
</script>

<style lang="scss" scoped>
.navbar {
  background: #FFFFFF;
  position: -webkit-sticky;
  /* Safari */
  position: sticky;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  top: 0;
  z-index: 100;
  padding: 5px 20px;
  // height: 100%;
  max-height: 80px;
}

.name {
  margin-bottom: 4px;
  font-weight: bold;
}

.type {
  margin-bottom: 0;
  font-size: 12px;
}

.name,
.type {
  margin-left: 14px;
  color: #000000;
  font-size: 14px;
}

.avatar {
  font-size: 40px !important;
  width: 40px !important;
  height: 40px !important;
  object-fit: cover;
}

.dropdown-menu {
  top: 1.25rem;

  li {
    padding: 0;
  }

  .dropdown-item {
    padding: 1rem;
  }
}

.notification {
  cursor: pointer;
  margin-right: 0.5rem;
}
</style>
