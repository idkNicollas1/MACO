<template>
    <div class="mt-2 mb-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2">
            <div class="flex flex-wrap">
                <p class="text-gray-500 font-semibold text-xl">
                    <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)">
                        <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                    <span class="border-b-2">{{ pageTitle }}</span>
                </p>
                <span v-if="!infoLoaded">
                    <Spinner />
                </span>
                <div class="w-full">
                    <form class="mt-5" @submit.prevent="saveUser">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="...">
                                <h3 class="mb-3 italic">Dados do usuário</h3>
                                <div class="grid grid-cols-1">
                                    <div class="...">
                                        <label class="block">
                                            <span class="text-sm text-gray-700">Nome <span
                                                    class="text-red-500 font-semibold">*</span></span>
                                            <input :disabled="!isEdit" type="text" maxlength="100"
                                                class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                                v-model="name" />
                                        </label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 mt-5">
                                    <div class="...">
                                        <label class="block">
                                            <span class="text-sm text-gray-700">E-mail <span
                                                    class="text-red-500 font-semibold">*</span></span>
                                            <input :disabled="!isEdit" type="text" maxlength="100"
                                                class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                                v-model="email" />
                                        </label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-5">
                                    <div class="...">
                                        <label class="block">
                                            <span class="text-sm text-gray-700">CPF <span
                                                    class="text-red-500 font-semibold">*</span></span>
                                            <input :disabled="!isEdit" type="text" @input="formatCPF" maxlength="14"
                                                class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                                v-model="cpf" />
                                        </label>
                                    </div>
                                    <div v-if="(addMode && role != 4) || (!addMode && ra)" class="...">
                                        <label class="block">
                                            <span class="text-sm text-gray-700">RA <span
                                                    class="text-red-500 font-semibold">*</span></span>
                                            <input :disabled="!isEdit" type="text" @input="formatRA" maxlength="7"
                                                class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                                v-model="ra" />
                                        </label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 mt-5">
                                    <div class="...">
                                        <label class="block">
                                            <span class="text-sm text-gray-700">Senha <span
                                                    class="text-red-500 font-semibold">*</span></span>
                                            <input :disabled="!isEdit" type="text" maxlength="30"
                                                class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800"
                                                placeholder="********" v-model="pass" />
                                        </label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-5" v-if="userRole == '1:ADMIN'">
                                    <div class="...">
                                        <label class="block">
                                            <span class="text-sm text-gray-700">Permissão <span
                                                    class="text-red-500 font-semibold">*</span></span>
                                            <select :disabled="!isEdit" v-model="role"
                                                class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800">
                                                <option v-if="!addMode" value="1">Administrador</option>
                                                <option v-if="!addMode" value="2">Revisor</option>
                                                <option value="3">Aluno</option>
                                                <option value="4">Externo</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="...">
                                        <label class="block">
                                            <label class="block">
                                                <span class="text-sm text-gray-700">Status <span
                                                        class="text-red-500 font-semibold">*</span></span>
                                            </label>
                                            <label class="relative inline-flex cursor-pointer items-center mt-4">
                                                <input :disabled="!isEdit" id="switch-2" type="checkbox"
                                                    class="peer sr-only" :checked="status"
                                                    v-on:click="status = !status">
                                                <div
                                                    class="peer h-4 w-11 rounded-full border bg-slate-200 after:absolute after:-top-1 after:left-0 after:h-6 after:w-6 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-300 peer-checked:after:translate-x-full peer-focus:ring-green-300">
                                                </div>
                                            </label>
                                            <span class="ml-3 mt-3">{{ getStatus() }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0" v-if="$router.currentRoute.value.params.action != 'add'">
                                <div v-if="role == 4">
                                    <h3 class="mb-3 italic">Escolaridade do usuário</h3>
                                    <label class="block">
                                        <span class="text-sm text-gray-700">Escolaridade <span
                                                class="text-red-500 font-semibold">*</span></span>
                                        <select :disabled="!isEdit" v-model="schooling"
                                            class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800">
                                            <option value="" selected disabled>Escolha a </option>
                                            <option value="1">Ensino Médio</option>
                                            <option value="2">Ensino Técnico e Profissionalizante</option>
                                            <option value="3">Tecnólogo</option>
                                            <option value="4">Bacharelado e Licenciatura</option>
                                            <option value="5">Pós-Graduação</option>
                                        </select>
                                    </label>
                                </div>

                                <div v-else>
                                    <h3 class="mb-3 italic">Curso(s) do usuário</h3>
                                    <div v-if="isset_courses" v-for="(item, index) in courses">
                                        <p class="border-t-2 shadow-lg p-3 rounded mt-2">
                                            <span class="semibold">{{ parseInt(index) + 1 }}</span> - {{ item.name }} -
                                            <span>{{ formatDate(item.created_at) }}</span>
                                            <a v-if="userRole == '1:ADMIN'" href="#" class="float-end"
                                                @click="removeCourse(item.id)"><font-awesome-icon class="text-red-600"
                                                    size="lg" :icon="['fas', 'trash-can']" /></a>
                                        </p>
                                    </div>
                                    <div v-else class="border border-red-600 p-3 rounded mt-2">
                                        <p>Nenhum vinculado</p>
                                    </div>
                                    <div v-if="userRole == '1:ADMIN'" class="mt-5 mb-10">
                                        <a class="p-2 bg-gray-800 mt-2 text-white rounded" href="#"
                                            @click="showCoursesModal"><font-awesome-icon
                                                :icon="['fas', 'file-circle-plus']" /> &nbsp; Vincular curso</a>
                                    </div>
                                </div>

                                <div v-if="isAuthor && (userRole == '1:ADMIN' || userRole == '2:ADVISOR')" class="mt-5">
                                    <h3 class="mb-3 italic">Artigo(s) do usuário</h3>
                                    <a class="p-2 bg-green-600 mt-2 text-white rounded" href="#"
                                        @click="userRole == '3:AUTHOR' ? $router.push('/historic') : $router.push('/historic/' + id)"><font-awesome-icon
                                            :icon="['fas', 'newspaper']" /> &nbsp; Ver histórico de submissões</a>
                                </div>
                            </div>
                            <!-- Se estiver criando um novo usuario -->
                            <div class="mt-4 md:mt-0" v-else>
                                <div v-if="role == 4">
                                    <h3 class="mb-3 italic">Escolaridade do usuário</h3>
                                    <label class="block mt-2">
                                        <span class="text-sm text-gray-700">Escolaridade <span
                                                class="text-red-500 font-semibold">*</span></span>
                                        <select v-model="schooling"
                                            class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800">
                                            <option value="" selected disabled >Selecione a Escolaridade</option>
                                            <option value="1">Ensino Médio</option>
                                            <option value="2">Ensino Técnico e Profissionalizante</option>
                                            <option value="3">Tecnólogo</option>
                                            <option value="4">Bacharelado e Licenciatura</option>
                                            <option value="5">Pós-Graduação</option>
                                        </select>
                                    </label>
                                </div>

                                <div v-else>
                                    <h3 class="mb-3 italic">Curso do usuário</h3>
                                    <label class="block mt-2">
                                        <span class="text-sm text-gray-700">Curso <span
                                                class="text-red-500 font-semibold">*</span></span>
                                        <select v-model="course"
                                            class="block w-full mt-1 border-gray-300 rounded-md focus:border-gray-800 focus:ring focus:ring-opacity-40 focus:ring-gray-800">
                                            <option value="" selected disabled>Selecione o curso</option>
                                            <option v-for="course in courses_add" :value="course.id">{{ course.name }}
                                            </option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div v-if="infoLoaded && isEdit" class="mt-5">
                            <div class="flex justify-end">
                                <button v-if="userRole == '1:ADMIN' && !addMode" v-on:click="loginAdmin(id)"
                                    type="button"
                                    class="px-2 sm:px-8 md:px-12 py-2 mr-2 text-sm text-center text-white bg-green-800 rounded-md focus:outline-none font-bold">
                                    <font-awesome-icon :icon="['fas', 'right-to-bracket']" /> &nbsp; Logar
                                </button>
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

    <!-- Modal de adição e remoção de cursos -->
    <Modal v-show="isModalCoursesVisible" @some-event="showCoursesModal" :height="'h-1/2'">
        <template #header>
            <p class="text-xl">Adição de cursos para o usuário</p>
        </template>
        <template #body>
            <div class="px-5 pb-5 pt-1">
                <div v-for="item in courses_add">
                    <p v-if="courseNotInUser(item.id)" class="border-t-2 shadow-lg p-3 rounded mt-2">
                        {{ item.name }}
                        <a href="#" class="float-end" @click="addCourse(item.id)"><font-awesome-icon size="lg"
                                :icon="['fas', 'circle-plus']" /></a>
                    </p>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import router from "../../router"
import { IUserState, userAdd, userAddCourse, userDetails, userEdit, userFormatCPF, userFormatRA, userRemoveCourse } from "../../hooks/useUser"
import { apiLoginAdmin, getUserRole } from "../../hooks/useAuth"
import Spinner from "../../components/Spinner.vue"
import { Toast } from "../../hooks/useToast"
import { format } from 'date-fns';
import { courseList } from "../../hooks/useCourse"
import Modal from "../../components/Modal.vue"
import Swal from "sweetalert2"

export default defineComponent({
    setup() {
        const state: IUserState = reactive({
            isLoading: false,
            message: '',
        })

        const infoLoaded = false
        const pageTitle = ref("")
        const id = ref("")
        const name = ref("")
        const cpf = ref("")
        const email = ref("")
        const ra = ref("")
        const pass = ref("")
        const role = ref()
        const status = ref()
        const courses = ref()
        const courses_add = ref()
        const course = ref("")
        const schooling = ref("")
        const isset_courses = false
        const created_at = ref("")
        const isAuthor = ref(false)
        const isEdit = ref(false)
        const userRole = ref("")
        const isModalCoursesVisible = ref(false)
        const addMode = ref(false)

        async function saveUser() {
            state.isLoading = true
            const requiredFields = [name.value, email.value, cpf.value]

            if (role.value != 4) {
                requiredFields.push(ra.value)
            }

            if (role.value == 4) {
                requiredFields.push(schooling.value)
            }

            if (requiredFields.some(field => field === '')) {
                Toast().fire({ icon: 'warning', title: 'Informe todas as informações obrigatórias!' })
                state.isLoading = false
                return
            }

            if (router.currentRoute.value.params.action == 'add') {
                if (course.value == "" && role.value != 4) {
                    Toast().fire({ icon: 'warning', title: 'Informe o curso!' })
                    state.isLoading = false
                    return
                }
                const result = await userAdd({
                    name: name.value,
                    cpf: cpf.value,
                    ra: ra.value,
                    email: email.value,
                    role: role.value,
                    password: pass.value,
                    course: course.value,
                    schooling: role.value == 3 ? null : schooling.value,
                    isUnifaeStudent: role.value == 3 ? true : false
                })
                if (result.status == 'success') {
                    Toast().fire({ icon: 'success', title: 'Usuário criado!' })
                    router.push('/users')
                } else {
                    Toast().fire({ icon: 'error', title: result.message })

                }
            } else {
                const result = await userEdit({
                    id: id.value,
                    name: name.value,
                    cpf: cpf.value,
                    ra: ra.value,
                    email: email.value,
                    password: pass.value,
                    role: role.value,
                    status: status.value ? 1 : 0,
                    schooling: role.value == 3 ? null : schooling.value,
                    isUnifaeStudent: role.value == 3 ? true : false
                })
                if (result.status == 'success') {
                    Toast().fire({ icon: 'success', title: 'Usuário editado!' })
                } else {
                    Toast().fire({ icon: 'error', title: result.message })
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
            cpf,
            email,
            ra,
            pass,
            role,
            status,
            created_at,
            courses,
            course,
            schooling,
            isset_courses,
            isAuthor,
            saveUser,
            isEdit,
            userRole,
            isModalCoursesVisible,
            courses_add,
            addMode,
        }
    },
    methods: {
        async loadUser() {
            let mode = ''
            if (getUserRole() == '3:AUTHOR') {
                mode = '&mode=author'
            }
            if (getUserRole() == '2:ADVISOR') {
                mode = '&mode=advisor'
            }
            if (getUserRole() == '4:EXTERNAL') {
                mode = '&mode=external'
            }

            const result = await userDetails(this.$route.params.action, mode)

            if (result == undefined) {
                Toast().fire({ icon: 'error', title: 'Usuário não encontrado!' })
                router.push('dashboard')
                return
            }
            this.id = result['id']
            this.name = result['name']
            this.cpf = userFormatCPF(result['cpf'])
            this.ra = userFormatRA(result['ra'])
            this.email = result['email']
            this.role = result['role']
            this.schooling = result['schooling'] || null
            if (this.role == 3) {
                this.isAuthor = true
            }
            this.status = result['status']
            this.created_at = result['created_at']
            this.courses = this.getCourses(result)
            this.infoLoaded = true

            if (localStorage.getItem('user-id') == this.id || getUserRole() == '1:ADMIN') {
                this.isEdit = true
            }
        },
        async getCoursesToAddUser() {
            this.courses_add = (await courseList('/public/')).value
        },
        formatDate(date) {
            return format(new Date(date), 'dd/MM/yyyy')
        },
        getStatus() {
            return this.status ? 'Ativo' : 'Inativo'
        },
        getCourses(data) {
            if (data['courses'][0] != undefined) {
                this.isset_courses = true
            }
            return data['courses']
        },
        formatCPF() {
            this.cpf = userFormatCPF(this.cpf)
        },
        formatRA() {
            this.ra = userFormatRA(this.ra)
        },
        showCoursesModal() {
            this.isModalCoursesVisible = !this.isModalCoursesVisible

            if (this.isModalCoursesVisible) {
                this.getCoursesToAddUser()
            }
        },
        courseNotInUser(course) {
            if (router.currentRoute.value.params.action != 'add') {
                for (let c = 0; c < Object.keys(this.courses).length; c++) {
                    if (this.courses[c].id == course) {
                        return false
                    }
                }
                return true
            }
        },
        async removeCourse(course) {
            let user = router.currentRoute.value.params.action
            Swal.fire({
                title: "Tem certeza?",
                html: `Você está prestes a excluir. Está certo disso?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sim, excluir!",
                cancelButtonText: "Cancelar",
            }).then(async (modal) => {
                if (modal.isConfirmed) {
                    const result = await userRemoveCourse(user, course)

                    if (result.status == 'success') {
                        Toast().fire({ icon: 'success', title: 'Curso removido do usuário!' })
                        this.loadUser()
                    } else {
                        Toast().fire({ icon: 'error', title: result.message })
                    }
                }
            })
        },
        async addCourse(course) {
            const result = await userAddCourse({
                'user': router.currentRoute.value.params.action,
                'course': course,
            })

            if (result.status == 'success') {
                Toast().fire({ icon: 'success', title: 'Curso vinculado ao usuário!' })
                this.loadUser()
            } else {
                Toast().fire({ icon: 'error', title: result.message })
            }
        },
        async loginAdmin(id) {
            Swal.fire({
                title: "Tem certeza?",
                html: `Você está prestes a logar no perfil desse usuário. Está certo disso?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sim, logar!",
                cancelButtonText: "Cancelar",
            }).then(async (modal) => {
                if (modal.isConfirmed) {
                    try {
                        await apiLoginAdmin(id)
                    } catch {
                        Toast().fire({ icon: 'error', title: 'Não foi possível logar com esse usuário' })
                    }
                }
            })
        }
    },
    beforeMount() {
        switch (router.currentRoute.value.params.action) {
            case 'add':
                this.pageTitle = 'Inserir usuário'
                this.infoLoaded = true
                this.isEdit = true
                this.status = true
                this.addMode = true
                this.role = 3
                this.getCoursesToAddUser()
                break;
            default:
                this.pageTitle = 'Editar usuário'
                this.loadUser()
        }
        this.userRole = getUserRole()
    },
    components: { Spinner, Modal }
})
</script>
