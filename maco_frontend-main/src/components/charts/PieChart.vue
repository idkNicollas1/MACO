<template>
    <div class="mt-3">
        <Pie :data="data" :options="options" />
    </div>    
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

export default defineComponent({
    props: {
        labels: Array,        
        datasets: Object,
    },        
    setup(props){
        return {
            data: {
                labels: props.labels,
                datasets: props.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    },
    components: {
        Pie
    },
})
</script>