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
                <div class="mt-5">
                    <p>
                        Seu papel no sistema será preencher e enviar seus trabalhos da semana de evidência da UNIFAE, para que os professores aprovem e enviem para o responsável do evento. <br><br>
                        Clique no botão asseguir para enviar seu trabalho:
                    </p>
                </div>
                <div class="mt-5">
                    <a class="px-2 sm:px-8 md:px-12 mt-1 mb-1 sm:mt-0 py-2 mr-2 text-sm text-center text-white bg-gray-800 rounded-md focus:outline-none font-bold" href="#" v-on:click="hrefSubmissions">
                        <font-awesome-icon :icon="['fas', 'paper-plane']" /> &nbsp; Enviar trabalho
                    </a>
                </div>
            </div>   
        </div>
    </div>    
</template>
  
<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import Spinner from "../../components/Spinner.vue"
import { getUserRole } from "../../hooks/useAuth";
import router from "../../router";

export default defineComponent({
    async setup() {           
        const infoLoaded = ref(false)       
        const roleUser = ref()        
        const username = ref()
        
        roleUser.value = getUserRole()
        username.value = localStorage.getItem('user-name')
        
        infoLoaded.value = true          
        return {               
            infoLoaded,   
            roleUser,     
            username
        }
    },    
    methods: {
        hrefSubmissions() {
            router.push('/submissions')
        }
    },
    components: { Spinner }
})
</script>
  