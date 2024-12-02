<template>
  <div>
    <div style="margin-bottom: 85px">
      <div class="container__table container__table--detail" v-show="form.report_detail.land.length > 0">
        <a-table bordered
                 :columns="columnsLand"
                 :data-source="form.report_detail.land"
                 :loading="isLoading"
                 :rowKey="(record, index) => 'land' + index"
                 table-layout="top"
                 class="table__import"
        >
          <template slot="area" slot-scope="area">
            <div>{{area ? formatFloat(area) : '-'}}m<sup>2</sup></div>
          </template>
          <template slot="average_unit_price" slot-scope="average_unit_price">
            <div>{{average_unit_price ? format(average_unit_price) + ' đ' : '-'}}</div>
          </template>
          <template slot="average_unit_price_update" slot-scope="average_unit_price_update">
            <div>{{average_unit_price_update ? format(average_unit_price_update) + ' đ' : '-'}}</div>
          </template>
          <template slot="is_update" slot-scope="is_update">
            <div>{{is_update ? 'Có' : 'Không'}}</div>
          </template>
          <template slot="average_unit_difference" slot-scope="average_unit_difference">
            <div>{{average_unit_difference ? format(average_unit_difference) + ' đ' : '-'}}</div>
          </template>
          <template slot="percent_difference" slot-scope="percent_difference">
            <div>{{percent_difference ? format(percent_difference) + ' %' : '-'}}</div>
          </template>
          <template slot="estimate_price" slot-scope="estimate_price">
            <div>{{estimate_price ? format(estimate_price) + ' đ' : '-'}}</div>
          </template>
          <template slot="estimate_price_update" slot-scope="estimate_price_update">
            <div>{{estimate_price_update ? format(estimate_price_update) + ' đ' : '-'}}</div>
          </template>
          <template slot="total_price" slot-scope="total_price">
            <div>{{total_price ? format(total_price) + ' đ' : '-'}}</div>
          </template>
          <template slot="total_price_update" slot-scope="total_price_update, is_update">
            <div>{{total_price_update && is_update.is_update ? format(total_price_update) + ' đ' : '-'}}</div>
          </template>
          <template slot="total_percent_difference" slot-scope="total_percent_difference">
            <div>{{total_percent_difference ? total_percent_difference + ' %' : '-'}}</div>
          </template>
        </a-table>
      </div>
      <div class="container__table container__table--detail mt-5" v-show="form.report_detail.building.length > 0">
        <a-table bordered
                 :columns="columnsBuilding"
                 :data-source="form.report_detail.building"
                 :loading="isLoading"
                 :rowKey="(record, index) => 'building' + index"
                 table-layout="top"
                 class="table__import"
        >
          <template slot="average_unit_price" slot-scope="average_unit_price">
            <div>{{average_unit_price ? format(average_unit_price) + ' đ' : '-'}}</div>
          </template>
          <template slot="area" slot-scope="area">
            <div>{{area ? formatFloat(area) : '-'}}m<sup>2</sup></div>
          </template>
          <template slot="remaining_quality" slot-scope="remaining_quality">
            <div>{{remaining_quality ? remaining_quality + ' %' : '-'}}</div>
          </template>
          <template slot="is_update" slot-scope="is_update">
            <div>{{is_update ? 'Có' : 'Không'}}</div>
          </template>
          <template slot="average_unit_price_update" slot-scope="average_unit_price_update">
            <div>{{average_unit_price_update ? format(average_unit_price_update) + ' đ' : '-'}}</div>
          </template>
          <template slot="average_unit_difference" slot-scope="average_unit_difference">
            <div>{{average_unit_difference ? format(average_unit_difference) + ' đ' : '-'}}</div>
          </template>
          <template slot="percent_difference" slot-scope="percent_difference">
            <div>{{percent_difference ? percent_difference + ' %' : '-'}}</div>
          </template>
          <template slot="estimate_price" slot-scope="estimate_price">
            <div>{{estimate_price ? format(estimate_price) + ' đ' : '-'}}</div>
          </template>
          <template slot="estimate_price_update" slot-scope="estimate_price_update">
            <div>{{estimate_price_update ? format(estimate_price_update) + ' đ' : '-'}}</div>
          </template>
          <template slot="total_price" slot-scope="total_price">
            <div>{{total_price ? format(total_price) + ' đ' : '-'}}</div>
          </template>
          <template slot="total_price_update" slot-scope="total_price_update, is_update">
            <div>{{total_price_update && is_update.is_update ? format(total_price_update) + ' đ' : '-'}}</div>
          </template>
          <template slot="total_percent_difference" slot-scope="total_percent_difference">
            <div>{{total_percent_difference ? total_percent_difference + ' %' : '-'}}</div>
          </template>
        </a-table>
      </div>
      <div class="container__table container__table--property container__table--detail mt-5" v-show="form.report_detail.assets.length > 0">
        <a-table bordered
                 :columns="columnsProperty"
                 :data-source="form.report_detail.assets"
                 :loading="isLoading"
                 :rowKey="(record, index) => 'asset' + index"
                 table-layout="top"
                 class="table__import"
        >
          <template slot="id" slot-scope="id">
            <div class="text-uppercase" >{{id > 6600 ? 'TSS_'+ id : 'TSC_' + id}}</div>
          </template>
          <template slot="version" slot-scope="version">
            <div class="text-uppercase" >{{version ? version : '-'}}</div>
          </template>
        </a-table>
      </div>
      <div class="container__table container__table--property container__table--detail mt-5" v-show="form.ids && form.ids.length > 0">
        <a-table bordered
                 :columns="columnsPreliminaryCode"
                 :data-source="ids"
                 :loading="isLoading"
                 :rowKey="(record, index) => 'ids' + index"
                 table-layout="top"
                 class="table__import"
        >
          <template slot="id" slot-scope="id">
            <div class="text-uppercase" >{{'MSB_' + id}}</div>
          </template>
        </a-table>
      </div>
    </div>
    <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
      <div class="d-md-flex d-block button-contain ">
        <button class="btn btn-white" @click="onCancel" type="button">
          <img class="img" src="../../assets/icons/ic_cancel.svg" alt="cancel">
          Trở về
        </button>
      </div>
      <div class="d-lg-flex d-block button-contain ml-3">
        <button class="btn btn-white" @click="onPrint" type="button">
          <img class="img" src="../../assets/icons/ic_printer.svg" alt="cancel">
          In
        </button>
      </div>
      <div class="d-lg-flex d-block button-contain ml-3" v-if="form.ids === undefined || form.ids === null || form.ids.length === 0">
        <button class="btn btn-white" @click="handlePriceEstimate" type="button">
          <img class="img" src="../../assets/icons/ic_reset.svg" alt="cancel">
          Ước tính lại
        </button>
      </div>
    </div>
    <ModalPrintEstimateLog
      v-if="openPrint"
      v-bind:print_item="form"
      @cancel="openPrint = false"
    />
    <ModalPrintEstimates
      v-if="openModalPrintEstimates"
      @cancel="openModalPrintEstimates = false"
      :dataReport="reports"
      :idLogs="form.id"
      :created="form.report.create_by"
      :date="form.report.create_date"
    />
  </div>
