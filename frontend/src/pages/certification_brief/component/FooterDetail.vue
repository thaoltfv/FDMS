<template>
	<div
		v-if="!isMobile()"
		class="btn-footer d-md-flex d-block justify-content-end align-items-center"
	>
		<div
			v-if="isPermission && checkVersion.length > 0"
			style="position: fixed; top: 4.5rem;
    right: 0.75rem;"
		>
			<div
				class="card"
				style="border-radius: 7px;background:#ddf4ff; border:1px solid #007EC6"
			>
				<div class="row">
					<div class="col-md-9">
						<p style="padding: 15px; margin-bottom: 0;">
							Tài sản thẩm định đã được chỉnh sửa. Vui lòng
							<span style="font-weight: bold; color: red">
								Cập nhật Version
							</span>
							để có kết quả và xuất file mới nhất
						</p>
					</div>
					<div class="col-md-3" style="    padding: 15px;">
						<button
							v-if="isPermission && checkVersion.length > 0"
							class="btn btn-white"
							@click="viewAppraiseListVersion"
							style="background: #1f883d;
    color: white;"
						>
							<!-- <img class="img" src="@/assets/icons/ic_edit.svg" alt="edit"> -->
							Cập nhật Version
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- <button v-if="isPermission && checkVersion.length > 0" class="btn btn-white" @click="viewAppraiseListVersion">
            <img class="img" src="@/assets/icons/ic_edit.svg" alt="edit">
            Cập nhật Version
        </button> -->

		<button
			v-if="isPermission && isGrossCheck"
			class="btn btn-white"
			@click="viewDetailAppraise"
		>
			<img class="img" src="@/assets/icons/ic_done-orange.svg" alt="edit" />
			Cross check
		</button>
		<!-- <button
			v-if="
				isBusinessManager &&
					config &&
					config.description !== 'Hoàn thành' &&
					config.description !== 'Hủy'
			"
			class="btn btn-white mr-3"
			@click="redistributeRecord"
		>
		
			Phân lại HS
		</button> -->
		<button
			v-for="(target, index) in getTargetDescription()"
			:key="index"
			class="btn "
			:class="target.css"
			@click="handleFooterAccept(target)"
		>
			<img
				class="img"
				:src="require(`@/assets/icons/${target.img}`)"
				alt="edit"
			/>{{ target.btnDescription || target.description }}
		</button>
		<button
			v-if="editForm && isPermission"
			class="btn btn-white"
			@click.prevent="handleEdit(idData)"
		>
			<img class="img" src="@/assets/icons/ic_edit.svg" alt="edit" />
			Chỉnh sửa
		</button>
		<b-button-group class="btn_group">
			<button
				style="margin-right: 2px"
				class="btn btn-white"
				@click="onCancel"
				type="button"
			>
				<img class="img" src="@/assets/icons/ic_cancel.svg" alt="cancel" />Trở
				về
			</button>
			<b-dropdown
				v-if="isCancel && isPermission"
				class="btn_dropdown"
				right
				dropup
			>
				<b-dropdown-item @click.prevent="handleCancelCertificate">
					<div class="div_item_dropdown">
						<img
							style="height: 20px"
							class="img"
							src="@/assets/icons/ic_destroy.svg"
							alt="edit"
						/>
						Hủy hồ sơ
					</div>
				</b-dropdown-item>
			</b-dropdown>
		</b-button-group>
	</div>
	<div v-else class="btn-footer row" style="margin: 0;padding-top: 0;">
		<!--hiện full -->
		<div class="col-6">
			<button
				class="btn btn-white"
				type="button"
				@click="onCancel"
				style="width: fit-content;"
			>
				<img
					src="@/assets/icons/ic_cancel.svg"
					style="margin-right: 12px"
					alt="save"
				/>
				<span style="font-size: 15px;">Trở lại</span>
			</button>
		</div>
		<div class="col-6" style="text-align: right;">
			<b-dropdown
				v-if="getTargetDescription().length > 0"
				class="btn_dropdown"
				no-caret
				right
				dropup
				style="margin-top: 5px;"
			>
				<template #button-content>
					<button style="margin-right: 2px" class="btn btn-white" type="button">
						<img
							class="img"
							src="@/assets/icons/ic_more.svg"
							alt="cancel"
						/>Hành động
					</button>
				</template>
				<b-dropdown-item
					style="margin-right:0;width: 150px;padding: 0;"
					v-if="isPermission && isGrossCheck"
					class="btn btn-white"
					@click="viewDetailAppraise"
				>
					<div class="div_item_dropdown">
						<img
							class="img"
							src="@/assets/icons/ic_done-orange.svg"
							alt="edit"
						/>
						<span style="font-size: 13px;">Cross check</span>
					</div>
				</b-dropdown-item>
				<b-dropdown-item
					style="margin-right:0;width: 150px;padding: 0;"
					v-if="isPermission && checkVersion.length > 0"
					class="btn btn-white"
					@click="viewAppraiseListVersion"
				>
					<div class="div_item_dropdown">
						<img class="img" src="@/assets/icons/ic_edit.svg" alt="edit" />
						<span style="font-size: 13px;">Cập nhật Version</span>
					</div>
				</b-dropdown-item>
				<b-dropdown-item
					style="margin-right:0;width: 150px;padding: 0;"
					v-for="(target, index) in getTargetDescription()"
					:key="index"
					class="btn"
					:class="target.css"
					@click="handleFooterAccept(target)"
				>
					<div class="div_item_dropdown">
						<img
							class="img"
							:src="require(`@/assets/icons/${target.img}`)"
							alt="edit"
						/>
						<span style="font-size: 13px;">{{
							target.btnDescription || target.description
						}}</span>
						<!-- {{target.description}} -->
					</div>
				</b-dropdown-item>
				<b-dropdown-item
					style="margin-right:0;width: 150px;padding: 0;"
					v-if="editForm && isPermission"
					class="btn btn-white"
					@click.prevent="handleEdit(idData)"
				>
					<div class="div_item_dropdown">
						<img class="img" src="@/assets/icons/ic_edit.svg" alt="edit" />
						<span style="font-size: 13px;">Chỉnh sửa</span>
					</div>
				</b-dropdown-item>
				<b-dropdown-item
					style="margin-right:0;width: 150px;padding: 0;"
					v-if="isCancel && isPermission"
					class="btn btn-white"
					@click.prevent="handleCancelCertificate"
				>
					<img class="img" src="@/assets/icons/ic_destroy.svg" alt="edit" />
					<span style="font-size: 13px;">Hủy hồ sơ</span>
				</b-dropdown-item>
			</b-dropdown>
		</div>
	</div>
