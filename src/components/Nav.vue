<template>
  <div v-if="!isMobile()" class="navbar" :class="toggleItem? 'sidebar-mini' : 'sidebar'">
    <aside :class="[toggleItem ? navbar_mini : '']" class="navbar navbar-vertical d-flex flex-column navbar-expand-lg h-100">
      <button class="navbar-toggler" data-target="#navbar-menu" data-toggle="collapse" type="button">
        <span class="navbar-toggler-icon"></span>
      </button>
        <div class="d-flex w-100 navbar-brand align-items-center justify-content-center">
          <img class="navbar-brand-image" :src="logo" alt="company logo">
        </div>

      <div class="collapse navbar-collapse" id="navbar-menu">
        <ul class="navbar-nav">
          <li  v-for="item of getNav"
               :key="item.id"
               @click="item.dropdown ? dropdownClick($event) : !item.dropdown ? removeDropdown() : () => {}"
               class="nav-item"
               :id = "item.id"
               :class="{'dropdown': item.dropdown}">
            <svg width="11" height="6" viewBox="0 0 11 6" xmlns="http://www.w3.org/2000/svg" class="icon-dropdown">
              <path d="M5.49999 3.78132L9.00624 0.481323L10.0078 1.42399L5.49999 5.66666L0.992157 1.42399L1.99374 0.481323L5.49999 3.78132Z" fill="currentColor"/>
            </svg>
            <!--navigation normal-->
            <router-link
              v-if="hasPermission(item)"
              :exact="item.exact"
              :to="{name: item.routeName}"
              class="nav-link">
              <div class="nav-contain-wrapper">
                <div class="contain-icon">
                  <icon-base :name="item.icon" v-if="item.customImage" width="20px" height="20px" class="item-icon svg-inline--fa" />
                  <font-awesome-icon :icon="item.icon" class="item-icon" width="20px" height="20px" v-else />
                </div>
                <span class="nav-link-title">
                  {{ $t(`${ item.title }`) }}
                </span>
              </div>
            </router-link>
            <ul v-if="item.dropdown" class="dropdown-item">
              <li v-for="dropdown in item.dropdown" :key="dropdown.id" class="nav-link-title nav-dropdown">
                <router-link v-if="hasPermission(dropdown)"
                             :exact="dropdown.exact"
                             :to="{name: dropdown.routeName}"
                             class="nav-link">
                  {{ dropdown.title }}
                </router-link>
              </li>
            </ul>
          </li>
          <li id="user" @click="dropdownClick($event)" class="nav-item nav-user dropdown">
            <svg width="11" height="6" viewBox="0 0 11 6" xmlns="http://www.w3.org/2000/svg" class="icon-dropdown">
              <path d="M5.49999 3.78132L9.00624 0.481323L10.0078 1.42399L5.49999 5.66666L0.992157 1.42399L1.99374 0.481323L5.49999 3.78132Z" fill="currentColor"/>
            </svg>
            <!--navigation normal-->
            <div class="nav-link">
              <div class="nav-contain-wrapper">
                <div class="contain-icon" >
                  <font-awesome-icon icon="user-circle" class="item-icon"></font-awesome-icon>
                </div>
                <span class="name mb-0 d-flex flex-column nav-link-title">{{currentUser ? currentUser.name : ''}}<span class="mt-1">{{currentUser ? currentUser.roles[0].role_name : ''}}</span></span>
              </div>
            </div>
            <ul class="dropdown-item">
              <li class="nav-link-title nav-dropdown" >
                <router-link :to="{name: 'profile.index'}" class="nav-link">
                  Thông tin
                </router-link>
              </li>
              <li class="nav-link-title nav-dropdown" @click="logout()"> <div class="nav-link">Đăng xuất</div> </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="toggle d-flex justify-content-end" :class="[toggleItem ? toggle_mini : '']">
        <div class="toggle-btn" @click="handleClick">
          <font-awesome-icon icon="angle-right" class="item-icon arrow"/>
        </div>
      </div>
    </aside>
  </div>
  <div v-else class="navbar" :class="toggleItem? 'sidebar-mini' : 'sidebar'">
    <aside :class="[toggleItem ? navbar_mini : '']" class="navbar navbar-vertical d-flex flex-column navbar-expand-lg h-100">
      <button class="navbar-toggler" data-target="#navbar-menu" data-toggle="collapse" type="button" style="z-index: 201;">
        <span class="navbar-toggler-icon" style="color: orange!important;" @click="minimize"></span>
      </button>
        <div class="d-flex w-100 navbar-brand align-items-center justify-content-center">
          <img class="navbar-brand-image" :src="logo" alt="company logo">
        </div>

      <div v-show="minimize_key" class="collapse navbar-collapse" id="navbar-menu" style="position: fixed;
    top: 0;
    z-index: 200;
    background: #F6F7FB;
    width: 70%!important;">
        <ul class="navbar-nav" style="margin-top: 40px;">
          <li  v-for="item of getNav"
               :key="item.id"
               @click="item.dropdown ? dropdownClick($event) : !item.dropdown ? removeDropdown() : () => {}"
               class="nav-item"
               :id = "item.id"
               :class="{'dropdown': item.dropdown}"
               >
            <svg width="11" height="6" viewBox="0 0 11 6" xmlns="http://www.w3.org/2000/svg" class="icon-dropdown">
              <path d="M5.49999 3.78132L9.00624 0.481323L10.0078 1.42399L5.49999 5.66666L0.992157 1.42399L1.99374 0.481323L5.49999 3.78132Z" fill="currentColor"/>
            </svg>
            <!--navigation normal-->
            <router-link
              v-if="hasPermission(item)"
              :exact="item.exact"
              :to="{name: item.routeName}"
              class="nav-link"
              style="justify-content: left;">
              <div class="nav-contain-wrapper">
                <div class="contain-icon">
                  <icon-base :name="item.icon" v-if="item.customImage" width="20px" height="20px" class="item-icon svg-inline--fa" />
                  <font-awesome-icon :icon="item.icon" class="item-icon" width="20px" height="20px" v-else />
                </div>
                <span class="nav-link-title nav-mobile">
                  {{ $t(`${ item.title }`) }}
                </span>
              </div>
            </router-link>
            <ul v-if="item.dropdown" class="dropdown-item">
              <li v-for="dropdown in item.dropdown" :key="dropdown.id" class="nav-link-title nav-dropdown">
                <router-link v-if="hasPermission(dropdown)"
                             :exact="dropdown.exact"
                             :to="{name: dropdown.routeName}"
                             class="nav-link"
                             style="justify-content: left;">
                  {{ dropdown.title }}
                </router-link>
              </li>
            </ul>
          </li>
          <li id="user" @click="dropdownClick($event)" class="nav-item nav-user dropdown">
            <svg width="11" height="6" viewBox="0 0 11 6" xmlns="http://www.w3.org/2000/svg" class="icon-dropdown">
              <path d="M5.49999 3.78132L9.00624 0.481323L10.0078 1.42399L5.49999 5.66666L0.992157 1.42399L1.99374 0.481323L5.49999 3.78132Z" fill="currentColor"/>
            </svg>
            <!--navigation normal-->
            <div class="nav-link" style="justify-content: left;">
              <div class="nav-contain-wrapper">
                <div class="contain-icon" >
                  <font-awesome-icon icon="user-circle" class="item-icon"></font-awesome-icon>
                </div>
                <span class="name mb-0 d-flex flex-column nav-link-title">{{currentUser ? currentUser.name : ''}}<span class="mt-1">{{currentUser ? currentUser.roles[0].role_name : ''}}</span></span>
              </div>
            </div>
            <ul class="dropdown-item">
              <li class="nav-link-title nav-dropdown" >
                <router-link :to="{name: 'profile.index'}" class="nav-link">
                  Thông tin
                </router-link>
              </li>
              <li class="nav-link-title nav-dropdown" @click="logout()"> <div class="nav-link">Đăng xuất</div> </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="toggle d-flex justify-content-end" :class="[toggleItem ? toggle_mini : '']">
        <div class="toggle-btn" @click="handleClick">
          <font-awesome-icon icon="angle-right" class="item-icon arrow"/>
        </div>
      </div>
    </aside>
  </div>
