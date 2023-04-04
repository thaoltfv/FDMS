<template>
  <div id="Resource">
    <slot>
      <router-view v-if="true" class="main-wrapper" />
    </slot>
  </div>
</template>

<script>
export default {
	name: 'Resource',

	components: {}
}
</script>

<style lang="scss" scoped>
.main-wrapper {
  margin: 16px 0;
}
</style>
