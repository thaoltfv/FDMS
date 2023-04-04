<template>
  <div class="table-wrapper">
    <div class="table-detail position-relative empty-data">
      <a-table
        bordered
        class="table-property"
        @change="handleTableChange"
        :columns="columns"
        :data-source="listCertificates"
        :loading="isLoading"
        :rowKey="record => record.id"
        :filtered="false"
        :row-class-name="(_record, index) => (index % 2 === 1 ? 'table-striped' : null)"
        :pagination="false"
      >
        <!--Custom type table-->
        <template slot="id" slot-scope="id, property">
          <div class="position-relative">
            <button @click.prevent="handleDetail(id, property)" class="link-detail">
              {{ "HSTD_" + id }}
            </button>
          </div>
        </template>
        <template slot="status" slot-scope="status">
          <div class="d-flex justify-content-center align-items-center position-relative">
            <div v-if="status === 1" class="status-color bg-info" />
            <div v-if="status === 2" class="status-color bg-primary" />
            <div v-if="status === 3" class="status-color bg-warning" />
            <div v-if="status === 4" class="status-color bg-success" />
            <div v-if="status === 5" class="status-color bg-secondary" />
            <b-dropdown class="dropdown-container" no-caret>
              <template #button-content>
                <img src="@/assets/icons/ic_more.svg" alt=''>
              </template>
              <b-dropdown-item>Action</b-dropdown-item>
            </b-dropdown>
          </div>
        </template>
        <template slot="created_at" slot-scope="created_at">
          <p class="public_date">
            {{ formatDate(created_at) }}
          </p>
        </template>
        <template slot="certificate_date" slot-scope="{ certificate_date, certificate_num }">
          <p class="text-main">
            {{ certificate_num }}
          </p>
          <p class="text-secondary">
            {{ certificate_date ? 'Ngày: ' + formatDate(certificate_date) : '' }}
          </p>
        </template>
        <template slot="document_date" slot-scope="{ document_date, document_num }">
          <p class="text-main">
            {{ document_num }}
          </p>
          <p class="text-secondary">
            {{ document_date ? 'Ngày: ' + formatDate(document_date) : '' }}
          </p>
        </template>
        <template slot="detail_appraise" slot-scope="detail_appraise">
          <p :id="`content${detail_appraise.id}`" class="appraise_detail text-none"><pre class="pre_detail">{{ showDetailAppraise(detail_appraise) }}</pre></p>
          <b-tooltip v-if="showDetailAppraise(detail_appraise)" class="text-none" :target="('content' + detail_appraise.id).toString()"><pre>{{ showDetailAppraise(detail_appraise) }}</pre></b-tooltip>
        </template>

        <template slot="total_asset_price" slot-scope="{ total_price, appraise_purpose }">
          <p class="text-main__blue">
            {{total_price ? formatNumber(total_price) + " đ" : '-' }}
          </p>
          <p class="text-secondary">
            Mục đích: {{ appraise_purpose ? appraise_purpose.name : '-' }}
          </p>
        </template>
        <template slot="petitioner_name" slot-scope="petitioner_name">
          <p class="text-main text-wrap">
            {{ petitioner_name }}
          </p>
        </template>
        <!-- <template slot="appraise_land_sum_area" slot-scope="appraise_land_sum_area">
          <p class="text-none">
            {{
                appraise_land_sum_area ? formatNumber(appraise_land_sum_area) : 0
            }}
            m
            <sup>2</sup>
          </p>
        </template> -->
        <!-- <template slot="total_construction_area" slot-scope="total_construction_area">
          <p class="text-none">
            {{
                total_construction_area
                  ? formatNumber(total_construction_area)
                  : 0
            }}
            m
            <sup>2</sup>
          </p>
        </template> -->
        <template slot="created_by" slot-scope="{ created_by, created_at }">
          <p class="text-main">
            {{ created_by ? created_by.name : ' ' }}
          </p>
          <p class="text-secondary">
            Ngày tạo: {{created_at ? formatDate(created_at) : ' ' }}
          </p>
        </template>
        <template slot="appraiser" slot-scope="{ appraiser, appraiser_perform }">
          <p class="text-main">
            CV: {{ appraiser_perform ? appraiser_perform.name : '-' }}
          </p>
          <p class="text-secondary">
            TĐV: {{ appraiser ? appraiser.name : '-' }}
          </p>
        </template>
      </a-table>
      <div class="pagination-wrapper">
        <div class="page-size">
          Hiển thị
          <a-select ref="select" :value="Number(pagination.pageSize)" style="width: 71px" :options="pageSizeOptions"
            @change="onSizeChange" />
          hàng
        </div>
        <a-pagination :current="Number(pagination.current)" :page-size="Number(pagination.pageSize)"
          :total="Number(pagination.total)"
          :show-total="(total, range) => `Kết quả hiển thị ${range[0]} - ${range[1]} của ${pagination.total} tài sản`"
          @change="onPaginationChange">
        </a-pagination>
      </div>
    </div>
  </div>
