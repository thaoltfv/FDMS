
export default {
	methods: {
		/**
     * Submit Action
     */
		onSuccess (mess, des) {
			this.$notification.success({
				message: mess,
				description: des
			})
		},

		/**
     * Submit Error
     */
		onError (mess, des) {
			this.$notification.error({
				message: mess,
				description: des
			})
		},

		scrollErrors (form) {
			const errors = form.$el.getElementsByClassName('errors')
			if (errors.length && errors[0]) {
				errors[0].scrollIntoView({
					behavior: 'smooth',
					block: 'center'
				})
			}
		}
	}
}
