<template>
	<div class="card">
		<div class="card-title">
			<div class="d-flex justify-content-between align-items-center">
				<h3 class="title">Giá trị tài sản</h3>
				<img class="img-dropdown" :class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
					src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailAppraise = !showCardDetailAppraise">
			</div>
		</div>
		<div class="card-body card-info" v-show="showCardDetailAppraise">
				<ValidationObserver tag="form" ref="tab_other_property">
					<div class="main-wrapper">
							<div class="responsive-table">
								<table class="table_contruction_pp1 color_content">
										<thead>
											<tr>
												<th>Tên tài sản</th>
												<th>ĐVT</th>
												<th>Số lượng</th>
												<th>Đơn giá</th>
												<!-- <th>CLCL</th> -->
												<th>Thành tiền</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<InputText
														v-model="form.name"
														:vid="'namePropertyOther'"
														nonLabel="Tên tài sản"
														:disabledInput="true"
													/>
												</td>
												<td>
													<InputText
														v-model="form.unit"
														:vid="'typePropertyOther'"
														nonLabel="Đvt"
														:max-length="200"
														rules="required"
													/>
												</td>
												<td>
													<InputNumberNoneFormat
														v-model="form.quantity"
														:vid="'total_amount_other'"
														class="label-none"
														label="Số lượng"
														:max="99999999999999"
														:min="0"
														rules="required"
														@change="changeQuantity($event)"
													/>
												</td>
												<td>
													<InputCurrency
															v-model="form.unit_price"
															vid="unit_price_m2"
															class="label-none"
															label="Đơn giá"
															rules="required"
															@change="changeUnitPrice($event)"
														/>
												</td>
												<!-- <td>
													<InputPercent
														class="label-none"
														v-model="form.remaining_quality"
														vid="remaining_quality"
														rules="required"
														label="Tỷ lệ"
														:max="100"
														:min="0"
														@change="changePercentRemain($event)"
													/>
												</td> -->
												<td>
													<InputCurrency
														:key="key_render"
														v-model="form.total_price"
														class="label-none"
														nonLabel="thành tiền"
														:disabled="true"
													/>
												</td>
											</tr>
										</tbody>
									</table>
							</div>
						<div class="btn-footer d-md-flex d-block justify-content-end align-items-center w-100">
							<div class="d-md-flex d-block">
								<button  @click.prevent="onCancel" class="btn btn-white text-nowrap" >
									<img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
								</button>
								<button class="btn btn-white btn-orange text-nowrap" @click.prevent="handleSaveOtherPrice">
									<img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
								</button>
							</div>
						</div>
					</div>
				</ValidationObserver>
		</div>
	</div>

</template>

