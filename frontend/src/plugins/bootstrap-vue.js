import Vue from 'vue'
import {
	BFormDatepicker,
	BPagination,
	BTable,
	FormDatepickerPlugin,
	PaginationPlugin,
	TableSimplePlugin
} from 'bootstrap-vue'
import 'bootstrap-vue/dist/bootstrap-vue.min.css'

Vue.use(TableSimplePlugin)
Vue.use(PaginationPlugin)
Vue.use(FormDatepickerPlugin)

Vue.component('b-table', BTable)
Vue.component('b-pagination', BPagination)
Vue.component('b-datepicker', BFormDatepicker)
