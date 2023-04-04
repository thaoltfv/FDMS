<template>
  <ValidationProvider tag="div"
                      class="upload-img mb-3"
                      :name="$t('thumbnail')"
                      :vid="vid"
                      :rules="rules"
                      ref="provider"
                      v-slot="{ errors, validate }">
    <!--label-->
    <label v-if="label"
           class="form-label font-weight-bold d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-start">
        {{ $t(label) }}
        <span v-if="required"
              class="required">
          {{$t('required')}}
        </span>
      </div>

      <span v-if="textRight"
            class="character-count">
        {{$t(textRight)}}
      </span>
    </label>

    <!--drag file-->
    <div :class="{'has__error': errors.length ? errors[0] : mesErr }">
      <div id="drop-area"
           ref="dropArea"
           class="text-center">
        <div v-if="urlImage || imageShow"
             class="image mb-2">
          <img :src="urlImage || imageShow"
               alt="picture"
               class="img-single">

          <div @click.prevent="deleteImage"
               class="image-delete">
            <img src="../../assets/images/icon-delete1.svg"
                 alt="btn_delete">
          </div>
        </div>

        <p class="text-center font-weight-medium mb-2">
          {{ $t('drag_file') }}
        </p>

        <!--input file-->
        <label
          :for="'fileElem' + vid"
          class="btn btn-primary btn-pill mb-0 font-weight-medium text-white"
        >
          {{$t('choose_file')}}
        </label>

        <input type="file"
               ref="fileField"
               :id="'fileElem' + vid"
               :multiple="typeUpload === 'single' ? false : true"
               :accept="accept"
               @change="handleFiles($event.target.files)">

        <!--progress-->
        <a-progress v-if="showProgress"
                    :percent="uploadPercentage"/>
      </div>

      <!--Message Error-->
      <span v-if="errors[0]"
            class="errors">
        {{ errors[0] }}
      </span>
      <span v-if="mesErr"
            class="errors">
        {{ mesErr }}
      </span>
    </div>
  </ValidationProvider>
</template>

<script>
import File from '@/models/File'
import SelectFile from '@/components/file/SelectFile'
import { FileSize } from '@/enum/file-size.enum'
import store from '@/store'

