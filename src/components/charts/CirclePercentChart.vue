<script>
import { Doughnut } from 'vue-chartjs'
import ChartDataLabels from 'chartjs-plugin-datalabels'
export default {
	extends: Doughnut,
	props: {
		data: {
			type: Object,
			default: null
		},
		options: {
			type: Object,
			default: null
		},
		text_center: {
			typeof: String || Number,
			defaults: ''
		}
	},
	mounted () {
		const customPlugin = (chart) => {
			const { width, height, ctx } = chart.chart
			ctx.restore()
			const fontSize = (height / 114).toFixed(2)
			ctx.font = `bold ${fontSize}rem sans-serif`
			ctx.textBaseline = 'middle'
			ctx.fillStyle = '#3D4D65'
			const text = this.text_center
			const textX = Math.round((width - ctx.measureText(text).width - 140) / 2)
			const textY = height / 2
			ctx.fillText(text, textX, textY)
			ctx.save()
		}
		this.addPlugin(ChartDataLabels)
		this.addPlugin({
			id: 'my-plugin',
			beforeDraw: customPlugin
		})
		this.renderChart(this.data, this.options)
	},
	methods: {
	}
}
</script>
