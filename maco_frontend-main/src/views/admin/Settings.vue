<template>
    <div class="mt-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">        
                <div class="w-full grid grid-cols-2 gap-4">                    
                    <div class="col-start-1 col-end-12 md:col-start-1 md:col-end-8">
                        <p class="text-gray-500 font-semibold text-xl">
                            <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)"> <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                            <span class="border-b-2">Configurações de evento</span>
                            <span v-if="!infoLoaded">
                                <Spinner />
                            </span>
                        </p>
                    </div>
                    <div class="col-start-1 col-end-12 md:col-end-10 md:col-span-2 text-end">
                        <button @click="showModal(), createEvent()" class="bg-green-700 text-white ps-2 pe-2 pt-1 pb-1 rounded-md"> 
                            <font-awesome-icon :icon="['fas', 'file-circle-plus']" /> Criar evento
                        </button>
                    </div>                
                </div>     
                <div v-if="infonotnull" class="w-full mt-5">                         
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3" v-for="item in events">                        
                        <div class="w-full grid grid-cols-2 gap-4">                    
                            <div class="col-start-1 col-end-12 md:col-start-1 md:col-end-8">
                                <div class="mt-1">
                                    {{ item.name }} - <span :class="item.status == 1 ? 'bg-green-700' : 'bg-red-700'" class="ps-2 pe-2 rounded-md text-white">{{ item.status == 1 ? 'Ativo' : 'Inativo' }}</span>
                                </div>
                            </div>
                            <div class="col-start-1 col-end-12 md:col-end-10 md:col-span-2 text-end">
                                <button @click="showModal(), loadEvent(item.id)" class="bg-blue-700 text-white ps-2 pe-2 pt-1 pb-1 rounded-md"> <font-awesome-icon :icon="['fas', 'pen-to-square']" /> Editar</button>                                
                            </div>                
                        </div>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
    <Modal v-show="isModalVisible" @some-event="showModal" :height="'h-2/3'">
        <template #header>
            <p class="text-xl">Criação e edição de eventos</p>
        </template>
        <template #body>
            <div v-if="eventLoaded" class="px-5 py-5 mt-0">
                <form @submit.prevent="">
                    <label class="block">
                        <span class="text-sm text-gray-700">Nome do evento <span class="text-red-500 font-semibold">*</span></span>
                        <input type="text"
                            class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                            v-model="event_name" />
                    </label>    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mt-2">
                                <span class="text-sm text-gray-700">Data inicial do evento <span class="text-red-500 font-semibold">*</span></span>
                                <input type="date"
                                    class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                    v-model="event_start" />
                            </label>   
                        </div>
                        <div>
                            <label class="block mt-2">
                                <span class="text-sm text-gray-700">Data final do evento <span class="text-red-500 font-semibold">*</span></span>
                                <input type="date"
                                    class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                    v-model="event_end" />
                            </label>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mt-2" title="Quantidade máxima de palavras permitidas no resumo">
                                <span class="text-sm text-gray-700">Máximo de P. no resumo <span class="text-red-500 font-semibold">*</span></span>
                                <input type="number"
                                    class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                    v-model="event_words" />
                            </label> 
                        </div>
                        <div>
                            <label class="block mt-2" title="Quantidade máxima de palavras chaves permitidas">
                                <span class="text-sm text-gray-700">Máximo de P. chaves <span class="text-red-500 font-semibold">*</span></span>
                                <input type="number"
                                    class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                    v-model="event_keywords" />
                            </label>  
                        </div>
                    </div>
                    <label class="block mt-2">
                        <span class="text-sm text-gray-700">Instruções para o evento <span class="text-red-500 font-semibold">*</span></span>                        
                        <textarea name="" id="" cols="30" rows="5" v-model="event_instructions"
                            class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"></textarea>
                    </label>  
                    <label class="block mt-2">
                        <span class="text-sm text-gray-700">Status <span class="text-red-500 font-semibold">*</span></span>
                    </label>                                            
                    <label class="relative inline-flex cursor-pointer items-center mt-4">                        
                        <input id="switch-2" type="checkbox" class="peer sr-only" :checked="event_status" v-on:click="event_status = !event_status">
                        <div class="peer h-4 w-11 rounded-full border bg-slate-200 after:absolute after:-top-1 after:left-0 after:h-6 after:w-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-300 peer-checked:after:translate-x-full peer-focus:ring-green-300"></div>
                    </label>                        
                    <span class="absolute ml-3 mt-3">{{ getStatus() }}</span>
                </form>
                <div class="flex justify-end">
                    <button v-on:click="saveEvent()" type="button" class="px-12 py-2 mt-3 text-sm text-center text-white bg-gray-900 rounded-md focus:outline-none font-bold">                                    
                        <font-awesome-icon :icon="['fas', 'floppy-disk']" /> &nbsp; Salvar                                   
                    </button>                    
                </div>            
            </div>   
            <div v-else class="px-5 py-5 mt-0 flex justify-center">
                <Spinner />
            </div>         
        </template>
    </Modal>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from 'vue';