export default {
	name: 'UploadDragDrop',

	components: {SelectFile},

	data () {
		return {
			filesDone: 0,
			filesToDo: 0,
			files: [],
			fileSize: FileSize,
			showProgress: false,
			uploadPercentage: 0,
			mesErr: '',
			imageShow: '',
			imgSrc: '',
			widthImg: '',
			heightImg: ''
		}
	},

	props: {
		type: {
			type: String,
			default: null
		},

		companyId: {
			type: String,
			default: null
		},

		label: {
			type: String,
			required: true,
			default: ''
		},

		textRight: {
			type: String,
			default: ''
		},

		required: {
			type: Boolean,
			default: false
		},

		vid: {
			type: String,
			default: ''
		},

		rules: {
			type: String,
			default: ''
		},

		typeUpload: {
			type: String,
			default: 'single'
		},

		accept: {
			type: String,
			default: ''
		},

		fileType: {
			type: String,
			default: ''
		},

		urlImage: {
			type: String,
			default: ''
		},

		widthProp: {
			type: Number,
			default: null
		},

		heightProp: {
			type: Number,
			default: null
		}
	},

	computed: {
		currentUser () {
			return store.getters.profile.data
		}
	},

	mounted () {
		this.handleUploadDragDrop()
	},

	filters: {
		formatBytes (bytes, decimals = 2) {
			if (bytes === 0) return '0 B'
			const k = 1024
			const dm = decimals < 0 ? 0 : decimals
			const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
			const i = Math.floor(Math.log(bytes) / Math.log(k))
			return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
		}
	},

	methods: {
		handleUploadDragDrop () {
			let dropArea = this.$refs.dropArea
			let eventDragDrop = ['dragenter', 'dragover', 'dragleave', 'drop']
			let eventDragEnter = ['dragenter', 'dragover']
			let eventDragLeave = ['dragleave', 'drop']

			eventDragDrop.forEach(eventName => {
				dropArea.addEventListener(eventName, this.preventDefaults, false)
			})

			eventDragEnter.forEach(eventName => {
				dropArea.addEventListener(eventName, this.highlight, false)
			})

			eventDragLeave.forEach(eventName => {
				dropArea.addEventListener(eventName, this.unhighlight, false)
			})

			dropArea.addEventListener('drop', this.handleDrop, false)
		},

		preventDefaults (e) {
			e.preventDefault()
			e.stopPropagation()
		},

		// add class to active border when drag
		highlight () {
			this.$refs.dropArea.classList.add('highlight')
		},

		// remove class to unactive border when drop
		unhighlight () {
			this.$refs.dropArea.classList.remove('highlight')
		},

		handleDrop (e) {
			let dt = e.dataTransfer
			let files = dt.files
			this.handleFiles(files)
		},

		handleFiles (files) {
			let fileSize = this.fileSize[this.type]
			// convert files from object to array
			this.files = [...files]

			if (this.files.length) {
				if (this.typeUpload === 'single' && this.fileType === 'image') {
					// handle demension image to have width & height
					this.handleDimensionImage(this.files[0]).then(res => {
						// check multi
						if (this.files.length > 1) {
							this.mesErr = this.$t('validate_multi_upload')
							return false
						}

						// check size
						if (this.files[0].size >= fileSize) {
							this.mesErr = this.$t('validate_size_upload', {
								msg: this.$options.filters.formatBytes(fileSize)
							})
							return false
						}

						// check width & height image (can check type)
						if (this.type === 'app_setting_logo' && res.width > this.widthProp && res.height > this.heightProp) {
							this.mesErr = this.$t('limit_dimension_logo')
							return false
						}

						// reset mes err and call API
						this.mesErr = ''
						this.showProgress = true
						// call API upload file to azure
						this.uploadFile(this.files[0])
					}).catch(() => {
						// check image
						if (!this.files[0].type.includes('image/')) {
							this.mesErr = this.$t('validate_is_image')
							return false
						}
					})
				} else if (this.typeUpload === 'single' && this.fileType === 'video') {
					// handle upload video
					const reader = new FileReader()

					// generate canvas image from video
					this.captureThumbnail(this.files[0], reader).then(res => {
						this.showProgress = true

						this.uploadFile(this.files[0], res)
					})
				} else {
					// call API upload file to azure
					this.files.forEach(this.uploadFile)

					// convert image to base64
					this.files.forEach(this.previewFile)
				}
			}
		},

		captureThumbnail (file, reader) {
			return new Promise((resolve, reject) => {
				reader.onload = () => {
					const blob = new Blob([reader.result], {type: file.type})
					const url = URL.createObjectURL(blob)
					const video = document.createElement('video')

					const timeupdate = () => {
						if (snapImage()) {
							video.removeEventListener('timeupdate', timeupdate)
							video.pause()
						}
					}

					video.addEventListener('loadeddata', () => {
						if (snapImage()) {
							video.removeEventListener('timeupdate', timeupdate)
						}
					})

					const snapImage = () => {
						const canvas = document.createElement('canvas')
						canvas.width = video.videoWidth
						canvas.height = video.videoHeight
						canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height)
						// const success = image.length > 100000
						resolve(canvas.toDataURL())
						return canvas.toDataURL()
					}

					video.addEventListener('timeupdate', timeupdate)
					video.preload = 'metadata'
					video.src = url
					// Load video in Safari / IE11
					video.muted = true
					video.playsInline = true
					video.play()
				}
				reader.readAsArrayBuffer(file)
			})
		},

		uploadFile (file, thumbnailUrl) {
			let formData = new FormData()
			formData.append('files[]', file)
			formData.append('type', this.type)

			if (this.type === 'video') {
				formData.append('thumbnail_url', thumbnailUrl)
			}

			if (this.currentUser.roles.name === this.ADMIN_TYPE.SUPER_ADMIN && this.companyId) {
				formData.append('company_id', this.companyId)
			}

			File.upload({
				data: formData,
				onUploadProgress: (progressEvent) => {
					this.uploadPercentage = Math.round((progressEvent.loaded / progressEvent.total) * 100)
				}
			}).then(res => {
				// hide and reset progress
				setTimeout(() => {
					if (this.uploadPercentage === 100) {
						this.showProgress = false
						this.uploadPercentage = 0
					}
				}, 1000)

				if (this.typeUpload === 'single') {
					if (this.type === 'video') {
						this.imageShow = res.data.data[0].thumbnail_url
					}

					// reset errors
					this.$refs.provider.errors = []
					this.mesErr = ''

					// emit data single file
					this.$emit('onFileSelect', res.data.data[0])
				}

				this.$refs.fileField.value = ''
			})
		},

		handleDimensionImage (file) {
			return new Promise((resolve, reject) => {
				let _URL = window.URL || window.webkitURL
				let objectUrl, img
				objectUrl = _URL.createObjectURL(file)
				img = new Image()
				img.src = objectUrl

				img.onload = () => {
					resolve({width: img.width, height: img.height})
				}
				img.onerror = (err) => {
					reject(err)
				}
			})
		},

		deleteImage () {
			this.$refs.fileField.value = ''
			this.imageShow = ''

			this.$emit('deleteImage', '')
		}
	}
}
</script>

<style lang="scss" scoped>
  .has__error {
    #drop-area {
      border: 2px dashed #cd201f;
    }
  }
  .character-count {
    font-size: 12px;
    font-weight: normal;
    color: #999999;
  }
  #drop-area {
    position: relative;
    border: 2px dashed #ccc;
    max-width: 100%;
    padding: 40px 50px;
    background: #F0F0F0;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    input[type=file] {
      display: none;
    }
    /deep/ .ant-progress {
      position: absolute;
      left: 2.5%;
      bottom: 10px;
      width: 95%;
    }
    &.highlight {
      border: 2px dashed #0062AF;
    }
    .image {
      position: relative;
      max-width: 150px;
      width: 100%;
      pointer-events: none;
      .img-single {
        width: 100%;
      }
      &-delete {
        position: absolute;
        top: -10px;
        right: -10px;
        cursor: pointer;
        pointer-events: auto;
      }
    }
    p {
      margin-bottom: 0;
      color: #333333;
    }
  }
</style>
