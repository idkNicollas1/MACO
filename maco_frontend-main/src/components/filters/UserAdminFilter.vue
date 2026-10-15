<template>
    <button
        @click="filter = !filter"
        class="relative z-10 block h-9 text-bold bg-blue-700 text-white ps-4 pe-4 rounded">
        <font-awesome-icon :icon="['fas', 'filter']" /> <span class="hidden md:inline">Filtro</span>
    </button>
    <div
        v-show="filter"
        @click="filter = false"
        class="fixed inset-0 z-10 w-full h-full">        
    </div>
    <transition
        enter-active-class="transition duration-150 ease-out transform"
        enter-from-class="scale-95 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in transform"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="scale-95 opacity-0">
        <div
            v-show="filter"
            class="absolute right-10 z-20 p-3 mt-2 bg-white rounded-md shadow-xl border-2">       
            <p class="mt-2">Filtro por nível de usuário</p>
            <select v-model="role" @change="$emit('someEvent', { user_role: role, user_info_with_role: info })" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full">                
                <option value="1">Administrador</option>
                <option value="2">Revisor</option>
                <option value="3">Aluno</option>                
                <option value="4">Exrterno</option>                
            </select>  
            
            <p class="mt-2">Filtro pelo nome, e-mail ou RA</p>
            <input v-model="info" @change="$emit('someEvent', { user_role: role, user_info_with_role: info })" 
                type="text" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full"
                placeholder="Ex: João Victor Cordeiro"
                />            
        </div>
    </transition>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

export default defineComponent({
    emits: ['someEvent'], 
    async setup(){       
        const filter = ref(false)
        const role = ref(1)        
        const info = ref("")

        return {            
            filter,            
            role,  
            info,      
        }
    },    
    methods: {

    },
    beforeMount() {      
        this.$emit('someEvent', { user_role: this.role })
    }  
})
</script>