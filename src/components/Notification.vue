<template>
	<a-popover v-model="visibleNotification" trigger="click">
		<template slot="title">
			<div class="popover-notification-title">
				Thông báo
				<a @click="markAllAsRead" class="text-secondary"
					><small>Đánh dấu tất cả đã đọc</small></a
				>
			</div>
		</template>
		<div slot="content">
			<div class="popover-notification">
				<b-list-group @scroll="onScroll">
					<b-list-group-item
						v-for="item in notificationShow"
						:key="item.id"
						:to="getLink(item.data)"
						class="border-0"
					>
						<div class="d-flex w-100">
							<div class="user-img">
								<img
									class="avatar"
									v-if="
										item.data.user &&
											item.data.user.image !== '' &&
											item.data.user.image !== undefined &&
											item.data.user.image !== null
									"
									:src="item.data.user.image"
									alt="image"
								/>
								<font-awesome-icon v-else icon="user-circle" class="avatar" />
							</div>
							<div class="flex-fill">
								<span
									:class="
										item.read_at !== null ? 'text-secondary' : 'text-black'
									"
									>{{ item.data.message }}</span
								>
								<small class="d-block">{{ formatDate(item.created_at) }}</small>
							</div>
						</div>
					</b-list-group-item>
				</b-list-group>
			</div>
			<div class="load-more border-0 text-sm-center">
				<a
					@click="handleNext"
					:class="
						notificationShow.length !== notifications.length
							? ' text-blue'
							: ' text-secondary'
					"
				>
					{{
						notificationShow.length !== notifications.length
							? "Xem thêm"
							: "Đã xem hết"
					}}
				</a>
			</div>
		</div>
		<a-badge :key="notiCount" :count="unreadNotificationCountCompute">
			<font-awesome-icon
				@click="handleGetNotifications"
				class="fa-lg"
				icon="bell"
			/>
		</a-badge>
	</a-popover>
</template>

<script>
import Notification from "@/models/Notification";
import moment from "moment";
import store from "@/store";
import { BListGroup, BListGroupItem } from "bootstrap-vue";
import { Badge } from "ant-design-vue";
import { SET_UNREAD_NOTIFICATION } from "@/store/mutation-types";

import { storeToRefs } from "pinia";
import { useWorkFlowConfig } from "@/store/workFlowConfig";
export default {
	name: "Notification",
	components: {
		"b-list-group": BListGroup,
		"b-list-group-item": BListGroupItem,
		"a-badge": Badge
	},
	data() {
		return {
			visibleNotification: false,
			notifications: [],
			notificationShow: [],
			limit: 10,
			intervalId: null,
			channel: null
		};
	},
	setup() {
		const workFlowConfig = useWorkFlowConfig();
		workFlowConfig.setNoti(store.getters.unreadNotification);
		const { notiCount } = storeToRefs(workFlowConfig);
		console.log("notiCount", notiCount.value, store.getters.unreadNotification);
		return { notiCount, workFlowConfig };
	},
	watch: {
		notiCount: {
			handler(newValue) {
				localStorage.setItem("unreadNotifications", newValue);
			},
			immediate: true
		}
	},
	computed: {
		currentUser() {
			if (store.getters.profile !== null) {
				return store.getters.profile.data.user;
			}
		},
		unreadNotificationCountCompute() {
			console.log("notiCount2", this.notiCount);
			return this.notiCount;
		}
	},

	created() {},

	mounted() {
		if (document.visibilityState === "visible") {
			this.startInterval();
		}
		document.addEventListener("visibilitychange", this.handleVisibilityChange);
	},
	beforeDestroy() {
		this.stopInterval();
		document.removeEventListener(
			"visibilitychange",
			this.handleVisibilityChange
		);
	},
	methods: {
		handleVisibilityChange() {
			if (document.visibilityState === "visible") {
				this.startInterval();
			} else {
				this.stopInterval();
			}
		},
		startInterval() {
			if (this.intervalId) {
				clearInterval(this.intervalId);
			}
			this.getNoti();
			this.intervalId = setInterval(this.getNoti, 90000);
		},
		stopInterval() {
			if (this.intervalId) {
				clearInterval(this.intervalId);
				this.intervalId = null;
			}
		},
		async getNoti() {
			const profile = await Notification.getUnreadCount(this.currentUser.id);
			// store.commit(SET_UNREAD_NOTIFICATION, profile.data.unreadNotifications);
			this.notiCount = profile.data.unreadNotifications;
			this.workFlowConfig.setNoti(profile.data.unreadNotifications);
			// console.log(
			// 	"unreadNotifications",
			// 	profile.data.unreadNotifications,
			// 	store.getters.unreadNotification,
			// 	this.notiCount
			// );
		},
		formatDate(value) {
			return moment(String(value)).format("hh:mm DD/MM/YYYY");
		},
		async handleGetNotifications() {
			console.log("this.noti", this.notiCount);
			try {
				const resp = await Notification.getAll(this.currentUser.id);
				this.notifications = resp.data.notifications;
				this.notificationShow = [];
				this.handleNext();
			} catch (err) {
				// console.log(err)
				throw err;
			}
		},
		handleNext() {
			this.notificationShow = this.notifications.slice(
				0,
				this.limit + this.notificationShow.length
			);
			const ids = this.notificationShow
				.filter(item => item.read_at == null)
				.map(item => item.id);
			if (ids.length > 0) {
				this.notifications
					.filter(item => ids.includes(item.id))
					.map(item => (item.read_at = true));
				this.markAsRead(ids);
			}
		},
		markAsRead(ids) {
			try {
				Notification.markAsRead({ read: ids }).then(resp =>
					store.commit(SET_UNREAD_NOTIFICATION, resp.data.unread)
				);
			} catch (err) {
				// console.log(err)
				throw err;
			}
		},
		markAllAsRead() {
			try {
				Notification.markAllAsRead().then(resp =>
					store.commit(SET_UNREAD_NOTIFICATION, 0)
				);
			} catch (err) {
				// console.log(err)
				throw err;
			}
		},
		onScroll(e) {
			const { scrollTop, offsetHeight, scrollHeight } = e.target;
			if (scrollTop + offsetHeight >= scrollHeight) {
				this.handleNext();
			}
		},
		getLink(data) {
			let routeName =
				data.message && data.message.includes("HSTD")
					? "certification_brief"
					: "pre_certification";

			if (data.id) {
				return {
					name: `${routeName}.detail`,
					query: {
						id: data.id
					}
				};
			} else {
				return {
					name: `${routeName}.index`
				};
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.popover-notification {
	max-width: 375px;
	height: calc(100dvh - 150px);

	.list-group {
		height: 100%;
		width: 100%;
		display: flex;
		flex-direction: column;
		overflow: auto;
	}
}

.ant-badge {
	font-size: inherit;
}

.avatar {
	width: 2rem;
}

.user-img {
	min-width: 3rem;
}

.load-more {
	padding: 10px;
}

.popover-notification-title {
	display: flex;
	justify-content: space-between;
}
</style>

<style lang="scss">
.ant-popover-inner-content {
	padding: 0;
}
</style>
