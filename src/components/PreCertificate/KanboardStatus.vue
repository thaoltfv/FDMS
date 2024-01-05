<template>
	<div class="d-flex scroll_board" id="infinite-list">
		<div
			class="p-2 container_kanban mr-3"
			:class="`border-${config.css.color}`"
			v-for="config in config_status"
			:key="config.id"
		>
			<div class="p-2 mt-3 ml-2 mb-2 d-flex justify-content-between">
				<h4 class="mr-3 title" :class="`text-${config.css.color}`">
					{{ config.description }}
				</h4>
				<div class="quatity text-white" :class="`bg-${config.css.color}`">
					{{
						subStatusDataTmp[config.id] ? subStatusDataTmp[config.id].length : 0
					}}
				</div>
			</div>
			<draggable
				:key="countData"
				:group="{ name: config.id, put: config.target }"
				:animation="200"
				:list="subStatusData[config.id]"
				:move="handleMoveDraft"
				ghost-class="ghost"
				class="list-group kanban-column"
			>
				<b-card
					:class="{ border_expired: checkDateExpired(element) }"
					v-for="element in subStatusData[config.id]"
					:key="element.id"
				>
					<div class="col-12 d-flex mb-2 justify-content-between">
						<span
							@click="handleDetailCertificate(element.id)"
							class="content_id"
							:class="`bg-${config.css.color}-15 text-${config.css.color}`"
							>{{ element.slug }}</span
						>
						<img
							v-if="checkDateExpired(element)"
							class="mr-2 icon_expired"
							src="@/assets/icons/ic_expire_calender.svg"
							alt="ic_expire_calender"
						/>
					</div>
					<div class="property-content mb-2 d-flex color_content">
						<div class="label_container d-flex">
							<img
								style="min-width:15px"
								width="15px"
								height="21px"
								class="mr-2"
								src="@/assets/icons/ic_user_2.svg"
								alt="user"
							/>
							<div class="d-flex">
								<span style="font-weight: 500"
									><strong class="d-none d_inline mr-1">Khách hàng:</strong
									>{{ element.petitioner_name }}</span
								>
							</div>
						</div>
					</div>
					<div class="property-content mb-2 d-flex color_content">
						<img class="mr-2" src="@/assets/icons/ic_price.svg" alt="user" />
						<div class="label_container d-flex">
							<strong class="d-none d_inline mr-1">Tổng giá trị:</strong
							><span style="font-weight: 500">{{
								element.total_preliminary_value
									? `${formatPrice(element.total_preliminary_value)}`
									: "-"
							}}</span>
						</div>
					</div>
					<div class="property-content mb-2 d-flex color_content">
						<img class="mr-2" src="@/assets/icons/ic_clock.svg" alt="user" />
						<div class="label_container d-flex">
							<strong class="d-none d_inline mr-1">Thời hạn:</strong
							><span style="font-weight: 500">{{
								element.status_expired_at
									? updateDate(element.status_expired_at, new Date())
									: "Đã hết hạn"
							}}</span>
						</div>
					</div>
					<div class="property-content d-flex justify-content-between mb-0">
						<div class="label_container d-flex">
							<img
								width="15px"
								class="mr-2"
								src="@/assets/icons/ic_taglink.svg"
								alt="user"
							/><span style="color:#8B94A3">{{ element.document_count }}</span>
						</div>
						<img
							class="img_user"
							:src="
								element.image
									? element.image
									: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
							"
						/>
					</div>
				</b-card>
			</draggable>
		</div>
	</div>
</template>

<script>
import { FormWizard, TabContent } from "vue-form-wizard";
import {
	UserIcon,
	DollarSignIcon,
	HomeIcon,
	ClockIcon
} from "vue-feather-icons";
import draggable from "vuedraggable";
import { BCard, BRow, BCol, BFormGroup, BFormInput } from "bootstrap-vue";
import moment from "moment";

