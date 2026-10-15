<template>    
    <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
        <div class="flex flex-wrap">
            <p class="text-gray-500 font-semibold text-xl border-b-2">Dashboard</p>
            <span v-if="!infoLoaded">
                <Spinner />
            </span>
            
            <div class="w-full">  
                <div>
                    <h1 class="mt-5">Bem-vindo <b><i>{{ username }}</i></b> </h1>
                </div>                       
            </div>   
        </div>
    </div>    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl border-b-2">Últimos logs</p>
                <span v-if="!infoLoaded">
                    <Spinner />
                </span>
                
                <div class="w-full max-h-80 overflow-x-hidden">  
                    <div v-for="item in logs">
                        <p v-if="parseInfo(item.log)['user'] != ''" @click="$router.push('/user/' + parseInfo(item.log)['user'])" class="bg-gray-400 bg-opacity-20 mt-3 py-1 px-2 rounded me-2 whitespace-nowrap overflow-hidden text-ellipsis w-full hover:cursor-pointer">
                            <a class="text-blue-700" href="#"><font-awesome-icon :icon="['fas', 'person-walking-arrow-right']" /></a> | 
                            <i>{{ formatDate(item.created_at) }}</i> - {{ parseInfo(item.log)['action'] }}
                        </p>
                    </div>
                </div>   
            </div>
        </div>    
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl border-b-2">Submissões por evento</p>
                <span v-if="!infoLoaded">
                    <Spinner />
                </span>
                
                <div class="w-full">  
                    <PieChart :labels="labelsPieChart" :datasets="datasetsPieChart" />
                </div>   
            </div>
        </div>    
    </div>
</template>
  
<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import Spinner from "../../components/Spinner.vue"
import PieChart from "../../components/charts/PieChart.vue"
import { getUserRole } from "../../hooks/useAuth";
import { logsList, submissionsByEventList } from "../../hooks/useReport";
import { format, parseJSON } from 'date-fns';

export default defineComponent({
    async setup() {           
        const infoLoaded = ref(false)       
        const roleUser = ref()        
        const username = ref()
        const logs = ref()
        const labelsPieChart = ref()
        const datasetsPieChart = ref()
        
        roleUser.value = getUserRole()
        username.value = localStorage.getItem('user-name')    
        
        const result = await submissionsByEventList()
        let labels: any = []
        let backgorunds: any = []
        let data: any = []
        for(let c = 0; c < Object.keys(result).length; c++) {
            const keys = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += keys[Math.floor(Math.random() * 16)];
            }            
            labels.push(result[c]['event'])
            backgorunds.push(color)
            data.push(result[c]['submissions'])
        }

        labelsPieChart.value = labels
        datasetsPieChart.value = [{
            backgroundColor: backgorunds,
            data: data
        }]
        
        infoLoaded.value = true          
        return {               
            infoLoaded,   
            roleUser,     
            username,
            logs,
            labelsPieChart,
            datasetsPieChart,
        }
    },   
    methods: {
        async loadLogs() {
            const result = await logsList()
            this.logs = result
        },
        formatDate(date) {
            return format(new Date(date), 'dd/MM/yyyy hh:mm:ss')
        },
        parseInfo(info) {
            info = JSON.parse(info)
            return {
                'action': info.action,
                'user': info.user_id
            }
        },       
    },
    beforeMount() {        
        this.loadLogs()        
    },
    components: { Spinner, PieChart }
})
</script>
  