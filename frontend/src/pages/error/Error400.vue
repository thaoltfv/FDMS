<template>
  <ErrorContent :code="400"/>
</template>

<script>
import ErrorContent from '@/components/ErrorContent'

export default {
	name: 'Error400',
	components: { ErrorContent }
}
</script>

<style scoped>

</style>