</template>

<script>
import { navigations } from '@/config'
import store from '@/store'
import { isEmpty } from 'lodash-es'
import Admin from '@/models/Admin'
import * as types from '@/store/mutation-types'
import firebase from 'firebase/app'
import 'firebase/auth'
import IconBase from './IconBase.vue'

export default {
	name: 'Nav',
	components: {
		IconBase
	},
	computed: {
		permissions () {
			return store.getters.currentPermissions || []
		},
		currentUser () {
			if (store.getters.profile !== null) {
				return store.getters.profile.data.user
			}
		},
		getNav () {
			let nav = []
			this.navigations.forEach(item => {
				if (this.hasPermission(item)) { nav.push(item) }
			})
			return nav
		}
	},
	data () {
		return {
			navigations,
			navbar_mini: 'navbar-mini',
			toggle_mini: 'toggle-mini',
			toggle_dropdown: 'toggle-dropdown',
			toggleItem: true,
			toggleDropdown: false,
			isLogout: false,
			logo: `${process.env.API_URL}/storage/images/company_logo.png`,
      minimize_key: false,
		}
	},
	// mounted() {
	//   // console.log(this.navigations)
	// },
	methods: {
    minimize(){
      // console.log('dính')
      this.minimize_key = !this.minimize_key
    },
    isMobile() {
   if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
     return true
   } else {
     return false
   }
 },
		async logout () {
			if (!this.isLogout) {
				this.isLogout = true
				try {
					await Admin.logout()
					await firebase.auth().signOut()
				} catch (e) {}
				store.commit(types.LOG_OUT)
				this.isLogout = false
				await this.$router.push({name: 'login'})
			}
		},
		hasPermission (item) {
			if (item.denied && item.denied.includes(process.env.CLIENT_ENV)) { return false }
			let currentRoute
			if (item.routeName) {
				currentRoute = this.$router.resolve({ name: item.routeName }).route
			} else {
				for (const dropdown of item.dropdown) {
					currentRoute = this.$router.resolve({ name: dropdown.routeName }).route
				}
			}
			if (!isEmpty(currentRoute.meta.permissions)) {
				return !!this.permissions.find(permission => currentRoute.meta.permissions.includes(permission))
			}
			return false
		},
		handleClick () {
			this.toggleItem = !this.toggleItem
      // console.log('mở rộng thu gọn', this.toggleItem)
      store.commit(types.SET_NAV_EXP, this.toggleItem )
		},
		dropdownClick (e) {
			const element = e.target.closest('li.nav-item')
			const isSubMenu = e.target.parentElement.classList.contains('nav-dropdown');

			[].forEach.call(document.querySelectorAll('#navbar-menu li.nav-item'), function (el) {
				const countActiveSubMenu = el.querySelectorAll('.dropdown-item .router-link-exact-active').length
				if (countActiveSubMenu === 0 && element.id !== el.id) {
					el.classList.remove('isDropdown')
				}
			})

			if (!isSubMenu) {
				element.classList.toggle('isDropdown')
			}

			// if (e.target.nodeName === 'A') {
			// 	e.target.parentElement.classList.toggle('isDropdown')
			// } else if (e.target.nodeName === 'SPAN' || e.target.nodeName === 'DIV') {
			// 	e.target.offsetParent.offsetParent.classList.toggle('isDropdown')
			// } else if (e.target.nodeName === 'svg') {
			// 	e.target.parentElement.parentElement.parentElement.classList.toggle('isDropdown')
			// } else if (e.target.nodeName === 'path') {
			// 	e.target.parentElement.parentElement.parentElement.parentElement.classList.toggle('isDropdown')
			// }
		},
		removeDropdown () {
			for (const id of ['manage', 'category', 'user']) {
				const element = document.getElementById(id)
				if (element) element.classList.remove('isDropdown')
			}
		}
	}
}
</script>

