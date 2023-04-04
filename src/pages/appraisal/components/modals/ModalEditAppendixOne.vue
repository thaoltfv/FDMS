<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer">
      <div
        class="modal-detail d-flex justify-content-center align-items-center">
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Chỉnh sửa phụ lục 1</h2>
            </div>
          </div>
          <div class="contain-detail">
            <Tabs :theme="theme" :navAuto="true">
              <TabItem v-for="(item) in form.appraises" :key="item.id" :name="`TSTĐ ${item.appraise_id ? item.appraise_id : item.id}`">
                <ModalAppendixOne :formData="item" @handleInput="hanldeInputChange" :id="item.id"  />
              </TabItem>
            </Tabs>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange" type="button" @click="saveData"><img src="../../../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save">Lưu</button>
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"><img src="../../../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save">Trở lại</button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import {Tabs, TabItem} from 'vue-material-tabs'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputText from '@/components/Form/InputText'
import Certificate from '@/models/Certificate'
import ModalAppendixOne from '@/pages/appraisal/components/modals/ModelAppendixOne.vue'
export default {
	name: 'ModalConstructionUnit',
	props: ['formData'],
	data () {
		return {
			appraises: [],
			form: this.formData !== undefined && this.formData !== '' ? JSON.parse(JSON.stringify(this.formData)) : {},
			isSubmit: false,
			isLoading: false,
			selectedRowKeys: [],
			data_comparison_send: {},
			data_other_comparison_send: {},
			data_delete_other_comparison: {},
			data_price_UBND: {},
			showError: false,
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
			indicativePrice: [],
			otherFactor: 'Yếu tố khác'
		}
	},
	components: {
		InputNumberFormat,
		Tabs,
		TabItem,
		InputText,
		ModalAppendixOne
	},
	computed: {
	},
	mounted () {
	},
	methods: {
		hanldeInputChange (data, id, error, otherData, otherDataDelete, price_UBND) {
			this.data_comparison_send[id] = data
			this.data_other_comparison_send[id] = otherData
			this.data_delete_other_comparison[id] = otherDataDelete
			this.data_price_UBND[id] = price_UBND
			this.showError = error
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			this.$emit('cancel', event)
			this.$emit('action', this.selectedRowKeys)
		},
		async saveData () {
			let dataSave = {}
			await this.form.appraises.forEach(appraise => {
				dataSave[appraise.id] = []
				this.data_comparison_send[appraise.id].forEach(data => {
					data.comparison_factor.forEach(factor => {
						dataSave[appraise.id].push(factor)
					})
				})
			})
			if (this.showError) {
				this.$toast.open({
					message: 'Mức giá chỉ dẫn đang âm , vui lòng kiểm tra lại',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			} else await this.postData(dataSave, this.data_other_comparison_send, this.data_delete_other_comparison, this.data_price_UBND)
		},
		async postData (dataSave, other_comparison, delete_other_comparison, price_UBND) {
			const res = await Certificate.postDataCertificate(dataSave, other_comparison, delete_other_comparison, price_UBND)
			if (res.data) {
				this.$toast.open({
					message: 'Điều chỉnh phụ lục 1 thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$emit('cancel')
				this.$emit('action', this.formData.id)
			}
		},
		changeLegalRate (e, item, index) {
			if (e) {
				item.comparison_factor[index].adjust_percent = e
			} else {
				item.comparison_factor[index].adjust_percent = 0
			}
			// this.getIndicativePrice()
		}
	}
}
</script>

<style scoped lang="scss">
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
      max-width: 1400px;
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
      &::-webkit-scrollbar {
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
    margin: -20px -30px auto;
    padding: 25px 30px 0;
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
      margin: auto -30px -20px;
      padding: 20px 30px 20px;
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
  .text-none{
    text-transform: none;
  }
  .table-property {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      tr {
        border-radius: 0 5px 5px 0;
      }
      th {
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
        @media (max-width: 787px) {
          padding: 12px;
        }
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          border-left: none;
        }
        padding: 20px 14px;
      }
    }
    &__order {
      tbody{
        td{
          &:first-child{
            width: 40%;
          }
          &:last-child{
            width: 70px;
          }
          padding: 20px 70px;
          @media (max-width: 1023px) {
            padding: 20px 30px;
          }
        }
      }
    }
  }
  .container-table {
    border-radius: 5px;
    border: 1px solid #F3F2F7;
  }
  ::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 12px !important;
  }
  ::-webkit-scrollbar-thumb {
    border-radius: 20px;
    background-color: rgba(0, 0, 0, .5);
    box-shadow: 0 0 1px rgba(255, 255, 255, .5);
  }
</style>
