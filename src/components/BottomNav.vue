<template>
	<div class="btn-container_foreground">
		<div class="btn-container row" style="margin: 0;">
			<div
				v-for="(button, index) in options"
				:key="`label-${index}`"
				:class="{
					[`btn-item-${index} labels btn-item`]: true,
					['checked']: button.isActive,
					['unchecked']: !button.isActive
				}"
				class="col-3"
				@click="handleLabelClick(button)"
			>
				<div class="active-label">
					<div v-if="button.badge" class="btn-badge">
						{{ button.badge }}
					</div>
					<slot name="icon" :props="button">
						<icon-base
							:name="button.icon"
							v-if="button.customImage"
							width="20px"
							height="20px"
							class="item-icon svg-inline--fa"
						/>
						<font-awesome-icon
							:icon="button.icon"
							class="item-icon"
							width="20px"
							height="20px"
							v-else
						/>
					</slot>
				</div>

				<div class="btn-title">
					<slot name="title" :props="button">
						{{ button.title }}
					</slot>
				</div>

				<div
					v-if="hasChild(button) && onlyClick == true"
					:class="{
						['btn-super-parent']: button.isActive,
						['btn-class-showable']: showable
					}"
				>
					<div class="btn-child-parent">
						<div
							v-for="(child, idx) in button.childs || []"
							:key="idx"
							class="btn-child"
							@click.stop="handleChildClick(child)"
						>
							<slot name="child-icon" :props="child">
								<icon-base
									:name="child.icon"
									v-if="child.customImage"
									width="20px"
									height="20px"
									class="item-icon svg-inline--fa"
								/>
								<font-awesome-icon
									:icon="child.icon"
									class="item-icon"
									width="20px"
									height="20px"
									v-else
								/>
							</slot>

							<span class="btn-child-title">
								<slot name="child-title" :props="child">
									{{ child.title }}
								</slot>
							</span>

							<div v-if="child.badge" class="btn-child-badge">
								{{ child.badge }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div v-show="hasActiveClass" id="sweep" style="padding: 0;">
				<div id="sweep-right" />
				<div id="sweep-center" />
				<div id="sweep-left" />
			</div>
		</div>
	</div>
</template>

<script>
import store from "@/store";
import "firebase/auth";
import IconBase from "./IconBase.vue";
import { forEach } from "lodash-es";

export default {
	name: "BottomNav",
	props: {
		modelValue: {
			type: [Number, String],
			default: 1
		},
		options: [],
		foregroundColor: String,
		backgroundColor: String,
		iconColor: String,
		badgeColor: String,
		replaceRoute: Boolean
	},
	components: {
		IconBase
	},
	computed: {
		permissions() {
			return store.getters.currentPermissions || [];
		},
		currentUser() {
			if (store.getters.profile !== null) {
				return store.getters.profile.data.user;
			}
		},
		hasActiveClass() {
			return this.options.find(option => option.isActive);
		}
	},
	data() {
		return {
			showable: false,
			enableWatch: true,
			onlyClick: false
		};
	},
	mounted() {
		this.cssLoader();
		// // console.log('---------this.$route', this.$route)
		// window.addEventListener('resize', this.onResize());

		// // console.log('------this.options', this.options)
		setTimeout(() => {
			// console.log('---------this.$route', this.$route),
			this.options.forEach(e => {
				if (e.path && e.path.name === this.$route.name) {
					// console.log('vô 1')
					e.isActive = true;
				}
				if (e.childs && e.childs.length > 0) {
					// console.log('vô 2')
					e.childs.forEach(i => {
						// console.log('vô 3')
						if (i.path && i.path.name === this.$route.name) {
							// console.log('vô 4')
							e.isActive = true;
						}
						// TSTĐ
						if (
							i.path &&
							i.path.name === "certification_asset.index" &&
							(this.$route.name === "certification_asset.create" ||
								this.$route.name === "certification_asset.detail" ||
								this.$route.name === "certification_asset.edit" ||
								this.$route.name === "certification_asset.apartment.create" ||
								this.$route.name === "certification_asset.apartment.detail" ||
								this.$route.name === "certification_asset.apartment.edit")
						) {
							// console.log('vô 5')
							e.isActive = true;
						}
						if (
							i.path &&
							i.path.name === "certification_personal_property.index" &&
							(this.$route.name ===
								"certification_asset.other_purpose.create" ||
								this.$route.name ===
									"certification_asset.other_purpose.detail" ||
								this.$route.name === "certification_asset.other_purpose.edit" ||
								this.$route.name === "certification_asset.vehicle.create" ||
								this.$route.name === "certification_asset.vehicle.detail" ||
								this.$route.name === "certification_asset.vehicle.edit" ||
								this.$route.name === "certification_asset.machine.create" ||
								this.$route.name === "certification_asset.machine.detail" ||
								this.$route.name === "certification_asset.machine.edit")
						) {
							// console.log('vô 6')
							e.isActive = true;
						}
					});
				}
				// HSTĐ
				if (
					e.path &&
					e.path.name === "certification_brief.index" &&
					(this.$route.name === "certification_brief.create" ||
						this.$route.name === "certification_brief.detail" ||
						this.$route.name === "certification_brief.edit")
				) {
					// console.log('vô 7')
					e.isActive = true;
				}

				// TSSS
				if (
					e.path &&
					e.path.name === "warehouse.index" &&
					(this.$route.name === "warehouse.create" ||
						this.$route.name === "warehouse.detail" ||
						this.$route.name === "warehouse.edit")
				) {
					// console.log('vô 8')
					e.isActive = true;
				}

				if (e.path && e.path.name === "menu" && this.$route.name === "menu") {
					// console.log('vô 8')
					e.isActive = true;
				}
			});
		}, 3000);
	},
	watch: {
		"this.options": {
			handler: function(newVal, oldVal) {
				if (newVal) {
					this.options = newVal.map(option => ({
						...option,
						isActive: this.isActive(option)
					}));
					console.log(this.options);
					this.cssLoader();
				}
			},
			deep: true
		},
		"this.modelValue": {
			handler: function(newVal, oldVal) {
				if (newVal != oldVal && this.enableWatch) {
					const childs = [];
					this.options.forEach(option => {
						if (this.hasChild(option) && option.childs) {
							childs.push(...option.childs);
						}
					});
					const target = [...this.options, ...childs].find(
						option => option.id == newVal
					);
					if (target) {
						this.updateValue(target, this.hasChild(target));
					}
				}
			},
			deep: true,
			immediate: true
		},
		"this.$route": {
			handler: function(route, newRoute) {
				// console.log('đasa', route, newRoute)
				if (newRoute) {
					nextTick(() => {
						const childs = [];
						this.options.forEach(option => {
							if (this.hasChild(option) && option.childs) {
								childs.push(...option.childs);
							}
						});
						const target = [...this.options, ...childs]
							.filter(item => item.path)
							.find(option => {
								if (typeof option.path === "string") {
									return option.path === newRoute.path;
								} else {
									return (option.path || {}).name === newRoute.name;
								}
							});
						if (target) {
							this.updateValue(target, this.hasChild(target));
						}
					});
				}
			},
			immediate: true
		}
	},
	methods: {
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
		hasChild(button) {
			return Boolean((button.childs || []).length);
		},
		handleChildClick(button) {
			this.onlyClick = false;
			this.updateValue(button);
			this.toggleClass();
		},
		handleLabelClick(button) {
			if (!this.showable || button.isActive) {
				this.toggleClass();
				this.onlyClick = true;
			}

			this.updateValue(button, this.hasChild(button));
		},
		updateValue(button, prevent = false) {
			this.options.forEach(
				option => (option.isActive = this.isActive(option, button.id))
			);

			if (!prevent) {
				// $emit('update:modelValue', button.id);
				this.enableWatch = false;
				setTimeout(() => {
					this.enableWatch = true;
				}, 0);

				if (button.path && Object.keys(button.path).length) {
					if (this.replaceRoute) {
						this.$router.replace(button.path).catch(() => {});
					} else {
						this.$router.push(button.path);
					}
				}
			}
		},
		isActive(button, value = props.modelValue) {
			return (
				button.id == value ||
				Boolean((button.childs || []).find(child => child.id == value))
			);
		},
		toggleClass() {
			this.showable = !this.showable;
		},
		cssLoader() {
			let customStyle = "";
			const containerWidth =
				document.querySelector(".btn-container").offsetWidth ||
				window.innerWidth;

			this.options.forEach((item, index) => {
				const translateX = ((item.childs || []).length * 45) / 2 - 35;
				const endsClassName = `.btn-item-${index}.checked .btn-class-showable .btn-child-parent`;
				if ((item.childs || []).length > 1) {
					if (index === 0 && hasChild(item)) {
						customStyle += `${endsClassName}{transform:translateX(${translateX}px)}`;
					}

					if (index === this.options.length - 1 && hasChild(item)) {
						customStyle += `${endsClassName}{transform:translateX(-${translateX}px)}`;
					}
				}

				const itemWidth = containerWidth / this.options.length;
				customStyle += `.btn-item-${index}{width:${itemWidth}px !important;}`;

				const sweepTranslateX =
					(index * containerWidth) / this.options.length +
					containerWidth / this.options.length / 4;
				customStyle += `.btn-item-${index}.checked ~ #sweep{transform:translateX(${sweepTranslateX}px)}`;

				if (this.hasChild(item)) {
					(item.childs || []).forEach((child, idx) => {
						customStyle += `.btn-item-${index}.checked .btn-class-showable .btn-child:nth-child(${idx +
							1}){transform:translateX(${(0.5 + idx) * 45 -
							((item.childs || []).length * 45) / 2}px)}`;
					});
				}
			});

			document.getElementById("sweep").style.left = `
          ${containerWidth / this.options.length / 4 - 135 / 2}px`;

			const head = document.getElementsByTagName("head")[0];
			const style = document.createElement("style");
			style.id = "bottom-navigation-style";

			if (style.styleSheet) {
				style.styleSheet.cssText = customStyle;
			} else {
				style.appendChild(document.createTextNode(customStyle));
			}

			head.appendChild(style);
		},
		onResize() {
			nextTick(() => {
				const styleElement = document.getElementById("bottom-navigation-style");
				styleElement && styleElement.remove();
			});

			this.cssLoader();
		}
	}
};
</script>

<style lang="scss" scoped>
.btn-super-parent {
	display: flex;
	justify-content: center;
	align-items: center;
	position: absolute;
	bottom: 55px;
	width: 135px;
	height: 60px;
	z-index: -1;
}

input {
	display: none;
}

.btn-container_foreground {
	position: fixed;
	direction: ltr;
	display: flex;
	align-items: flex-end;
	bottom: 0;
	left: 0;
	width: 100%;
	z-index: 2147483647;
	height: 60px;
	background: #42a5f5;
}

.btn-container {
	direction: ltr;
	display: flex;
	justify-content: space-around;
	background-color: #ffffff;
	width: 100%;
	height: 55px;
}

.active-label {
	width: 35px;
	height: 35px;
	border-radius: 40%;
	display: flex;
	justify-content: center;
	align-items: center;
	transition: all 300ms ease;
	position: absolute;
	top: 10px;
	background: #ffffff !important;
	color: #0000008a;
}

.btn-title {
	position: absolute;
	color: #0000008a;
	font-size: 10px;
	line-height: 1.15 !important;
}

.btn-badge {
	width: 18px;
	height: 18px;
	display: flex;
	align-items: center;
	justify-content: center;
	position: absolute;
	top: 0px;
	left: 25px;
	border-radius: 50%;
	font-size: 12px;
	color: #fff;
	background: #fbc02d;
}

.checked .active-label {
	transform: translateY(-10px);
}

.checked .btn-title {
	animation: fadein 200ms;
	position: absolute;
	top: 40px;
}

.unchecked .active-label {
	background: transparent;
}

.unchecked .btn-title {
	visibility: hidden;
}

#sweep {
	height: 100%;
	width: 135px;
	display: flex;
	position: absolute;
	left: 0;
	top: 5px;
}

