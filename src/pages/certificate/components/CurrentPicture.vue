<template>
  <div class="card">
    <div class="loading" :class="{'loading__true': isLoading}">
      <a-spin />
    </div>
    <div class="card-title d-flex align-items-center justify-content-between">
      <form enctype="multipart/form-data" class="d-flex align-items-center">
        <h3 class="title">Hình ảnh</h3>
      </form>
      <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
    </div>
    <div class="card-body" v-show="showCard">
      <Tabs :theme="theme" :navAuto="true">
        <TabItem name="Đường tiếp giáp tài sản">
          <div class="mt-2">
            <div class="d-flex justify-content-between align-items-end mb-2">
              <h3 class="mb-0"> </h3>
              <!-- <div class="img-upload">
                <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
                Tải ảnh lên
                <input type="file" ref="file" id="image_property" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'đường tiếp giáp tài sản thẩm định giá')" />
              </div> -->
            </div>
            <div class="container-img row mr-0 ml-0" v-if="imageType" >
              <!-- <div class="img-empty text-center" v-if="pic.length === 0 || (imageType.id && pic.find(image => image.type_id === imageType.id) === undefined || pic.find(image => image.type_id === imageType.id) === null)">
                <img src="../../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content">Chưa có hình</p>
              </div> -->
              <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imageType.id && images.type_id === imageType.id" v-for="(images, index) in pic" :key="images.id">
                <div class="delete" @click="removeImage(index)">X</div>
                <img class="img" style="max-height: 100%;" :src="images.link" alt="img" @click="openModalImage(images)">
              </div>
              <div style="min-width:170px" class="img-empty text-center">
                <div class="container_input">
                  <img class="img_add" src="../../../assets/images/add_img.png" alt="empty">
                  <input class="input_file_4" type="file" ref="file" id="image_property" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'đường tiếp giáp tài sản thẩm định giá')" />
                </div>
              </div>
            </div>
          </div>
        </TabItem>
        <TabItem name="Tổng thể tài sản">
          <div class="mt-2">
            <div class="d-flex justify-content-between align-items-end mb-2">
              <h3 class="mb-0"> </h3>
              <!-- <div class="img-upload">
                <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
                Tải ảnh lên
                <input type="file" ref="file" id="image_property_2" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'tổng thể tài sản thẩm định giá')" />
              </div> -->
            </div>
            <div class="container-img row mr-0 ml-0" v-if="imgOverall" >
              <!-- <div class="img-empty text-center" v-if="pic.length === 0 || (imgOverall.id && pic.find(image => image.type_id === imgOverall.id) === undefined || pic.find(image => image.type_id === imgOverall.id) === null)">
                <img src="../../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content">Chưa có hình</p>
              </div> -->
              <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imgOverall.id && images.type_id === imgOverall.id" v-for="(images, index) in pic" :key="images.id">
                <div class="delete" @click="removeImage(index)">X</div>
                <img class="img" style="max-height: 100%;" :src="images.link" alt="img" @click="openModalImage(images)">
              </div>
              <div style="min-width:170px" class="img-empty text-center">
                <div class="container_input">
                  <img class="img_add" src="../../../assets/images/add_img.png" alt="empty">
                  <input class="input_file_4" type="file" ref="file" id="image_property_2" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'tổng thể tài sản thẩm định giá')" />
                </div>
              </div>
            </div>
          </div>
        </TabItem>
        <TabItem name="Hiện trạng tài sản">
          <div class="mt-2">
            <div class="d-flex justify-content-between align-items-end mb-2">
              <h3 class="mb-0"> </h3>
              <!-- <div class="img-upload">
                <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
                Tải ảnh lên
                <input type="file" ref="file" id="image_property_3" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'hiện trạng tài sản thẩm định giá')" />
              </div> -->
            </div>
            <div class="container-img row mr-0 ml-0" v-if="imageCurrentStatus">
              <!-- <div class="img-empty text-center" v-if="pic.length === 0 || (imageCurrentStatus.id && pic.find(image => image.type_id === imageCurrentStatus.id) === undefined || pic.find(image => image.type_id === imageCurrentStatus.id) === null)">
                <img src="../../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content">Chưa có hình</p>
              </div> -->
              <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imageCurrentStatus.id && images.type_id === imageCurrentStatus.id" v-for="(images, index) in pic" :key="images.id">
                <div class="delete" @click="removeImage(index)">X</div>
                <img class="img" style="max-height: 100%;" :src="images.link" alt="img" @click="openModalImage(images)">
              </div>
              <div style="min-width:170px" class="img-empty text-center">
                <div class="container_input">
                  <img class="img_add" src="../../../assets/images/add_img.png" alt="empty">
                  <input class="input_file_4" type="file" ref="file" id="image_property_3" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'hiện trạng tài sản thẩm định giá')" />
                </div>
              </div>
            </div>
          </div>
        </TabItem>
        <TabItem name="Pháp lý tài sản">
          <div class="mt-2">
            <div class="d-flex justify-content-between align-items-end mb-2">
              <h3 class="mb-0"> </h3>
              <!-- <div class="img-upload">
                <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
                Tải ảnh lên
                <input type="file" ref="file" id="image_property_4" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'pháp lý tài sản')" />
              </div> -->
            </div>
            <div class="container-img row mr-0 ml-0" v-if="imageJuridical">
              <!-- <div class="img-empty text-center" v-if="pic.length === 0 || (imageJuridical.id && pic.find(image => image.type_id === imageJuridical.id) === undefined || pic.find(image => image.type_id === imageJuridical.id) === null)">
                <img src="../../../assets/images/img_emply.svg" alt="empty">
                <p class="empty-content">Chưa có hình</p>
              </div> -->

              <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imageJuridical.id && images.type_id === imageJuridical.id" v-for="(images, index) in pic" :key="images.id">
                <div class="delete" @click="removeImage(index)">X</div>
                <img class="img" style="max-height: 100%;" :src="images.link" alt="img" @click="openModalImage(images)">
              </div>
              <div style="min-width:170px" class="img-empty text-center">
                <div class="container_input">
                  <img class="img_add" src="../../../assets/images/add_img.png" alt="empty">
                  <input class="input_file_4" type="file" ref="file" id="image_property_4" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'pháp lý tài sản')" />
                </div>
              </div>
            </div>
          </div>
        </TabItem>
        <!-- <div class="mt-2">
          <div class="d-flex justify-content-between align-items-end mb-2">
            <h3 class="mb-0">Đường tiếp giáp tài sản thẩm định giá</h3>
            <div class="img-upload">
              <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
              Tải ảnh lên
              <input type="file" ref="file" id="image_property" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'đường tiếp giáp tài sản thẩm định giá')" />
            </div>
          </div>
          <div class="container-img row mr-0 ml-0" v-if="imageType" >
            <div class="img-empty text-center" v-if="pic.length === 0 || (imageType.id && pic.find(image => image.type_id === imageType.id) === undefined || pic.find(image => image.type_id === imageType.id) === null)">
              <img src="../../../assets/images/img_emply.svg" alt="empty">
              <p class="empty-content">Chưa có hình</p>
            </div>
            <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imageType.id && images.type_id === imageType.id" v-for="(images, index) in pic" :key="images.id">
              <div class="delete" @click="removeImage(index)">X</div>
              <img class="img" :src="images.link" alt="img" @click="openModalImage(images)">
            </div>
          </div>
        </div> -->
        <!-- <div class="mt-2">
          <div class="d-flex justify-content-between align-items-end mb-2">
            <h3 class="mb-0">Tổng thể tài sản thẩm định giá</h3>
            <div class="img-upload">
              <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
              Tải ảnh lên
              <input type="file" ref="file" id="image_property_2" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'tổng thể tài sản thẩm định giá')" />
            </div>
          </div>
          <div class="container-img row mr-0 ml-0" v-if="imgOverall" >
            <div class="img-empty text-center" v-if="pic.length === 0 || (imgOverall.id && pic.find(image => image.type_id === imgOverall.id) === undefined || pic.find(image => image.type_id === imgOverall.id) === null)">
              <img src="../../../assets/images/img_emply.svg" alt="empty">
              <p class="empty-content">Chưa có hình</p>
            </div>
            <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imgOverall.id && images.type_id === imgOverall.id" v-for="(images, index) in pic" :key="images.id">
              <div class="delete" @click="removeImage(index)">X</div>
              <img class="img" :src="images.link" alt="img" @click="openModalImage(images)">
            </div>
          </div>
        </div> -->
        <!-- <div class="mt-2">
          <div class="d-flex justify-content-between align-items-end mb-2">
            <h3 class="mb-0">Hiện trạng tài sản thẩm định giá</h3>
            <div class="img-upload">
              <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
              Tải ảnh lên
              <input type="file" ref="file" id="image_property_3" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'hiện trạng tài sản thẩm định giá')" />
            </div>
          </div>
          <div class="container-img row mr-0 ml-0" v-if="imageCurrentStatus">
            <div class="img-empty text-center" v-if="pic.length === 0 || (imageCurrentStatus.id && pic.find(image => image.type_id === imageCurrentStatus.id) === undefined || pic.find(image => image.type_id === imageCurrentStatus.id) === null)">
              <img src="../../../assets/images/img_emply.svg" alt="empty">
              <p class="empty-content">Chưa có hình</p>
            </div>
            <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imageCurrentStatus.id && images.type_id === imageCurrentStatus.id" v-for="(images, index) in pic" :key="images.id">
              <div class="delete" @click="removeImage(index)">X</div>
              <img class="img" :src="images.link" alt="img" @click="openModalImage(images)">
            </div>
          </div>
        </div> -->
        <!-- <div class="mt-2">
          <div class="d-flex justify-content-between align-items-end mb-2">
            <h3 class="mb-0">Pháp lý tài sản</h3>
            <div class="img-upload">
              <img src="../../../assets/icons/ic_image-white.svg" style="margin-right: 5px" alt="image">
              Tải ảnh lên
              <input type="file" ref="file" id="image_property_4" multiple accept="image/png, image/gif, image/jpeg, image/jpg" @change="onImageChange($event, 'pháp lý tài sản')" />
            </div>
          </div>
          <div class="container-img row mr-0 ml-0" v-if="imageJuridical">
            <div class="img-empty text-center" v-if="pic.length === 0 || (imageJuridical.id && pic.find(image => image.type_id === imageJuridical.id) === undefined || pic.find(image => image.type_id === imageJuridical.id) === null)">
              <img src="../../../assets/images/img_emply.svg" alt="empty">
              <p class="empty-content">Chưa có hình</p>
            </div>
            <div class="contain-img col-4 col-lg-2 contain-img__property" v-if="pic.length > 0 && imageJuridical.id && images.type_id === imageJuridical.id" v-for="(images, index) in pic" :key="images.id">
              <div class="delete" @click="removeImage(index)">X</div>
              <img class="img" :src="images.link" alt="img" @click="openModalImage(images)">
            </div>
          </div>
        </div> -->
      </Tabs>
    </div>
    <ModalCurrentPicture
      v-if="openModalCurrentPicture"
      @cancel="cancelImage"
      :imageDescriptions="imageDescriptions"
      @action="handleAddImage"
    />
    <ModalImage
      v-if="openImage"
      v-bind:image_detail ="this.image_detail.link"
      @cancel="openImage = false"
    />
  </div>
