import Vue from 'vue'
import * as moment from 'moment-timezone'

Vue.filter('formatDate', function (value, format = '') {
	let date = moment(value)
	if (!date.isValid()) return ''
	return date.format(format)
})

Vue.filter('momentTimestamp', function (value, format = '') {
	return moment.unix(value).format(format)
})

export const convertPagination = (pagination) => {
	return {
		defaultCurrent: 1,
		current: pagination.current_page,
		total: pagination.total,
		defaultPageSize: 15,
		pageSize: pagination.per_page,
		position: 'both'
	}
}

Vue.filter('truncate', (text, suffix, num) => {
	if (text !== undefined && text.length > num) {
		return text.substring(0, num) + suffix
	} else {
		return text
	}
})

Vue.filter('momentJa', date => {
	return moment(date).format('YYYY/MM/DD')
})

Vue.filter('imageThumbnailObject', value => {
	return value || require('../assets/images/dummy_image.png')
})
