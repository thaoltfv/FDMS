import Vue from 'vue'
import 'ant-design-vue/dist/antd.css'

import {
	Card,
	Table,
	Tooltip,
	Popconfirm,
	Icon,
	Button,
	Select,
	Input,
	Switch,
	DatePicker,
	Modal,
	Empty,
	Collapse,
	notification,
	message,
	Progress,
	Pagination,
	Popover,
	Spin,
	Transfer,
	Radio,
	Checkbox,
	Drawer,
	Timeline
} from 'ant-design-vue'

Vue.use(Card)
Vue.use(Table)
Vue.use(Tooltip)
Vue.use(Popconfirm)
Vue.use(Icon)
Vue.use(Button)
Vue.use(Select)
Vue.use(Input)
Vue.use(Switch)
Vue.use(DatePicker)
Vue.use(Modal)
Vue.use(Empty)
Vue.use(Progress)
Vue.use(Pagination)
Vue.use(Collapse)
Vue.use(Popover)
Vue.use(Spin)
Vue.use(Transfer)
Vue.use(Radio)
Vue.use(Checkbox)
Vue.use(Drawer)
Vue.use(Timeline)
Vue.prototype.$notification = notification
Vue.prototype.$message = message
Vue.prototype.$confirm = Modal.confirm
