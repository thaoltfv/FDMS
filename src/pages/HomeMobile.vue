<template>
	<div class="background_news_mobile d-flex flex">
		<div class="row background_row d-flex" style="margin-top: 50px;">
			<div
				v-for="item of getNav"
				:key="item.id"
				@click="checkBeforeLink(item)"
				class="col-4 d-flex align-items-center"
				:id="item.id"
			>
				<!-- <router-link
					style="margin: 0 auto;"
					class="d-flex align-items-center justify-content-center"
					v-if="hasPermission(item)"
					:exact="item.exact"
					:to="{ name: item.routeName }"
				> -->
				<div
					style="margin: 0 auto;"
					class="d-flex align-items-center justify-content-center"
				>
					<div
						class="button-link d-flex align-items-center justify-content-center"
					>
						<div
							class="d-flex flex-column align-items-center justify-content-center"
						>
							<img
								v-if="item.image"
								:src="item.image"
								width="25px"
								height="25px"
								class="item-icon svg-inline--fa"
								style="margin: 0 auto;"
							/>
							<icon-base
								:name="item.icon"
								v-else-if="item.customImage"
								width="25px"
								height="25px"
								class="item-icon svg-inline--fa"
								style="margin: 0 auto;"
							/>
							<font-awesome-icon
								:icon="item.icon"
								class="item-icon"
								width="30px"
								height="30px"
								style="margin: 0 auto;"
								v-else
							/>
							<span
								v-if="item.id !== 'price_estimates'"
								class="text-center wrap-text"
								style="color:black; font-size: 0.7rem; padding-left: 5px; padding-right: 5px;"
							>
								{{ $t(`${item.title}`) }}
							</span>
							<span
								v-else
								class="text-center wrap-text"
								style="color:black; font-size: 0.7rem; padding-left: 7px; padding-right: 7px;"
							>
								{{ $t(`${item.title}`) }}
							</span>
						</div>
					</div>
				</div>
				<!-- </router-link> -->
			</div>
		</div>
		<div v-if="listDropDown.length > 0" class="btn-footer footer-custom">
			<!-- <div class="d-flex justify-content-end">
				<img
					height="25px"
					@click="listDropDown = []"
					class="cancel"
					src="@/assets/icons/ic_cancel_2.svg"
					alt="close"
				/>
			</div> -->
			<div class="mt-4 d-flex justify-content-center align-items-center">
				<router-link
					style="margin: 0 auto;"
					v-if="hasPermission(item)"
					:exact="item.exact"
					:to="{ name: item.routeName }"
					:key="item.id"
					v-for="item in listDropDown"
					>{{ item.title.toUpperCase() }}</router-link
				>
			</div>
		</div>
	</div>
</template>

