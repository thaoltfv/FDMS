<template>
  <div class="list-group-item" v-if="file">
    <div class="row align-items-center upload-task">
      <div class="col-auto text-center">
        <img
          :src="thumbnail ? thumbnail : file.src"
          class="img-fluid rounded object-cover file-upload-preview"/>
      </div>
      <div class="col">
        <p class="m-0 font-weight-bold text-muted">{{ file.name }}</p>
        <small class="text-muted">{{ file.size | formatBytes }}</small>
        <a-progress :percent="uploadPercentage" status="active"/>
        <small class="text text-danger" v-if="isRetry">
          <a-icon type="exclamation-circle" class="mr-1"/>
          {{ $t('error_happened_please_try_again') }}
        </small>
        <span v-if="file.size > maxSize" class="errors">アップロードできるファイルは{{ maxSize | formatBytes }}以内です。</span>
        <span v-if="file.dimensions.width > typeUpload" class="errors">横幅は {{ typeUpload }}px 以内に収めてください。</span>
      </div>
      <div class="col-auto text-muted align-items-center">
<!--        <button class="btn btn-success btn-sm rounded-circle"-->
<!--                @click="handleFileUpload"-->
<!--                :disabled="file.size > maxSize || file.dimensions.width > typeUpload"-->
<!--                v-if="!isUploaded">-->

<!--          <a-icon type="upload"/>-->
<!--        </button>-->
        <button class="btn btn-warning btn-sm rounded-circle"
                @click="handleFileUpload(file)"
                :class="{'d-none': !isRetry}"
                :disabled="file.size > maxSize"
                v-if="isUploaded">

          <a-icon type="sync"/>
        </button>
        <button class="btn btn-danger btn-sm rounded-circle"
                @click="$emit('onFileRemove', file)"
                :disabled="isUploaded">
          <a-icon type="delete"/>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import File from '@/models/File'
import { Progress } from 'ant-design-vue'
import { FileSize } from '@/enum/file-size.enum'

export default {
	name: 'UploadTask',

	props: ['file', 'thumbnail', 'type'],

	components: {
		Progress
	},

	data () {
		return {
			uploadPercentage: 0,
			isUploaded: false,
			isRetry: false,
			fileSize: FileSize
		}
	},

	computed: {
		maxSize () {
			return this.fileSize[this.type] || (100 * 1024 * 1024) // 100MB default
		},

		typeUpload () {
			if (this.type === 'app_setting' || this.type === 'main_visual') {
				return 600
			}
		}
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
		async handleFileUpload () {
			this.isUploaded = false
			this.isRetry = false

			if (this.file.size > this.maxSize || this.file.dimensions.width > this.typeUpload) return

			try {
				const resp = await this.upload(this.file)
				if (resp) {
					this.isUploaded = true
					this.$emit('onUploadSuccess', this.file)
				}
			} catch (error) {
				if (error) {
					this.uploadPercentage = 0
					this.isRetry = true
					this.$emit('onHandleError', this.file)
				}
			}
		},

		async upload () {
			const formData = new FormData()
			formData.append('files[]', this.file)
			formData.append('type', this.$parent.type)

			if (this.$parent.type === 'video') {
				formData.append('thumbnail_url', this.thumbnail)
			}

			return File.upload({
				data: formData
				// onUploadProgress: (progressEvent) => {
				//   this.uploadPercentage = Math.round((progressEvent.loaded / progressEvent.total) * 100)
				// }
			})
		}
	}
}
</script>

<style lang="scss" scoped>
.errors {
  display: block;
  font-size: 12px;
  color: red;
}
</style>
