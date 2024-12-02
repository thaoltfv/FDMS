<template>
  <ErrorContent :code="503"/>
</template>

<script>
import ErrorContent from '@/components/ErrorContent'

export default {
	name: 'Error503',
	components: { ErrorContent }
}
</script>

<style scoped>

</style>
