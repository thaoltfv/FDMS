<template>
  <ErrorContent :code="404"/>
</template>

<script>
import ErrorContent from '@/components/ErrorContent'

export default {
	name: 'Error404',
	components: { ErrorContent }
}
</script>

<style scoped>

</style>
