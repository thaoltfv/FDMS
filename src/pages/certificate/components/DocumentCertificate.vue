<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <h3 class="title">Tài liệu thẩm định</h3>
          <!-- <button type="button" class="btn btn-orange mt-0 w-auto ml-2">Xuất dữ liệu</button>
          <button type="button" class="btn btn-orange mt-0 ml-2 w-auto text-nowrap">Xóa dữ liệu đã xuất</button> -->
        </div>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard">
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">PL 1: Bảng điều chỉnh QSDĐ</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrint">Xem</button>
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" :class="{ 'btn_loading disabled': isSubmit }" v-if="(status === 1 || status === 2) && edit && checkRole" @click="openModalAppendix">Chỉnh sửa</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrint">Tải xuống</button>
        </div>
      </div>
      <div v-if="form.asset_type_id !== 37" class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">PL 2: Bảng điều chỉnh CTXD</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintAppendix">Xem</button>
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" :class="{ 'btn_loading disabled': isSubmit }" v-if="(status === 1 || status === 2) && edit && checkRole" @click="openModalAppendixTwo">Chỉnh sửa</button>
         <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintAppendix">Tải xuống</button>
        </div>
      </div>
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">PL 3: Hình ảnh hiện trạng tài sản</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintImage">Xem</button>
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" v-if="(status === 1 || status === 2) && edit && checkRole" >Chỉnh sửa</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintImage">Tải xuống</button>
        </div>
      </div>
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">Tổng hợp kết quả</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintReport">Xem</button>
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowSummarizationDetail" v-if="(status === 1 || status === 2) && edit && checkRole">Chỉnh sửa</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintReport">Tải xuống</button>
        </div>
      </div>
    </div>
    <ModalShowPrint
      v-if="isShowPrint"
      @cancel="isShowPrint = false"
      :filePrint="filePrint"
      :title="title"
    />
    <ModalEditAppendixOneCert
      v-if="modalAppendixOne"
      :formData="form"
      @cancel="cancelModalAppendix"
      @action="getDataAppraise"
    />
    <ModalEditAppendixTwoCert
      v-if="modalAppendixTwo"
      :formData="form"
      @cancel="cancelModalAppendixTwo"
      @action="getDataAppraise"
    />
    <ModalSummarizationAppraise
      v-if="modalSummarizationDetail"
      :formData="form"
      @cancel="cancelModalSummarization"
      @action="getDataSummarization"
    />
    <ModalNotificationDocumentDetail
      v-if="isNotHasProperty"
      :notification= "messageWarning"
      @cancel="handleCancelWarning"
      @action="handleActionRouteToEdit"
    />
  </div>
</template>

<script>

