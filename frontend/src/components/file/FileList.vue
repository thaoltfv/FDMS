<template>
  <div id="file-list">
    <div class="card" v-if="files.length">
      <div class="card-body">
        <div class="row">
          <div class="col-sm-6 col-md-4 col-lg-2" v-for="file in files" :key="file.id">
            <div class="card shadow-none">
              <div class="card-body p-0"
                   :class="{'card-active': isSelected(file)}"
                   @click="select(file)"
                   @dblclick="multiple ? '' : $emit('onChooseImage')"
              >
                <a-tooltip placement="bottom">
                  <template slot="title">
                    <span v-text="file.name"/>
                  </template>

                  <img :src="type === 'video' ? file.thumbnail_url : file.url"
                       :alt="file.name"
                       class="img-file"
                  />
                </a-tooltip>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="card-footer text-center">
        <a-pagination :default-current="paginator.currentPage"
                      :current="paginator.currentPage"
                      :pageSize="paginator.perPage"
                      :total="paginator.total"
                      @change="onPageChange"/>
      </div>
    </div>
    <div class="card" v-else>
      <div class="card-body">
        <a-empty description=""/>
      </div>
    </div>
  </div>
</template>

<script>
import FileUpload from './FileUpload'
import { Pagination } from 'ant-design-vue'

export default {
	name: 'FileList',

	props: {
		files: Array,
		selection: Array,
		multiple: {
			type: Boolean,
			default: false
		},
		paginator: {
			perPage: Number,
			currentPage: Number,
			total: Number
		},
		type: null
	},

	data () {
		return {}
	},

	components: {
		FileUpload,
		Pagination
	},

	computed: {
		selectable () {
			return this.selection !== undefined
		}
	},

	methods: {
		select (file) {
			if (!this.selectable) return

			if (this.selection.length > 0 && !this.multiple) this.selection.pop()

			const index = this.selection.indexOf(file)
			if (index > -1) {
				this.selection.splice(index, 1)
			} else {
				this.selection.push(file)
			}

			this.$emit('update:selection', this.selection)
		},

		isSelected (file) {
			if (!this.selectable) return false

			if (!this.selection) return false

			const index = this.selection.map(item => {
				return item && item.id ? item.id : ''
			}).indexOf(file.id)

			return index > -1
		},

		onPageChange (page, pageSize) {
			this.$emit('onDataChange', { page, pageSize })
		}
	}
}
</script>

<style scoped lang="scss">
</style>
