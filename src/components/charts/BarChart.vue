<script>
import { Bar, mixins } from 'vue-chartjs'
import 'chartjs-plugin-annotation'
import ChartDataLabels from 'chartjs-plugin-datalabels'
const { reactiveProp } = mixins

export default {
	extends: Bar,
	mixins: [reactiveProp],
	props: ['chartData', 'options'],
	mounted () {
		this.addPlugin(ChartDataLabels)
		this.renderChart(this.chartData, this.options)
	}
}
</script>
