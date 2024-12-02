<script>
import { PolarArea } from 'vue-chartjs'
import ChartDataLabels from 'chartjs-plugin-datalabels'
export default {
	extends: PolarArea,
	props: {
		data: {
			type: Object,
			default: null
		},
		options: {
			type: Object,
			default: null
		}
	},
	mounted () {
		this.addPlugin(ChartDataLabels)
		this.renderChart(this.data, this.options)
	}
}
</script>
