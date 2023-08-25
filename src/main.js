// Import Core
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'

// import Plugins
import './plugins'
import './plugins/progress-bar'
import './plugins/lazyload'
import './plugins/validation'
import './plugins/antdesign'
import i18n from './plugins/i18n'
import VueToast from 'vue-toast-notification'
// import filter
import './utils/filters'
import './mixins/global.mixin'
import 'vue-toast-notification/dist/theme-sugar.css'
import * as VueGoogleMaps from 'vue2-google-maps'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faBookOpen, faMapMarkedAlt, faMoneyBillAlt, faHistory, faUsersCog, faListUl, faRoad, faCogs, faUserCircle, faAngleRight, faUsers, faListAlt, faCertificate, faFileSignature, faChartBar, faChartLine, faBell, faSync, faPrint, faCloudUploadAlt, faTrashAlt, faStickyNote } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import firebase from 'firebase/app'
import 'firebase/auth'
import 'firebase/firestore'

import VueEllipseProgress from 'vue-ellipse-progress';
Vue.use(VueEllipseProgress, "vep")


//cors
// import AxiosPlugin from 'vue-axios-cors';
// Vue.use(AxiosPlugin)

Vue.config.productionTip = false
Vue.use(VueToast)
Vue.use(VueGoogleMaps, {
	load: {
		key: 'AIzaSyCDqzkgEKPFrfR65Qz6Ha80cW9bK9nRrMo',
		libraries: 'places',
		region: 'VI',
		language: 'vi'
	}
})

library.add(faBookOpen, faMapMarkedAlt, faMoneyBillAlt, faHistory, faUsersCog, faListUl, faRoad, faCogs, faUserCircle, faAngleRight, faUsers, faListAlt, faCertificate, faFileSignature, faChartBar, faChartLine, faBell, faSync, faPrint, faCloudUploadAlt, faTrashAlt, faStickyNote)

Vue.component('font-awesome-icon', FontAwesomeIcon)

export default {
	computed: {
		google: VueGoogleMaps.gmapApi
	}
}
// Initialize Firebase
if (!firebase.apps.length) {
	firebase.initializeApp(process.env.firebaseConfig)
}
/* eslint-disable no-new */
new Vue({
	router,
	store,
	i18n: i18n, // language
	render: h => h(App)
}).$mount('#app')
