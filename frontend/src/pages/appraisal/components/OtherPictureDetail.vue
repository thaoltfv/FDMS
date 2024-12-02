<template>
  <div class="card">
    <div class="loading" :class="{'loading__true': isLoading}">
      <a-spin />
    </div>
    <div class="card-title d-flex align-items-center justify-content-between">
      <form enctype="multipart/form-data" class="d-flex align-items-center">
        <h3 class="title">Phụ lục khác</h3>
       <div class="d-flex justify-content-between align-items-end mb-2">
          <div class="img-upload">
            <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
            Tải phụ lục
            <input type="file" ref="file" id="image_property" multiple accept="image/png, image/gif, image/jpeg, image/jpg, .doc, .docx, .xlsx, .xls, application/pdf" @change="onImageChange($event)" />
          </div>
        </div>
      </form>
      <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
    </div>
    <div class="card-body" v-show="showCard">
      <div class="mt-2">
        <!-- <div class="d-flex justify-content-between align-items-end mb-2">
          <div class="img-upload">
            <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
            Tải thư mục
            <input type="file" ref="file" id="image_property" multiple accept="image/*, .doc, .docx, .pdf, .xlsx, .xls" @change="onImageChange($event)" />
          </div>
        </div> -->
        <div class="container-img row mr-0 ml-0" >
            <div class="img-empty text-center" v-if="form.files.length === 0" >
                <img src="../../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content">Chưa có thư mục</p>
            </div>
            <div style="width:100%" class="container-table" v-if="form.files.length > 0">
              <table class="table-property">
                <thead>
                  <th>Tên</th>
                  <th>Dung lượng</th>
                  <th>Người Tạo</th>
                  <th>Ngày tạo</th>
                  <th>Thao tác</th>
                </thead>
                <tbody>
                  <tr v-for="(file, index) in form.files" :key="index">
                    <td>
                      <div class="file_name">{{file.name}}</div>
                    </td>
                    <td>{{formatSizeUnits(file.size)}}</td>
                    <td>{{file.created_by.name}}</td>
                    <td>{{formatDate(file.created_at)}}</td>
                    <td>
                      <div>
                        <div class="d-flex" style="justify-content: center">
                          <div class="btn-delete button_action" @click="downloadFile(file.id, file)">
                              <img height="30px" src="../../../assets/images/img_download2.png" alt="download">
                          </div>
                          <div class="btn-delete button_action" @click="handleDialogRemove(index, file)">
                              <img height="30px" src="../../../assets/icons/ic_delete.svg" alt="delete">
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- <div class=" container_file d-flex col-12 col-lg-12" v-if="files.length > 0" v-for="(file, index) in files" :key="index">
                <div class="file_name">{{file.name}} -- {{formatSizeUnits(file.size)}}</div>
                <div>{{formatDate(file.created_at)}}</div>
                <div style="margin:8px" class="btn-delete" @click="downloadFile(index, file)">
                    <img height="20px" src="../../../assets/images/img_download2.png" alt="download">
                </div>
            </div> -->
        </div>
      </div>
    </div>
    <!-- <ModalCurrentPicture
      v-if="openModalCurrentPicture"
      @cancel="cancelImage"
      :imageDescriptions="imageDescriptions"
      @action="handleAddImage"
    /> -->
    <ModalDelete
      v-if="openModalDelete"
      @cancel="openModalDelete = false"
      @action="handleDelete"
    />
    <ModalImage
      v-if="openImage"
      v-bind:image_detail ="this.image_detail.link"
      @cancel="openImage = false"
    />
  </div>
</template>

<script>

// import ModalCurrentPicture from './modals/ModalCurrentPicture'
import ModalImage from '@/components/Modal/ModalImage'
import ModalDelete from '@/components/Modal/ModalDelete'
import File from '@/models/File'
import moment from 'moment'
import axios from '@/plugins/axios'