</template>
<script>
import { BDropdown, BDropdownItem, BTooltip } from 'bootstrap-vue'
import moment from 'moment'
import Certificate from '@/models/Certificate'
export default {
	name: 'Tables',
	props: ['listCertificates', 'pagination', 'isLoading'],
	data () {
		return {
			selectedRowKeys: [],
			activeStatus: false,
			pageSizeOptions: [
				{ value: '10', label: '10' },
				{ value: '20', label: '20' },
				{ value: '30', label: '30' }
			]
		}
	},
	components: {
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip
	},
	computed: {
		columns () {
			let dataColumn = [
				{
					title: 'Mã HSTD',
					align: 'left',
					scopedSlots: { customRender: 'id' },
					dataIndex: 'id',
					// sorter: (a, b) => a.id - b.id,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Mã TSTĐ',
					class: 'optional-data',
					align: 'left',
					scopedSlots: { customRender: 'detail_appraise' },
					// sorter: (a, b) => a.document_num - b.document_num,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Số hợp đồng',
					align: 'left',
					scopedSlots: { customRender: 'document_date' },
					// sorter: (a, b) => a.document_num - b.document_num,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Số chứng thư',
					class: 'optional-data',
					align: 'left',
					scopedSlots: { customRender: 'certificate_date' },
					// sorter: (a, b) => a.certificate_num.length - b.certificate_num.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Khách hàng',
					align: 'left',
					scopedSlots: { customRender: 'petitioner_name' },
					dataIndex: 'petitioner_name',
					// sorter: (a, b) => a.petitioner_name - b.petitioner_name,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tổng giá trị (VNĐ)',
					align: 'left',
					scopedSlots: { customRender: 'total_asset_price' },
					// sorter: (a, b) => a.total_asset_price - b.total_asset_price,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tổ thẩm định',
					align: 'left',
					scopedSlots: { customRender: 'appraiser' },
					// sorter: (a, b) => a.total_asset_price - b.total_asset_price,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Người tạo',
					class: 'optional-data',
					align: 'left',
					scopedSlots: { customRender: 'created_by' },
					// sorter: (a, b) => a.created_by.name.length - b.created_by.name.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Trạng thái',
					align: 'center',
					scopedSlots: { customRender: 'status' },
					dataIndex: 'status',
					// filters: [
					//   { text: 'Mới', value: 2 },
					//   { text: 'Đang duyệt', value: 3 },
					//   { text: 'Đã duyệt', value: 4 },
					//   { text: 'Đã hủy', value: 5 }
					// ],
					// onFilter: (value, record) => record.status === value,
					// sorter: (a, b) => a.status - b.status,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				}
			]
			return dataColumn.filter(item => item.hiddenItem === false)
		}
	},
	beforeMount () {
		this.getProfiles()
	},
	methods: {
		showDetailAppraise (data) {
			let arconymText = ''
			if (data.detail_list_id && data.detail_list_id.length > 0) {
				data.detail_list_id.forEach((item, index) => {
					if (index === 0) {
						if (data.document_type && data.document_type[0] === 'KHAC') {
							arconymText = `DS_${item}`
						} else if (data.document_type && data.document_type[0] === 'DS') {
							arconymText = `DS_${item}`
						} else {
							arconymText = `BDS_${item}`
						}
					} else {
						if (data.document_type && data.document_type[0] === 'KHAC') {
							arconymText += `\nDS_${item}`
						} else if (data.document_type && data.document_type[0] === 'DS') {
							arconymText += `\nDS_${item}`
						} else {
							arconymText += `\nBDS_${item}`
						}
					}
				})
				return arconymText
			} else return ''
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (
				profile.data.user.roles[0].name === 'ADMIN' ||
        profile.data.user.roles[0].name === 'ROOT_ADMIN'
			) {
				this.activeStatus = true
			}
		},
		handleTableChange (pagination, filters, sorter) {
			this.$emit('handleChange', pagination, 'All', filters, sorter)
		},
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		async openPrint (id) {
			this.isSubmit = true
			await Certificate.getPrint(id).then(resp => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
					this.isSubmit = false
				}
			})
		},
		async openPrintAppendix (id) {
			this.isSubmit = true
			await Certificate.getPrintAppendix(id).then(resp => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
					this.isSubmit = false
				}
			})
		},
		async openPrintImage (id) {
			this.isSubmit = true
			await Certificate.getPrintImage(id).then(resp => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
					this.isSubmit = false
				}
			})
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		formatNumber (num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async handleDetail (id) {
			this.$router
				.push({
					name: 'certification_brief.detail',
					query: {
						id: id
					}
				})
				.catch(_ => { })
		},
		onSizeChange (pageSize) {
			const pagination = { ...this.pagination, pageSize: Number(pageSize) }
			this.handleTableChange(pagination)
		},
		onPaginationChange (current) {
			const pagination = { ...this.pagination, current: Number(current) }
			this.handleTableChange(pagination)
		}
	}
}
</script>