export default {
	name: "KanboardStatus",
	props: ["config_status", "data", "dataTmp", "count"],
	data() {
		return {
			countData: 0,
			subStatusDataTmp: [],
			subStatusData: []
		};
	},
	components: {
		draggable,
		BCard,
		FormWizard,
		TabContent,
		BRow,
		BCol,
		BFormGroup,
		BFormInput,
		UserIcon,
		DollarSignIcon,
		HomeIcon,
		ClockIcon
	},
	mounted() {
		this.subStatusDataTmp = this.dataTmp;
		this.countData = this.count;
		this.subStatusData = this.data;
		const listElm = document.querySelector("#infinite-list");
		listElm.addEventListener("scroll", e => {
			if (
				listElm.scrollTop + listElm.clientHeight >=
				listElm.scrollHeight - 5
			) {
				// console.log(this.subStatusData)
				this.loadMore2();
			}
		});
	},
	computed: {
		updateDate() {
			return dateUpdate => {
				return moment(dateUpdate).fromNow();
			};
		}
	},
	methods: {
		loadMore2() {
			setTimeout(e => {
				this.pushSubStatusData();
			}, 200);
		},
		pushSubStatusData() {
			let count = this.countData ? this.countData : 0;
			let tmp = this.subStatusDataTmp;
			let config = this.config_status;
			let data = [];
			config.forEach(item => {
				data = this.subStatusData[item.id] ? this.subStatusData[item.id] : [];
				if (tmp[item.id] && tmp[item.id].length > count) {
					for (var i = count; i < count + 10; i++) {
						tmp[item.id][i] && data.push(tmp[item.id][i]);
					}
				}
				this.subStatusData[item.id] = data;
			});
			this.countData += 10;
			// // console.log('this.countData',this.countData)
		},
		handleMoveDraft(event) {
			// // console.log(event)
			// if (event.draggedContext.element.appraiser_sale && (this.user_id === event.draggedContext.element.appraiser_sale.user_id || event.draggedContext.element.created_by === this.profile.data.user.id)) {
			this.idDragger = event.draggedContext.element.id;
			this.elementDragger = event.draggedContext.element;
			// } else return false
		},
		getData(status, sub_status) {
			let data = this.data.filter(
				item => item.status === status && item.sub_status === sub_status
			);
			return data;
		},
		checkDateExpired(element) {
			if (element.status_expired_at) {
				if (
					this.updateDate(element.status_expired_at, this.now).includes(
						"Đã hết hạn"
					)
				) {
					return true;
				} else return false;
			} else {
				return true;
			}
		},
		handleDetailCertificate(id) {
			this.$emit("handleDetailCertificate", id);
		},
		formatPrice(value) {
			let num = parseFloat(value / 1)
				.toFixed(0)
				.replace(".", ",");
			if (num.length > 3 && num.length <= 6) {
				return (
					parseFloat(num / 1000)
						.toFixed(1)
						.replace(".", ",") + " Nghìn"
				);
			} else if (num.length > 6 && num.length <= 9) {
				return (
					parseFloat(num / 1000000)
						.toFixed(1)
						.replace(".", ",") + " Triệu"
				);
			} else if (num.length > 9) {
				return (
					parseFloat(num / 1000000000)
						.toFixed(1)
						.replace(".", ",") + " Tỷ"
				);
			} else if (num < 900) {
				return num + " đ";
			}
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
	}
};
</script>

<style scoped lang="scss">
.scroll_board {
	// transform:rotateX(180deg);
	// -ms-transform:rotateX(180deg); /* IE 9 */
	// -webkit-transform:rotateX(180deg); /* Safari and Chrome */
	scroll-snap-align: start;
	overflow: auto;
	overflow-y: scroll;
	overflow-x: scroll;
	margin-bottom: 1px;
	max-height: 71vh !important;
	@media (max-height: 800px) and (min-height: 660px) {
		// M-MD Screen
		max-height: 75vh !important;
	}
	@media (max-height: 970px) and (min-height: 800px) {
		// FD Screen
		max-height: 78vh !important;
	}
	@media (min-height: 970px) {
		// >2k Screen
		max-height: 85vh !important;
	}
}
.name_card {
	text-align: left;
	width: 50%;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
}
.badge {
	border-radius: 10px;
	display: inline-block;
	text-transform: none;
	padding: 0.3rem 0.5rem;
	font-size: 85%;
	color: #fff;
	font-weight: 600;
	line-height: 1;
}
.badgeSuccess {
	background-color: rgba(40, 199, 111, 0.12);
	color: #28c76f !important;
}
.badgeWarning {
	background-color: rgba(255, 159, 67, 0.12);
	color: #ff9f43 !important;
}
.badgeDanger {
	background-color: rgba(234, 84, 85, 0.12);
	color: #ea5455 !important;
}
.badgeInfo {
	background-color: rgba(0, 207, 232, 0.12);
	color: #00cfe8 !important;
}
.badgePrimary {
	background-color: rgba(115, 103, 240, 0.12);
	color: #7367f0 !important;
}
.content_id {
	border-radius: 5px;
	padding: 0px 3px;
	font-weight: 500;
	cursor: pointer;
	&_primary {
		color: #007ec6;
		background-color: #e3f5ff;
	}
	&_secondary {
		color: #ffffff;
		background-color: #8b94a3;
	}
	&_warning {
		color: #ff963d;
		background-color: #fff1e6;
	}
	&_danger {
		color: #ff5e7b;
		background-color: #ffebef;
	}
	&_success {
		color: #ffffff;
		background-color: #26bf7f;
	}
}
.img_user {
	border-radius: 50%;
	height: 20px;
	width: 20px;
}
.appraise-container {
	padding: 0 1.25rem;
}
.kanban-column {
	min-height: 300px;
}
.height_icon {
	height: 1.3rem;
}
.card-body {
	padding: 0.75rem 0.75rem !important;
}
.card_container {
	border-radius: 5px;
	&_primary {
		border: 1px solid #b5e5ff;
	}
	&_secondary {
		border: 1px solid #8b94a3;
	}
	&_warning {
		border: 1px solid #ffd1ad;
	}
	&_danger {
		border: 1px solid #ffc8d3;
	}
	&_success {
		border: 1px solid #26bf7f;
		background-color: #eafff6;
	}
}
.container_kanban {
	background-color: #f6f7fb;
	border-radius: 5px;
	border: 1px solid #e8e8e8;
	border-top: 4px solid;
	border-bottom: none;
	border-left: none;
	border-right: none;
}
// border
.border {
	&_primary {
		color: #72cdff;
	}
	&_secondary {
		color: #9ea6b4;
	}
	&_danger {
		color: #ff7e9b;
	}
	&_warning {
		color: #ffb880;
	}
	&_success {
		color: #3ddc99;
	}
}
// title
.title {
	font-weight: 600;
	&_primary {
		color: #00507c;
	}
	&_secondary {
		color: #9ea6b4;
	}
	&_warning {
		color: #ffb880;
	}
	&_danger {
		color: #ff5e7b;
	}
	&_success {
		color: #3ddc99;
	}
}
//quatity
.quatity {
	min-width: 32px;
	height: 22px;
	padding: 0px 5px;
	align-items: center;
	text-align: center;
	border-radius: 5px;
	color: white;
	font-weight: 600;
	&_primary {
		background-color: #007ec6;
	}
	&_warning {
		background-color: #ff963d;
	}
	&_danger {
		background-color: #ff5e7b;
	}
	&_success {
		background-color: #26bf7f;
	}
	&_secondary {
		background-color: #8b94a3;
	}
}

.title_kanban {
	font-weight: 600;
}
.title_group {
	border: 1px solid #d9d9d9;
	border-radius: 5px;
	text-align: center;
}
.kanban_board {
	font-size: 0.875rem !important;
	min-width: 1200px;
}
.d_inline {
	@media (min-width: 1500px) {
		display: inline !important;
		min-width: 4.7rem;
	}
}
.label_container {
	@media (min-width: 1500px) {
		min-width: 120px;
	}
}
.icon_expired {
	margin-inline-end: 1rem;
	width: 1rem;
	justify-content: end;
}
.container_card_success {
	background: white;
	margin-bottom: 1rem;
	.card {
		margin-bottom: unset !important;
	}
}
.border_expired {
	border-color: red !important;
}
.flex_grow {
	flex-grow: 1;
}
</style>