<script>
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import InputCurrency from '@/components/Form/InputCurrency'
import InputPercent from '@/components/Form/InputPercent'
import InputText from '@/components/Form/InputText'
import CertificateAsset from '@/models/CertificateAsset'
import Vue from 'vue'
import Icon from 'buefy'
Vue.use(Icon)
export default {
	name: 'tab_other_property',
	props: ['data', 'idData', 'asset_type_id'],
	components: {
		InputText,
		InputCurrency,
		InputPercent,
		InputNumberNoneFormat
	},
	computed: {
		optionsJuridicals () {
			return {
				data: this.juridicals,
				id: 'id',
				key: 'content'
			}
		}
	},
	data () {
		return {
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			key_render: 6152,
			key_render_percent: 12321,
			key_render_description: 3210,
			showDescription: false,
			showCardDetailAppraise: true,
			description: '',
			indexEdit: ''
		}
	},
	mounted () {
	},
	beforeUpdate () {
	},
	methods: {
		onCancel () {
			return this.$router.go(-1)
		},
		changeUnitPrice (e) {
			if (e) {
				this.form.unit_price = e
				this.form.total_price = e * (this.form.quantity ? this.form.quantity : 0) * (this.form.remaining_quality ? this.form.remaining_quality : 0) / 100
			} else {
				this.form.total_price = ''
			}
			this.key_render += 1
		},
		changeQuantity (e) {
			if (e) {
				this.form.quantity = e
				this.form.total_price = e * (this.form.unit_price ? this.form.unit_price : 0) * (this.form.remaining_quality ? this.form.remaining_quality : 0) / 100
			} else {
				this.form.total_price = ''
			}
			this.key_render += 1
		},
		changePercentRemain (e) {
			if (e) {
				this.form.remaining_quality = e
				this.form.total_price = e * (this.form.unit_price ? this.form.unit_price : 0) * (this.form.quantity ? this.form.quantity : 0) / 100
			} else {
				this.form.total_price = ''
			}
			this.key_render += 1
		},
		async handleSaveOtherPrice () {
			const valid = await this.$refs.tab_other_property.validate()
			if (valid) {
				await this.saveOtherAsset()
			}
		},
		async saveOtherAsset () {
			const data = { price: this.form }
			if(data.price.unit_price <= 0)
			{
				this.$toast.open({
					message: 'Vui lòng nhập đơn giá lơn hơn 0',
					type: 'error',
					position: 'top-right'
				})
				return
			} else if(data.price.quantity <= 0)
			{
				this.$toast.open({
					message: 'Vui lòng nhập số lượng lớn hơn 0',
					type: 'error',
					position: 'top-right'
				})
				return
			}
			const res = await CertificateAsset.submitVehicleStep4(this.idData, data)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu tài sản khác thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				await this.$emit('updateTotalOtherAsset', res.data)
			} else if (res.error) {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				await this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
		}
	}

}
</script>
<style scoped lang="scss">

	.card {
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		background: #FFFFFF;
		margin-bottom: 1rem;

		&-footer {
			padding: 15px 24px;
		}

		&-title {
			background: #F3F2F7;
			padding: 16px 20px;
			margin-bottom: 0;

			&__img {
				padding: 8px 20px;
			}

			@media (max-width: 768px) {
				padding: 12px;
			}

			.title {
				color: #007EC6 !important;
				font-size: 1.125rem;
				font-weight: 600;
				margin-bottom: 0;
			}
		}

		&-body {
			@media (max-width: 787px) {
				padding: 15px;
			}
		}

		&-sub_header_title {
			padding: 15px 24px;
		}

		&-info {
			.title {
				font-size: 1.125rem;
				font-weight: 700;
				margin-top: 28px;

				&-highlight {
					background: rgba(252, 194, 114, 0.53);
					text-align: center;
					padding: 10px 0;
					border-radius: 2px;
				}
			}
		}

		&-land {
			position: relative;
			padding: 0;
		}
	}

	.form-group-container {
		margin-top: 10px;
	}

	.color-black {
		color: #333333;
	}

	.btn-delete {
		cursor: pointer;
		display: flex;
		align-items: center;
		background: #FFFFFF;
		border: none;

		// margin: auto;
		// width: 36px;
		// height: 36px;

		img {
			width: 100%;
			height: auto;
		}
	}

	.btn {
		&-orange {
			background: #FAA831;
			text-align: center;
			border-radius: 5px;
			box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
			height: 35px;
			color: #fff;
			box-sizing: border-box;

			&:hover {
				border-color: #dc8300;
			}
		}
	}

	.img-dropdown {
		cursor: pointer;
		width: 18px;

		&__hide {
			transform: rotate(90deg);
			transition: .3s;
		}
	}

	.img-locate {
		cursor: pointer;
		position: absolute;
		right: 14px;
		top: 30px;
		background: #FFFFFF;
		height: 2.295rem;
		width: 32px;
		display: grid;
		place-items: center;

		img {
			height: 60%;
		}
	}

	.text-error {
		color: #cd201f;
		font-size: 12px;
	}

	.select-group {
		background-color: #F6F7FB;
		border: 1px solid #E8E8E8;
		border-radius: 3px;
		padding: 16px 22px;

		.select-title {
			color: #FAA831;
			font-weight: 700;
			white-space: nowrap;
		}
	}
		.img_add {
			width: 100%;
			height: 100% !important;
			cursor: pointer;
		}
		.container_input {
			border-radius: 10px;
			border: 2px solid #FAA831;
			width: 100%;
			height: 100%;
			position: relative;
		}
		.input_file_4 {
			left: 0;
			opacity: 0;
			height: 100%;
			width: 100%;
			cursor: pointer;
			position: absolute;
		}
		.table_contruction_pp1 {
			width: 100%;
			font-weight: 500;
			color: #000000;
			text-align: center;
			thead{
				th{
					padding: 12px 0;
					font-weight: 700;
					background-color:  #DEE6EE;;
					color: #3D4D65;
					border-right: 1px solid white;
					&:first-child{
						border-top-left-radius: 3px;
						border-left: 1px solid #CED4DA;
					}
					&:last-child{
						border-top-right-radius: 3px;
						border-right: 1px solid #CED4DA;
					}
				}
			}
			tbody{
				td{
					border: 1px solid #CED4DA;
					&:first-child{
						width: 30%;
						min-width: 200px;
					}
					&:nth-child(2) {
						width: 10%;
						min-width: 100px;
					}
					&:nth-child(3) {
						width: 15%;
						min-width: 80px;
					}
					&:nth-child(4) {
						width: 20%;
						min-width: 200px;
					}
					// &:nth-child(5) {
					// 	width: 10%;
					// 	min-width: 140px;
					// }
					&:nth-child(5) {
						width: 25%;
						min-width: 200px;
					}
					box-sizing: border-box;
					padding: 10px 14px;
				}
			}
		}

		.main-wrapper {
			width: 100%;
			overflow-x: auto;
			box-sizing: border-box;
		}

		.responsive-table {
			display: inline-block;
			min-width: 100%;
			box-sizing: border-box;
		}

		.responsive-table > table {
			width: 100%;
			border-collapse: collapse;
		}
		.class_content {
			padding: 0.75rem;
			border: 1px solid #E8E8E8;
		}
		.class_content_header {
			padding: 1rem 1.3rem;
			border: 1px solid #E8E8E8;
		}
		.class_header {
			background: #DEE6EE;
			color: #3D4D65;
			font-weight: 700;
			text-align: center;
		}
		// .container_header {
		//   border: 1px solid #E8E8E8;
		//   border-radius: 3px 3px 0px 0px;
		// }
		.row {
		margin-right: unset !important;
		margin-left: unset !important;
	}
</style>
