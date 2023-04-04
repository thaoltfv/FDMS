<template>
  <div class="empty">
    <div class="empty-icon">
      <img alt="" class="h-6 mb-3" src="../assets/images/img_not_found.png">
    </div>
    <p class="empty-title h3">Không tìm thấy trang</p>
    <p class="empty-subtitle text-muted">
      Chúng tôi rất tiếc nhưng không tìm thấy trang bạn đang tìm
    </p>
    <div class="empty-action">
      <button class="btn btn-primary" onclick="window.history.go(-1); return false;">
        <arrow-left-icon class="icon"></arrow-left-icon>
        Trở lại
      </button>
    </div>
  </div>
</template>

<script>
import { ArrowLeftIcon } from 'vue-feather-icons'

export default {
	name: 'PageNotFound',
	components: {
		ArrowLeftIcon
	}
}
</script>

<style scoped>

</style>
