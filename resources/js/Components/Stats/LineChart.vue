<template>
    <div>
        <vue3-chart-js v-bind="{ ...lineChart }" />

<!--       <Button label="Toggle Values" class="mt-2 p-button-sm p-button-outlined p-button-info" @click="toggleValues"/>-->
    </div>
</template>

<script>
import Vue3ChartJs from "@j-t-mcc/vue3-chartjs";
import zoomPlugin from "chartjs-plugin-zoom";
import dataLabels from "chartjs-plugin-datalabels";
import Button from "primevue/button/sfc";

Vue3ChartJs.registerGlobalPlugins([zoomPlugin]);

export default {
    name: "LineChart",
    components: {
      Button,
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
            showLabels: true,

            lineChart: {
                type: "line",
                //height: this.chartOptions.height,
                //width: "800",
                // locally registered and available for this chart
                plugins: [],
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
                        datalabels: {
                          anchor: 'start',
                          align: 'left',
                          display: (context) => {
                            console.log('running display eval', this.showLabels);
                            return this.showLabels ? 'auto': false;
                          },

                          /*
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
                            */
                        },
                    },
                },
            }
        }
    },

    methods: {
      toggleValues(){
        //this.showLabels = !this.showLabels;
        this.lineChart.plugins.push(dataLabels);
        this.$forceUpdate();
      }
    }
}
</script>

<style scoped>

</style>
