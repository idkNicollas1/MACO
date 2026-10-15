<template>
    <img src="/logo_maco.png" title="Maco: Magnoli & Cordeiro" alt="Logo Maco" width="150"
        class="hidden lg:block absolute top-0 left-0 pl-2 pt-2 rounded-full" />

    <div class="flex items-center justify-center min-h-screen px-3 py-6 bg-zinc-100">
        <div class="w-full max-w-2xl bg-white shadow-xl rounded-xl py-6 px-4 md:py-8 md:px-8">
            <form class="w-full" @submit.prevent="sendRegister">

                <!-- Header com botão voltar -->
                <div class="flex items-center gap-3 mb-6">
                    <a href="#" onclick="history.go(-1)" class="text-gray-600 hover:text-gray-800 transition">
                        <font-awesome-icon :icon="['fas', 'arrow-left']" class="text-xl" />
                    </a>
                    <h1 class="font-semibold text-xl md:text-2xl text-gray-800">
                        Cadastro
                    </h1>
                </div>

                <!-- Toggle: Você é aluno da UNIFAE? -->
                <div class="mb-8">
                    <p class="text-center text-gray-700 font-normal mb-4">
                        Você é aluno da UNIFAE?
                    </p>
                    <div class="flex justify-center">
                        <div class="relative inline-flex items-center bg-gray-200 rounded-full p-1 shadow-inner">
                            <!-- Indicador deslizante -->
                            <div class="absolute h-[calc(100%-8px)] bg-white rounded-full shadow-md transition-all duration-300 ease-in-out"
                                :style="{
                                    width: '50%',
                                    left: isUnifaeStudent ? '4px' : 'calc(50% - 4px)',
                                }"></div>

                            <!-- Botão Sim -->
                            <button type="button" @click="isUnifaeStudent = true" :class="[
                                'relative z-10 px-8 py-2 rounded-full text-sm font-medium transition-colors duration-300',
                                isUnifaeStudent ? 'text-gray-800' : 'text-gray-500'
                            ]">
                                Sim
                            </button>

                            <!-- Botão Não -->
                            <button type="button" @click="isUnifaeStudent = false" :class="[
                                'relative z-10 px-8 py-2 rounded-full text-sm font-medium transition-colors duration-300',
                                !isUnifaeStudent ? 'text-gray-800' : 'text-gray-500'
                            ]">
                                Não
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm text-gray-800 mb-1">
                            Primeiro Nome <span class="text-red-500 font-semibold">*</span>
                        </label>
                        <input type="text" maxlength="100" placeholder="Digite seu primeiro nome"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                            v-model="name" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-800 mb-1">
                            Último Nome <span class="text-red-500 font-semibold">*</span>
                        </label>
                        <input type="text" maxlength="100" placeholder="Digite seu último nome"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                            v-model="lastname" />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-800 mb-1">
                        Email <span class="text-red-500 font-semibold">*</span>
                    </label>
                    <input type="email" maxlength="100" placeholder="seu.email@exemplo.com"
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                        v-model="email" />
                </div>

                <div v-if="!isUnifaeStudent" class="mb-4">
                    <label class="block text-sm text-gray-800 mb-1">
                        CPF <span class="text-red-500 font-semibold">*</span>
                    </label>
                    <input type="text" maxlength="14" placeholder="000.000.000-00" @input="formatCPF"
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                        v-model="cpf" />
                </div>

                <!-- CPF e RA (para alunos UNIFAE) -->
                <div v-if="isUnifaeStudent" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm text-gray-800 mb-1">
                            CPF <span class="text-red-500 font-semibold">*</span>
                        </label>
                        <input type="text" maxlength="14" placeholder="000.000.000-00" @input="formatCPF"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                            v-model="cpf" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-800 mb-1">
                            RA <span class="text-red-500 font-semibold">*</span>
                        </label>
                        <input type="text" maxlength="7" placeholder="00000-0" @input="formatRA"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                            v-model="ra" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm text-gray-800 mb-1">
                            Senha <span class="text-red-500 font-semibold">*</span>
                        </label>
                        <div class="relative">
                            <input :type="type_input_1 ? 'text' : 'password'" placeholder="Mínimo 8 caracteres"
                                class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                                v-model="pass1" />
                            <button
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition"
                                type="button" @click="type_input_1 = !type_input_1">
                                <font-awesome-icon :icon="['fas', type_input_1 ? 'eye-slash' : 'eye']" />
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-800 mb-1">
                            Confirme a senha <span class="text-red-500 font-semibold">*</span>
                        </label>
                        <div class="relative">
                            <input :type="type_input_2 ? 'text' : 'password'" placeholder="Repita a senha"
                                class="block w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition"
                                v-model="pass2" />
                            <button
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition"
                                type="button" @click="type_input_2 = !type_input_2">
                                <font-awesome-icon :icon="['fas', type_input_2 ? 'eye-slash' : 'eye']" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Escolaridade (para não alunos UNIFAE) -->
                <div v-if="!isUnifaeStudent" class="mb-4">
                    <label class="block text-sm text-gray-800 mb-1">
                        Escolaridade <span class="text-red-500 font-semibold">*</span>
                    </label>
                    <select v-model="schooling"
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition bg-white">
                        <option value="" selected disabled>Selecione a escolaridade</option>
                        <option v-for="schooling in schoolings" :key="schooling.id" :value="schooling.id">
                            {{ schooling.name }}
                        </option>
                    </select>
                </div>

                <!-- Curso (para alunos UNIFAE) -->
                <div v-if="isUnifaeStudent" class="mb-6">
                    <label class="block text-sm text-gray-800 mb-1">
                        Curso <span class="text-red-500 font-semibold">*</span>
                    </label>
                    <select v-model="course"
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:border-gray-800 focus:ring-2 focus:ring-gray-800 focus:ring-opacity-20 transition bg-white">
                        <option value="" selected disabled>Selecione o curso</option>
                        <option v-for="course in courses" :key="course.id" :value="course.id">
                            {{ course.name }}
                        </option>
                    </select>
                </div>

                <!-- Botão Cadastrar -->
                <div class="mt-8">
                    <button type="submit" :disabled="isLoading" :class="[
                        'w-full px-4 py-3 text-base font-semibold text-white rounded-lg transition-all',
                        isLoading
                            ? 'bg-gray-400 cursor-not-allowed'
                            : 'bg-gray-800 hover:bg-gray-700 hover:shadow-lg active:scale-[0.98]'
                    ]">
                        <span v-if="!isLoading">
                            Cadastrar
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <Loading />
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import router from "../../router"
import Loading from "../../components/Loading.vue"
import { Toast } from "../../hooks/useToast"
import { IUserState, userAdd } from "../../hooks/useUser"
import { courseList } from "../../hooks/useCourse"
import { schoolingList } from "../../hooks/useSchooling"

