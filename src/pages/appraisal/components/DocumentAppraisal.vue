<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <h3 class="title">Tài liệu chứng thư thẩm định</h3>
          <!-- <button type="button" class="btn btn-orange mt-0 w-auto ml-2">Xuất dữ liệu</button>
          <button type="button" class="btn btn-orange mt-0 ml-2 w-auto text-nowrap">Xóa dữ liệu đã xuất</button> -->
        </div>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard">
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">Chứng thư thẩm định</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintProof">Xem</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" v-if="(status === 1 || status === 2) && edit && checkRole">Chỉnh sửa</button> -->
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintProof">Tải xuống</button>
        </div>
      </div>
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">Báo cáo thẩm định</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintReport">Xem</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" v-if="(status === 1 || status === 2) && edit && checkRole">Chỉnh sửa</button> -->
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintReport">Tải xuống</button>
        </div>
      </div>
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">PL 1: Bảng điều chỉnh QSDĐ</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrint">Xem</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" v-if="(status === 1 || status === 2) && edit && checkRole" @click="openModalAppendix">Chỉnh sửa</button> -->
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrint">Tải xuống</button>
        </div>
      </div>
      <div class="container-document row align-items-center mb-4" v-if="showPL2">
        <h3 class="container-content__title mb-0 col-3">PL 2: Bảng điều chỉnh CTXD</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintAppendix">Xem</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" v-if="(status === 1 || status === 2) && edit && checkRole" @click="openModalAppendixTwo">Chỉnh sửa</button> -->
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintAppendix">Tải xuống</button>
        </div>
      </div>
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">PL 3: Hình ảnh hiện trạng tài sản thẩm định</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintImage">Xem</button>
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" v-if="(status === 1 || status === 2) && edit && checkRole" >Chỉnh sửa</button> -->
          <!-- <button type="button" class="mt-0 ml-1 w-auto btn btn-orange">Xuất file</button> -->
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintImage">Tải xuống</button>
        </div>
      </div>
      <div class="container-document row align-items-center mb-4">
        <h3 class="container-content__title mb-0 col-3">Phiếu thu thập TSSS</h3>
        <div class="col d-flex">
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="handleShowPrintAsset">Xem</button>
          <button type="button" class="mt-0 ml-1 w-auto btn btn-orange" @click="openPrintAsset">Tải xuống</button>
        </div>
      </div>
    </div>
    <ModalShowPrint
      v-if="isShowPrint"
      @cancel="isShowPrint = false"
      :filePrint="filePrint"
      :title="title"
    />
    <ModalEditAppendixOne
      v-if="modalAppendixOne"
      :formData="formData"
      @cancel="cancelModalAppendix"
      @action="getDataAppraises"
    />
    <ModalEditAppendixTwo
      v-if="modalAppendixTwo"
      :formData="formData"
      @cancel="cancelModalAppendixTwo"
      @action="getDataAppraises"
    />
  </div>
</template>

<script>

import ModalShowPrint from '@/pages/appraisal/components/modals/ModalShowPrint'
import ModalEditAppendixOne from '@/pages/appraisal/components/modals/ModalEditAppendixOne'
import ModalEditAppendixTwo from '@/pages/appraisal/components/modals/ModalEditAppendixTwo'
import Certificate from '@/models/Certificate'
import WareHouse from '@/models/WareHouse'
export default {
	name: 'DocumentAppraisal',
	props: ['idData', 'status', 'dataForm', 'checkRole'],
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
		ModalEditAppendixOne,
		ModalEditAppendixTwo
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
			form: {
				appraiser: '',
				manager: '',
				staff: ''
			},
			pic: [],
			property: null,
			propertyIndex: null,
			index: null,
			title: null,
			filePrint: null,
			formData: null,
			showPL2: false,
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false
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
		getDataAppraises (id) {
			this.getCertificate(id)
		},
		async getCertificate (id) {
			let count = 0
			const res = await Certificate.getCertificate(id)
			this.formData = await res.data
			if (this.formData) {
				this.formData.appraises.forEach(data => {
					if (data.tangible_assets && data.tangible_assets.length > 0) {
						count = +1
					}
				})
				if (count > 0) {
					this.showPL2 = true
				} else {
					this.showPL2 = false
				}
			}
		},
		// function Phụ Lục 1
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
		async handleShowPrint () {
			this.isSubmit = true
			await this.openModalPrint()
			this.isSubmit = false
			this.title = 'Phụ lục 1 Bảng điều chỉnh QSDD-2022'
			this.isShowPrint = true
		},
		// function phụ lục 2
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
		async handleShowPrintAppendix () {
			this.isSubmit = true
			await this.openModalPrintAppendix()
			this.isSubmit = false
			this.title = 'Phụ lục 2 Bảng điều chỉnh CTXD-2022'
			this.isShowPrint = true
		},
		// function phụ lục 3: Hình ảnh
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
		async handleShowPrintImage () {
			this.isSubmit = true
			await this.openModalPrintImage()
			this.isSubmit = false
			this.title = 'Phụ lục hình ảnh QSDD và CTXD-2022'
			this.isShowPrint = true
		},
		// function báo cáo thẩm định
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
		async handleShowPrintReport () {
			this.isSubmit = true
			await this.openModalPrintReport()
			this.isSubmit = false
			this.title = 'Báo cáo QSDD và CTXD-2022'
			this.isShowPrint = true
		},
		// function chứng thư thẩm định
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
		async handleShowPrintProof () {
			this.isSubmit = true
			await this.openModalPrintProof()
			this.isSubmit = false
			this.title = 'Chứng thư QSDD và CTXD-2022'
			this.isShowPrint = true
		},
		// function phiếu thu thập TSSS
		async handleShowPrintAsset () {
			this.isSubmit = true
			await this.openModalPrintAsset()
			this.isSubmit = false
			this.title = 'Phiếu thu thập thông tin tài sản so sanh'
			this.isShowPrint = true
		},
		async openPrintAsset () {
			let arrayAsset = await []
			if (this.dataForm.real_estate && this.dataForm.real_estate.length > 0) {
				await this.dataForm.real_estate.forEach(item => {
					if (item.appraises.appraise_has_assets && item.appraises.appraise_has_assets.length > 0) {
						item.appraises.appraise_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id)
						})
					}
				})
			}
			this.isSubmit = true
			const res = await WareHouse.getPrint(arrayAsset, this.idData)
			if (res.data) {
				const file = res.data
				const fileLink = document.createElement('a')
				fileLink.href = file.url
				fileLink.setAttribute('download', file.file_name)
				document.body.appendChild(fileLink)
				fileLink.click()
				fileLink.remove()
				window.URL.revokeObjectURL(fileLink)
				this.isSubmit = false
			} else {
				await this.$toast.open({
					message: 'Tải file bị lỗi vui lòng gọi hỗ trợ',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		// function bổ trợ
		async openModalPrintAsset () {
			let arrayAsset = await []
			if (this.dataForm.real_estate && this.dataForm.real_estate.length > 0) {
				await this.dataForm.real_estate.forEach(item => {
					if (item.appraises.appraise_has_assets && item.appraises.appraise_has_assets.length > 0) {
						item.appraises.appraise_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id)
						})
					}
				})
			}
			const res = await WareHouse.getPrint(arrayAsset, this.idData)
			if (res.data) {
				const file = res.data
				this.filePrint = file.url
			} else {
				await this.$toast.open({
					message: 'Xem file bị lỗi vui lòng gọi hỗ trợ',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
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
			this.modalAppendixOne = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		cancelModalAppendix () {
			this.modalAppendixOne = false
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
</style>
