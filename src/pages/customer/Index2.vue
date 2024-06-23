<template>
	<div class="container-fluid">
		<div class="pannel">
			<div
				class="pannel__content d-md-flex d-block justify-content-end align-items-end"
			>
				<button
					@click.prevent="handleCreate(type)"
					:class="
						type !== '' && type !== undefined && type !== null ? '' : 'disabled'
					"
					class="btn btn-create btn-white text-nowrap index-screen-button mb-0 px-3"
					tag="button"
					v-if="add"
				>
					<img
						src="@/assets/icons/ic_add.svg"
						style="margin-right: 8px"
						alt="icon add"
					/>
					Tạo
				</button>
			</div>
		</div>
		<div :class="totalRecord === 0 ? 'empty-data' : ''">
			<a-table
				:columns="columns"
				:data-source="list_apartments"
				:loading="isLoading"
				:rowKey="record => record.id"
				table-layout="top"
				:pagination="{
					...pagination
				}"
				@change="onPageChange"
			>
				<!--Custom type table-->
				<template slot="action" slot-scope="props">
					<button
						v-if="type === 'NHOM_DOI_TAC' && props && props.status === 0"
						@click="changeStatusCustomerGroup(props)"
						class="btn btn-success"
					>
						Kích hoạt
					</button>
					<button
						v-if="type === 'NHOM_DOI_TAC' && props && props.status === 1"
						class="btn btn-warning"
						@click="changeStatusCustomerGroup(props)"
					>
						Tạm ngưng
					</button>
					<!-- <div class="d-flex justify-content-end">
          <a-tooltip placement="bottom"
                     :title="$t('tooltip_delete')" v-if="deleted">
            <a href="#"
               @click.prevent="handleOpenModal(action_delete.id)"
               class="text-decoration-none action">
              <img class="icon-action" src="../../../assets/images/icon-delete.svg"
                   alt="icon">
            </a>
          </a-tooltip>
         </div> -->
				</template>
				<template slot="status" slot-scope="status">
					<p :class="status === 0 ? '' : 'status'">
						{{
							status === 0
								? "Đã vô hiệu hóa"
								: status === 1
								? "Đang hoạt động"
								: ""
						}}
					</p>
				</template>
				<template slot="description" slot-scope="description">
					<p class="mb-0 text-capitalize">
						{{ description }}
					</p>
				</template>
				<template slot="created_at" slot-scope="created_at">
					<p class="mb-0">{{ created_at | formatDate }}</p>
				</template>
			</a-table>
		</div>
		<ModalDelete
			v-if="openModal"
			@cancel="openModal = false"
			@action="handleDelete"
		/>
	</div>
</template>

