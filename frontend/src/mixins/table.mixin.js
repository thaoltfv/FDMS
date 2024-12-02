import { LOCALE } from '../enum/locale.enum'

export default {
	methods: {
		// pagination in table
		buildOptionText (pageSize) {
			if (this.$i18n.locale === LOCALE.ja) {
				return `${pageSize.value} 件 / ページ`
			} else if (this.$i18n.locale === LOCALE.vi) {
				return `${pageSize.value} 件 / trang`
			}
			return `${pageSize.value} 件 / page`
		},

		showTotal (total, range) {
			if (this.$i18n.locale === LOCALE.ja) {
				return `${range[0]} - ${range[1]} 件 / 総件数 ${total}件`
			}
			return `${range[0]}-${range[1]} of ${total} items`
		}
	}
}