#sweep-center {
	height: 38px;
	display: flex;
	flex: 1;
	background: #42a5f5;
	border-radius: 0 0 45% 45%;
}

#sweep-left {
	height: 33px;
	width: 45px;
	overflow: hidden;
	position: relative;
	right: 2px;
}

#sweep-left:before {
	content: "";
	display: block;
	width: 220%;
	height: 200%;
	position: absolute;
	border-radius: 50%;
	top: 0;
	left: 0;
	box-shadow: -40px -40px 0 0 #42a5f5;
}

#sweep-right {
	height: 33px;
	width: 45px;
	overflow: hidden;
	position: relative;
	left: 2px;
}

#sweep-right:before {
	content: "";
	display: block;
	width: 220%;
	height: 200%;
	position: absolute;
	border-radius: 50%;
	top: 0;
	right: 0;
	box-shadow: 40px -40px 0 0 #42a5f5;
}

@media screen and (min-width: 576px) {
	.labels {
		cursor: pointer;
	}
}

@keyframes fadein {
	from {
		opacity: 0;
		transform: translateY(20px);
	}
	to {
		opacity: 1;
		transform: translateY(0px);
	}
}

/* child */

.btn-child-badge {
	width: 18px;
	height: 18px;
	display: flex;
	align-items: center;
	justify-content: center;
	position: absolute;
	top: -4px;
	left: 20px;
	border-radius: 50%;
	font-size: 12px;
	color: #fff;
	background: #fbc02d;
	opacity: 0;
}

