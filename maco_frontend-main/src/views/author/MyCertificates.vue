<template>   
    <div class="mt-2 mb-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl">
                    <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)"> <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                    <span class="border-b-2">{{ pageTitle }}</span>
                </p>    
                <span v-if="!infoLoaded">
                    <Spinner />
                </span>                      
                <div v-if="!infoNull" class="mt-5 w-full">                     
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">                                    
                        <div class="col w-full rounded overflow-hidden shadow-lg" v-for="item in articles">
                            <div class="px-6 py-4">
                                <div class="items-center">
                                    <p><b>Título:</b> {{ item.title }}</p>                                    
                                    <p><b>Autores:</b> {{ formatAuthors(item.authors, 'name') }}</p>
                                    <p><b>Evento:</b> <i>{{ item.event_name }}</i></p>
                                </div>     
                                <div class="mt-2 text-center">                                    
                                    <Certificate v-if="item.status == 'finished'" :article="item" :btnText="true"/>                          
                                </div>                                                               
                            </div>     
                        </div>  
                    </div>                                                                                                                                                                                                         
                </div>   
                <div v-else class="w-full mt-5"> 
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3">                        
                        <div class="w-full grid grid-cols-6 gap-4">                    
                            Nenhum certificado                                        
                        </div>
                    </div>   
                </div>            
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from 'vue';
import { IEventState } from '../../hooks/useEvent';
import { submissionsList, articleExport } from '../../hooks/useArticle';
import Spinner from "../../components/Spinner.vue"
import Certificate from "../../components/Certificate.vue"
import { format } from 'date-fns';
import router from '../../router';
import { Toast } from '../../hooks/useToast';
import { getUserRole } from "../../hooks/useAuth";

export default defineComponent({
    setup(){
        const state: IEventState = reactive({
            isLoading: false,
            message: ''
        })

        const pageTitle = ref("")
        const infoLoaded = ref(false)     
        const infoNull = ref(true)           
        const events = ref()
        const articles = ref()

        return {
            ...toRefs(state),         
            pageTitle,
            events, 
            infoLoaded,
            infoNull,            
            articles,
        }
    }, 
    methods: {       
        viewArticle(articleID, eventID) {            
            sessionStorage.setItem('article-added', 'y')    
            sessionStorage.setItem('event-id-selected', eventID)
            router.push(`/submit/${articleID}`)
        },       
        async loadUserHistoric() {              
            const role = getUserRole(true).toLowerCase()                      
            this.pageTitle = "Meus certificados"
            let result = (await submissionsList(role, 'article_status=5')).value    
            if(result[0] != undefined) {      
                this.infoNull = false          
                this.articles = result                
            }                                       
            this.infoLoaded = true 
        },               
        formatDate(date) {
            return format(new Date(date), 'dd/MM/yyyy')
        },       
        formatAuthors(data, key) {
            let _array : string[] = [];            
            let _str = ''
            for(let c = 0; c < Object.keys(data).length; c++) {
                try {
                    if (! _array.includes(data[c][key])){
                        _array.push(data[c][key])
                    }                                        
                } catch(e) {}                
            }
            _array.forEach(item => {
                _str += item + ' / '
            });
            return _str.slice(0, -3)         
        },       
    },  
    beforeMount() {        
        this.loadUserHistoric()        
    },
    components: { Spinner, Certificate }
})
</script>