<template>
  <div id="file-upload" class="m-4 mt-0">
    <div class="d-flex justify-content-center">
      <span class="btn btn-secondary btn-file text-capitalize">
<!--        <a-icon type="folder" class="mr-1"/> -->
        {{$t('select_file_from_computer')}}

        <input type="file" @change="onFileChange($event)"
               multiple
               ref="inputFile"
               @click="$refs.inputFile.value = []"
               :accept="type === 'video' ? acceptVideo : accept">
      </span>
    </div>

    <div class="card mt-4" v-if="fileUploads.length > 0">
      <div class="card-status-top bg-blue"></div>
      <div class="card-header text-capitalize">
<!--        <h4 class="card-title" v-text="$t('upload_process')"/>-->
      </div>

      <div class="card-body p-0">
        <div class="list-group card-list-group">
          <template v-for="file in fileUploads">
            <UploadTask
                v-if="file"
                :key="file.uid || 0"
                :file="file"
                :type="type"
                :thumbnail="thumbnailUrl"
                @onFileRemove="removeFile"
                @onHandleError="onHandleError"
                @onUploadSuccess="onUploadSuccess"
                ref="uploadTaskRefs"
            />
          </template>
        </div>
      </div>

      <div v-if="type !== 'video'" class="card-footer text-right">
<!--        <button class="btn btn-sm btn-success mr-2 text-capitalize" @click="uploadAll">-->
<!--          <a-icon type="upload"/> {{ $t('upload') }}-->
<!--        </button>-->
        <button class="btn btn-sm text-capitalize" @click="removeAll">
          <a-icon type="delete"/> {{ $t('remove_all') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import UploadTask from './UploadTask'
import uniqueId from 'lodash-es/uniqueId'

export default {
	name: 'FileUpload',

	components: {
		UploadTask
	},

	data () {
		return {
			fileUploads: [],
			uploadTaskSuccess: 0,
			fileErrors: [],
			thumbnailUrl: ''
		}
	},

	props: {
		type: null,
		accept: {
			type: String,
			default: '.png, .jpg, .jpeg'
		},
		acceptVideo: {
			type: String,
			default: 'video/mp4,video/x-m4v,video/mpe,video/quicktime'
		}
	},

	methods: {
		async onFileChange (evt) {
			let files = evt.target.files || evt.dataTransfer.files

			if (!files.length) return

			// if (files.length > 20) return this.$message.error(this.$t('error_max_20_files_upload'))

			const fileUploads = []
			for (let file of files) {
				file = Object.assign(file, {
					uid: uniqueId('fileUpload_'),
					src: await this.toBase64(file),
					dimensions: this.type === 'video' ? '' : await this.getDimension(file)
				})
				fileUploads.push(file)
			}
			this.fileUploads = [...fileUploads]
		},

		async onUploadSuccess (file) {
			await this.removeFile(file)
			this.$emit('onUploadComplete')
		},

		resetFileUpload () {
			this.uploadTaskSuccess = 0
			this.$refs.inputFile.value = ''
		},

		async removeFile (file) {
			const index = this.fileUploads.indexOf(file)

			if (index > -1) {
				await this.fileUploads.splice(index, 1)
			}
		},

		async uploadAll () {
			if (!this.fileUploads.length) {
				return
			}

			await new Promise(resolve => {
				const promiseAll = this.$refs.uploadTaskRefs.map(uploadTask => uploadTask.handleFileUpload())

				Promise.all([...promiseAll]).then((resp) => {
					resolve(resp)
				})
			}).then(() => {
				this.fileUploads = [...this.fileErrors]
				this.fileErrors = []
			})
		},

		removeAll () {
			if (!this.fileUploads.length) return
			this.fileUploads.splice(0, this.fileUploads.length)
		},

		async toBase64 (file) {
			const reader = new FileReader()

			this.thumbnailUrl = file.type.match('video') ? await this.captureThumbnail(file, reader) : ''

			return new Promise((resolve, reject) => {
				reader.readAsDataURL(file)
				reader.onload = () => resolve(reader.result)
				reader.onerror = error => reject(error)
			})
		},

		getDimension (file) {
			const reader = new FileReader()
			return new Promise(resolve => {
				reader.onload = () => {
					const blob = new Blob([reader.result], {type: file.type})
					const url = URL.createObjectURL(blob)
					let image = document.createElement('img')
					image.src = url
					image.onload = function () {
						resolve({width: image.width, height: image.height})
						return {
							width: image.width,
							height: image.height
						}
					}
				}
				reader.readAsArrayBuffer(file)
			})
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

		onHandleError (file) {
			this.fileErrors.push(file)
		},

		async refreshMedia () {
			this.$emit('onUploadComplete')
		}
	}
}
</script>

<style scoped>

</style>
