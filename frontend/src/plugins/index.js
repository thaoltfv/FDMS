import '@/assets/css/tabler-buttons.min.css'
import '@/assets/css/tabler.css'
import '@/assets/css/common.css'
import '@/assets/scss/main.scss'

import '@/assets/libs/jquery/dist/jquery.slim.min.js'
import '@/assets/libs/bootstrap/dist/js/bootstrap.bundle.min'
import '@/assets/js/tabler.min'

import './axios'
import './bootstrap-vue'

import * as moment from 'moment-timezone'

moment.locale('vi')
moment.updateLocale('vi', {
	relativeTime: {
		future: 'Còn lại %s',
		past: 'Đã hết hạn %s',
		s: 'vài giây',
		ss: '%d giây',
		m: '1 phút',
		mm: '%d phút',
		h: '1 giờ', // this is the setting that you need to change
		hh: '%d giờ',
		d: '1 ngày',
		dd: '%d ngày',
		w: '1 tuần',
		ww: '%d tuần',
		M: '1 tháng', // change this for month
		MM: '%d tháng',
		y: '1 năm',
		yy: '%d năm'
	}
})
// moment.tz.setDefault('Japan')
