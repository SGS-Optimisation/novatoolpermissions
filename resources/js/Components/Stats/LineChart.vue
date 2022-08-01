<template>
    <div>
        <vue3-chart-js v-bind="{ ...lineChart }" />
    </div>
</template>

<script>
import Vue3ChartJs from "@j-t-mcc/vue3-chartjs";
import zoomPlugin from "chartjs-plugin-zoom";
import dataLabels from "chartjs-plugin-datalabels";

Vue3ChartJs.registerGlobalPlugins([zoomPlugin]);

export default {
    name: "LineChart",
    components: {
        Vue3ChartJs,
    },
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
    data(){
        return {
            lineChart: {
                type: "line",
                //height: this.chartOptions.height,
                //width: "800",
                // locally registered and available for this chart
                plugins: [dataLabels],
                data: this.chartData,
                options: {
                    responsive: true,
                    plugins: {
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: false,
                                },
                                pinch: {
                                    enabled: true,
                                },
                                mode: "y",
                            },
                        },
                        /*datalabels: {
                            backgroundColor: function (context) {
                                return context.dataset.backgroundColor;
                            },
                            borderRadius: 4,
                            color: "white",
                            font: {
                                weight: "bold",
                            },
                            formatter: Math.round,
                            padding: 6,
                        },*/
                    },
                },
            }
        }
    },
}
</script>

<style scoped>

</style>
