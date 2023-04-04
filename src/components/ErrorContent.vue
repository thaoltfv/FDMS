<template>
  <div class="flex-fill d-flex align-items-center justify-content-center">
    <div class="container-tight py-6">
      <div class="empty">
        <div class="empty-icon">
          <div class="display-4">{{code}}</div>
        </div>

        <p class="empty-title h3" v-text="$t(`error_title`)"/>
        <p class="empty-subtitle text-muted" v-text="$t(`error_${code}_content`)"/>

        <div class="empty-action" v-if="code !== 400 && code !== 401">
          <router-link :to="{name: 'home'}"
                       class="btn btn-primary">
            <i class="fe fe-chevron-left"/>

            {{ $t('take_me_home') }}
          </router-link>
        </div>
        <div class="empty-action" v-else>
          <router-link :to="{name: 'Login'}"
                       class="btn btn-primary">
            <i class="fe fe-chevron-left"/>

            {{ $t('login') }}
          </router-link>
        </div>
      </div>
    </div>F
  </div>
</template>

<script>
export default {
	name: 'ErrorContent',

	props: {
		code: {
			type: Number,
			required: true,
			default: 500
		}
	}
}
</script>

<style scoped>

</style>
