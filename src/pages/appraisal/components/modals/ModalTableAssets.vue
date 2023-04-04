<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer">
      <div
        class="modal-detail d-flex justify-content-center align-items-center">
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">{{title}}</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="property-detail">
              <div class="container__table">
                <a-table bordered
                         :columns="columnAssets"
                         :data-source="listCertificates"
                         :loading="isLoading"
                         :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }"
                         :rowKey="record => record.id"
                         table-layout="top"
                         :pagination="{
                           ...pagination,
                         }"
                         @change="onPageChange"
                         class="table__import"
                >
                  <template slot="asset" slot-scope="asset">
                    <p :id="asset.id" class="full-address mb-0">{{asset.asset_type.description ? asset.asset_type.description + ',' : ''}} {{asset.doc_no ? 'Số tờ: ' + asset.doc_no + ',' : ''}} {{asset.land_no ? 'Số thửa: ' + asset.land_no + ',' : ''}} {{asset.street ? asset.street.name + ', ' : ''}} {{asset.ward ? asset.ward.name + ', ' : ''}} {{asset.district ? asset.district.name + ', ' : ''}} {{asset.province ? asset.province.name : ''}}</p>
                    <b-tooltip :target="(asset.id).toString()">{{asset.asset_type.description ? asset.asset_type.description + ',' : ''}} {{asset.doc_no ? asset.doc_no + ',' : ''}} {{asset.land_no ? asset.land_no + ',' : ''}} {{asset.street ? asset.street.name + ', ' : ''}} {{asset.ward ? asset.ward.name + ', ' : ''}} {{asset.district ? asset.district.name + ', ' : ''}} {{asset.province ? asset.province.name : ''}}</b-tooltip>
                  </template>
                  <template slot="land" slot-scope="land">
                    <p class="text-none mb-0">{{land.properties && land.properties.length > 0 && land.properties[0].property_detail && land.properties[0].property_detail.length > 0 ? land.properties[0].property_detail[0].land_type_purpose.acronym : ''}} {{land.properties && land.properties.length > 0 && land.properties[0].property_detail && land.properties[0].property_detail.length > 1 ? ', ' + land.properties[0].property_detail[1].land_type_purpose.acronym : ''}}</p>
                  </template>
                  <template slot="area" slot-scope="area">
                    <p class="text-none mb-0">{{ area ? formatArea(area) : 0 }} m <sup>2</sup> </p>
                  </template>
                  <template slot="created_at" slot-scope="created_at">
                    <p class="public_date mb-0">{{ formatDate(created_at) }}</p>
                  </template>
                  <template slot="id" slot-scope="id">
                    <p class="link-detail mb-0">{{ 'TSTD_' + id }}</p>
                  </template>
                </a-table>
              </div>
            </div>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange btn-action-modal" type="button" @click="handleAction" :class="{'btn-loading disabled': isSubmit}"> <img src="../../../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save">Lưu</button>
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"><img src="../../../../assets/icons/ic_cancel.svg"  style="margin-right: 12px" alt="save">Trở lại</button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>

import {BTooltip} from 'bootstrap-vue'
import AppraiseData from '@/models/AppraiseData'
import {convertPagination} from '@/utils/filters'
import moment from 'moment'