export default defineComponent({
    setup() {
        const state: IUserState = reactive({
            isLoading: false,
            message: '',
        })
        const name = ref("")
        const lastname = ref("")
        const cpf = ref("")
        const email = ref("")
        const ra = ref("")
        const pass1 = ref("")
        const pass2 = ref("")
        const course = ref("")
        const courses = ref()
        const schooling = ref("")
        const schoolings = ref()
        const type_input_1 = ref(false)
        const type_input_2 = ref(false)
        const isUnifaeStudent = ref(true)

        async function sendRegister() {
            const requiredFields = [name.value, lastname.value, cpf.value, email.value, pass1.value, pass2.value]

            if (isUnifaeStudent.value) {
                requiredFields.push(ra.value, course.value)
            } else {
                requiredFields.push(schooling.value)
            }

            if (requiredFields.some(field => field === '')) {
                Toast().fire({ icon: 'warning', title: 'Preencha todas as informações obrigatórias!' })
                return
            }

            if (pass1.value.length < 8) {
                Toast().fire({ icon: 'error', title: 'Senha deve conter pelo menos 8 caracteres!' })
                return
            }

            if (pass1.value != pass2.value) {
                Toast().fire({ icon: 'error', title: 'Senhas não são iguais!' })
                return
            }

            state.isLoading = true
            const result = await userAdd({
                isUnifaeStudent: isUnifaeStudent.value,
                name: name.value + " " + lastname.value,
                cpf: cpf.value,
                ra: isUnifaeStudent.value ? ra.value : null, 
                email: email.value,
                password: pass1.value,
                course: isUnifaeStudent.value ? course.value : null,
                schooling: !isUnifaeStudent.value ? schooling.value : null,
            })
            state.isLoading = false

            if (result.status == 'success') {
                Toast().fire({ icon: 'success', title: 'Usuário criado!' })
                router.push('/login')
            } else {
                Toast().fire({ icon: 'error', title: result.message })
            }
        }

        return {
            ...toRefs(state),
            name,
            lastname,
            cpf,
            email,
            ra,
            pass1,
            pass2,
            course,
            courses,
            schooling,
            schoolings,
            type_input_1,
            type_input_2,
            isUnifaeStudent,
            sendRegister,
        }
    },
    methods: {
        async getCourses() {
            this.courses = (await courseList('/public/')).value
        },
        async getSchooling() {
            this.schoolings = (await schoolingList('/public/')).value
        },
        formatCPF() {
            let cpf = this.cpf.replace(/\D/g, '');
            if (cpf.length <= 11) {
                cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
                cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
                cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            this.cpf = cpf;
        },
        formatRA() {
            let ra = this.ra.replace(/\D/g, '');
            if (ra.length <= 6) {
                ra = ra.replace(/(\d{5})(\d)/, '$1-$2');
            }
            this.ra = ra;
        }
    },
    beforeMount() {
        this.getCourses()
        this.getSchooling()
    },
    components: { Loading }
})
</script>