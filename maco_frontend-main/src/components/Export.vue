<template>
    <button type="button" :disabled="isLoading" title="Exportar artigo para Docx" v-on:click="exportArticle" class="px-2 mt-1 mb-1 sm:mt-0 py-2 text-sm text-center text-white bg-blue-800 me-2 rounded-md focus:outline-none font-bold" :class="!$props.btnText ? 'w-10' : 'sm:px-8 md:px-12'">
        <span v-if="!isLoading">
            <font-awesome-icon :icon="['fas', 'file-word']" /> <span v-if="$props.btnText"> Exportar artigo</span>
        </span>        
        <span v-else>
            <Loading />
        </span>
    </button>   
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { Toast } from "../hooks/useToast"
import Loading from './Loading.vue';
import { articleExport } from '../hooks/useArticle';

export default defineComponent({
    props: {
        article: Number,        
        btnText: Boolean,
    },
    async setup(props){              
       
        const isLoading = ref(false)
        
        async function exportArticle() {            
            isLoading.value = true
            const result = await articleExport(props.article)
            if (result) {
                Toast().fire({icon: 'success', title: 'Artigo exportado'})
            } else {
                Toast().fire({icon: 'error', title: 'Erro ao exportar artigo'})
            }            
            isLoading.value = false
        }
        
        return {
            exportArticle,            
            isLoading,
        }    
    },
    components: {
        Loading
    }
})
</script>