<template>
    <img src="/logo_maco.png" title="Maco: Magnoli & Cordeiro" alt="Logo Maco" width="150" class="fixed top-0 pl-2 pt-2 rounded-full">
    <div class="flex items-center justify-center h-screen px-6 bg-zinc-100">
        <div class="w-full max-w-md bg-white border-t-2 rounded-xl border-gray px-5 py-5 shadow-xl">            
            <form class="mt-4 mb-4 md:mt-10 md:mb-10 max-w-96 m-auto" @submit.prevent="login">
                <div class="space-y-2">                    
                    <p class="font-semibold text-2xl text-gray-800">Login</p>
                    <p class="text-xs text-gray-400">Insira seu e-mail e senha para entrar.</p>
                </div>
                <label class="block mt-4">
                    <span class="text-sm text-gray-800">E-mail</span>
                    <input type="text"
                        class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                        v-model="username" />
                </label>

                <label class="block mt-3">
                    <span class="text-sm text-gray-800">Senha</span>
                    <div class="relative w-full">
                        <input :type="type_input ? 'text' : 'password'"
                            class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                            v-model="password" />
                        <button class="absolute inset-y-0 right-0 pr-3 flex items-center" type="button" @click="type_input = !type_input">
                            <font-awesome-icon :icon="['fas', 'eye']" class="icon-color" />
                        </button>  
                    </div>                    

                    <div class="flex justify-end mt-2">
                        <button type="button" @click="forgotPasswordPage" class="text-xs underline">Esqueceu a senha?</button>
                    </div>
                </label>

                <div class="mt-6">
                    <button type="submit" :disabled="isLoading"
                        class="w-full px-4 py-2 text-sm text-center text-white bg-gray-800 rounded-md focus:outline-none hover:bg-gray-700 transition">
                        <span v-if="!isLoading">
                            Entrar
                        </span>
                        <span v-else>
                            <Loading />
                        </span>
                    </button>
                </div>
                
                <div class="mt-3">
                    <p class="flex justify-center text-sm">Não possui uma conta?&nbsp;<a href="#" @click="registerPage" class="text-blue-500">Crie já</a></p>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import { ILoginState, apiLogin, isValidToken } from "../../hooks/useAuth"
import router from "../../router"
import Swal from "sweetalert2"
import { Toast } from "../../hooks/useToast"
import Loading from "../../components/Loading.vue"

export default defineComponent({
    setup() {
        const state: ILoginState = reactive({
            isLoading: false,
            message: '',
            token: '',
        });

        const username = ref("")
        const password = ref("")
        const type_input = ref(false)

        function forgotPasswordPage() {
            router.push('/forgotpassword')
        }
        function registerPage() {
            router.push('/register');
        }

        async function login() {
            state.isLoading = true

            if (username.value == "" || password.value == "") {
                Toast().fire({ icon: 'warning', title: 'Informe o e-mail e a senha!' })
                state.isLoading = false
                return
            }

            const result = await apiLogin(username.value, password.value)

            state.message = result.value['message']
            state.token = result.value['token']

            if (state.token != undefined) {
                state.isLoading = false
                let timerInterval;
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    icon: "success",
                    title: "Sucesso! Redirecionando...",
                    timer: 300,
                    timerProgressBar: true,
                    didOpen: () => { },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        location.href = '/dashboard'
                    }
                });
            } else {
                Toast().fire({ icon: 'error', title: 'Usuário ou senha incorretos!' })
                state.isLoading = false
            }
        }

        return {
            ...toRefs(state),
            username,
            password,
            login,
            forgotPasswordPage,
            registerPage,
            type_input,
        }
    },
    components: { Loading }
});
</script>