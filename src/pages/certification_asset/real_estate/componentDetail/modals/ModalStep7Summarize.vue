<template>
  <div class="row content_detail_asset">
		<div class="col-12">
			<div class="main-wrapper">
				<div class="responsive-table">
					<table class="table_summarize color_content">
								<thead>
										<tr>
												<th><div class="col-10 col-lg-10">Tên tài sản</div></th>
												<th>Giá trị thẩm định</th>
										</tr>
								</thead>
								<tbody>
										<tr>
												<td>
													<div class="col-10 col-lg-10">Quyền sử dụng đất</div>
												</td>
												<td>{{form.price_land_asset ? `${formatNumber(roundPrice(form.price_land_asset, 0))} đ` : '0 đ'}}</td>
										</tr>
										<tr>
												<td>
													<div class="col-10 col-lg-10">Nhà cửa, vật kiến trúc</div>
												</td>
												<td>{{form.price_tangible_asset ? `${formatNumber(form.price_tangible_asset)} đ` : '0 đ'}}</td>
										</tr>
										<tr>
												<td>
													<div class="col-10 col-lg-10">Tài sản khác</div>
												</td>
												<td>{{form.price_other_asset ? `${formatNumber(form.price_other_asset)} đ` : '0 đ'}}</td>
										</tr>
										<tr>
												<td>
													<div class="col-10 col-lg-10"><strong>TỔNG CỘNG</strong></div>
												</td>
												<td><strong>{{form.price_total_asset ? `${formatNumber(form.price_total_asset)} đ` : '0 đ'}}</strong></td>
										</tr>
										<tr>
											<td>
												<div class="d-flex">
													<div style="padding-top: 6px" class="col-10 col-lg-10"><strong>Làm tròn</strong></div>
													<div class="col-2 col-lg-2">
															<!-- <InputNumberNegative
																:key="key_render"
																:text_center="true"
																vid="round_total"
																:max="8"
																:min="-7"
																:sufix="false"
																:percent="true"
																@change="changeRoundTotal($event)"
																v-model="round_appraise_total"
															/> -->
															<InputNumberFormat
																:disabledInput="!editAsset"
																vid="test"
																:max="99999999"
																:min="-99999999"
																:text_center="true"
																@change="changeRoundTotal($event)"
																v-model="round_appraise_total"
															/>
													</div>
												</div>
											</td>
											<td><strong>{{form.price_total_asset ? `${formatNumber(formatCurrent(form.price_total_asset))} đ` : `0 đ`}}</strong></td>
										</tr>
								</tbody>
					</table>
					<!-- <div style="margin-left:10px" class="d-flex mt-2">
						<td @click="printPL3" class="d-flex align-items-center document_action mr-2" >
							<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="document_action mr-2">
							<h3 class="document_action mb-0">Hình ảnh hiện trạng</h3>
						</td>
					</div>
					<div style="margin-left:10px" class="d-flex mt-2">
						<td @click="exportShinhan" class="d-flex align-items-center document_action mr-2" >
							<img src="@/assets/icons/ic_download_2.svg" alt="download_2" class="document_action mr-2">
							<h3 class="document_action mb-0">File import Shinhan</h3>
						</td>
					</div> -->
				</div>
				<div class="card mt-2">
					<div class="card-title text-center">
						<h3 class="title title_input_content">Tài liệu tự động</h3>
					</div>
					<div class="card-body card-info">
						<div class="row input_download_certificate">
							<div class="d-flex align-items-center col">
								<img class="img_input_download" src="@/assets/icons/ic_document.svg" alt="document"/>
								<div class="title_input_content title_input_download cursor_pointer"  @click="printPL1">Bảng điều chỉnh QSDĐ</div>
							</div>
							<div v-if="form.price_tangible_asset" class="d-flex align-items-center col">
								<img class="img_input_download" src="@/assets/icons/ic_document.svg" alt="document"/>
								<div class="title_input_content title_input_download cursor_pointer"  @click="printPL2">Bảng điều chỉnh CTXD</div>
							</div>
							<div class="d-flex align-items-center col">
								<img class="img_input_download" src="@/assets/icons/ic_document.svg" alt="document"/>
								<div class="title_input_content title_input_download cursor_pointer"  @click="printTSSS">Phiếu thu thập TSSS</div>
							</div>
							<div class="d-flex align-items-center col">
								<img class="img_input_download" src="@/assets/icons/ic_document.svg" alt="document"/>
								<div class="title_input_content title_input_download cursor_pointer"  @click="printPL3">Hình ảnh hiện trạng</div>
							</div>
							<div class="d-flex align-items-center col">
								<img class="img_input_download" src="@/assets/icons/ic_document.svg" alt="document"/>
								<div class="title_input_content title_input_download cursor_pointer"  @click="exportShinhan">File import Shinhan</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="btn-footer d-md-flex d-block justify-content-end align-items-center w-100">
			<div class="d-md-flex d-block">
				<button  @click="onCancel" class="btn btn-white text-nowrap" >
					<img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Thoát
				</button>
				<button v-if="editAsset" :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="saveData">
					<img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import InputCurrency from '@/components/Form/InputCurrency'
