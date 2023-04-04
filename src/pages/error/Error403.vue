<template>
  <ErrorContent :code="403"/>
</template>

<script>
import ErrorContent from '@/components/ErrorContent'

export default {
	name: 'Error403',
	components: { ErrorContent }
}
</script>

<style scoped>

</style>