<style scoped lang="scss">
// .total {
//   color: #000000;
//   bottom: 17px;
//   right: 0;

//   @media (max-width: 418px) {
//     position: relative !important;
//     text-align: center;
//     margin-top: 20px;
//   }
// }

// .full-address {
//   width: 200px;
//   white-space: nowrap;
//   -webkit-line-clamp: 2 !important;
//   overflow: hidden;
//   text-overflow: ellipsis;
//   margin-bottom: 0;
//   text-transform: none;

//   &:first-letter {
//     text-transform: none;
//   }
// }

 /deep/
.optional-data {
	@media (max-width: 1024px) {
		display: none;
	}
}

.text-none {
  text-transform: none;
}

.text-main {
  font-weight: 500;
  text-transform: capitalize;
  margin-bottom: 0.5rem;

  &__blue {
    color: #007EC6;
  }
}

.text-secondary {
  font-weight: 500;
  font-size: 12px !important;

  @media (max-height: 660px){
      font-size: 12px !important;
  }

  @media (max-height: 800px) and (min-height: 660px) {
      font-size: 14px !important;
  }

  @media (max-height: 970px) and (min-height: 800px) {
      font-size: 14px !important;
  }
  color: #617F9E;
  text-transform: none;
  margin-bottom: 0;
}

.status {
  // color: #2d9000;
  margin-bottom: 0;

  &.red {
    color: red;
  }

  &.orange {
    color: #FAA831;
  }
}

.status-color {
  width: 14px;
  height: 14px;
  border-radius: 3px;
  margin: auto;
}

.link-detail {
  white-space: nowrap;
  text-transform: uppercase;
  background: transparent;
  border: none;

  &:hover,
  &:focus,
  &:active {
    color: #faa831;
    border: none;
    outline: none;
  }
}

.dropdown-container {
  border-radius: 2px;
  position: absolute;
  right: 0;

  img {
    padding: 7px;
  }
}

.dropdown-item-container {
  color: #555555;
  text-transform: none;

  img {
    width: 30px;
    margin-right: 10px;
  }
}

.table-wrapper {
  .ant-table-filter-dropdown-btns {
    background-color: white !important;
  }

  .ant-table-filter-dropdown-link.confirm {
    color: red;
  }

  /deep/ .ant-table-wrapper .ant-spin-container .ant-table {
    border: 1px solid #DEE6EE;
  }

  /deep/ .ant-table-column-title {
    color: #00507C;
  }

  /deep/ .table-striped td {
    background-color: #F6F7FB;
    border-color: #DEE6EE;
    border-width: 0;
  }

  /deep/ .ant-table-tbody,
  /deep/ .ant-table-body {
    box-shadow: none;
  }

  .pagination-wrapper {
    margin-top: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;

    .ant-select {
      margin-left: 11px;
      margin-right: 11px;
    }

    .page-size {
      display: flex;
      align-items: center;
      margin-right: 20px;

			@media (max-width: 1024px) {
				display: none;
			}
    }

    .ant-pagination {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      row-gap: 10px;

      /deep/ .ant-pagination-total-text {
        height: unset;
        flex-grow: 1;

			@media (max-width: 1024px) {
				display: none;
			}
      }

      /deep/ .ant-pagination-item-active {
        background: #007EC6;

        a {
          color: #FFFFFF;
        }
      }

      /deep/ .ant-pagination-prev,
      /deep/ .ant-pagination-next {
        border: 1px solid #d9d9d9;

        &:hover {
          border-color: #1890ff;
          transition: all 0.3s;
        }

        a:hover {
          i {
            color: #1890ff;
          }
        }
      }
    }

    @media (max-width: 1024px) {
      flex-direction: column;
      gap: 20px;
    }
  }
}
.appraise_detail {
		@media (max-width: 1600px) {
			max-height: 60px;
		}
		@media (min-width: 1600px) {
			max-height: 60px;
		}
		@media (min-width: 1900px) {
			max-height: 60px;
		}
    min-width: 120px;
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
.pre_detail {
  margin: unset;
  font-family: unset;
}
</style>
