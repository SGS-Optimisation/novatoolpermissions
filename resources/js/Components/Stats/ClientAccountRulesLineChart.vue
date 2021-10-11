<script>
import { defineComponent } from 'vue'
import { Line } from 'vue3-chart-v2'

export default defineComponent({
    name: 'RulesChart',
    extends: Line,
    props: {
        chartData: {
            type: Object,
            required: true
        },
        chartOptions: {
            type: Object,
            required: false
        },
    },
    mounted () {
        // this.chartData is created in the mixin.
        // If you want to pass options please create a local options object
        if(this.chartData)
            this.renderChart(this.chartData, this.chartOptions)
    }
})
</script>
