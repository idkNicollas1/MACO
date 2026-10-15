<template>
    <div class="mt-2 mb-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2 mb-2">
            <div class="flex flex-wrap">
                <div class="w-full grid grid-cols-6 gap-4">                    
                    <div class="col-start-1 col-end-8 ...">
                        <p class="text-gray-500 font-semibold text-xl">
                            <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)"> <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                            <span class="border-b-2">Listagem de escolaridades</span>
                            <span v-if="!infoLoaded">
                                <Spinner />
                            </span>
                        </p>
                    </div>
                    <div class="col-end-10 col-span-2 ...">
                        <button @click="$router.push('/schooling/add')" class="bg-green-700 text-white ps-2 pe-2 pt-1 pb-1 rounded-md"> 
                            <font-awesome-icon :icon="['fas', 'file-circle-plus']" />  <span class="hidden md:inline">Adicionar Escolaridade</span>
                        </button>
                    </div>                
                </div>    
                <div v-if="infonotnull" class="overflow-x-auto inline-block min-w-full rounded-lg">                    
                    <table class="min-w-full leading-normal mt-5">
                        <thead>
                            <tr>
                                <th class="px-5 text-start border-b-2">ID</th>
                                <th class="px-5 text-start border-b-2">Nome</th>                                     
                                <th class="px-5 text-center border-b-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in schoolings">
                                <td class="px-5 py-4 text-sm bg-white border-b border-gray-200">
                                    <p :class="!item.status ? 'text-red-500' : ''" class="text-gray-900 whitespace-nowrap">{{ item.id }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm bg-white border-b border-gray-200 hover:cursor-pointer" @click="$router.push('/schoolign/' + item.id)">
                                    <p :class="!item.status ? 'text-red-500' : ''" class="text-gray-900 whitespace-nowrap">{{ item.name }}</p>
                                </td>                                                            
                                <td class="px-5 py-4 text-sm bg-white border-b border-gray-200 text-center">
                                    <div class="sm:inline-block">                                        
                                        <a title="Editar curso" href="#" @click="$router.push('/schooling/' + item.id)" class="bg-gray-900 text-white rounded p-2 ps-3 pe-3 me-2 w-10"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></a>                                        
                                    </div>                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import Spinner from "../../components/Spinner.vue"
import { ISchoolingState, schoolingList } from "../../hooks/useSchooling"
import { Toast } from "../../hooks/useToast"
import router from "../../router"

export default defineComponent({
    async setup() {
        const state: ISchoolingState = reactive({
            isLoading: false,
            message: '',
        })
        const infoLoaded = ref(false)        
        const infonotnull = ref(false)
        const schoolings = ref()
       
        return {
            ...toRefs(state),
            schoolings,
            infonotnull,
            infoLoaded,            
        }
    },
    methods: {
        async loadSchoolings() {                        
            const result = (await schoolingList()).value // Consome a API

            if(result.length != 1) {
                this.schoolings = result
                this.infonotnull = true
            } else {
                Toast().fire({icon: 'warning', title: 'Nenhum curso encontrado'})      
            }            
            this.infoLoaded = true                                          
        },                           
    },
    beforeMount() {
        this.loadSchoolings()
    },
    components: { Spinner }
})
</script>