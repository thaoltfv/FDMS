<template>
	<div class="modal-delete d-flex justify-content-center align-items-center">
		<div class="card">
			<div
				class="card-header d-flex justify-content-between align-items-center py-3"
			>
				<h3 class="font-weight-bold mb-0">
					<!--          {{$t('btn_delete')}}-->
					Thông báo
				</h3>

				<img
					@click="handleCancel"
					src="../../assets/images/icon-btn-back.svg"
					alt="icon"
				/>
			</div>

			<div class="card-body">
				<h5
					style="font-size: 18px"
					v-html="notification"
					class="padding-bottom : 5px"
				></h5>
				<div>
					<InputCategory
						v-if="notification == `Bạn có muốn 'Từ chối' hồ sơ này?`"
						v-model="reason_id"
						vid="reason_id"
						label="Lí do"
						class="mb-3"
						:options="optionsReasons"
					/>
					<InputCategory
						v-if="notification == `Bạn có muốn 'Hủy' hồ sơ này?`"
						v-model="reason_id"
						vid="reason_id"
						label="Lí do hủy sơ bộ"
						class="mb-3"
						:options="optionsReasonsCancelPC"
					/>

					<InputTextarea
						:rows="rows"
						:autosize="false"
						:maxLength="1000"
						v-model="note"
						label="Ghi chú"
						class="form-group-container mb-3"
					/>
				</div>
				<div v-if="!isMobile()" class="btn__group">
					<button
						class="btn btn-white font-weight-normal font-weight-bold"
						@click.prevent="handleCancel"
						v-text="$t('popup_btn_no')"
					/>
					<button
						class="btn btn-white btn-orange font-weight-bold mt-md-0 mt-2"
						@click.prevent="handleAction(note, reason_id)"
						v-text="$t('popup_btn_yes')"
					/>
				</div>
				<div v-else class="btn__group row" style="padding: 0;">
					<div class="col-6" style="padding:0;     margin-top: 8px;">
						<button
							class="btn btn-white font-weight-normal font-weight-bold"
							@click.prevent="handleCancel"
							v-text="$t('popup_btn_no')"
						/>
					</div>
					<div class="col-6" style="padding:0;">
						<button
							class="btn btn-white btn-orange font-weight-bold mt-md-0 mt-2"
							@click.prevent="handleAction(note, reason_id)"
							v-text="$t('popup_btn_yes')"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import InputTextarea from "@/components/Form/InputTextarea";
import InputCategory from "@/components/Form/InputCategory";
import WareHouse from "@/models/WareHouse";
import store from "@/store";
import * as types from "@/store/mutation-types";
export default {
	components: { InputTextarea, InputCategory },
	name: "ModalNotificationPreCertificateNote",
	data() {
		return {
			note: "",
			rows: 3,
			reason_id: null,
			reasons: [],
			reasonCancelPC: []
		};
	},
	props: ["notification"],
	computed: {
		optionsReasons() {
			return {
				data: this.reasons,
				id: "id",
				key: "description"
			};
		},
		optionsReasonsCancelPC() {
			return {
				data: this.reasonCancelPC,
				id: "id",
				key: "description"
			};
		}
	},
	mounted() {
		this.getDictionary();
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
		handleCancel(event) {
			this.$emit("cancel", event);
		},

		handleAction(event, note, reason_id) {
			this.$emit("action", event, note, reason_id);
			this.$emit("cancel", event);
		},
		async getDictionary() {
			if (
				this.notification == `Bạn có muốn 'Từ chối' hồ sơ này?` ||
				this.notification == `Bạn có muốn 'Hủy' hồ sơ này?`
			) {
				const resp = await WareHouse.getDictionaries();
				this.reasons = resp.data.li_do;
				this.reasonCancelPC = resp.data.li_do_huy_so_bo;
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.modal-delete {
	position: fixed;
	z-index: 1002;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);

	@media (max-width: 787px) {
		padding: 20px;
	}
	.card {
		max-width: 600px;
		width: 100%;
		margin-bottom: 0;
		&-header {
			border-bottom: 1px solid #dddddd;
			h3 {
				color: #333333;
			}
			img {
				cursor: pointer;
			}
		}
		&-body {
			text-align: center;
			padding: 40px;
			p {
				color: #333333;
				margin-bottom: 40px;
			}

			.btn__group {
				.btn {
					max-width: 150px;
					width: 100%;
					margin: 0 10px;
				}
			}
		}
	}
}
.btn {
	&-orange {
		background: #faa831;
		color: #ffffff;
		font-weight: bold !important;
	}
}
</style>
