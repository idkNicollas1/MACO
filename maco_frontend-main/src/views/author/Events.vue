<template>
    <div class="mt-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl">
                    <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)"> <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                    <span class="border-b-2">Eventos disponíveis</span>
                </p>     
                <span v-if="!infoLoaded">
                    <Spinner />
                </span>         
                <div v-if="infonotnull" class="w-full mt-5">                         
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3" v-for="item in events">                        
                        <div class="w-full grid grid-cols-2 gap-4">                    
                            <div class="col-start-1 col-end-12 md:col-start-1 md:col-end-8">
                                <div class="items-center pt-1">
                                    {{ item.name }} - <b>{{ formatDate(item.start) }}</b> até <b>{{ formatDate(item.end) }}</b>
                                </div>
                            </div>
                            <div class="col-start-1 col-end-12 md:col-end-10 md:col-span-2">
                                <a href="#" v-on:click="newSubmission(item)" class="bg-green-700 text-white ps-2 pe-2 pt-1 pb-1 rounded-md float-end"> 
                                    <font-awesome-icon :icon="['fas', 'newspaper']" /> Submeter
                                </a>                                
                            </div>                
                        </div>
                    </div>                     
                </div>
                <div v-else class="w-full mt-5"> 
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3">                        
                        <div class="w-full">                    
                            Nenhum evento disponível para submissão                                  
                        </div>
                    </div>   
                </div> 
            </div>
        </div>
    </div>

    <div v-if="showIncomplete" class="mt-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl border-b-2">Artigos incompletos</p>                     
                <div class="w-full mt-5">                         
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3" v-for="item in articles">                        
                        <div class="w-full grid grid-cols-2 gap-4">                    
                            <div class="col-start-1 col-end-12 md:col-start-1 md:col-end-8">
                                <div class="items-center pt-1">
                                    <span>{{ item.id }}</span> - {{ item.title == ' ' ? 'Título não informado' : item.title }}
                                </div>
                            </div>
                            <div class="col-start-1 col-end-12 md:col-end-10 md:col-span-2 inline-flex">
                                <a href="#" v-on:click="continueSubmission(item.id, item.event)" class="bg-blue-700 text-center text-white ps-2 pe-2 pt-1 pb-1 rounded-md"> 
                                    <font-awesome-icon :icon="['fas', 'newspaper']" /> Continuar
                                </a>   
                                &nbsp;
                                <a href="#" v-on:click="delArticleIncomplete(item.id)" class="bg-red-600 text-center text-white ps-2 pe-2 pt-1 pb-1 rounded-md"> 
                                    <font-awesome-icon :icon="['fas', 'trash-can']" /> Excluir
                                </a>                                
                            </div>                
                        </div>
                    </div>                     
                </div>               
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from 'vue';
import { IEventState, eventActiveList } from '../../hooks/useEvent';
import { submissionsList, submissionDelete } from '../../hooks/useArticle';
import Spinner from "../../components/Spinner.vue"
import Swal from "sweetalert2"
import { format } from 'date-fns';
import router from '../../router';
import { Toast } from '../../hooks/useToast';

export default defineComponent({
    setup(){
        const state: IEventState = reactive({
            isLoading: false,
            message: ''
        })

        const infoLoaded = ref(false)     
        const infonotnull = ref(false)   
        const showIncomplete = ref(false)
        const events = ref()
        const articles = ref()

        return {
            ...toRefs(state),         
            events, 
            infoLoaded,
            infonotnull,
            showIncomplete,
            articles,
        }
    }, 
    methods: {
        newSubmission(event) {  
            Swal.fire({
                title: 'Instruções para o evento',
                html: `<div class="text-justify">${event.instructions.replaceAll('\n', '<br>')}</div>`,
                focusConfirm: false,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",                
                showCancelButton: true,
                confirmButtonText: 'Estou ciente! Continuar',
                cancelButtonText: 'Cancelar',
                customClass: 'swal-wide'
            }).then(async (result) => {            
                if (result.isConfirmed) {      
                    sessionStorage.setItem('article-added', 'n')    
                    sessionStorage.setItem('event-id-selected', event.id)
                    router.push('/submit')
                }
            })                      
        },
        continueSubmission(articleID, eventID) {            
            sessionStorage.setItem('article-added', 'y')    
            sessionStorage.setItem('event-id-selected', eventID)
            router.push(`/submit/${articleID}`)
        },
        async loadevents() {
            const result = (await eventActiveList()).value
            
            if(result[0]) {
                this.events = result   
                this.infonotnull = true             
            } else {
                Toast().fire({icon: 'warning', title: 'Nenhum evento encontrado'})                     
            }            
            this.infoLoaded = true                 
        },       
        async loadArticlesIncomplete() {            
            this.showIncomplete = false
            const result = (await submissionsList('author', 'article_status=1')).value

            if(result[0] != undefined) {
                this.articles = result
                this.showIncomplete = true
            }                  
        },
        async delArticleIncomplete(articleID) {
            Swal.fire({
                title: "Tem certeza?",
                html: `Você está prestes a excluir. Está certo disso?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sim, excluir!",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {                    
                    const result = await submissionDelete(articleID)
                    if (result.status == 'success') {                            
                        Toast().fire({icon: 'success', title: 'Artigo excluído com sucesso'})      
                        this.loadArticlesIncomplete()
                    } else {                 
                        Toast().fire({icon: 'error', title: 'Erro ao excluir'})        
                    }                            
                }
            })      
        },
        formatDate(date) {
            return format(new Date(date), 'dd/MM/yyyy')
        }
    },  
    beforeMount() {        
        this.loadArticlesIncomplete()
        this.loadevents() 
    },
    components: { Spinner }
})
</script>