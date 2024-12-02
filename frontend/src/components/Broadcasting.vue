<template>
  <div id="laravel-echo">
    <!-- <template v-if="isConnected">
      <ul v-for="object in notifications">
        {{ object }}
      </ul>
      <button @click="disconnect">Disconnect</button>
    </template>
    <template v-else-if="currentUser">
      <button @click="connect">Connect</button>
    </template> -->
  </div>
</template>

<script>
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import store from '@/store'
import { SET_UNREAD_NOTIFICATION } from '@/store/mutation-types'

// Enable pusher logging - don't include this in production
Pusher.logToConsole = false

export default {
	name: 'Broadcasting',
	data () {
		return {
			echo: null
		}
	},
	computed: {
		currentUser () {
			if (store.getters.profile !== null) {
				return store.getters.profile.data.user
			}
		},
		unreadNotificationCount () {
			return store.getters.unreadNotification
		},
		isConnected () {
			return (
				this.echo && this.echo.connector.pusher.connection.connection !== null
			)
		}
	},
	methods: {
		bindChannels () {
			const channel = this.echo.private(`App.Models.User.${this.currentUser.id}`)
			channel.notification((object) => {
				this.$toast.open({
					message: object.data.message || 'test',
					type: 'info',
					position: 'top-right',
					duration: 5000
				})
				store.commit(SET_UNREAD_NOTIFICATION, this.unreadNotificationCount + 1)
			})
		},
		disconnect () {
			if (!this.echo) return
			this.echo.disconnect()
		},
		connect () {
			if (!this.echo) {
				this.echo = new Echo({
					// broadcaster: 'socket.io',
					// host: window.location.hostname + ':6001',
					broadcaster: 'pusher',
					key: process.env.PUSHER_KEY || '1c19bd127bdec70c85b3',
					authEndpoint: process.env.API_URL + '/broadcasting/auth',
					cluster: 'ap1',
					encrypted: true,
					auth: {
						headers: {
							Authorization: `Bearer ${localStorage.getItem('token')}`
						}
					}
				})
				this.echo.connector.pusher.connection.bind('connected', (event) =>
					this.connect(event)
				)
				this.echo.connector.pusher.connection.bind('disconnected', () =>
					this.disconnect()
				)
			} else {
				this.echo.connector.pusher.connect()
				this.bindChannels()
			}
		}
	}
}
</script>
