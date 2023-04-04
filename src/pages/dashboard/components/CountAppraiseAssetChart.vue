<template>
	<div>
		<div class="map card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title"> {{ name }} </h3>
					<!-- <div class="d-flex justify-content-between align-items-center">
						<InputDatePickerRange
								:key="key_render"
								vid="search"
								format-date="DD/MM/YYYY"
								:startDateValue="form.fromDate"
								:endDateValue="form.toDate"
								@startDate="form.fromDate = $event"
								@endDate="form.toDate = $event"
							/>
							<button class="btn ml-2" @click="getDataAssetAppraiser()">
								<img src="@/assets/icons/ic_search.svg" alt="search">
							</button>
					</div> -->
				</div>
			</div>
			<div class="card-body card-info">
				<div class="row">
					<div class="col-12">
						<div class="map--vietnam">
							<svg-map :key="key_render" :map="VietNam" :location-class="getLocationClass" @mouseover="pointLocation"
								@mouseout="unpointLocation" @mousemove="moveOnLocation" />
						</div>
						<div v-if="this.dataRange && this.dataRange.length > 0" class='legend-scale '>
							<h3 class='legend-title'>Tài Sản Thẩm Định</h3>
							<ul class='legend-labels'>
								<li v-for="(item, index) in this.dataRange"  :key="index"><span :class="item.bgcolor"></span>{{ item.description }}</li>
							</ul>
						</div>
						<div class="map__tooltip" :style="tooltipStyle">
							<div>
								<p>{{ pointedLocationName }}</p>
								<p>Số lượng: {{ pointedLocationTotal ? this.formatNumber(pointedLocationTotal) : 0 }}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import Report from '@/models/Report'
// import InputDatePickerRange from '@/components/Form/InputDatePickerRange'
import { SvgMap } from 'vue-svg-map'
import VietNam from '@/assets/libs/maps/vietnam'
/**
 * Get the name of the targeted location
 *
 * @param {Node} node - HTML node
 * @returns {string} Name of the location
 */
// eslint-disable-next-line no-redeclare
export function getLocationName (node) {
	return node && node.attributes.name.value
}
// eslint-disable-next-line no-redeclare
export function getLocationID (node) {
	return node && node.attributes.id.value
}

/**
 * Get the name of the selected location
 *
 * @param {Object} map - Map of component
 * @param {string} locationId - Id of selected location
 * @returns {string} Name of the selected location
 */
export function getSelectedLocationName (map, locationId) {
	return locationId && map.locations.find(location => location.id === locationId).name
}
export function getSelectedLocationID (map, locationId) {
	return locationId && map.locations.find(location => location.id === locationId).id
}
export default {
	components: {
		SvgMap
		// InputDatePickerRange
	},
	props: {
		name: {
			type: String,
			default: 'Tài sản thẩm định'
		}
	// 	fromDate: {
	// 		type: String,
	// 		default: null
	// 	},
	// 	toDate: {
	// 		type: String,
	// 		default: null
	// 	}
	},
	data () {
		return {
			// form: {
			// 	fromDate: this.fromDate ? this.fromDate : null,
			// 	toDate: this.toDate ? this.toDate : null
			// },
			VietNam,
			pointedLocationName: null,
			pointedLocationTotal: null,
			tooltipStyle: null,
			colorRange: ['primary', 'info', 'success', 'warning', 'danger'],
			dataMap: [],
			dataRange: [],
			stepRange: 4,
			throttle: 2,
			key_render: 1021
		}
	},
	async mounted () {
		await this.getDataAssetAppraiser()
	},
	methods: {
		formatNumber (num) {
			// convert number to dot format
			return num.toString().replace(/^[+-]?\d+/, function (int) {
				return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
			})
		},
		pointLocation (event) {
			let check = this.dataMap.filter(item => item.province_id === +event.target.attributes.id.value)
			if (check && check.length > 0) {
				this.pointedLocationTotal = check[0].total
			} else this.pointedLocationTotal = 0
			this.pointedLocationName = getLocationName(event.target)
		},
		unpointLocation () {
			this.pointedLocationName = null
			this.tooltipStyle = { display: 'none' }
		},
		moveOnLocation (event) {
			this.tooltipStyle = {
				display: 'block',
				top: `${event.clientY + 25}px`,
				left: `${event.clientX - 70}px`
			}
		},
		getLocationClass (location) {
			let province = this.dataMap.filter(item => item.province_id === Number(location.id))
			if (province && province.length > 0 && province[0].total > 0 && this.dataRange && this.dataRange.length > 0) {
				let provinceClass = this.dataRange[0].class
				for (let i = 1; i < this.dataRange.length; i++) {
					if (province[0].total > this.dataRange[i].value) { provinceClass = this.dataRange[i].class }
				}
				return provinceClass
			} else {
				return ''
			}
		},
		async getDataAssetAppraiser () {
			const res = await Report.getReportAssetAppraiser()
			if (res.data && res.data.length > 0) {
				this.dataMap = res.data
				let maxValue = 0
				res.data.forEach(item => {
					if (item.total > maxValue) { maxValue = item.total }
				})
				let sqrtMaxValue = parseInt(Math.pow(maxValue, 1.0 / this.throttle))

				let interaval = this.stepRange && this.stepRange <= 5 ? parseInt(100 / this.stepRange) : 25
				let preRawValue = 0
				for (let range = interaval, step = 0; step < this.stepRange; range += interaval, step++) {
					let cValue = parseInt(sqrtMaxValue * range / 100)
					this.dataRange.push({
						value: preRawValue,
						class: 'svg-map__location--heat' + step,
						description: this.formatNumber(preRawValue + 1) + ' - ' + this.formatNumber((step < this.stepRange - 1) ? parseInt(Math.pow(cValue, this.throttle)) : maxValue),
						bgcolor: this.colorRange.length > step ? 'bg-' + this.colorRange[step] : ''
					})
					preRawValue = parseInt(Math.pow(cValue, this.throttle))
				}
			} else {
				await this.$toast.open({
					message: 'Hiện không có dữ liệu thống kê tài sản thẩm định',
					type: 'error',
					position: 'top-right',
					duration: 4000
				})
			}
		}
	}
}
</script>

