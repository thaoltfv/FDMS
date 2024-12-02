<template>
  <div class="d-md-flex justify-content-md-between justify-content-center align-items-start">
    <div class="mb-3">
      <select class="form-select w-100" v-model.number="pagination.perPage">
        <option v-for="(option, index) of perPageOptions" :key="index" :value="option">{{ option }}</option>
      </select>
    </div>
    <b-pagination
      v-model.number="pagination.currentPage"
      :total-rows="pagination.total"
      :per-page="pagination.perPage"
    />
    <div class="m-0 text-muted">Showing <span>{{startIndex}}</span> to <span>{{endIndex}}</span> of <span>{{pagination.total}}</span> entries</div>
  </div>
</template>

<script>
import { PER_PAGE, PER_PAGE_OPTIONS } from '@/config'

export default {
	name: 'Pagination',
	props: {
		perPageOptions: {
			type: Array,
			default: function () {
				return PER_PAGE_OPTIONS
			}
		},
		pagination: {
			type: Object,
			required: true,
			default: function () {
				return {
					currentPage: undefined,
					total: undefined,
					perPage: PER_PAGE
				}
			}
		}
	},
	computed: {
		startIndex () {
			return (this.pagination.currentPage - 1) * this.pagination.perPage + 1
		},
		endIndex () {
			return (this.pagination.currentPage - 1) * this.pagination.perPage + 1
		}
	}
}
</script>

<style scoped>

</style>
