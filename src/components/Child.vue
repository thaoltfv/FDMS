<template>
  <transition name="fade">
    <slot>
      <router-view v-if="true"/>
    </slot>
  </transition>
</template>

<script>
export default {
	name: 'Child'
}
</script>

<style scoped>

</style>