export default {
	name: 'ModalDocuments',
	props: ['documents', 'title', 'data', 'certificate_id', 'isHaveAppraiseId'],
	components: {
		'b-tooltip': BTooltip
	},
	data () {
		return {
			isSubmit: false,
			isLoading: false,
			selectedRowKeys: [],
			selectedRows: [],
			listCertificates: [],
			pagination: {}
		}
	},
	computed: {
		columnAssets () {
			return [
				{
					title: 'Mã TSTĐ',
					align: 'left',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id'
				},
				{
					title: 'Loại tài sản',
					align: 'left',
					dataIndex: 'asset_type.description'
				},
				{
					title: 'Mô tả',
					align: 'left',
					scopedSlots: {customRender: 'asset'}
				},
				{
					title: 'Loại đất',
					align: 'left',
					scopedSlots: {customRender: 'land'}
				},
				{
					title: 'Tổng diện tích',
					align: 'right',
					scopedSlots: {customRender: 'area'},
					dataIndex: 'properties[0].appraise_land_sum_area'
				},
				{
					title: 'Người tạo',
					align: 'left',
					dataIndex: 'created_by.name'
				},
				{
					title: 'Ngày tạo',
					align: 'left',
					scopedSlots: {customRender: 'created_at'},
					dataIndex: 'created_at'
				}
			]
		}
	},
	async mounted () {
		await this.getCompanies()
		if (this.documents.length > 0) {
			this.documents.forEach(document => {
				if (document.appraise_id) {
					this.selectedRowKeys.push(document.appraise_id)
				} else this.selectedRowKeys.push(document.id)

				this.selectedRows.push(document)
			})
		}
	},
	methods: {
		async getSelectRows () {
			const ids = this.selectedRowKeys
			if (this.isHaveAppraiseId && this.isHaveAppraiseId.length > 0) {
			}
			const res = await AppraiseData.getAppraisals(ids)
			this.selectedRows = [...res.data]
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async getCompanies (params = {}) {
			this.isLoading = true
			const filter = {}
			let resp = null
			try {
				if (this.certificate_id) {
					resp = await AppraiseData.paginate({ query: { page: 1, limit: 30, status: 2, popup: true, certificate_id: this.certificate_id, ...params, ...filter } })
				} else resp = await AppraiseData.paginate({ query: { page: 1, limit: 30, status: 2, popup: true, ...params, ...filter } })
				if (resp) {
					this.listCertificates = [...resp.data.data]
					this.pagination = convertPagination(resp.data)
					this.isLoading = false
				}
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChange (pagination) {
			this.perPage = pagination.pageSize

			const params = {
				page: pagination.current,
				limit: pagination.pageSize
			}
			await this.getCompanies(params)
		},
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async handleAction (event) {
			await this.getSelectRows()
			this.$emit('cancel', event)
			this.selectedRows.sort()
			this.$emit('action', this.selectedRows)
		}
	}
}
</script>

<style scoped lang="scss">
  .full-address {
    width: 30vw;
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

  .modal-detail {
    position: fixed;
    z-index: 1031;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.6);
    @media (max-width: 768px) {
      padding: 20px;
    }
    .card {
      border-radius: 5px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
      max-width: 1300px;
      width: 100%;
      max-height: 90vh;
      margin-bottom: 0;
      padding: 20px 30px;
      @media (max-width: 768px) {
        padding: 20px 10px;
      }
      &-header {
        border-bottom: 1px solid #DDDDDD;
        h3 {
          color: #333333;
        }
        img {
          cursor: pointer;
        }
      }
      &-body {
        text-align: center;
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
  .title-property{
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 18px;
  }
  .input-contain{
    margin-bottom: 25px;
  }
  .card-table{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    max-width: 99%;
    margin: 50px auto 75px;
  }
  .card-table tbody tr:last-child td, .card-table tbody tr:last-child th{
    border-bottom: 1px solid #E5E5E5 ;
  }
  .card{
    .contain-detail{
      overflow-y: auto;
      overflow-x: hidden;
      margin-top: 20px;
      margin-bottom: 20px;
      &::-webkit-scrollbar{
        width: 2px;
      }
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
    }
    &-info{
      .title{
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .table-property{
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px;
        font-weight: 500;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          border-left: none;
          border-right: none;
        }
        &:last-child{
          border-right: none;
        }
        box-sizing: border-box;
        padding: 20px 14px;
      }
    }
  }
  .btn-delete{
    cursor: pointer;
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border: 0.777778px solid #000000;
    border-radius: 5.88235px;
    padding: 10px;
    margin: auto;
    width: 36px;
    height: 36px;
    img{
      width: 100%;
      height: auto;
    }

  }
  .contain-table{
    overflow-x: auto;
    @media (max-width: 1024px) {
      overflow-y: hidden;
      overflow-x: auto;
    }
    .table-property{
      width: 100%;
    }
  }
  .contain-file{
    display: flex;
    align-items: center;
    h3{
      margin-top: 8px;
      margin-bottom: 0;
    }
  }
  .btn-upload{
    background: #FFFFFF;
    white-space: nowrap;
    border: 1px solid #555555;
    box-sizing: border-box;
    border-radius: 5px;
    padding: 5px 19px;
    font-size: 10px;
  }
  .btn-property{
    padding: 10px;
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  .img-upload{
    margin-left: 20px;
    position: relative;
    width: 123px;
    height: 35px;
    color: #fff;
    background: #FAA831;

    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    display: flex;
    justify-content: center;
    align-items: center;
    box-sizing: border-box;
    cursor: pointer;
    input{
      cursor: pointer !important;
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      width: 100%;
      opacity: 0;
    }
  }
  .contain-img{
    height: auto;
    position: relative;
    .img{
      width: 100%;
    }
    .delete{
      position: absolute;
      top: 0;
      right: 0;
      background: #000000;
      color: #FFFFFF;
      width: 20px;
      height: 20px;
      text-align: center;
      line-height: 1.5;
      cursor: pointer;
      font-weight: 700;
      border-radius: 5px;
    }
  }
  .contain-total{
    &__left{
      color: #000000;
      .num{
        padding: 0 11px 0 24px;
        width: 340px;
        height: 35px;
        line-height: 1.5;
        border-radius: 5px;
        border: 1px solid #555555;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        background: #f1f1f1 !important;
        cursor: not-allowed;
        user-select: none;
        p{
          margin-bottom: 0;
        }
      }
      .name{
        min-width: 175px;
        margin-bottom: 0;
        margin-right: 20px;
      }
    }
  }
  .img-locate{
    cursor: pointer;
    position: absolute;
    right: 15px;
    top: 35px;
  }
  .form-control {
    width: 100%;
  }
  .btn-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 146px;
    color: #fff;
    margin-right: 15px;
    box-sizing: border-box;
    &:hover{
      border-color: #dc8300;
    }
  }
  .container-title{
    margin: -35px -95px auto;
    padding: 35px 95px 0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    @media (max-width: 767px) {
      margin: -20px -10px auto;
      padding: 20px 10px 0;
    }
    .title{
      font-size: 1.2rem;
      margin-bottom: 25px;
      font-weight: 700;
      @media (max-width: 767px) {
        font-size: 1.125rem;
      }
    }
    &__footer{
      margin: auto -95px -35px;
      padding: 20px 95px 30px;
      @media (max-width: 767px) {
        margin: auto -10px -20px;
        padding: 20px 10px 0;
        .btn-white{
          margin-bottom: 20px;
        }
      }
    }
  }
  .contain-img{
    aspect-ratio:1/1;
    overflow: hidden;
    height: auto;
    position: relative;
    text-align: center;
    margin-bottom: 10px;
    .img{
      object-fit: cover;
      margin-right: 0 ;
      width: 100%;
      height: 100%;
      cursor: pointer;
      &-table{
        margin: auto;
        min-width: 50px;
        min-height: 50px;
        width: 50px;
        height: 50px;
        object-fit: cover;
      }
    }
    &__table{
      width: auto;
    }
    .delete{
      position: absolute;
      top: 0;
      right: 0.75rem;
      background: #000000;
      color: #FFFFFF;
      width: 20px;
      height: 20px;
      text-align: center;
      line-height: 1.5;
      cursor: pointer;
      font-weight: 700;
      border-radius: 5px;
    }
  }
  .container-img{
    padding: .75rem 0;
    border: 1px solid #0b0d10;
  }
  .loading{
    display: none;
    &__true{
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 100vh;
      background: rgba(0, 0, 0, 0.62);
      z-index: 100000;
      display: flex;
      align-items: center;
      justify-content: center;
      &.btn-loading{
        &:after{
          width: 2rem !important;
          height: 2rem !important;
        }
      }
    }
  }
  .input-disabled {
    min-height: 30px;
    height: 33px;
  }
  .text-none {
    text-transform: none;
  }
  .full-address {
    width: 200px;
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
  .link-detail {
    color: #FAA831;
    font-weight: 600;
    text-transform: uppercase;
  }
</style>
