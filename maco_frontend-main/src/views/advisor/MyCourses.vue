<template>
    <div class="mt-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl">
                    <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)"> <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                    <span class="border-b-2">Meus Cursos</span>
                </p>     
                <span v-if="!infoLoaded">
                    <Spinner />
                </span>         
                <div v-if="infonotnull" class="w-full mt-5">     
                    <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-4">                        
                        <div class="col rounded overflow-hidden shadow-xl border-t-2" v-for="item in courses">                            
                            <div class="px-6 py-4">
                                <div class="font-bold text-xl mb-2">{{ item.name }}</div>
                                <p class="text-gray-700 text-base">{{ item.description }}</p>
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
import { ICourseState, courseListByUser } from '../../hooks/useCourse';
import Spinner from "../../components/Spinner.vue"
import { Toast } from '../../hooks/useToast';

export default defineComponent({
    setup(){
        const state: ICourseState = reactive({
            isLoading: false,
            message: ''
        })

        const infoLoaded = ref(false)     
        const infonotnull = ref(false)   
        const courses = ref()

        return {
            ...toRefs(state),         
            courses, 
            infoLoaded,
            infonotnull,
        }
    }, 
    methods: {
        async loadCourses() {
            const result = (await courseListByUser()).value
            
            if(result.length != 1) {
                this.courses = result   
                this.infonotnull = true             
            } else {
                Toast().fire({icon: 'warning', title: 'Nenhum curso encontrado'})      
            }            
            this.infoLoaded = true                 
        },       
    },  
    beforeMount() {
        this.loadCourses() 
    },
    components: { Spinner }
})

</script>