<script>
import { navigationsMobile } from "@/config";
import store from "@/store";
import { isEmpty } from "lodash-es";
import Admin from "@/models/Admin";
import * as types from "@/store/mutation-types";
import firebase from "firebase/app";
import "firebase/auth";
import IconBase from "@/components/IconBase.vue";
export default {
	name: "Nav",
	components: {
		IconBase
	},
	computed: {
		permissions() {
			return store.getters.currentPermissions || [];
		},
		currentUser() {
			if (store.getters.profile !== null) {
				const profile = store.getters.profile.data.user;
				if (
					profile &&
					profile.roles &&
					(profile.roles[0].role_name.toUpperCase() === "KHÁCH" ||
						profile.roles[0].role_name.toUpperCase() === "KHÁCH HÀNG" ||
						profile.roles[0].role_name.toUpperCase() === "ĐỐI TÁC")
				) {
					this.isGuest = true;
					this.toggleItem = false;
					console.log("Vào đây");
				}
				return store.getters.profile.data.user;
			}
		},
		getNav() {
			let nav = [];
			this.navigationsMobile.forEach(item => {
				if (this.hasPermission(item)) {
					nav.push(item);
				}
			});
			nav.push({
				id: "users-cog",
				type: "item",
				icon: "users-cog",
				title: "Tài khoản",
				image: require("@/assets/icons/tai-khoan.png"),
				routeName: "profile.index",
				exact: true
			});
			return nav;
		}
	},
	data() {
		return {
			navigationsMobile,
			navbar_mini: "navbar-mini",
			toggle_mini: "toggle-mini",
			toggle_dropdown: "toggle-dropdown",
			toggleItem: true,
			// toggleDropdown: false,
			showDropdown: {},
			listDropDown: [],
			isLogout: false,
			logo: `${process.env.API_URL}/storage/images/company_logo.png`,
			minimize_key: false,
			isGuest: false
		};
	},
	// mounted() {
	//   // console.log(this.navigations)
	// },
	methods: {
		minimize() {
			// console.log('dính')
			this.minimize_key = !this.minimize_key;
		},
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
		async logout() {
			if (!this.isLogout) {
				this.isLogout = true;
				try {
					await Admin.logout();
					await firebase.auth().signOut();
				} catch (e) {}
				store.commit(types.LOG_OUT);
				this.isLogout = false;
				await this.$router.push({ name: "login" });
			}
		},
		hasPermission(item) {
			if (item.denied && item.denied.includes(process.env.CLIENT_ENV)) {
				return false;
			}
			let currentRoute;
			if (item.routeName) {
				currentRoute = this.$router.resolve({ name: item.routeName }).route;
			} else {
				for (const dropdown of item.dropdown) {
					currentRoute = this.$router.resolve({ name: dropdown.routeName })
						.route;
				}
			}
			if (!isEmpty(currentRoute.meta.permissions)) {
				return !!this.permissions.find(permission =>
					currentRoute.meta.permissions.includes(permission)
				);
			}
			return false;
		},
		handleClick() {
			this.toggleItem = !this.toggleItem;
			// console.log('mở rộng thu gọn', this.toggleItem)
			store.commit(types.SET_NAV_EXP, this.toggleItem);
		},
		checkBeforeLink(item) {
			this.listDropDown = [];
			// const unSupport = [
			// 	"appraise",
			// 	"manage",
			// 	"category",
			// 	"customer",
			// 	"menu_certification"
			// ];
			const unSupport = ["price_estimates", "menu_certification"];
			if (unSupport.includes(item.id)) {
				this.$toast.open({
					message:
						"Phiên bản Mobile hiện không hỗ trợ chức năng này, vui lòng sử dụng phiên bản Desktop để tiếp tục.",
					type: "warning",
					position: "top-right",
					duration: 3000
				});
				return;
			}
			if (item.dropdown) {
				this.listDropDown = item.dropdown;
			} else {
				this.$router.push({ name: item.routeName });
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.footer-custom {
	border-top-left-radius: 20px;
	border-top-right-radius: 20px;
	height: 80px;
	box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
	transform: translateY(100%);
	opacity: 0;
	animation: slideUp 0.3s ease-in-out forwards;
}
@keyframes slideUp {
	0% {
		transform: translateY(100%);
		opacity: 0;
	}
	100% {
		transform: translateY(0);
		opacity: 1;
	}
}
.button-link {
	width: 60px;
	height: 60px;
	border-radius: 20%;
	border: white;
	border-style: solid;
	background: white;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.45);
}
.background_row {
	padding-left: 10%;
	padding-right: 10%;
	margin: 0 auto;
	width: 100% !important;
	background: rgba(255, 255, 255, 0.95);
}
.background_news_mobile {
	margin-top: -50px;
	height: 90dvh;
	width: 100% !important;
	background-repeat: repeat-y;
	background-size: 100%;
	background-image: url(https://firebasestorage.googleapis.com/v0/b/fast-value.appspot.com/o/assets%2Fbackground-mobile-fastvalue.png?alt=media&token=b4d62121-9971-4d8f-bbba-20871b809821);
}
.item-icon {
	font-size: 20px;
	margin-right: 15px;
	width: 1em;
}
.contain-icon {
	text-align: center;
	.item-icon {
		font-size: 20px;
		margin-right: 15px;
		width: 1em;
	}
	.nav-hover {
		position: absolute;
		background: #0b0d10;
		left: 50px;
		z-index: 100;
		display: block !important;
	}
}
ul,
li {
	color: rgba(53, 64, 82, 0.72);
	list-style: none;
}
.arrow {
	font-size: 20px;
}
.navbar {
	&-vertical {
		&.navbar-expand-lg {
			.navbar-collapse {
				.navbar-nav {
					margin: 0;
				}
			}
		}
	}
	&-collapse,
	&-nav {
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
		.dropdown {
			position: relative;
			justify-content: flex-start;
			.icon-dropdown {
				transition: 0.3s;
				position: absolute;
				display: block;
				right: 14px;
				transform: rotate(90deg);
				top: 18px;
				color: rgba(53, 64, 82, 0.72);
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
			&.dropdown {
				position: relative;
				transition: background 0.3s ease-in-out;
			}
		}
		&-link {
			display: flex;
			flex: 1;
			align-items: center;
			padding: 0.8445rem 1rem;
			text-decoration: none;
			white-space: nowrap;
			transition: background 0.3s, color 0.3s;
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
	color: rgba(53, 64, 82, 0.72);
	background: transparent;
	&:hover {
		color: #faa831 !important;
		background: rgba(224, 224, 224, 0.1);
	}
}
.navbar-nav .nav-item.dropdown .nav-link.router-link-active {
	color: rgba(53, 64, 82, 0.72) !important;
	background: transparent;
	&:hover {
		color: #faa831 !important;
		background: rgba(224, 224, 224, 0.1);
	}
}
.navbar-nav .nav-item.dropdown.isDropdown .nav-link.router-link-exact-active {
	color: #faa831 !important;
	background: rgba(224, 224, 224, 0.1);
}
.navbar-nav .nav-item .nav-link {
	width: 100%;
}
.navbar {
	user-select: none;
	-webkit-user-select: none;
	background: #f6f7fb;
	padding: 0;
	box-shadow: 3px 0 10px rgba(0, 0, 0, 0.16);
	transition: 0.3s !important;
	@media (max-width: 1023px) {
		width: 100vw !important;
	}
	// .icon-nav {
	//   margin-right: 15px;
	// }
	&-collapse {
		overflow-y: auto;
		overflow-x: hidden;
		margin-bottom: 44px;
		&::-webkit-scrollbar {
			display: none;
		}
		@media (max-width: 1023px) {
			display: none;
			&.show {
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
	.logo-text {
		margin: 0;
	}
	.toggle {
		text-align: center;
		color: white;
		bottom: 0;
		left: 0;
		right: 0;
		position: absolute;
		width: 100%;
		transition: 0.3s;
		@media (max-width: 1023px) {
			display: none !important;
		}
		&-btn {
			cursor: pointer;
			display: flex;
			align-items: center;
			background: transparent;
			padding: 10px;
			transition: 0.2s;
			color: #faa831;
			span {
				transition: 0.2s;
			}
			&:hover {
				color: rgb(186, 126, 22);
			}
		}
	}
	.toggle-mini {
		transition: 0.2s;
		padding: 0;
		width: 100%;
		@media (max-width: 1023px) {
			display: none !important;
		}
		.toggle-btn {
			transition: 0.2s;
			width: 100%;
		}
		.arrow {
			transition: 0.3s;
			transform: rotate(180deg);
		}
		span {
			transition: 0.3s;
			transform: rotate(180deg);
		}
	}
	&-nav {
		.nav-hover {
			display: none;
		}
		.nav-item {
			.nav-link {
				margin: 0.5rem 0;
				user-select: none;
				padding: 10px 14px;
				line-height: 1;
				transition: 0.3s;
				border-radius: 5px;
				&:hover {
					color: #faa831 !important;
					background: rgba(224, 224, 224, 0.1);
				}
				&-title {
					font-weight: 600 !important;
				}
				&.router-link-exact-active {
					color: #faa831 !important;
					background: rgba(224, 224, 224, 0.1);
				}
				&.router-link-active {
					color: #faa831 !important;
					background: rgba(224, 224, 224, 0.1);
					& + .dropdown-menu {
						opacity: 0.2;
						background: rgba(224, 224, 224, 0.1);
					}
				}
			}
			.dropdown-menu {
				li {
					.dropdown-item {
						color: rgba(255, 255, 255, 0.6);
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
								content: "";
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
				background: rgba(255, 255, 255, 0.15);
			}
			.dropdown-item {
				display: none;
			}
			&.isDropdown {
				.icon-dropdown {
					transition: 0.3s;
					transform: rotate(0deg);
					color: #faa831;
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
		&-nav,
		&-collapse {
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
								background: #faa831;
								color: #ffffff !important;
								.nav-link-title {
									display: block;
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
								&:hover {
									width: 250px;
									background: #faa831;
									color: #ffffff !important;
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
							background: #f6f7fb;
							width: 250px;
							@media (max-width: 1023px) {
								width: 100%;
							}
							.dropdown {
								&-item {
									width: 100%;
									color: #ffffff !important;
								}
							}
							.nav-link {
								width: 100%;
							}
							.nav-link .nav-link-title,
							.dropdown-item {
								display: block;
							}
						}
					}
				}
				&-link {
					&:hover {
						width: 312px;
						background: #faa831;
						color: #ffffff !important;

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
	transition: 0.3s;
	@media (max-width: 1024px) {
		min-width: 15rem;
	}
	&-mini {
		transition: 0.3s;
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

.sidebar-mini
	.nav-item:not(.dropdown)
	.nav-link:not(:hover).router-link-exact-active
	.nav-contain-wrapper {
	padding: 0.6rem;
	background: #ff963d;
	box-shadow: 0px 1px 9px #c7cad9;
	border-radius: 10px;
	.nav-mobile {
		color: white !important;
	}
}

.sidebar-mini
	.nav-item:not(.dropdown)
	.nav-link.router-link-exact-active
	.contain-icon {
	color: #f6f7fb;
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
