<template>
  <ErrorContent :code="409"/>
</template>

<script>
import ErrorContent from '@/components/ErrorContent'

export default {
	name: 'Error409',
	components: { ErrorContent }
}
</script>

<style scoped>

</style>