import { IEventState, eventList, eventDetails, eventAdd, eventEdit } from '../../hooks/useEvent';
import Spinner from "../../components/Spinner.vue"
import Modal from '../../components/Modal.vue';
import { Toast } from '../../hooks/useToast';
import { format } from 'date-fns';

export default defineComponent({
    setup(){
        const state: IEventState = reactive({
            isLoading: false,
            message: ''
        })

        const infoLoaded = ref(false)     
        const infonotnull = ref(false)   
        const events = ref()        
        const isModalVisible = ref(false)
        const eventLoaded = ref(false)
        const event_id = ref()
        const event_name = ref()
        const event_start = ref()
        const event_end = ref()
        const event_words = ref() 
        const event_keywords = ref()        
        const event_instructions = ref()        
        const event_status = ref()     

        return {
            ...toRefs(state),         
            events, 
            infoLoaded,
            infonotnull,            
            isModalVisible,
            eventLoaded,
            event_id,
            event_name,
            event_start,
            event_end,
            event_words, 
            event_keywords,
            event_instructions,
            event_status,                
        }
    }, 
    methods: {
        async loadevents() {
            const result = (await eventList()).value
            
            if(result[0]) {
                this.events = result   
                this.infonotnull = true             
            } else {
                Toast().fire({icon: 'warning', title: 'Nenhum evento encontrado'})      
            }            
            this.infoLoaded = true                 
        },     
        async loadEvent(eventID) {
            const result = await eventDetails(eventID)
            
            if (result.value != undefined) {
                this.event_id = eventID
                this.event_name = result.value['name']
                this.event_start = this.formatDate(result.value['start'])
                this.event_end = this.formatDate(result.value['end'])
                this.event_words = result.value['number_words']
                this.event_keywords = result.value['number_keywords']
                this.event_instructions = result.value['instructions']
                this.event_status = result.value['status']
                this.eventLoaded = true
            } else {
                Toast().fire({icon: 'error', title: 'Erro ao carregar evento!'})      
            }
        },
        async saveEvent() {
            if (this.event_name == '' || this.event_name == undefined || this.event_name == null) {
                Toast().fire({icon: 'warning', title:'Informe o nome do evento'})
                return
            }
            if (this.event_start == '' || this.event_start == undefined || this.event_start == null) {
                Toast().fire({icon: 'warning', title:'Informe a data inicial do evento'})
                return
            }
            if (this.event_end == '' || this.event_end == undefined || this.event_end == null) {
                Toast().fire({icon: 'warning', title:'Informe o data final do evento'})
                return
            }
            if (this.event_words == '' || this.event_words == undefined || this.event_words == null) {
                Toast().fire({icon: 'warning', title:'Informe a quantidade de caracteres'})
                return
            }
            if (this.event_keywords == '' || this.event_keywords == undefined || this.event_keywords == null) {
                Toast().fire({icon: 'warning', title:'Informe o quantidade de palavras chaves'})
                return
            }
            if (this.event_instructions == '' || this.event_instructions == undefined || this.event_instructions == null) {
                Toast().fire({icon: 'warning', title:'Informe as instruções do evento'})
                return
            }

            if (this.event_id != undefined) {
                const result = await eventEdit({
                    'id': this.event_id,
                    'name': this.event_name,
                    'start': this.event_start,
                    'end': this.event_end,
                    'number_words': this.event_words,
                    'number_keywords': this.event_keywords,
                    'instructions': this.event_instructions,
                    'status': this.event_status ? 1 : 0,
                })
                if (result.status == 'success') {
                    Toast().fire({icon: 'success', title: 'Evento atualizado!'})
                    this.isModalVisible = false
                    this.loadevents()
                } else {
                    Toast().fire({icon: 'error', title: 'Erro ao atualizar evento!'})
                }
            } else {
                const result = await eventAdd({
                    'name': this.event_name,
                    'start': this.event_start,
                    'end': this.event_end,
                    'number_words': this.event_words,
                    'number_keywords': this.event_keywords,
                    'instructions': this.event_instructions,
                    'status': this.event_status ? 1 : 0,
                })
                if (result.status == 'success') {
                    Toast().fire({icon: 'success', title: 'Evento gravado!'})
                    this.isModalVisible = false
                    this.loadevents()
                } else {
                    Toast().fire({icon: 'error', title: 'Erro ao salvar evento!'})
                }
            }            
        },
        createEvent() {
            this.event_id = ref()
            this.event_name = ref()
            this.event_start = ref()
            this.event_end = ref()
            this.event_words = ref()
            this.event_keywords = ref()        
            this.event_instructions = ref()     
            this.event_status = 1
            this.eventLoaded = true
        },
        getStatus() {            
            return this.event_status ? 'Ativo' : 'Inativo'
        },
        formatDate(date) {
            return format(new Date(date), 'yyyy-MM-dd')
        },  
        showModal () {
            this.isModalVisible = !this.isModalVisible
        },
    },  
    beforeMount() {
        this.loadevents() 
    },
    components: { Spinner, Modal }
})
</script>