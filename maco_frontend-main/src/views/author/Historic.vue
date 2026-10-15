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
                                    <p><b>Status:</b> <span :class="getColorArticleStatus(item.status)" class="rounded pr-3 pl-3 text-white">{{ formatArticleStatus(item.status) }}</span></p>
                                    <p><b>Autores:</b> {{ formatAuthors(item.authors, 'name') }}</p>
                                    <p><b>Evento:</b> <i>{{ item.event_name }}</i></p>
                                </div>     
                                <div class="mt-2 text-center">
                                    <button title="Visualizar o artigo" href="#" v-on:click="viewArticle(item.id, item.event)" class="px-2 mt-1 mb-1 sm:mt-0 py-2 text-sm text-center text-white bg-gray-900 me-2 rounded-md focus:outline-none font-bold w-10"> 
                                        <font-awesome-icon :icon="['fas', 'eye']" />
                                    </button>                                    
                                    <Export :article="item.id"/> 
                                    <Certificate v-if="item.status == 'finished'" :article="item"/>                          
                                </div>                                                               
                            </div>     
                        </div>  
                    </div>                                                                                                                                                                                                         
                </div>   
                <div v-else class="w-full mt-5"> 
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3">                        
                        <div class="w-full grid grid-cols-6 gap-4">                    
                            Nenhum artigo                                        
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
import { submissionsList } from '../../hooks/useArticle';
import Spinner from "../../components/Spinner.vue"
import Certificate from "../../components/Certificate.vue"
import { format } from 'date-fns';
import router from '../../router';
import { Toast } from '../../hooks/useToast';
import { getUserRole } from '../../hooks/useAuth';
import Export from '../../components/Export.vue';

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
            let userid = this.$route.params.userid
            let result
            let articles = []

            if (userid != undefined && userid != "") {
                this.pageTitle = "Histórico de submissões do usuário"
                result = (await submissionsList('admin', '')).value
                for(let c = 0; c < Object.keys(result).length; c++) {
                    for(let j = 0; j < Object.keys(result[c].authors).length; j++) {
                        if (result[c].authors[j].id == userid) {
                            try {
                                articles.push(result[c])
                                break
                            } catch {}                        
                        }                    
                    }
                }  
                if(articles.length > 0) {      
                    this.infoNull = false          
                    this.articles = articles                
                }   
            } else if(role == 'author' || role == 'external') {
                this.pageTitle = "Meus artigos"
                result = (await submissionsList(role , '')).value    
                if(result[0] != undefined) {      
                    this.infoNull = false          
                    this.articles = result                
                }   
            }                                             
            this.infoLoaded = true 
        },               
        formatDate(date) {
            return format(new Date(date), 'dd/MM/yyyy')
        },
        formatArticleStatus(status) {
            let newStatus = ''
            switch(status) {
                case 'in_submission':
                    newStatus = 'Em submissão'
                    break
                case 'in_revision':
                    newStatus = 'Em revisão'
                    break
                case 'in_correction':
                    newStatus = 'Em correção'
                    break
                case 'approved':
                    newStatus = 'Aprovado'
                    break  
                case 'finished':
                    newStatus = 'Finalizado'
                    break   
            }
            return newStatus;
        },
        getColorArticleStatus(status) {
            let color = ''
            switch(status) {
                case 'in_submission':
                    color = 'bg-yellow-500'
                    break
                case 'in_revision':
                    color = 'bg-cyan-500'
                    break
                case 'in_correction':
                    color = 'bg-red-500'
                    break
                case 'approved':
                    color = 'bg-green-500'
                    break  
                case 'finished':
                    color = 'bg-gray-900'
                    break   
            }
            return color;
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
    components: { Spinner, Certificate, Export }
})
</script>