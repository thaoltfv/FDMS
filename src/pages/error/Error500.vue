<template>
  <ErrorContent :code="500"/>
</template>

<script>
import ErrorContent from '@/components/ErrorContent'

export default {
	name: 'Error500',
	components: { ErrorContent }
}
</script>

<style scoped>

</style>
