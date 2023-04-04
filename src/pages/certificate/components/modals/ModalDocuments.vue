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
              <div class="container__table container__table--detail">
                <a-table bordered
                         :columns="columnDocuments"
                         :data-source="documents"
                         :loading="isLoading"
                         :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }"
                         :rowKey="record => record.id"
                         table-layout="top"
                         class="table__import"
                >
                  <template slot="document_type" slot-scope="document_type">
                    <p class="text-none mb-0">{{ document_type }}</p>
                  </template>
                  <template slot="date" slot-scope="date">
                    <p class="text-none mb-0">{{ date }}</p>
                  </template>
                  <template slot="content" slot-scope="content, id">
                    <p :id="id.id" class="full-address text-none mb-0">{{ content }}</p>
                    <b-tooltip :target="(id.id).toString()">{{ content }}</b-tooltip>
                  </template>
                </a-table>
              </div>
            </div>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange btn-action-modal" type="button" @click="handleAction" :class="{'btn-loading disabled': isSubmit}"> <img src="../../../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"> Lưu</button>
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

export default {
	name: 'ModalDocuments',
	props: ['documents', 'title', 'data'],
	components: {
		'b-tooltip': BTooltip
	},
	data () {
		return {
			isSubmit: false,
			isLoading: false,
			selectedRowKeys: []
		}
	},
	mounted () {
		this.selectedRowKeys = []
		this.data.forEach(id => {
			this.selectedRowKeys.push(id.appraise_law_id)
		})
	},
	computed: {
		columnDocuments () {
			return [
				{
					title: 'Loại văn bản',
					align: 'left',
					scopedSlots: {customRender: 'document_type'},
					dataIndex: 'document_type'
				},
				{
					title: 'Số, ngày',
					align: 'left',
					scopedSlots: {customRender: 'date'},
					dataIndex: 'date'
				},
				{
					title: 'Nội dung văn bản',
					align: 'left',
					scopedSlots: {customRender: 'content'},
					dataIndex: 'content'
				}
			]
		}
	},
	methods: {
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			this.$emit('cancel', event)
			this.$emit('action', this.selectedRowKeys)
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
</style>