export default {
	name: 'OtherPictureDetail',
	props: ['imageDescriptions', 'files', 'data'],
	components: {
		// ModalCurrentPicture,
		ModalImage,
		ModalDelete
	},
	data () {
		return {
			openImage: false,
			openModalDelete: false,
			openModalCurrentPicture: false,
			isLoading: false,
			showCard: true,
			image_detail: {},
			form: {
				files: this.files || []
			},
			imageType: null,
			imgOverall: null,
			imageCurrentStatus: null,
			imageJuridical: null,
			id_delete_file: null,
			indexDelete: null
		}
	},
	mounted () {
	},
	methods: {
		formatSizeUnits (bytes) {
			if (bytes >= 1073741824) { bytes = (bytes / 1073741824).toFixed(2) + ' GB' } else if (bytes >= 1048576) { bytes = (bytes / 1048576).toFixed(2) + ' MB' } else if (bytes >= 1024) { bytes = (bytes / 1024).toFixed(2) + ' KB' } else if (bytes > 1) { bytes = bytes + ' bytes' } else if (bytes === 1) { bytes = bytes + ' byte' } else { bytes = '0 bytes' }
			return bytes
		},
		formatDate (date) {
			return moment(date).format('DD/MM/YYYY')
		},
		getImageDescriptions (data) {

		},
		handleDialogRemove (index, file) {
			this.openModalDelete = true
			this.indexDelete = index
			this.id_delete_file = file.id
		},
		async handleDelete () {
			const res = await File.deleteFileCertificate(this.id_delete_file)
			if (res.data) {
				this.form.files.splice(this.indexDelete, 1)
				// this.files = this.form.files
				this.$toast.open({
					message: 'Xóa thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		async onImageChange (e) {
			const formData = new FormData()
			let check = true
			let files = e.target.files
			if (!files.length) { return }
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				if (
					this.file.type === 'image/png' ||
            this.file.type === 'image/jpeg' ||
            this.file.type === 'image/jpg' ||
            this.file.type === 'image/gif' ||
            this.file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
            this.file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
            this.file.type === 'application/pdf'
				) {
				} else {
					check = false
					await this.$toast.open({
						message: 'Hình không đúng định dạng vui lòng kiểm tra lại',
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
			if (check) {
				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						formData.append('files[' + i + ']', files[i])
					}
					const res = await File.uploadFileCertificate(formData, this.data.id)
					if (res.data) {
						// await this.$emit('handleChangeFile', res.data.data)
						this.form.files = res.data.data
						await this.$toast.open({
							message: 'Thêm file thành công',
							type: 'success',
							position: 'top-right',
							duration: 3000
						})
					}
				}
			}
		},
		createImage () {
			let reader = new FileReader()
			let v = this
			reader.onload = (e) => {
				v.image = e.target.result
			}
			reader.readAsDataURL(this.file)
		},
		uploadImage () {
			this.isLoading = true
			const formData = new FormData()
			formData.append('image', this.file)
			return File.upload({data: formData}).then(response => {
				if (response && response.data && response.data.data) {
					const item = {
						type_id: this.type,
						link: response.data.data.link
					}
					this.pic.push(item)
					this.isLoading = false
				} else if (response.data.error) {
					this.isLoading = false
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		toDataURL (url) {
			return fetch(url, {mode: 'cors'}).then((response) => {
				return response.blob()
			}).then(blob => {
				return URL.createObjectURL(blob)
			})
		},
		async downloadFile (id, file) {
			axios({
				url: process.env.API_URL + '/api/certificate/other-document/download/' + id,
				method: 'GET',
				responseType: 'blob'
			}).then((response) => {
				const url = window.URL.createObjectURL(new Blob([response.data]))
				const link = document.createElement('a')
				link.href = url
				link.setAttribute('download', file.name)
				document.body.appendChild(link)
				link.click()
				window.URL.revokeObjectURL(link)
				this.$toast.open({
					message: `Tải xuống thành công`,
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
			})
		},
		ModalCurrentPicture () {
			this.openModalCurrentPicture = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		handleAddImage (event) {
			event.pic.forEach(image => {
				this.pic.push({
					type_id: image.type_id,
					link: image.link
				})
			})
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		cancelImage () {
			this.openModalCurrentPicture = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
		},
		openModalImage (data) {
			this.openImage = true
			this.image_detail = data
		}
	}
}
</script>

<style scoped lang="scss">
  .button_action {
    cursor:pointer;
    width: 20%;
  }
  .container-table {
    border-radius: 5px;
    border: 1px solid #FAA831;
    padding: unset;
  }
  .table-property {
    width: 100%;
    // font-weight: 500;
    text-align: center;
    thead {
      th {
        border: 1px solid #e5e5e5;
        color: #000000;
        padding: 12px;
        background-color: #FAA831;
        // font-weight: 500 !important;
      }
    }
    tbody {
      td {
        border: 1px solid #e5e5e5;
        &:first-child {
          border-left: none;
        }
        &:last-child {
          border-right: none;
        }
        box-sizing: border-box;
        padding: 20px 14px;
      }
    }
  }
  .file_name {
    width: 95%;
    color: cornflowerblue;
    margin: 10px;
    text-align: left;;
  }
  .container_file {
    border: 2px solid #FAA831;
    width: 100%;
    border-radius: 10px;
    margin: 7px 0px;
  }
  .card {
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
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body {
      padding: 35px 30px 40px;
      @media (max-width: 787px) {
        padding: 15px;
      }
    }
    &-info {
      .title {
        font-size: 1.125rem;
        font-weight: 700;
        margin-top: 28px;
      }
    }
    &-land {
      position: relative;
      padding: 0;
    }
  }
  .contain-img {
    aspect-ratio:1/1;
    overflow: hidden;
    height: auto;
    position: relative;
    text-align: center;
    margin: 5px 0;
    @media (min-width: 64rem) {
      flex: 0 0 11%;
      width: 11%;
    }
    .img {
      object-fit: cover;
      width: 100%;
      height: 100%;
      cursor: pointer;
      &-table {
        margin: auto;
        min-width: 50px;
        min-height: 50px;
        width: 50px;
        height: 50px;
        object-fit: cover;
      }
    }
    &__table {
      width: auto;
      margin-bottom: 0;
    }
    .delete {
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
  .container-img {
    padding: .75rem 0;
    border: 1px solid #0b0d10;
    padding: 15px 25px
  }

  .img-dropdown {
    cursor: pointer;
    width: 18px;
    &__hide {
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
  .loading{
    display: none;
    &__true{
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 100dvh;
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
    .btn-delete {
        display: flex;
        align-items: center;
        cursor: pointer;
        background: #FFFFFF;
        border: 0.777778px solid #000000;
        border-radius: 5.88235px;
        padding: 10px;
        margin: auto;
        width: 36px;
        height: 36px;
        img {
            width: 100%;
            height: auto;
        }
    }
  }
</style>
