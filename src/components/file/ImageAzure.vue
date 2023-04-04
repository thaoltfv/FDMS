<template>
  <div class="image-azure position-relative d-inline-block mb-3">
    <a-popconfirm
      :title="$t('delete_content')"
      :ok-text="$t('yes')"
      :cancel-text="$t('no')"
      @confirm="$emit('deleteImage')">
      <button class="btn btn-remove-image rounded-circle">
        <XIcon size="16"/>
      </button>
    </a-popconfirm>
    <img class="img-fluid rounded object-cover img-thumbnail-150 border border-light"
         :src="imageShow | imageThumbnailObject"
         alt=""
    />
  </div>
</template>

<script>
import {XIcon} from 'vue-feather-icons'

export default {
	name: 'ImageAzure',

	props: {
		imageShow: {
			type: String,
			default: ''
		},
		valueThumbnail: {
			type: String,
			default: ''
		}
	},

	components: {
		XIcon
	}
}
</script>

<style lang="scss" scoped>
  .image-azure {
    .btn-remove-image {
      position: absolute;
      top: -8px;
      right: -8px;
      line-height: 1;
      color: #dd4b39;
      border: 1px solid #dd4b39;
      border-radius: 50%;
      padding: 3px;
      background: rgba(255,255,255,0.8);
    }
  }
</style>