</template>

<script>
import {
	BTooltip,
	BDropdown,
	BDropdownItem,
	BButtonGroup
} from "bootstrap-vue";

export default {
	name: "FooterDetail",
	props: [
		"form",
		"status",
		"sub_status",
		"jsonConfig",
		"profile",
		"idData",
		"checkVersion"
	],
	components: {
		"b-tooltip": BTooltip,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown
	},
	data() {
		return {
			nextStatus: "",
			nextSubStatus: "",
			preStatus: "",
			preSubStatus: "",
			isCancel: false,
			isButton: true,
			editForm: false,
			requireData: {},
			user: "",
			config: "",
			isPermission: false,
			isGrossCheck: false,
			isBusinessManager: false,
			targetDescription: []
		};
	},
	mounted() {
		this.user = this.profile.data.user;
		if (
			this.form &&
			this.form.appraiser_business_manager &&
			this.form.appraiser_business_manager.user_id === this.user.id
		) {
			this.isBusinessManager = true;
		}
		let configData = this.loadConfigByStatus(this.status, this.sub_status);
		if (configData) {
			this.loadConfigData(configData);
			this.isPermission = this.checkPermission(configData);
			if (
				this.profile.data.permissions &&
				this.profile.data.permissions.length > 0
			) {
				const check = this.profile.data.permissions.find(
					e => e === "ACCEPT_CERTIFICATE_BRIEF"
				);
				if (!check) {
					this.isPermission = false;
				}
			}
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
		getTargetDescription() {
			let data = [];
			if (this.isPermission) {
				data = this.targetDescription;
			}
			return data;
		},
		viewAppraiseListVersion() {
			this.$emit("viewAppraiseListVersion");
		},
		redistributeRecord() {
			if (this.config.id) {
				this.$emit("handleFooterRedistributeRecord", this.config.id);
			}
		},
		loadConfigByStatus(status, sub_status) {
			return this.jsonConfig.principle.find(
				item =>
					item.status === status &&
					item.sub_status === sub_status &&
					item.isActive === 1
			);
		},
		loadConfigData(configData) {
			this.config = configData;

			let checkRole = false;
			if (
				this.user.roles[0].name === "ADMIN" ||
				this.user.roles[0].name === "ROOT_ADMIN"
			) {
				checkRole = true;
			} else if (configData.put_require && configData.put_require.length > 0) {
				configData.put_require.forEach(key_required => {
					if (
						key_required !== "created_by" &&
						this.form[key_required] === this.user.appraiser.id &&
						this.form.status !== 9 &&
						this.form.status !== 4
					) {
						checkRole = true;
					}
				});
			}
			this.isCancel = configData.isCancel && checkRole;
			this.editForm = configData.edit.form;
			this.isGrossCheck = configData.require.check_price;
			this.requireData = configData.require;
			this.targetDescription = configData.target_description;
		},
		checkPermission(configData) {
			let check = false;
			if (
				configData.put_require_roles &&
				this.user.roles &&
				configData.put_require_roles.includes(this.user.roles[0].name)
			) {
				return true;
			}
			if (configData.put_require && configData.put_require.length > 0) {
				configData.put_require.forEach(key_required => {
					if (
						// (key_required === "created_by" &&
						// 	this.form[key_required] === this.user.id) ||
						key_required !== "created_by" &&
						this.form[key_required] === this.user.appraiser.id
					) {
						check = true;
					}
				});
			}
			return check;
		},
		handleFooterAccept(target) {
			if (target) {
				this.$emit("handleFooterAccept", target);
			}
		},
		handleFooterReject(status, sub_status, text) {
			if (status && sub_status) {
				this.$emit("handleFooterReject", status, sub_status, text);
			}
		},
		viewDetailAppraise() {
			this.$emit("viewDetailAppraise");
		},
		handleEdit(idData) {
			this.$emit("handleEdit", idData);
		},
		handleCancelCertificate() {
			this.$emit("handleCancelCertificate");
		},
		onCancel() {
			this.$emit("onCancel");
		}
	}
};
</script>

<style lang="scss" scoped>
/deep/ .dropdown-item {
	min-width: unset !important;
	// padding: 0!important;
}
/deep/ .dropdown-menu.show {
	background: transparent !important;
	box-shadow: none !important;
	text-align: right;
}
.btn_dropdown {
	border: white;
	border-radius: 5px;
	height: 35px;
	@media (max-width: 767px) {
		margin-top: 10px;
	}
}
.btn_group {
	@media (max-width: 767px) {
		width: 100%;
	}
	/deep/ .btn-secondary {
		background-image: none;
		border-color: none !important;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
	}
}
</style>