<style lang="scss" scoped>
.contain-icon {
  text-align: center;
  .item-icon {
    font-size: 20px;
    margin-right: 15px;
    width: 1em;
  }
  .nav-hover{
    position: absolute;
    background: #0b0d10;
    left: 50px;
    z-index: 100;
    display: block !important;
  }
}
ul,li {
  color: rgba(53,64,82,.72);
  list-style: none;
}
.arrow {
  font-size: 20px;
}
.navbar {
  &-vertical {
    &.navbar-expand-lg {
      .navbar-collapse {
        .navbar-nav{
          margin: 0;
        }
      }
    }
  }
  &-collapse, &-nav{
    position: relative;
    display: flex;
    flex: 1;
    padding: 0;
    margin: 0;
    overflow-x: hidden;
    overflow-y: auto;
    list-style: none;
    width: 100% !important;
  }
  &-nav {
    .icon-dropdown {
      display: none;
    }
    .dropdown{
      position: relative;
      justify-content: flex-start;
      .icon-dropdown {
        transition: .3s;
        position: absolute;
        display: block;
        right: 14px;
        transform: rotate(90deg);
        top: 18px;
        color: rgba(53,64,82,.72);
      }
      .dropdown-item {
        .nav-link {
          padding: 7px 0 7px 35px !important;
        }
      }
    }
  }
  .nav {
    &-item {
      width: inherit;
      &.dropdown{
        position: relative;
        transition: background .3s ease-in-out;
      }
    }
    &-link {
      display: flex;
      flex: 1;
      align-items: center;
      padding: .8445rem 1rem;
      text-decoration: none;
      white-space: nowrap;
      transition: background .3s, color .3s;
    }
  }
}
.nav-user {
  display: none !important;
  @media (max-width: 1023px) {
    display: flex !important;
  }
}
.navbar-nav .nav-item.dropdown .nav-link.router-link-exact-active {
  color: rgba(53,64,82,0.72);
  background: transparent;
  &:hover{
    color: #FAA831 !important;
    background: rgba(224,224,224,0.1);
  }
}
.navbar-nav .nav-item.dropdown .nav-link.router-link-active {
  color: rgba(53,64,82,0.72) !important;
  background: transparent;
  &:hover{
    color: #FAA831 !important;
    background: rgba(224,224,224,0.1);
  }
}
.navbar-nav .nav-item.dropdown.isDropdown .nav-link.router-link-exact-active{
  color: #FAA831 !important;
  background: rgba(224,224,224,0.1);
}
.navbar-nav .nav-item .nav-link{
  width: 100%;
}
.navbar {
  user-select: none;
  -webkit-user-select: none;
  background: #F6F7FB;
  padding: 0;
  box-shadow: 3px 0 10px rgba(0, 0, 0, .16);
  transition: .3s !important;
  @media (max-width: 1023px) {
    width: 100vw !important;
  }
  // .icon-nav {
  //   margin-right: 15px;
  // }
  &-collapse{
    overflow-y: auto;
    overflow-x: hidden;
    margin-bottom: 44px;
    &::-webkit-scrollbar{
      display: none;
    }
    @media (max-width: 1023px) {
      display: none;
      &.show{
        display: block;
      }
    }
  }
  &-brand {
    padding: 17px 17px 50px 17px !important;
    @media (max-width: 1023px) {
      display: none !important;
    }
    &-image {
      width: 48px;
      height: auto;
    }
  }
  .logo-text{
    margin: 0;
  }
  .toggle{
    text-align: center;
    color: white;
    bottom: 0;
    left: 0;
    right: 0;
    position: absolute;
    width: 100%;
    transition: .3s;
    @media (max-width: 1023px) {
      display: none !important;
    }
    &-btn{
      cursor: pointer;
      display: flex;
      align-items: center;
      background: transparent;
      padding: 10px;
      transition: .2s;
      color: #FAA831;
      span{
        transition: .2s;
      }
      &:hover{
        color: rgb(186, 126, 22);
      }
    }
  }
  .toggle-mini{
    transition: .2s;
    padding: 0;
    width: 100%;
    @media (max-width: 1023px) {
      display: none!important;
    }
    .toggle-btn{
      transition: .2s;
      width: 100%;
    }
    .arrow {
      transition: .3s;
      transform: rotate(180deg);
    }
    span{
      transition: .3s;
      transform: rotate(180deg);
    }
  }
  &-nav {
    .nav-hover{
      display: none;
    }
    .nav-item {
      .nav-link {
        margin: 0.5rem 0;
        user-select: none;
        padding: 10px 14px;
        line-height: 1;
        transition: .3s;
        border-radius: 5px;
        &:hover{
          color: #FAA831 !important;
          background: rgba(224, 224, 224, 0.1);
        }
        &-title{
          font-weight: 600!important;
        }
        &.router-link-exact-active {
          color: #FAA831 !important;
          background: rgba(224, 224, 224, 0.1);
        }
        &.router-link-active {
          color: #FAA831 !important;
          background: rgba(224, 224, 224, 0.1);
          &+.dropdown-menu {
            opacity: 0.2;
            background: rgba(224, 224, 224, 0.1);
          }
        }
      }
      .dropdown-menu {
        li {
          .dropdown-item {
            color: rgba(255, 255, 255, .6);
            padding: 10.5px 1.25rem 10.5px 2.5rem;
            line-height: 1;
            position: relative;
            span {
              font-size: 12px;
            }
            &:hover {
              color: #ffffff;
              background: transparent;
            }
            &-active {
              color: #ffffff;
              &:before {
                content: '';
                position: absolute;
                width: 8px;
                height: 8px;
                background: #ffffff;
                border-radius: 50%;
                left: 20px;
                top: 50%;
                margin-top: -4px;
              }
            }
          }
        }
      }
      &-active {
        background: rgba(255, 255, 255, .15);
      }
      .dropdown-item {
        display: none;
      }
      &.isDropdown {
        .icon-dropdown {
          transition: .3s;
          transform: rotate(0deg);
          color: #FAA831;
        }
        .dropdown-item {
          display: block;
          &:hover {
            background: transparent;
          }
        }
      }
    }
  }
}
.navbar-mini {
  width: 4rem !important;
  .icon-dropdown {
    display: none !important;
  }
  @media (max-width: 1023px) {
    width: 100vw !important;
  }
  .navbar {
    &-nav, &-collapse {
      padding: 0;
      overflow: visible;
    }
    &-nav {
      .nav {
        &-item {
          .nav {
            &-link {
              .nav-link-title {
                display: none;
              }
              &:hover {
                border-radius: 0;
                width: 250px;
                background: #FAA831;
                color: #FFFFFF !important;
                .nav-link-title {
                  display: block
                }
              }
              @media (max-width: 1023px) {
                .nav-link-title {
                  display: block;
                  width: 100%;
                }
                &:hover {
                  width: 100%;
                }
              }
            }
          }
          &.dropdown {
            .nav {
              &-link {
                margin-top: 0;
                &:hover{
                  width: 250px;
                  background: #FAA831;
                  color: #FFFFFF !important;
                  .nav-link .nav-link-title {
                    display: block;
                  }
                  @media (max-width: 1023px) {
                    width: 100%;
                  }
                }
              }
            }
          }
          &.isDropdown {
            .dropdown {
              &-item {
                display: none;
              }
            }
            .nav {
              &-link {
                .nav-link-title {
                  display: none;
                  @media (max-width: 1023px) {
                    display: block;
                  }
                }
              }
            }
            &:hover {
              background: #F6F7FB;
              width: 250px;
              @media (max-width: 1023px) {
                width: 100%;
              }
              .dropdown {
                &-item {
                  width: 100%;
                  color: #FFFFFF !important;
                }
              }
              .nav-link {
                width: 100%;
              }
              .nav-link .nav-link-title, .dropdown-item {
                display: block
              }
            }

          }
        }
        &-link {
          &:hover {
            width: 312px;
            background: #FAA831;
            color: #FFFFFF !important;

            // .contain-icon {
            //   flex: 0 0 50px;
            // }
          }
        }
      }
    }
  }
  &.navbar-vertical.navbar-expand-lg .navbar-collapse .navbar-nav {
    margin: auto;
  }
  .contain-icon {
    text-align: center;
    .item-icon {
      font-size: 20px;
    }
    .nav-hover {
      position: absolute;
      background: #0b0d10;
      left: 50px;
      z-index: 100;
    }
  }
  .arrow {
    font-size: 20px;
    width: 50px !important;
    margin: auto !important;
  }
  .navbar-brand {
    justify-content: start !important;
    @media (max-width: 1023px) {
      justify-content: space-between !important;
    }
    &-image {
      width: 48px;
      height: auto;
      @media (max-width: 1023px) {
        height: 26px;
        margin-right: 10px;
      }
    }
  }
}
.navbar .navbar-toggler {
  color: white;
}
.navbar-vertical.navbar-expand-lg {
  overflow: visible !important;

  @media (max-width: 1024px) {
    justify-content: center;
  }
}
.toggle-dropdown {
  display: none;
}
.sidebar {
  width: 17.2rem;
  transition: .3s;
  @media (max-width: 1024px) {
    min-width: 15rem;
  }
  &-mini {
    transition: .3s;
  }
}

