<template>
    <img src="/logo_maco.png" title="Maco: Magnoli & Cordeiro" alt="Logo Maco" width="150" class="fixed top-0 pl-2 pt-2 rounded-full">
    <div class="flex items-center justify-center h-screen px-6 bg-zinc-100">
        <div class="w-full max-w-md bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">            
            <form class="mt-4 mb-4 max-w-96 m-auto" @submit.prevent="sendEmail">
                <div class="space-y-2 flex">
                    <a href="#" onclick="history.go(-1)" class="mt-3 mr-3"><font-awesome-icon :icon="['fas', 'arrow-left']" /></a>
                    <p class="font-semibold text-2xl text-gray-800">Esqueceu Senha?</p>                
                </div>
                <div class="mt-2">
                    <p class="text-xs text-gray-400">Nós entendemos, coisas acontecem. Basta inserir seu endereço de e-mail abaixo e enviaremos uma nova senha para você.</p>
                </div>
                <label class="block mt-4">
                    <span class="text-sm text-gray-800">Email</span>
                    <input type="text"
                        class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                        v-model="email" />
                </label>

                <div class="mt-6">
                    <button type="submit" :disabled="isLoading"
                        class="w-full px-4 py-2 text-sm text-center text-white bg-gray-800 rounded-md focus:outline-none hover:bg-gray-700 transition">
                        <span v-if="!isLoading">
                            Enviar
                        </span>
                        <span v-else>
                            <Loading />
                        </span>
                    </button>
                </div>
                <div class="flex justify-center mt-2">
                    <button @click="loginPage" class="text-xs underline">Já possui uma conta? Faça seu login.</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import { IUserState } from "../../hooks/useUser"
import router from "../../router"
import Loading from "../../components/Loading.vue"
import { Toast } from "../../hooks/useToast"
import { userRecoverPassword } from "../../hooks/useUser"

export default defineComponent({
    setup() {
        const state: IUserState = reactive({
            isLoading: false,
            message: '',
        })

        const email = ref("");

        function loginPage() {
            router.push('/login');
        }

        async function sendEmail() {     
            if (email.value == "") {
                Toast().fire({
                    icon: 'warning',
                    title: 'Informe o e-mail!'
                });                
                return
            }
            state.isLoading = true
            await userRecoverPassword({'email': email.value})
            Toast().fire({
                icon: 'success',
                title: 'E-mail enviado! Faça login com a nova senha'
            })
            state.isLoading = false  
            router.push('/login')
        }
        return {
            ...toRefs(state),
            email,
            sendEmail,
            loginPage
        }
    },
    components: { Loading }
})

</script>