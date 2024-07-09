<template>
	<div class="container-fluid">
		<div class="pannel">
			<div
				class="pannel__content d-xl-flex d-block justify-content-between align-items-end"
			>
				<Search @filter-changed="onFilterChange($event)" />
				<router-link
					:to="{ name: 'customer_group_fourth.create' }"
					class="btn btn-create btn-white text-nowrap index-screen-button mt-xl-0 mt-2 px-3 m-0 mb-0"
					tag="button"
					v-if="add"
					><img
						src="../../../assets/icons/ic_add.svg"
						style="margin-right: 8px"
						alt="icon add"
					/>
					Tạo Phân cấp 4
				</router-link>
			</div>
		</div>
		<a-table
			:columns="columns"
			:data-source="list"
			:loading="isLoading"
			:rowKey="record => record.id"
			:pagination="{
				...pagination
			}"
			@change="onPageChange"
		>
			<!--Custom type table-->
			<template slot="action" slot-scope="action_edit, action_delete">
				<div class="d-flex justify-content-end">
					<!-- <a-tooltip
						placement="bottom"
						:title="$t('tooltip_edit')"
						v-if="edit"
						class="mr-2"
					>
						<a
							@click.prevent="handleEdit(action_edit.id)"
							href="#"
							class="text-decoration-none action"
						>
							<img
								class="icon-action"
								src="../../../assets/images/icon-edit.svg"
								alt="icon"
							/>
						</a>
					</a-tooltip> -->
					<!-- <a-tooltip placement="bottom"
                    :title="$t('tooltip_delete')" v-if="deleted">
           <a href="#"
              @click.prevent="handleOpenModal(action_delete.id)"
              class="text-decoration-none action">
             <img class="icon-action" src="../../../assets/images/icon-delete.svg"
                  alt="icon">
           </a>
         </a-tooltip> -->
				</div>
			</template>
		</a-table>
	</div>
</template>

<script>
import CustomerGroupFourth from "@/models/CustomerGroupFourth";
import { convertPagination } from "@/utils/filters";
import { replace } from "lodash-es";
import InputCategory from "@/components/Form/InputCategory";
import Search from "./Search";
export default {
	name: "index",
	components: {
		Search,
		InputCategory
	},
	data() {
		return {
			isLoading: false,
			list: [],
			filter: {},
			pagination: {},
			perPage: "",
			openModal: false,
			form: {
				first_id: "",
				second_id: "",
				third_id: "",
				search: ""
			},
			add: false,
			edit: false,
			deleted: false
		};
	},
	created() {
		this.list = this.$route.query["list"];
		this.pagination = this.$route.meta["pagination"];
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
					title: "Phân cấp 4",
					align: "left",
					dataIndex: "name"
				},
				{
					title: "Phân cấp 3",
					align: "left",
					dataIndex: "third_group.name"
				},
				{
					title: "Phân cấp 2",
					align: "left",
					dataIndex: "second_group.name"
				},
				{
					title: "Phân cấp 1",
					align: "left",
					dataIndex: "first_group.name"
				},
				{
					title: "Thao tác",
					scopedSlots: { customRender: "action" },
					align: "right",
					width: "100px"
				}
			];
		}
	},
	methods: {
		async onFilterChange($event) {
			const params = {
				page: 1,
				limit: this.limit || 20
			};

			this.filter = { ...$event };
			await this.listCustomerGroupFourth(params);
		},

		async onPageChange(pagination, filters, sorter) {
			this.perPage = pagination.pageSize;
			const sortBy = `sortBy[${sorter.field}]`;
			const sortDesc = replace(sorter.order, "end", "");

			const params = {
				page: pagination.current,
				per_page: pagination.pageSize,
				[sortBy]: sortDesc
			};
			await this.listCustomerGroupFourth(params);
		},

		async handleEdit(id) {
			this.$router
				.push({
					name: "customer_group_fourth.edit",
					query: {
						id: id
					}
				})
				.catch(_ => {});
		},
		async listCustomerGroupFourth(params = {}) {
			this.isLoading = true;
			const filter = {};

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property];
			}

			try {
				const resp = await CustomerGroupFourth.paginate({
					query: {
						page: 1,
						limit: 20,
						...params,
						...filter
					}
				});

				this.list = [...resp.data.data];
				this.pagination = convertPagination(resp.data);
				this.isLoading = false;
			} catch (e) {
				this.isLoading = false;
			}
		}
	},
	beforeMount() {
		this.listCustomerGroupFourth();
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
			margin-top: 1rem !important;
		}
	}
}
.input_category {
	width: 20%;
	label {
		display: none;
	}
}
.icon-action {
	width: 18px !important;
	height: auto;
}
</style>