<style lang="scss" scoped >
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #FFFFFF;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 15px;
		margin-bottom: 0;
		color: #E8E8E8;
		border-bottom: 2px solid;

		&__img {
			padding: 8px 20px;
		}

		h3 {
			color: #007EC6;
		}

		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}

	&-body {

		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;

			&-highlight {
				background: rgba(252, 194, 114, 0.53);
				text-align: center;
				padding: 10px 0;
				border-radius: 2px;
			}
		}
	}

	&-land {
		position: relative;
		padding: 0;
	}
}

.map {
	&--vietnam {
		/deep/ .svg-map__location {
			fill: var(--gray);
			&--heat4 {
				fill: var(--danger);
			}

			&--heat3 {
				fill: var(--warning);
			}

			&--heat2 {
				fill: var(--success);
			}

			&--heat1 {
				fill: var(--info);
			}

			&--heat0 {
				fill: var(--primary);
			}

			&:focus,
			&:hover {
				opacity: 0.75;
			}
		}
	}

	&__tooltip {
		position: fixed;
		width: 150px;
		padding: 5px;
		background-color: rgba(0, 0, 0, 0.8);
		color: white;
		text-align: center;
		border-radius: 6px;

		&:after {
			content: "";
			position: absolute;
			top: -10%;
			left: 50%;
			margin-left: -5px;
			border-bottom: 5px solid black;
			border-left: 5px solid transparent;
			border-right: 5px solid transparent;
		}
	}

}
.legend-title {
	text-align: center;
	margin-bottom: 10px;
	margin-top: 10px;
	font-weight: bold;
	font-size: 90%;
	@media (max-width: 1023px) {
		display: none;
		}
}

.legend-scale {
	position: absolute;
	width: 25%;
	top: 15%;
	right: 2%;
	margin: 0;
	word-wrap: break-word;
	background-color: #ffffff;
	background-clip: border-box;
	border: 1px solid rgba(110,117,130,0.2);
	border-radius: 3px;
	@media only screen and (min-width: 481px) and (max-width: 1024px) {
		width: 14%;
		top: 10%;
		right: 3%;
		padding-top: 1em;
		}
	@media (max-width: 481px) {
		padding-top: 1em;
		width: 27%;
		}
}

.legend-scale ul {
	list-style: none;
	padding-left: 10px;
}

.legend-scale ul li {
	font-size: 80%;
	list-style: none;
	line-height: 18px;
	margin-bottom: 2px;
	@media (max-width: 768px) {
		font-size: 0.75rem;
		}
}

.legend-scale .legend-labels li span {
	display: block;
	float: left;
	height: 16px;
	width: 30px;
	margin-right: 5px;
	margin-left: 0;
	border: 1px solid #999;
}

</style>
