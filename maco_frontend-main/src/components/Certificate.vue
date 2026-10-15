<template>
    <button type="button" :disabled="isLoading" title="Emitir certificado" v-on:click="getCertificate" class="px-2 mt-1 mb-1 sm:mt-0 py-2 text-sm text-center text-white bg-green-800 me-2 rounded-md focus:outline-none font-bold" :class="!$props.btnText ? 'w-10' : 'sm:px-8 md:px-12'">
        <span v-if="!isLoading">
            <font-awesome-icon :icon="['fas', 'file-pdf']" /> <span v-if="$props.btnText"> Emitir certificado</span>
        </span>        
        <span v-else>
            <Loading />
        </span>
    </button>

    <transition        
        v-show="visibleCertificate"
        enter-active-class="transition duration-300 ease-out transform"
        enter-from-class="scale-95 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="transition duration-300 ease-in transform"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="scale-95 opacity-0"
    >
        <div id="modal-backdrop" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div id="modal" class="bg-white rounded-3xl overflow-x-auto flex flex-col">
                <!-- Modal Header -->
                <header id="modal-header" class="ps-5 pe-3 py-3 flex justify-between items-center border-b-2 border-gray">
                    <p class="text-gray-500 font-semibold text-xl border-b-2" v-html="titleCertificate"></p>
                    <button type="button" class="w-7 p-2 bg-gray-300 rounded-full" v-on:click="visibleCertificate = !visibleCertificate">
                        <font-awesome-icon :icon="['fas', 'xmark']" />
                    </button>            
                </header>          
                
                <!-- Modal Body -->
                <div v-html="iframeHtml"></div>
            </div>
        </div>
    </transition>       
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { generateCertificate } from '../hooks/useUser';
import { credentials, getUserRole } from '../hooks/useAuth';
import { Toast } from "../hooks/useToast"
import Swal from "sweetalert2"
import Loading from './Loading.vue';

export default defineComponent({
    props: {
        article: Object,        
        btnText: Boolean,
    },
    async setup(props){              

        const visibleCertificate = ref(false)
        const titleCertificate = ref('')
        const iframeHtml = ref()
        const isLoading = ref(false)
        
        async function getCertificate() {                
            isLoading.value = true            
            let user: string | number | null
            user = window.localStorage.getItem('user-id')
            
            if (getUserRole() != '3:AUTHOR') {                
                let htmlFormAuthors = ''
                let authors = props.article?.authors
                let authorsLength = Object.keys(authors).length
                
                if (authorsLength > 1) {
                    for (let i = 0; i < authorsLength; i++) {
                        htmlFormAuthors += `<option value="${props.article?.authors[i]['id']}">&nbsp;${props.article?.authors[i]['name']}&nbsp;</option>`
                    }              

                    Swal.fire({
                        title: 'Escolha um(a) autor(a) para emitir o certificado',
                        html: `
                            <form class="form" id="selectionForm">
                                <label for="selectOption">Autores(as):</label>
                                <select id="selectOption" class="border border-gray-300 text-gray-900 rounded-lg focus:ring-gray-500 focus:border-gray-500">
                                    <option value="" disabled selected>Escolha...</option>
                                    ${htmlFormAuthors}
                                </select>
                            </form>
                        `,
                        focusConfirm: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'Cancelar',
                        preConfirm: () => {
                            const selectedValue = document.getElementById('selectOption')?.value
                            if (!selectedValue) {
                            Swal.showValidationMessage('Por favor, selecione um(a) autor(a)');
                            }
                            return selectedValue;
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Confirmar',
                    }).then(async (result) => {              
                        if (result.isConfirmed) {                                 
                            const certificate = await generateCertificate({'article': props.article?.id, 'user': result.value})
                            showCertificate(certificate)                 
                        } else {
                            isLoading.value = false
                        }
                    })
                } else {
                    const certificate = await generateCertificate({'article': props.article?.id, 'user': authors[0]['id']})
                    showCertificate(certificate)  
                }
                
            } else {
                const certificate = await generateCertificate({'article': props.article?.id, 'user': user})                
                showCertificate(certificate)
            }                                   
        }

        async function showCertificate(certificate) {
            const api = await credentials()

            var windowWidth = window.innerWidth - 50
            var windowHeight = window.innerHeight - 100  

            if (certificate.status != 'error') {
                iframeHtml.value = `<iframe width="${windowWidth}px" height="${windowHeight}px" src="${api.url}/${certificate.file}" frameborder="0"></iframe>`
                
                titleCertificate.value = `Certificado do aluno`
                if (getUserRole() == '3:AUTHOR') {   
                    titleCertificate.value = `Certificado ${window.localStorage.getItem('user-name')}`                    
                }
                visibleCertificate.value = true
            } else {
                Toast().fire({icon: 'error', title: 'Erro ao emitir certificado!'})
            }
            isLoading.value = false
        }

        return {
            visibleCertificate,
            titleCertificate,
            iframeHtml,
            getCertificate,
            showCertificate,
            isLoading,
        }    
    },
    components: {
        Loading
    }
})
</script>