</template>

<script>

import ModalCurrentPicture from './modals/ModalCurrentPicture'
import ModalImage from '@/components/Modal/ModalImage'
import {Tabs, TabItem} from 'vue-material-tabs'
import File from '@/models/File'
export default {
	name: 'CurrentPicture',
	props: ['imageDescriptions', 'pic'],
	components: {
		ModalCurrentPicture,
		ModalImage,
		TabItem,
		Tabs
	},
	data () {
		return {
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
			openImage: false,
			openModalCurrentPicture: false,
			isLoading: false,
			showCard: true,
			image_detail: {},
			form: {
				pic: []
			},
			imageType: null,
			imgOverall: null,
			imageCurrentStatus: null,
			imageJuridical: null
		}
	},
	mounted () {
	},
	methods: {
		getImageDescriptions (data) {
			this.imageType = data.find(imageDescription => imageDescription.description.toLowerCase() === 'đường tiếp giáp tài sản thẩm định giá')
			this.imgOverall = data.find(imageDescription => imageDescription.description.toLowerCase() === 'tổng thể tài sản thẩm định giá')
			this.imageCurrentStatus = data.find(imageDescription => imageDescription.description.toLowerCase() === 'hiện trạng tài sản thẩm định giá')
			this.imageJuridical = data.find(imageDescription => imageDescription.description.toLowerCase() === 'pháp lý tài sản')
		},
		onImageChange (e, type) {
			const typeImage = this.imageDescriptions.find(imageDescription => imageDescription.description.toLowerCase() === type)
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) { return }
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				if (this.file.type === 'image/png' || this.file.type === 'image/jpeg' || this.file.type === 'image/jpg' || this.file.type === 'image/gif') {
					this.type = typeImage.id
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
		removeImage (index) {
			this.pic.splice(index, 1)
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
      padding: 25px 10px;
    }
    .img {
      object-fit: cover;
      // width: 100%;
      // height: 100%;

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
</style>