.btn-child-parent {
	position: absolute;
	bottom: -35px;
	width: 35px;
	height: 35px;
	border-radius: 100px;
	display: flex;
	justify-content: center;
	align-items: center;
	background: #42a5f5;
}

.btn-child {
	position: absolute;
	height: 30px;
	width: 30px;
	border-radius: 50%;
	background: #ffffff;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	color: #0000008a;
}

.btn-child-title {
	font-size: 10px;
	opacity: 0;
	position: absolute;
	top: 37px;
	line-height: 1.15 !important;
}

.unchecked .btn-child-parent {
	background: transparent;
}

.checked .btn-class-showable .btn-child-parent {
	animation: child-background 500ms ease-in-out forwards;
}

.checked .btn-class-showable .btn-child-title {
	animation: child-title 500ms ease-in-out forwards;
}

.checked .btn-class-showable .btn-child-badge {
	animation: child-title 500ms ease-in-out forwards;
}

@keyframes child-title {
	50% {
		opacity: 0;
	}
	100% {
		opacity: 1;
	}
}

@keyframes child-background {
	0% {
		bottom: -30px;
		background: transparent;
	}

	25% {
		bottom: 20px;
		width: 35px;
		height: 35px;
	}

	40% {
		bottom: 20px;
		width: 35px;
		height: 40px;
	}

	100% {
		bottom: 20px;
		width: 100%;
		height: 40px;
	}
}

/* shared */
.btn-item {
	padding: 10px;
	transition: transform 100ms ease;
	display: flex;
	justify-content: center;
	align-items: center;
	position: relative;
	z-index: 10;
}

.btn-item.checked .btn-class-showable .btn-child-parent {
	transition: transform 500ms ease 300ms;
}

.btn-item.checked ~ #sweep {
	transition: transform 500ms ease;
}

.btn-item.checked .btn-class-showable .btn-child {
	transition: transform 500ms ease 300ms;
}
</style>