import InputPercent from '@/components/Form/InputPercent'
import InputText from '@/components/Form/InputText'
import InputNumberNegative from '@/components/Form/InputNumberNegative'
import Certificate from '@/models/Certificate'
import InputNumberFormat from '@/components/Form/InputNumber'
import Vue from 'vue'
import Icon from 'buefy'
import CertificateAsset from '@/models/CertificateAsset'
Vue.use(Icon)
export default {
	name: 'tab_summarize',
	props: ['data', 'idData', 'edit', 'status', 'checkRole', 'editAsset'],
	components: {
		InputText,
		InputCurrency,
		InputPercent,
		InputNumberNoneFormat,
		InputNumberNegative,
		InputNumberFormat
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
			form: JSON.parse(JSON.stringify(this.data)),
			key_render: 6152,
			round_appraise_total: 0,
			isSubmit: false
		}
	},
	mounted () {
		console.log('tổng', this.form.price_land_asset)
		if (this.form && this.form.round_appraise_total) {
			this.round_appraise_total = this.form.round_appraise_total
		} else this.round_appraise_total = 0
		this.key_render += 1
	},
	beforeUpdate () {
	},
	methods: {
		roundPrice (value, roundPrice) {
			if (!value) {
				return value
			}
			if (roundPrice && roundPrice > 0 && roundPrice <= 7) {
				let round = Math.pow(10, roundPrice)
				return Math.ceil(value / round) * round
			} else if (roundPrice && roundPrice < 0 && roundPrice >= -7) {
				let round = Math.pow(10, Math.abs(roundPrice))
				return Math.floor(value / round) * round
			} else return parseInt(Number(value).toFixed(0))
		},
		async printTSSS () {
			let arrayAsset = []
			if (this.data.appraise_has_assets && this.data.appraise_has_assets.length > 0) {
				await this.data.appraise_has_assets.forEach(item => {
					arrayAsset.push(item.asset_general_id)
				})
			}
			const res = await CertificateAsset.getPrintTSS(arrayAsset, this.idData)
			if (res.data) {
				const file = res.data
				const fileLink = document.createElement('a')
				fileLink.href = file.url
				fileLink.setAttribute('download', file.file_name)
				document.body.appendChild(fileLink)
				fileLink.click()
				fileLink.remove()
				window.URL.revokeObjectURL(fileLink)
			} else {
				this.openMessage('Tải file bị lỗi vui lòng gọi hỗ trợ')
			}
		},
		async printPL1 () {
			await CertificateAsset.getPrintPL1(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
				} else {
					this.openMessage('Tải file bị lỗi vui lòng gọi hỗ trợ')
				}
			})
		},
		async printPL2 () {
			await CertificateAsset.getPrintPL2(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
				} else {
					this.$toast.open({
						message: 'Tải file bị lỗi vui lòng gọi hỗ trợ',
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
			)
		},
		async printPL3 () {
			await CertificateAsset.getPrintPL3(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
				} else {
					this.$toast.open({
						message: 'Tải file bị lỗi vui lòng gọi hỗ trợ',
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
			)
		},
		async exportShinhan () {
			await CertificateAsset.getExportShinhan(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
				} else {
					this.$toast.open({
						message: 'Tải file bị lỗi vui lòng gọi hỗ trợ',
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
			)
		},
		onCancel () {
			return this.$router.push({name: 'certification_asset.index'})
		},
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = parseInt(num).toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		changeRoundTotal (event) {
			if (event || event === 0) {
				this.round_appraise_total = parseFloat(event).toFixed(0)
			} else {
				this.round_appraise_total = ''
			}
		},
		formatCurrent (value) {
			if (this.round_appraise_total && this.round_appraise_total > 0 && this.round_appraise_total <= 7) {
				let round = Math.pow(10, this.round_appraise_total)
				return Math.ceil(value / round) * round
			} else if (this.round_appraise_total && this.round_appraise_total < 0 && this.round_appraise_total >= -7) {
				let round = Math.pow(10, Math.abs(this.round_appraise_total))
				return Math.floor(value / round) * round
			} else return value
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		saveData () {
			const round_appraise_total = this.round_appraise_total
			if (round_appraise_total < -7 || round_appraise_total > 7 || (!round_appraise_total && round_appraise_total !== 0)) {
				this.$toast.open({
					message: 'Số làm tròn thuộc khoảng -7 tới 7, vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else this.postDataSummarizationReport(round_appraise_total)
		},
		async postDataSummarizationReport (round_appraise_total) {
			this.isSubmit = true
			const res = await Certificate.postDataSummarize(this.idData, round_appraise_total)
			if (res.data) {
				this.$toast.open({
					message: 'Điều chỉnh bảng tổng hợp kết quả thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
			} else {
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
			this.isSubmit = false
		}
	}

}
</script>
<style scoped lang="scss">
.btn_loading {
    position: relative;
    color: white !important;
    text-shadow: none !important;
    pointer-events: none;
  }
  .btn_loading:after {
    content: '';
    display: inline-block;
    vertical-align: text-bottom;
    border: 1px solid wheat;
    border-right-color: transparent;
    border-radius: 50%;
    color: #ffffff;
    position: absolute;
    width: 1rem;
    height: 1rem;
    left: calc(50% - .5rem);
    top: calc(50% - .5rem);
    -webkit-animation: spinner-border .75s linear infinite;
    animation: spinner-border .75s linear infinite;
  }

.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #FFFFFF;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 20px 25px 10px;
		margin-bottom: 0;
		color: #E8E8E8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007EC6;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-bottom: 0;
		}
	}

	&-body {

		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;

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
.card-status {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #FFFFFF;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;

	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}
.input_download_certificate {
	position:relative;
	border: 1px solid #B6D5F3;
	border-radius: 5px;
	height: 3.85rem;
	padding: 0.85rem 0px;
	margin-bottom: 5px;
}
.title_input_download {
	color: #00507C;
	font-weight: 600;
}
.img_input_download {
	margin-right:10px;
	max-width: 2rem;
}

.img_document_action {
	width: 2rem;
	height: 2rem;
	cursor: pointer;
	background: #FFFFFF;
	min-width: 1.5rem;
	min-height: 1.5rem;
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
  padding: 10px;
  border: none;

  // margin: auto;
  // width: 36px;
  // height: 36px;

  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
  }
}

.btn {
  &-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 100%;
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
  top: 2.1rem;
  background: #FFFFFF;
  height: 2.1rem;
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
  .sub_header_title {
    background-color: #F6F7FB;
    border: 1px solid #E8E8E8;
    border-radius: 3px;
    padding: 0.5rem 2rem;
    position: relative;
    color: #00507C;
    font-weight: 700;

    .label {
      margin-right: 15px;
    }
    label {
      margin: 0;
    }
    &::before {
      content: '';
      position: absolute;
      height: calc(100% - 16px);
      width: 3px;
      background-color: #99D161;
      border-radius: 3px;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
    }
  }
  .sub_header_title-rows {
    padding-top: 10px;
  }
  .footer-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #00507C;
  }
  /deep/ {
    .form-group-container.disabled {
      background-color: rgba(222, 230, 238, 0.3);
      .ant-input {
        background-color: rgba(222, 230, 238, 0.3) !important;
      }
    }
  }
  .container_land {
    padding: unset;
    width: 100%;
    display: flex;
  }
  .name_law {
    @media (max-width: 1600px) {
      width: 250px;
    }
    @media (min-width: 1600px) {
      width: 280px;
    }
    @media (min-width: 1900px) {
      width: 300px;
    }

    white-space: nowrap;
    -webkit-line-clamp: 2 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0;
    text-transform: none;

    &:first-letter {
      text-transform: none;
    }
  }
  .agency_law {
    width: 150px;
    white-space: nowrap;
    -webkit-line-clamp: 2 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0;
    text-transform: none;

    &:first-letter {
      text-transform: none;
    }
  }
.table_legal {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 7%;
        }
        &:nth-child(2) {
          max-width: 300px
        }
        &:last-child{
          width: 10%;
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
 .table_summarize {
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
      tr{
        &:nth-child(4) {
          min-width: 200px;
          color: #3D4D65;
          background-color: rgba(222, 230, 238, 0.2);
        }
        &:nth-child(5) {
          min-width: 200px;
          color: #3D4D65;
          background-color: rgba(222, 230, 238, 0.5);
        }
      }
      td{
        border: 1px solid #CED4DA;
        &:first-child{
          width: 60%;
        }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }
  }

  .row {
  margin-right: unset !important;
  margin-left: unset !important;
}
.document_action {
	cursor: pointer;
	background: #FFFFFF;
}
.cursor_pointer {
	cursor: pointer;
}
</style>