<script>
import Dictionary from "@/models/Dictionary";
import { convertPagination } from "@/utils/filters";
import Search from "./Search";
// import {replace} from 'lodash-es'
import ModalDelete from "@/components/Modal/ModalDelete";
import Vue from "vue";
import moment from "moment";
Vue.filter("formatDate", function(value) {
	if (value) {
		return moment(String(value)).format("DD/MM/YYYY");
	}
});
export default {
	name: "index",
	components: {
		Search,
		ModalDelete
	},
	data() {
		return {
			filter_search: "",
			type: "NHOM_DOI_TAC",
			totalRecord: 0,
			isLoading: false,
			list_apartments: [],
			filter: {},
			pagination: {},
			perPage: "",
			openModal: false,
			add: false,
			edit: false,
			deleted: false
		};
	},
	async created() {
		// this.list_apartments = this.$route.query['list']
		if ("search" in this.$route.query) {
			this.filter_search = this.$route.query.search;
		} else {
		}
		this.pagination = this.$route.meta["pagination"];
		const permission = this.$store.getters.currentPermissions;
		permission.forEach(value => {
			if (value === "ADD_CUSTOMER") {
				this.add = true;
			}
			if (value === "EDIT_CUSTOMER") {
				this.edit = true;
			}
			if (value === "DELETE_CUSTOMER") {
				this.deleted = true;
			}
		});
		const params = {
			search: this.type,
			query: this.type,
			status: "ALL"
		};
		this.$router.push({
			query: {
				search: this.type,
				status: "ALL"
			}
		});
		await this.getDictionaries(params);
	},
	computed: {
		columns() {
			return [
				{
					title: "ID",
					align: "center",
					dataIndex: "id",
					width: "4%"
				},
				{
					title: "Tên",
					align: "left",
					scopedSlots: { customRender: "description" },
					dataIndex: "description",
					width: "30%"
				},
				// {
				// 	title: "Viết tắt",
				// 	class: "text-none",
				// 	align: "left",
				// 	dataIndex: "acronym"
				// },
				{
					title: "Trạng thái",
					align: "center",
					scopedSlots: { customRender: "status" },
					dataIndex: "status",
					sorter: true,
					sortDirections: ["descend", "ascend"]
				},
				{
					title: "Ngày tạo",
					align: "left",
					scopedSlots: { customRender: "created_at" },
					dataIndex: "created_at"
				},
				{
					title: "Thao tác",
					scopedSlots: { customRender: "action" },
					align: "right",
					width: "10%"
				}
			];
		}
	},
	methods: {
		async changeStatusCustomerGroup(data) {
			const resp = await Dictionary.changeStatusCustomerGroup(
				data.id,
				data.status === 0 ? 1 : 0
			);
			if (resp.data) {
				const params = {
					search: "NHOM_DOI_TAC",
					status: "ALL"
				};
				await this.getDictionaries(params);
			} else {
				this.$toast.open({
					message: "Kích hoạt/Tạm ngưng thất bại",
					type: "error",
					position: "top-right"
				});
			}
		},
		async onFilterChange($event) {
			this.type = $event.search;
			if (
				$event.search !== "" &&
				$event.search !== undefined &&
				$event.search !== null
			) {
				this.filter = { ...$event };
				const params = {
					query: $event.search,
					status: "ALL"
				};
				this.$router.push({
					query: {
						search: this.type,
						status: "ALL"
					}
				});
				await this.getDictionaries(params);
			} else {
				this.filter = {};
			}
		},

		async onPageChange(pagination) {
			this.perPage = pagination.pageSize;
			const params = {
				page: pagination.current,
				per_page: pagination.pageSize
			};
			await this.getDictionaries(params);
		},

		handleCreate(event) {
			this.$router
				.push({
					name: "dictionary.create",
					query: {
						type: event
					}
				})
				.catch(_ => {});
		},
		async handleDelete() {
			const resp = await Dictionary.deleteApartment(this.id);
			if (resp.data) {
				await this.getDictionaries();
				this.$toast.open({
					message: "Xóa thành công",
					type: "success",
					position: "top-right"
				});
			} else {
				this.$toast.open({
					message: "Không có quyền xóa",
					type: "error",
					position: "top-right"
				});
			}
		},

		async getDictionaries(params = {}) {
			this.isLoading = true;
			const filter = {};

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property];
			}
			try {
				const resp = await Dictionary.paginate({
					query: {
						page: 1,
						limit: 20,
						query: this.type,
						...params,
						...filter
					}
				});

				this.list_apartments = [...resp.data.data];
				this.totalRecord = resp.data.total;
				this.pagination = convertPagination(resp.data);
				this.isLoading = false;
			} catch (e) {
				this.isLoading = false;
			}
		},

		handleOpenModal(id) {
			this.openModal = true;
			this.id = id;
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		// eslint-disable-next-line no-sequences,standard/computed-property-even-spacing
		const dictionary = await Dictionary.find(to.query["search"]);
		to.meta["detail"] = dictionary.data;
		return next();
	},
	beforeMount() {},

	async handleDelete() {
		try {
			await Dictionary.deleteApartment(this.id);
		} catch (err) {
			await this.onError(
				this.$t("message_error"),
				this.$t("delete_message_error")
			);
		}
	}
};
</script>
<style scoped lang="scss">
.pannel {
	background: #ffffff;
	border-radius: 5px;
	margin-bottom: 47px;
	&__table {
		padding: 25px 0;
		border-radius: 5px;
	}
	&__input {
		p {
			color: #5a5386;
			font-weight: 600;
		}
	}
}
.form-control {
	margin-right: 5px;
	width: auto;
	color: #555555;
	border-radius: 5px;

	@media (max-width: 1023px) {
		width: 100%;
	}
	&:focus {
		border-color: #cccccc;
		box-shadow: none;
	}
}
.table {
	thead {
		th {
			padding: 0.5rem 40px;
			background: transparent;
			color: #000000;
			font-weight: 700;

			border-bottom: 2px solid rgba(110, 117, 130, 0.2);
			@media (max-width: 1023px) {
				padding: 0.5rem;
			}
		}
	}
	tbody {
		tr {
			&:nth-child(odd) {
				background: #ffffff;
			}
		}
		td {
			padding: 0.5rem 40px;
			color: #000000;
			vertical-align: middle !important;

			font-weight: 700;
			@media (max-width: 1023px) {
				padding: 0.5rem;
			}
			&:first-child {
				width: 100%;
			}
		}
	}
	&__action {
		padding-right: 20px;
	}
}
.btn {
	&-create {
		@media (max-width: 1023px) {
		}
	}
}
.icon-action {
	width: 18px !important;
	height: auto;
}

.status {
	text-transform: none;
	color: #2d9000;
	margin-bottom: 0;

	&.red {
		color: red;
	}
	&.orange {
		color: #faa831;
	}
}
</style>