</template>
<script>
import ModalTangibleEstimate from '@/components/Modal/ModalTangibleEstimate'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputSwitch from '@/components/Form/InputSwitch'
import InputText from '@/components/Form/InputText'
import estimateLog from '@/models/estimateLog'
import ToggleSwitch from '@/components/Form/ToggleSwitch'
import Result from '@/pages/log_estimate/Components/Result'
import ResultEstimate from '@/pages/log_estimate/Components/ResultEstimate'
import ModalPrintEstimateLog from '@/components/Modal/ModalPrintEstimateLog'
import ModalPrintEstimates from '@/components/Modal/ModalPrintEstimates'

export default {
	name: 'Detail',
	data () {
		return {
			created_by: '',
			openModalPrintEstimates: false,
			isLoading: false,
			estimatesLand: [],
			ids: [],
			form: {},
			reports: [],
			openPrint: false,
			printDetail: ''
		}
	},
	computed: {
		columnsLand () {
			return [
				{
					title: 'Loại đất',
					align: 'left',
					dataIndex: 'land_type_purpose_name'
				},
				{
					title: 'Loại quy hoạch',
					align: 'left',
					dataIndex: 'type'
				},
				{
					title: 'Diện tích',
					align: 'right',
					scopedSlots: {customRender: 'area'},
					dataIndex: 'area'
				},
				{
					title: 'Đơn giá gốc',
					align: 'right',
					scopedSlots: {customRender: 'average_unit_price'},
					dataIndex: 'average_unit_price'
				},
				{
					title: 'Giá trị chỉnh sửa',
					align: 'right',
					scopedSlots: {customRender: 'average_unit_price_update'},
					dataIndex: 'average_unit_price_update'
				},
				{
					title: 'Chênh lệch',
					align: 'right',
					scopedSlots: {customRender: 'average_unit_difference'},
					dataIndex: 'average_unit_difference'
				},
				{
					title: 'Tỉ lệ',
					align: 'right',
					scopedSlots: {customRender: 'percent_difference'},
					dataIndex: 'percent_difference'
				},
				{
					title: 'Có chỉnh sửa',
					align: 'center',
					scopedSlots: {customRender: 'is_update'},
					dataIndex: 'is_update'
				},
				{
					title: 'Trước điều chỉnh',
					align: 'right',
					scopedSlots: {customRender: 'estimate_price'},
					dataIndex: 'estimate_price'
				},
				{
					title: 'Sau điều chỉnh',
					align: 'right',
					scopedSlots: {customRender: 'estimate_price_update'},
					dataIndex: 'estimate_price_update'
				},
				{
					title: 'Tổng gốc',
					align: 'right',
					scopedSlots: {customRender: 'total_price'},
					dataIndex: 'total_price'
				},
				{
					title: 'Tổng điều chỉnh',
					align: 'right',
					scopedSlots: {customRender: 'total_price_update'},
					dataIndex: 'total_price_update'
				},
				{
					title: 'Chênh lệch tổng',
					align: 'right',
					scopedSlots: {customRender: 'total_percent_difference'},
					dataIndex: 'total_percent_difference'
				}
			]
		},
		columnsBuilding () {
			return [
				{
					title: 'Loại nhà',
					align: 'left',
					dataIndex: 'building_category'
				},
				{
					title: 'Đơn giá xây dựng',
					align: 'right',
					scopedSlots: {customRender: 'average_unit_price'},
					dataIndex: 'average_unit_price'
				},
				{
					title: 'Diện tích',
					align: 'right',
					scopedSlots: {customRender: 'area'},
					dataIndex: 'area'
				},
				{
					title: 'Chất lượng còn lại',
					align: 'right',
					scopedSlots: {customRender: 'remaining_quality'},
					dataIndex: 'remaining_quality'
				},
				{
					title: 'Đơn giá sau điều chỉnh',
					align: 'right',
					scopedSlots: {customRender: 'average_unit_price_update'},
					dataIndex: 'average_unit_price_update'
				},
				{
					title: 'Chênh lệch',
					align: 'right',
					scopedSlots: {customRender: 'average_unit_difference'},
					dataIndex: 'average_unit_difference'
				},
				{
					title: 'Tỉ lệ',
					align: 'right',
					scopedSlots: {customRender: 'percent_difference'},
					dataIndex: 'percent_difference'
				},
				{
					title: 'Có chỉnh sửa',
					align: 'center',
					scopedSlots: {customRender: 'is_update'},
					dataIndex: 'is_update'
				},
				{
					title: 'Trước điều chỉnh',
					align: 'right',
					scopedSlots: {customRender: 'estimate_price'},
					dataIndex: 'estimate_price'
				},
				{
					title: 'Sau điều chỉnh',
					align: 'right',
					scopedSlots: {customRender: 'estimate_price_update'},
					dataIndex: 'estimate_price_update'
				},
				{
					title: 'Tổng gốc',
					align: 'right',
					scopedSlots: {customRender: 'total_price'},
					dataIndex: 'total_price'
				},
				{
					title: 'Tổng điều chỉnh',
					align: 'right',
					scopedSlots: {customRender: 'total_price_update'},
					dataIndex: 'total_price_update'
				},
				{
					title: 'Chênh lệch tổng',
					align: 'right',
					scopedSlots: {customRender: 'total_percent_difference'},
					dataIndex: 'total_percent_difference'
				}
			]
		},
		columnsProperty () {
			return [
				{
					title: 'Mã TSSS',
					align: 'center',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id'
				},
				{
					title: 'Độ tin cậy',
					align: 'center',
					dataIndex: 'reliability'
				},
				{
					title: 'Phiên bản tài sản',
					align: 'center',
					scopedSlots: {customRender: 'version'},
					dataIndex: 'version'
				}
			]
		},
		columnsPreliminaryCode () {
			return [
				{
					title: 'Mã Sơ Bộ',
					align: 'center',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id'
				}
			]
		}
	},
	components: {
		InputCategory,
		InputText,
		InputNumberFormat,
		ModalTangibleEstimate,
		ModalPrintEstimateLog,
		InputSwitch,
		Result,
		ToggleSwitch,
		ResultEstimate,
		ModalPrintEstimates
	},
	created () {
		if ('id' in this.$route.query && this.$route.name === 'log.detail') {
			if (this.$route.meta['detail']) {
				this.form = Object.assign(this.form, {
					...this.$route.meta['detail']
				})
				if (this.form.ids && this.form.ids.length > 0) {
					this.form.ids.forEach(id => {
						this.ids.push(
							{
								id: id
							}
						)
					})
				}
			} else {
				this.$router.push({name: 'page-not-found'})
			}
		} else {
		}
	},
	methods: {
		onCancel () {
			return this.$router.push({name: 'log.index'})
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			this.created_by = profile.data.user.name
		},
		async onPrint () {
			if (this.form.ids === undefined || this.form.ids === null || this.form.ids.length === 0) {
				this.openPrint = true
			} else {
				await this.getEstimateLogs()
				this.openModalPrintEstimates = true
			}
		},
		async getEstimateLogs () {
			const ids = this.form.ids
			const resp = await estimateLog.getEstimateLogs(ids)
			this.reports = resp.data
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async handlePriceEstimate () {
			this.$router.push({
				name: 'price_estimate.log',
				query: {
					id: this.form.id
				}
			}).catch(_ => {
			})
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		const estimate = await estimateLog.find(to.query['id'])
		to.meta['detail'] = estimate.data[0]
		return next()
	},
	async beforeMount () {
		await this.getProfiles()
	}
}
</script>

<style lang="scss" scoped>
.container {
  &__estimate {
    max-width: 1545px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    margin: 0 auto 20px;
    padding: 30px 30px 50px;
    @media (max-width: 767px) {
      padding: 20px;
    }
  }
  &__table {
    padding: 0 20px;
  }
  &__detail{
    max-width: 1027px;
    margin: auto;
  }
  &__cancel{
    padding: 10px 0;
    border-bottom: 1px solid #D0D0D0;
    margin-bottom: 30px;
  }
  &__synthetic{
    margin-bottom: 50px;
  }
  &__total{
    .title{
      margin-right: 15px;
      margin-bottom: 0;
      font-size: 1.125rem;
      color: #000000;
    }
    .input-total{
      height: 40px;
      width: 306px;
      background: #E5E5E5;
      border-radius: 5px;
      padding: 0 10px;
      display: flex;
      align-items: center;
      .total{
        margin-bottom: 0;
      }
    }
  }
  &__input{
    @media (max-width: 1440px) {
      width: auto;
      margin-right: 10px;
    }
  }
}
.table__tangible{
  thead{
    background: #F28C1C;
    text-align: center;
    tr {
      th{
        color: #FFFFFF;

        font-weight: 700;
        text-transform: none;
      }
    }
  }
  tbody{
    text-align: center;
    tr{
      td{
        white-space: nowrap;
        color: #000000;
      }
    }
  }
}
.table{
  width: 100%;
  box-sizing: border-box;
  border: 1px solid #D0D0D0;
  border-radius: 5px;
  overflow-x: auto;
  overflow-y: hidden;
  &__header{
    padding: 7px 20px;
    background: #F28C1C;
    color: #FFFFFF;
    border-radius: 5px 5px 0 0;
    box-sizing: border-box;
    .title{
      margin-bottom: 0;
      font-weight: 700;
      font-size: 1.125rem;
    }
  }
  &__body{
    padding: 0 20px;
  }
  &--margin {
    margin-bottom: 54px;
  }
}
.btn{
  &__add {
    box-shadow: none !important;
    color: #000000;
    font-size: 1.125rem;
    padding-left: 0;
    img{
      margin-right: 5px;
    }
  }
  &-orange{
    font-weight: 600;

    @media (max-width: 767px) {
      margin-bottom: 10px;
    }
  }
  &-cancel{
    color: #999999;
    box-shadow: none !important;
    padding: 0;
  }
  .img {
    height: 17px;
  }
}
.land__input{
  margin-top: 18px;
}
.ic{
  &__delete{
    cursor: pointer;
  }
}
.btn{
  &-orange{
    &__result{
      width: 204px;
    }
  }
}
.table {
  margin-bottom: 35px;
  &__description{
    thead{
      background: #F28C1C;
      text-align: left;
      tr {
        th{
          color: #FFFFFF;

          font-weight: 700;
          text-transform: none;
        }
      }
    }
    tbody{
      text-align: left;
      tr{
        td{
          &:first-child {
            width: 20%;
            font-weight: 700;
          }
          white-space: nowrap;
          color: #000000;
        }
      }
    }
  }
}
.tangible{
  margin-top: 50px;
  .title{
    font-weight: 700;
  }
}
.estimate{
  &__title{
    font-size: 30px;
    text-align: center;
    text-transform: uppercase;
    color: #000000;
    font-weight: 700;
    margin-bottom: 60px;
  }
}
.plot-num{
  margin-left: 30px;
  .name{
    margin-right: 30px;
    font-weight: 700;
  }
}
.input {
  &__num{
    @media (max-width: 767px) {
      min-width: 200px;
    }
  }
}
.front-side {
  margin-bottom: 0;
  margin-right: 10px;
  color: #333333;
  font-weight: 700;
}
.contain-table{
  overflow-x: auto;
  @media (max-width: 767px) {
    overflow-y: hidden;
  }
  .table-property{
    width: 100%;
  }
}
.table-property{
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead{
    th{
      padding: 12px 0;
      font-weight: 500;
      @media (max-width: 418px) {
        padding: 12px;
      }
    }
  }
  tbody{
    td{
      border: 1px solid #E5E5E5;
      &:first-child{
        border-left: none;
        width: 180px
      }
      &:last-child{
        border-right: none;
      }
      box-sizing: border-box;
      padding: 14px;
    }
  }
}
.estimate{
  &__content{
    .name{
      color: #555555;
      margin-bottom: 5px;

      font-weight: 500;
    }
    .detail {
      font-size: 1.125rem;
      color: #000000;
      margin-bottom: 15px;
      font-weight: 600;
      @media (max-width: 767px) {

      }
    }
  }
}
.container__table {
  &--property {
    width: 40%;
  }
}
</style>
