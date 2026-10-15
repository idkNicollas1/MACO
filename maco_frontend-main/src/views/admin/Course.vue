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
                <div class="w-full">                    
                    <form class="mt-4" @submit.prevent="saveCourse">
                        <div class="grid grid-cols-1 md:gap-4 md:grid-cols-3 xl:grid-cols-3">
                            <div class="">
                                <label class="block">
                                    <span class="text-sm text-gray-700">Nome do curso <span class="text-red-500 font-semibold">*</span></span>
                                    <input type="text"
                                        class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                        v-model="name" />
                                </label>
                            </div>
                            <div class="col-span-2 mt-2 md:mt-0">
                                <label class="block">
                                    <span class="text-sm text-gray-700">Descrição do curso</span>
                                    <input type="text"
                                        class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                        v-model="description" />
                                </label>
                            </div>       
                            <div class="...">
                                <label class="block">
                                    <label class="block">
                                        <span class="text-sm text-gray-700">Status <span class="text-red-500 font-semibold">*</span></span>
                                    </label>
                                    <label class="relative inline-flex cursor-pointer items-center mt-4">                        
                                        <input id="switch-2" type="checkbox" class="peer sr-only" :checked="status" v-on:click="status = !status">
                                        <div class="peer h-4 w-11 rounded-full border bg-slate-200 after:absolute after:-top-1 after:left-0 after:h-6 after:w-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-300 peer-checked:after:translate-x-full peer-focus:ring-green-300"></div>
                                    </label>                        
                                    <span class="absolute ml-3 mt-3">{{ getStatus() }}</span>
                                </label>                                            
                            </div>                    
                        </div>
                        <div v-if="infoLoaded" class="mt-5">
                            <div class="flex justify-end">
                                <button type="submit" :disabled="isLoading"
                                    class="px-12 py-2 text-sm text-center text-white bg-gray-900 rounded-md focus:outline-none font-bold">
                                    <span v-if="isLoading == false">
                                        <font-awesome-icon :icon="['fas', 'floppy-disk']" /> &nbsp; Salvar
                                    </span>
                                    <span v-if="isLoading == true">
                                        <svg aria-hidden="true"
                                            class="inline w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300"
                                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                fill="currentColor" />
                                            <path
                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                fill="currentFill" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import { ICourseState, courseAdd, courseDetails, courseEdit } from "../../hooks/useCourse"
import Spinner from "../../components/Spinner.vue"
import { Toast } from "../../hooks/useToast"
import router from "../../router"

export default defineComponent({
    setup() {
        const state: ICourseState = reactive({
            isLoading: false,
            message: '',
        })

        const infoLoaded = false
        const pageTitle = ref("")
        const id = ref("")
        const name = ref("")
        const description = ref("")     
        const status = ref()   
        const isEdit = false
       
        async function saveCourse() {
            state.isLoading = true

            if (name.value == "") {
                Toast().fire({icon: 'warning', title: 'Informe o nome do curso!'})
                state.isLoading = false
                return
            }            

            if (router.currentRoute.value.params.action == 'add') {        
                const result = await courseAdd({
                    name: name.value,
                    description: description.value, 
                    status: status.value                   
                })
                if (result.status == 'success') {
                    Toast().fire({icon: 'success', title: 'Curso gravado!'})
                    router.push('/courses')
                } else {
                    Toast().fire({icon: 'error', title: result.message})
                }                    
            } else {
                const result = await courseEdit({
                    id: id.value,
                    name: name.value,
                    description: description.value,       
                    status: status.value             
                })
                if (result.status == 'success') {
                    Toast().fire({icon: 'success', title: 'Curso editado!'})                    
                } else {
                    Toast().fire({icon: 'error', title: result.message})
                }                    
            }            
            state.isLoading = false            
        }

        return {
            ...toRefs(state),
            infoLoaded,
            pageTitle,
            id,
            name,
            description,
            status,
            isEdit,
            saveCourse,
        }
    },
    methods: {
        async loadCourse() {
            const result = await courseDetails(this.$route.params.action)                                

            if (result.value['name'] == undefined) {
                Toast().fire({icon: 'error', title: 'Curso não encontrado!'})
            }                            
            this.id = result.value['id']
            this.name = result.value['name']
            this.description = result.value['description']            
            this.status = result.value['status']
            this.infoLoaded = true          
        },      
        getStatus() {            
            return this.status ? 'Ativo' : 'Inativo'
        }, 
    },
    beforeMount() {
        switch (router.currentRoute.value.params.action) {
            case 'add':
                this.pageTitle = 'Inserir curso'
                this.infoLoaded = true
                break;            
            default:
                this.pageTitle = 'Editar curso'
                this.loadCourse()
                this.isEdit = true
        }
    },
    components: { Spinner }
})
</script>
