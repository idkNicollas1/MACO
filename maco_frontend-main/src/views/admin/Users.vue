<template>
    <div class="mt-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2 mb-2">
            <div class="flex flex-wrap">
                <div class="w-full grid grid-cols-6 gap-4">
                    <div class="col-start-1 col-end-8 ...">
                        <p class="text-gray-500 font-semibold text-xl">
                            <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)">
                                <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                            <span class="border-b-2"> Listagem de usuários | {{ total }}</span>
                            <span v-if="!infoLoaded">
                                <Spinner />
                            </span>
                        </p>
                    </div>
                    <div class="col-end-10 col-span-2 ...">
                        <button @click="$router.push('/user/add')"
                            class="bg-green-700 text-white rounded-md ps-4 pe-4 me-2 h-9 float-start">
                            <font-awesome-icon :icon="['fas', 'user-plus']" /> <span
                                class="hidden md:inline">Adicionar</span>
                        </button>
                        <UserAdminFilter @some-event="loadUsers" />
                    </div>
                </div>
                <div v-if="infonotnull" class="overflow-x-auto inline-block min-w-full rounded-lg">
                    <table class="min-w-full leading-normal mt-5">
                        <thead>
                            <tr>
                                <th class="px-5 text-start border-b-2">Id</th>
                                <th class="px-5 text-start border-b-2">Nome</th>

                                <th v-if="users[0].schooling == undefined" class="px-5 text-start border-b-2">RA</th>
                                <th v-else class="px-5 text-start border-b-2">CPF</th>

                                <th v-if="users[0].schooling == undefined" class="px-5 text-start border-b-2">Curso</th>
                                <th v-else class="px-5 text-start border-b-2">Escolaridade</th>


                                <th class="px-5 text-start border-b-2">Papel</th>
                                <th class="px-5 text-center border-b-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in users">
                                <td class="px-5 py-4 text-sm bg-white border-b border-gray-200">
                                    <p :class="item.status ? 'text-gray-900' : 'text-red-600'"
                                        class="whitespace-nowrap">{{ item.id }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm bg-white border-b border-gray-200 hover:cursor-pointer"
                                    @click="$router.push('/user/' + item.id)">
                                    <p :class="item.status ? 'text-gray-900' : 'text-red-600'"
                                        class="whitespace-nowrap">{{ item.name }}</p>
                                </td>
                                <td v-if="item.schooling == null"
                                    class="px-5 py-4 text-sm bg-white border-b border-gray-200">
                                    <p :class="item.status ? 'text-gray-900' : 'text-red-600'"
                                        class="whitespace-nowrap">{{ formatRA(item.ra) }}</p>
                                </td>
                                <td v-else class="px-5 py-4 text-sm bg-white border-b border-gray-200">
                                    <p :class="item.status ? 'text-gray-900' : 'text-red-600'"
                                        class="whitespace-nowrap">{{ formatCPF(item.cpf) }}</p>
                                </td>
                                <td v-if="item.schooling == null"
                                    class="px-5 py-4 text-sm bg-white border-b border-gray-200">
                                    <p :class="item.status ? 'text-gray-900' : 'text-red-600'"
                                        class="whitespace-nowrap">{{ formatCourse(item.courses) }}</p>
                                </td>
                                <td v-else class="px-5 py-4 text-sm bg-white border-b border-gray-200">
                                    <p :class="item.status ? 'text-gray-900' : 'text-red-600'"
                                        class="whitespace-nowrap">{{ item.schooling[0].name }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm bg-white border-b border-gray-200">
                                    <p :class="item.status ? 'text-gray-900' : 'text-red-600'"
                                        class="whitespace-nowrap">{{ getRole(item.role) }}</p>
                                </td>
                                <td
                                    class="px-5 py-2 text-sm bg-white border-b border-gray-200 min-w-32 sm:min-w-60 text-center">
                                    <div class="sm:inline-block">
                                        <a title="Editar aluno" href="#" @click="$router.push('/user/' + item.id)"
                                            class="bg-gray-900 text-white rounded p-2 ps-3 pe-3 me-2 w-10"><font-awesome-icon
                                                :icon="['fas', 'pen-to-square']" /></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="w-full mt-5">
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3">
                        <div class="w-full">
                            Nenhum usuário encontrado com o filtro aplicado
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from "vue"
import Spinner from "../../components/Spinner.vue"
import { IUserState, userList, userFormatRA, userFormatCPF } from "../../hooks/useUser"
import { Toast } from "../../hooks/useToast"
import UserAdminFilter from '../../components/filters/UserAdminFilter.vue'

export default defineComponent({
    async setup() {
        const state: IUserState = reactive({
            isLoading: false,
            message: '',
        })
        const infoLoaded = ref(false)
        const infonotnull = ref(false)
        const users = ref()
        const total = ref()

        return {
            ...toRefs(state),
            users,
            infonotnull,
            infoLoaded,
            total,
        }
    },
    methods: {
        async loadUsers(filter) {
            this.infoLoaded = false
            this.users = []

            const result = (await userList('admin', filter)).value // Consome a API

            if (result[0] != undefined) {
                this.users = result
                this.total = Object.keys(this.users).length
                this.infonotnull = true
            } else {
                Toast().fire({ icon: 'warning', title: 'Nenhum usuário encontrado' })
                this.total = 0
                this.infonotnull = false
            }
            this.infoLoaded = true
        },
        formatRA(ra) {
            return userFormatRA(ra)
        },
        formatCPF(cpf) {
            return userFormatCPF(cpf)
        },
        getRole(id) {
            let paper
            switch (id) {
                case 1:
                    paper = 'Administrador'
                    break;
                case 2:
                    paper = 'Revisor'
                    break;
                case 3:
                    paper = 'Aluno'
                    break;
                case 4:
                    paper = 'Externo'
                    break;
            }
            return paper
        },
        formatCourse(courses) {
            let courses_array: string[] = [];
            let courses_str = ''
            for (let c = 0; c < Object.keys(courses).length; c++) {
                try {
                    if (!courses_array.includes(courses[c].name)) {
                        courses_array.push(courses[c].name)
                    }
                } catch (e) { }
            }
            courses_array.forEach(item => {
                courses_str += item + ' / '
            });
            return courses_str.slice(0, -3)
        },
    },
    components: { Spinner, UserAdminFilter }
})
</script>