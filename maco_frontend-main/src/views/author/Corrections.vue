<template>   
    <div class="mt-2 mb-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl">
                    <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)"> <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                    <span class="border-b-2">Correções pendentes</span>
                </p>    
                <span v-if="!infoLoaded">
                    <Spinner />
                </span>                      
                <div v-if="!infoNull" class="w-full mt-5">                         
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3" v-for="item in articles">                        
                        <div class="w-full grid grid-cols-6 gap-4">                    
                            <div class="col-start-1 col-end-8 ...">
                                <div class="items-center">
                                    <span>{{ item.id }}</span> - {{ item.title }}
                                </div>
                            </div>
                            <div class="col-end-10 col-span-2 ...">
                                <a href="#" v-on:click="continueSubmission(item.id, item.event)" class="bg-blue-700 text-white ps-2 pe-2 pt-1 pb-1 rounded-md"> 
                                    <font-awesome-icon :icon="['fas', 'newspaper']" /> Realizar correção
                                </a>   
                                &nbsp;
                                <a href="#" v-on:click="delArticleInCorrection(item.id)" class="bg-red-600 text-white ps-2 pe-2 pt-1 pb-1 rounded-md"> 
                                    <font-awesome-icon :icon="['fas', 'trash-can']" /> Excluir
                                </a>                                
                            </div>                
                        </div>
                    </div>                     
                </div>   
                <div v-else class="w-full mt-5"> 
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3">                        
                        <div class="w-full">                    
                            Nenhum artigo pendente de correção                                         
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
        const infoNull = ref(true)           
        const events = ref()
        const articles = ref()

        return {
            ...toRefs(state),         
            events, 
            infoLoaded,
            infoNull,            
            articles,
        }
    }, 
    methods: {       
        continueSubmission(articleID, eventID) {            
            sessionStorage.setItem('article-added', 'y')    
            sessionStorage.setItem('event-id-selected', eventID)
            router.push(`/submit/${articleID}`)
        },       
        async loadArticlesInCorrection() {            
            const result = (await submissionsList('author', 'article_status=3')).value

            if(result[0] != undefined) {      
                this.infoNull = false          
                this.articles = result                
            }   
            this.infoLoaded = true                 
        },
        async delArticleInCorrection(articleID) {
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
                    Toast().fire({icon: 'info', title: 'Carregando...'})                
                    const result = await submissionDelete(articleID)
                    if (result.status == 'success') {                            
                        Toast().fire({icon: 'success', title: 'Artigo excluído com sucesso'})      
                        this.loadArticlesInCorrection()
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
        this.loadArticlesInCorrection()        
    },
    components: { Spinner }
})
</script>