<template>
  <div>
    <ValidationObserver tag="form"
                        ref="images"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
      >
        <div class="loading" :class="{'loading__true': isSubmit}">
          <a-spin />
        </div>
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Hình ảnh hiện trạng</h2>
            </div>
          </div>
          <div class="contain-detail">
            <div class="property-detail position-relative">
              <div class="row">
                <div class="col-12 input-contain">
                  <InputCategory
                    v-model="type"
                    vid="land_shape_id"
                    label="Loại hình hiện trạng"
                    rules="required"
                    :options="optionsType"
                    @change="changeType"
                  />
                </div>
              </div>
            </div>
            <div class="card-table">
              <div class="card-title card-title__img">
                <form enctype="multipart/form-data" class="d-flex align-items-center">
                  <h3 class="title">Hình ảnh</h3>
                  <div class="img-upload">
                    <img src="../../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
                    Tải ảnh lên
                    <input type="file" ref="file" id="image_property" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event)" />
                  </div>
                </form>
              </div>
              <div class="card-body">
                <div class="container-img row mr-0 ml-0" >
                  <div class="img-empty text-center" v-if="form.pic.length === 0">
                    <img src="../../../../assets/images/img_emply.svg" alt="empty">
                    <p class="empty-content"> Chưa có hình</p>
                  </div>
                  <div class="contain-img col-4 col-lg-2 " v-for="(image, index) in form.pic" :key="image.id">
                    <div class="delete" @click="removeImage(index)">X</div>
                    <img class="img" :src="image.link" alt="img">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange btn-action-modal" :class="{'btn-loading disabled': isSubmit}"> <img src="../../../../assets/icons/ic_save.svg"  style="margin-right: 12px" alt="save">Lưu</button>
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"> <img src="../../../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save"> Trở lại</button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>

import InputCategory from '@/components/Form/InputCategory'
import File from '@/models/File'
export default {
	name: 'ModalCurrentPicture',
	props: ['imageDescriptions'],
	data () {
		return {
			isSubmit: false,
			file: null,
			types: [],
			type: '',
			form: {
				pic: []
			}
		}
	},
	components: {
		InputCategory
	},
	computed: {
		optionsType () {
			return {
				data: this.imageDescriptions,
				id: 'id',
				key: 'description'
			}
		}
	},
	methods: {
		changeType () {
			if (this.form.pic.length > 0) {
				this.form.pic.forEach(pic => {
					pic.type_id = this.type
				})
			}
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		onImageChange (e) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				if (this.file.type === 'image/png' || this.file.type === 'image/jpeg' || this.file.type === 'image/jpg' || this.file.type === 'image/gif') {
					this.createImage()
					this.uploadImage()
				} else {
					this.$toast.open({
						message: 'Hình không đúng định dạng vui lòng kiểm tra lại',
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
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
			this.isSubmit = true
			const formData = new FormData()
			formData.append('image', this.file)
			return File.upload({data: formData}).then(response => {
				if (response && response.data && response.data.data) {
					const item = {
						type_id: this.type,
						link: response.data.data.link
					}
					this.form.pic.push(item)
					this.isSubmit = false
				} else if (response.data.error) {
					this.isSubmit = false
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		removeImage (index) {
			this.form.pic.splice(index, 1)
			document.getElementById('image_property').value = ''
		},
		async validateBeforeSubmit (event) {
			const isValid = await this.$refs.images.validate()
			this.isSubmit = true
			if (isValid) {
				await this.handleAction(event)
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleAction (event) {
			this.isSubmit = true
			try {
				const data = this.form
				this.$emit('action', data)
				this.$emit('cancel', event)
				this.$toast.open({
					message: 'Thêm mới hình ảnh thành công',
					type: 'success',
					position: 'top-right'
				})
				this.isSubmit = false
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	}
}
</script>

<style lang="scss" scoped>
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
      padding: 35px 95px;
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
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);;
    background: #FFFFFF;
    width: 99%;
    margin: 50px auto 75px ;
  }
  .card-table tbody tr:last-child td, .card-table tbody tr:last-child th{
    border-bottom: 1px solid #E5E5E5 ;
  }
  .card{
    .contain-detail{
      overflow-y: auto;
      overflow-x: hidden;
      margin-top: 20px;
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
        @media (max-width: 767px) {

        }
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
  .btn-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 100px;
    color: #fff;
    margin-right: 15px;
    box-sizing: border-box;
    &:hover{
      border-color: #dc8300;
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
        color: #000000;
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
      padding: 20px 95px 20px;
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
  }
  .errors-messages {
    color: #cd201f;
    position: absolute;
    font-size: 12px;
  }
  .front-side{
    font-weight: 700;
  }
</style>