import ModalShowPrint from '@/pages/appraisal/components/modals/ModalShowPrint'
import ModalNotificationDocumentDetail from '@/components/Modal/ModalNotificationDocumentDetail'
import ModalEditAppendixOneCert from '@/pages/certificate/components/modals/ModalEditAppendixOneCert'
import ModalEditAppendixTwoCert from '@/pages/certificate/components/modals/ModalEditAppendixTwoCert'
import ModalSummarizationAppraise from '@/pages/certificate/components/modals/ModalSummarizationAppraise'
import Certificate from '@/models/Appraise'
import AppraiseData from '@/models/AppraiseData'
export default {
	name: 'DocumentAppraisal',
	props: ['idData', 'formData', 'routerName', 'status', 'checkRole'],
	computed: {
		optionsAppraiser () {
			return {
				data: this.appraisers,
				id: 'id',
				key: 'name'
			}
		}
	},
	components: {
		ModalShowPrint,
		ModalEditAppendixOneCert,
		ModalEditAppendixTwoCert,
		ModalSummarizationAppraise,
		ModalNotificationDocumentDetail
	},
	data () {
		return {
			modalAppendixOne: false,
			modalAppendixTwo: false,
			isShowPrint: false,
			openModalMapAsset: false,
			open_detail: false,
			showCard: true,
			name: '',
			openModalMap: false,
			directions: [],
			form: this.formData ? this.formData : {},
			pic: [],
			property: null,
			propertyIndex: null,
			index: null,
			title: null,
			filePrint: null,
			formDataEdit: [],
			modalSummarizationDetail: false,
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			isNotHasProperty: false,
			openWarningProperty: false,
			messageWarning: '',
			isSubmit: false
		}
	},
	created () {
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		permission.forEach(value => {
			if (value === 'VIEW_PRICE') {
				this.view = true
			}
			if (value === 'ADD_PRICE') {
				this.add = true
			}
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
			if (value === 'DELETE_PRICE') {
				this.deleted = true
			}
			if (value === 'ACCEPT_PRICE') {
				this.accept = true
			}
		})
	},
	methods: {
		async getDataSummarization (id) {
			this.isSubmit = await true
			const res = await AppraiseData.find(id)
			if (res && res.data) {
				this.form = res.data
			} else {
				console.log('error-refresh-data')
			}
			this.isSubmit = await false
		},
		async getDataAppraise (id) {
			this.isSubmit = await true
			const res = await AppraiseData.find(id)
			if (res && res.data) {
				this.form = res.data
			} else {
				console.log('error-refresh-data')
			}
			this.isSubmit = await false
			// this.$emit('refresh_action', id)
		},
		async openPrint () {
			this.isSubmit = true
			await Certificate.getPrint(this.idData).then((resp) => {
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
			}
			)
		},
		async openPrintAppendix () {
			this.isSubmit = true
			await Certificate.getPrintAppendix(this.idData).then((resp) => {
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
			}
			)
		},
		async openPrintImage () {
			this.isSubmit = true
			await Certificate.getPrintImage(this.idData).then((resp) => {
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
			}
			)
		},
		async openPrintReport () {
			this.isSubmit = true
			await Certificate.getPrintReport(this.idData).then((resp) => {
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
			}
			)
		},
		async openPrintProof () {
			this.isSubmit = true
			await Certificate.getPrintProof(this.idData).then((resp) => {
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
			}
			)
		},
		async openModalPrint () {
			await Certificate.getPrint(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					this.filePrint = file.url
				}
			})
		},
		async openModalPrintAppendix () {
			await Certificate.getPrintAppendix(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					this.filePrint = file.url
				}
			}
			)
		},
		async openModalPrintImage () {
			await Certificate.getPrintImage(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					this.filePrint = file.url
				}
			}
			)
		},
		async openModalPrintReport () {
			await Certificate.getPrintReport(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					this.filePrint = file.url
				}
			}
			)
		},
		async openModalPrintProof () {
			await Certificate.getPrintProof(this.idData).then((resp) => {
				const file = resp.data
				if (file) {
					this.filePrint = file.url
				}
			}
			)
		},
		openModalAppendix () {
			this.isNotHasProperty = false
			// khai báo biến check TSSS
			let arrayCheck = []
			// khai báo biến show TSSS đã thay đổi
			let arrayHasChangeAsset = []
			this.formData.asset_general.forEach(itemAsset => {
				// check property của TSSS dựa trên appraise_has_assets có tồn tại không?
				this.formData.appraise_has_assets.forEach(item => {
					itemAsset.properties.forEach(itemProperty => {
						if (item.asset_general_id === itemAsset.id && item.asset_property_detail_id === itemProperty.id) {
							arrayCheck.push(itemProperty)
						}
					})
					// else if (item.asset_general_id === itemAsset.id && item.asset_property_detail_id === itemProperty.id) {
					//   itemAsset.version.forEach(itemVersion => {
					//     if (itemVersion.version > item.version) {
					//       // show warning
					//       // show 2 nút
					//     }
					//   })
					// }
				})
			})
			// nếu arrayCheck của TSSS không đủ sẽ show dialog
			if (arrayCheck.length < 3) {
				this.isNotHasProperty = true
				this.modalAppendixOne = false
				this.formData.asset_general.forEach(asset => {
					let check = arrayCheck.filter(item => item.asset_general_id === asset.id)
					if (check && check.length === 0) {
						arrayHasChangeAsset.push(asset.id)
					}
				})
				this.messageWarning = `Tài sản so sánh ${arrayHasChangeAsset} đã thay đổi thông tin, vui lòng cập nhật lại TSSS để có dữ liệu mới nhất`
				document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			} else {
				this.modalAppendixOne = true
				document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			}
		},
		handleCancelWarning () {
			this.isNotHasProperty = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		async cancelModalAppendix (id) {
			this.modalAppendixOne = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			const res = await AppraiseData.find(id)
			if (res && res.data) {
				this.form = res.data
			} else {
				console.log('error-refresh-data')
			}
		},
		handleActionRouteToEdit () {
			this.isNotHasProperty = false
			this.$emit('changeRouteToEdit')
		},
		cancelModalSummarization () {
			this.modalSummarizationDetail = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		openModalAppendixTwo () {
			this.modalAppendixTwo = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		cancelModalAppendixTwo () {
			this.modalAppendixTwo = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		handleShowSummarizationDetail () {
			this.modalSummarizationDetail = true
		},
		async handleShowPrint () {
			this.isSubmit = true
			await this.openModalPrint()
			this.isSubmit = false
			this.title = 'Phụ lục 1 Bảng điều chỉnh QSDD-2022'
			this.isShowPrint = true
		},
		async handleShowPrintAppendix () {
			this.isSubmit = true
			await this.openModalPrintAppendix()
			this.isSubmit = false
			this.title = 'Phụ lục 2 Bảng điều chỉnh CTXD-2022'
			this.isShowPrint = true
		},
		async handleShowPrintImage () {
			this.isSubmit = true
			await this.openModalPrintImage()
			this.isSubmit = false
			this.title = 'Phụ lục hình ảnh QSDD và CTXD-2022'
			this.isShowPrint = true
		},
		async handleShowPrintReport () {
			this.isSubmit = true
			await this.openModalPrintReport()
			this.isSubmit = false
			this.title = 'Báo cáo QSDD và CTXD-2022'
			this.isShowPrint = true
		},
		async handleShowPrintProof () {
			this.isSubmit = true
			await this.openModalPrintProof()
			this.isSubmit = false
			this.title = 'Chứng thư QSDD và CTXD-2022'
			this.isShowPrint = true
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		}
	}
}
</script>

<style scoped lang="scss">
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 768px) {
      margin-bottom: 20px;
    }
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      @media (max-width: 768px) {
        padding: 12px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 787px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
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
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .form-group-container {
    margin-top: 15px;
  }
  .color-black{
    color: #333333;
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
      &:hover{
        border-color: #dc8300;
      }
    }
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
  .title__property {
    text-align: center;
    font-size: 25px;
    font-weight: 600;
    color: #000000;
    margin-bottom: 30px;
  }
  .container {
    &__property {
      height: 100%;
      border: 1px solid #D0D0D0;
      padding: 15px;
      border-radius: 5px;
      @media (max-width: 1023px) {
        margin-bottom: 20px;
      }
      .property {
        &__detail{

          color: #000000;
          margin-bottom: 5px;
          .name, .content{
            margin-bottom: 0;
            padding: 0 !important;
          }
          .name {
            text-align: left;
            width: 50%;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
          }
          .content {
            color: #333333;
            display: block;
            text-align: end;
            &__id{
              color: #FAA831;
            }
          }
        }
      }
    }
  }
  .property {
    &__title {
      color: #333333;

      text-decoration: underline;
      margin-bottom: 10px !important;
      text-align: left;
    }
  }
  .container {
    &__img{
      width: calc(100% + 30px);
      height: 135px;
      margin: -15px -15px 5px;
      img{
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      &--empty{
        width: auto;
        background: #eeeeee;
        img{
          object-fit: contain;
        }
      }
    }
  }
  .color {
    &__blue{
      color: #37C3F4 !important;
    }
    &__purple {
      color: #8659FA !important;
    }

    &__orange {
      color: #FAA831 !important;
    }

    &__green {
      color: #1F8B24 !important;
    }
  }
  .asset-null {
    color: #333333;
    font-size: 24px;
    line-height: 12;
  }
  .popup{
    &-link {
      text-align: right;
      color: #F28C1C !important;
      font-weight: 600;
      text-decoration-line: underline;
      cursor: pointer;
      margin-bottom: 0 !important;
    }
  }
  .container-content {
    &__detail {
      margin-bottom: 20px;
    }
    &__title {
      font-size: 0.875rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: #333333;
    }
  }
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
</style>