.navbar-nav .nav-item {
  justify-content: flex-start;
}

.sidebar-mini .navbar-nav .nav-link {
  padding: 0.6rem;
  .nav-link-title {
    margin-left: 1rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
  }
}

.sidebar-mini .navbar-nav .nav-item > .nav-link {
  justify-content: center;
}

.sidebar-mini .navbar-nav .nav-item:hover > .nav-link.router-link-exact-active {
  justify-content: flex-start;
}

.navbar-nav .nav-item .nav-link .nav-contain-wrapper {
  display: flex;
  align-items: center;
}

.sidebar-mini .nav-item .nav-link .nav-contain-wrapper {
  padding: 0.3rem;
}

.sidebar-mini .nav-item:not(.dropdown) .nav-link:not(:hover).router-link-exact-active .nav-contain-wrapper {
  padding: 0.6rem;
  background: #FF963D;
  box-shadow: 0px 1px 9px #C7CAD9;
  border-radius: 10px;
  .nav-mobile {
    color: white! important;
  }
}

.sidebar-mini .nav-item:not(.dropdown) .nav-link.router-link-exact-active .contain-icon {
  color: #F6F7FB;
}

.sidebar-mini .navbar-nav .nav-item.isDropdown .nav-link {
  width: 100%;
}

.sidebar-mini .nav-item .dropdown-item .nav-link,
.sidebar-mini .navbar-nav .nav-item .nav-link:hover {
  justify-content: flex-start;
}

.sidebar-mini .navbar-nav .nav-item.isDropdown a.router-link-exact-active {
  color: #0b0d10;
}

.sidebar-mini .contain-icon .item-icon {
  margin-right: unset;
}

</style>
