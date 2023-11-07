<template>
	<div>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Pháp lý tài sản</h3>
					<img class="img-dropdown" :class="!showDetailLegal ? 'img-dropdown__hide' : ''"
						alt="dropdown" src="@/assets/images/icon-btn-down.svg"
						@click="() => { showDetailLegal = !showDetailLegal;  }">
				</div>
			</div>
			<div class="card-body card-info" v-show="showDetailLegal">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="main-wrapper">
								<div class="responsive-table">
									<table class="table_legal color_content">
										<thead>
											<tr>
												<th>Mã số</th>
												<th>Loại pháp lý</th>
												<th class="d-none d-lg-table-cell">Số pháp lý</th>
												<th class="d-none d-lg-table-cell">Ngày pháp lý</th>
												<th class="d-none d-xl-table-cell">Nội dung</th>
												<th>Cơ quan cấp, xác nhận</th>
												<th> </th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="(item, indexLaw) in data.law" :key="indexLaw">
												<td><p class="mb-0">{{ item.id ? `PL_${item.id}` : ''}}</p></td>
												<td>
													<p :id="`namelaw${item.id}`" class="name_law">{{ item.law ? item.law.content : item.description }}</p>
													<b-tooltip :target="('namelaw' + item.id).toString()">{{ item.law ? item.law.content : item.description}}</b-tooltip>
												</td>
												<td class="d-none d-lg-table-cell"><p class="mb-0">{{ item.date }}</p></td>
												<td class="d-none d-lg-table-cell"><p class="mb-0">{{ item.law_date ? formatDate(item.law_date) : '' }}</p></td>
												<td class="d-none d-xl-table-cell">
													<p :id="`content${item.id}`" class="name_law">{{ item.content }}</p>
													<b-tooltip :target="('content' + item.id).toString()">{{ item.content }}</b-tooltip>
												</td>
												<td>
													<p :id="`agency${item.id}`" class="agency_law">{{ item.certifying_agency }}</p>
													<b-tooltip :target="('agency' + item.id).toString()">{{ item.certifying_agency }}</b-tooltip>
												</td>
												<td>
													<div class="d-flex justify-content-center">
														<button class="btn-delete" type="button" @click="handleEditLegal(indexLaw)"><img src="@/assets/icons/ic_eye.svg" alt="add"/></button>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<ModalStep4LegalDetail
			:data="form"
			v-if="showModalActionLegal"
			:juridicals="juridicals"
			@cancel="showModalActionLegal = false"
		/>
	</div>
</template>

<script>
import ModalStep4LegalDetail from './modals/ModalStep4LegalDetail'
import { BDropdown, BDropdownItem, BTooltip, BButtonGroup, BAlert } from 'bootstrap-vue'
import Vue from 'vue'
import Icon from 'buefy'
import moment from 'moment'

Vue.use(Icon)
export default {
	name: 'Step4',
	props: ['data', 'juridicals', 'provinceName'],
	components: {
		BAlert,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip,
		'b-button-group': BButtonGroup,
		ModalStep4LegalDetail
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
			showDetailLegal: true,
			showModalActionLegal: false,
			showAddLegal: true,
			isAddLegal: false,
			isEditLegal: false,
			indexEdit: '',
			form: {
				appraise_law_id: '',
				date: '',
				law_date: '',
				description: '',
				legal_name_holder: '',
				certifying_agency: '',
				origin_of_use: '',
				content: '',
				duration: '',
				law: '',
				land_details: [
					{
						doc_no: '',
						land_no: ''
					}
				],
				purpose_details: [
                    {
                        land_type_purpose_id: '',
                        total_area: ''
                    }
                ],
                note: ''
			},
			GPXDType: [
				{ id: 1, description: 'Có giấy phép' },
				{ id: 0, description: 'Không có giấy phép' }
			],
			contentRows: 3
		}
	},
	methods: {
		formatDate (date) {
			return moment(date).format('DD/MM/YYYY')
		},
		handleEditLegal (index) {
			this.showModalActionLegal = true
			this.isEdit = true
			this.indexEdit = index
			this.form = JSON.parse(JSON.stringify(this.data.law[index]))
			if (this.form.law_date) {
				this.form.law_date = moment(this.form.law_date).format('DD/MM/YYYY')
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
		padding: 15px;
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
	// padding: 10px;
	border: none;
	// margin: auto;
	// width: 36px;
	// height: 36px;

	img {
		max-width: 1.5rem;
		min-width: 1rem;
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
		width: 100px;
		color: #fff;
		margin: 15px 0 0;
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

	.m-h-75 {
		min-height: 75px;
		max-height: 75px;
	}
</style>
