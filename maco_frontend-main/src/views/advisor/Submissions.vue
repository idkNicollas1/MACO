<template>
    <div class="mt-2">
        <div class="bg-white border-2 rounded-xl border-gray px-5 py-5 mt-2 mb-2">
            <div class="flex flex-wrap">
                <div class="w-full grid grid-cols-6 gap-4">
                    <div class="col-start-1 col-end-8 ...">
                        <p class="text-gray-500 font-semibold text-xl">
                            <span class="pr-3 hover:text-gray-900 hover:cursor-pointer" onclick="history.go(-1)">
                                <font-awesome-icon :icon="['fas', 'arrow-left']" /> </span>
                            <span class="border-b-2"> {{ pageTitle }} | {{ total }}</span>
                            <span v-if="!infoLoaded">
                                <Spinner />
                            </span>
                        </p>
                    </div>
                    <div v-if="getRole() == 'admin' || getRole() == 'advisor'" class="col-end-10 col-span-2 ...">
                        <ArticleAdminFilter @some-event="loadArticles" v-if="getRole() == 'admin'" />
                        <ArticleAdvisorFilter @some-event="loadArticles" v-if="getRole() == 'advisor'" />
                    </div>
                </div>
                <div v-if="infonotnull" class="overflow-x-auto inline-block min-w-full rounded-lg">
                    <table class="min-w-full leading-normal mt-5">
                        <thead>
                            <tr>
                                <th class="px-5 text-start border-b-2">ID</th>
                                <th class="px-5 text-start border-b-2">Título</th>

                                <th v-if="currentOrigin == 'all'" class="px-5 text-start border-b-2">
                                    Escolaridade/Cursos(s)</th>
                                <th v-if="currentOrigin == 'external'" class="px-5 text-start border-b-2">Escolaridade
                                </th>
                                <th v-if="currentOrigin == 'internal'" class="px-5 text-start border-b-2">Curso(s)</th>

                                <th class="px-5 text-start border-b-2">Autores(s)</th>
                                <th class="px-5 text-start border-b-2">Status</th>
                                <th class="px-5 text-center border-b-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in articles">
                                <td class="px-5 py-2 text-sm bg-white border-b border-gray-200">
                                    <p class="text-gray-900 whitespace-nowrap">{{ item.id }}</p>
                                </td>
                                <td class="px-5 py-2 text-sm bg-white border-b border-gray-200 hover:cursor-pointer"
                                    v-on:click="viewSubmission(item.id, item.event)">
                                    <p class="text-gray-900 whitespace-nowrap">{{ item.title == ' ' ? 'Título não informado' : item.title}}</p>
                                </td>

                                <td v-if="currentOrigin == 'internal'"
                                    class="px-5 py-2 text-sm bg-white border-b border-gray-200 max-w-36"
                                    :title="formatCourseAndAuthors(item.authors, 'course_name')">
                                    <p class="text-gray-900 whitespace-nowrap overflow-hidden text-ellipsis w-full">{{
                                        formatCourseAndAuthors(item.authors, 'course_name') }}</p>
                                </td>
                                <td v-if="currentOrigin == 'external'"
                                    class="px-5 py-2 text-sm bg-white border-b border-gray-200 max-w-36"
                                    :title="formatCourseAndAuthors(item.authors, 'schooling_name')">
                                    <p class="text-gray-900 whitespace-nowrap overflow-hidden text-ellipsis w-full">{{
                                        formatCourseAndAuthors(item.authors, 'schooling_name') }}</p>
                                </td>
                                <td v-if="currentOrigin == 'all' && formatCourseAndAuthors(item.authors, 'course') != 'null'"
                                    class="px-5 py-2 text-sm bg-white border-b border-gray-200 max-w-36"
                                    :title="formatCourseAndAuthors(item.authors, 'course_name')">
                                    <p class="text-gray-900 whitespace-nowrap overflow-hidden text-ellipsis w-full">{{
                                        formatCourseAndAuthors(item.authors, 'course_name') }}</p>
                                </td>
                                <td v-if="currentOrigin == 'all' && formatCourseAndAuthors(item.authors, 'schooling') != 'null'"
                                    class="px-5 py-2 text-sm bg-white border-b border-gray-200 max-w-36"
                                    :title="formatCourseAndAuthors(item.authors, 'schooling_name')">
                                    <p class="text-gray-900 whitespace-nowrap overflow-hidden text-ellipsis w-full">
                                        <b>{{ formatCourseAndAuthors(item.authors, 'schooling_name') }}</b>
                                    </p>
                                </td>
                                <td class="px-5 py-2 text-sm bg-white border-b border-gray-200 max-w-36"
                                    :title="formatCourseAndAuthors(item.authors, 'name', item.external_participants)">
                                    <p class="text-gray-900 whitespace-nowrap overflow-hidden text-ellipsis w-full">{{
                                        formatCourseAndAuthors(item.authors, 'name', item.external_participants) }}</p>
                                </td>
                                <td class="px-5 py-2 text-sm border-b border-gray-200">
                                    <p class="text-gray-900 whitespace-nowrap"><span
                                            :class="getColorArticleStatus(item.status)"
                                            class="rounded pt-1 pb-1 pr-3 pl-3 text-white">{{
                                                formatArticleStatus(item.status) }}</span></p>
                                </td>
                                <td
                                    class="px-5 py-2 text-sm bg-white border-b border-gray-200 min-w-32 sm:min-w-60 text-center">
                                    <div class="sm:inline-block">
                                        <Export :article="item.id" />
                                        <Certificate v-if="item.status == 'finished'" :article="item" />
                                        <button title="Visualizar o artigo"
                                            v-on:click="viewSubmission(item.id, item.event)"
                                            class="bg-gray-900 text-white rounded-md p-2 ps-3 pe-3 w-10"><font-awesome-icon
                                                :icon="['fas', 'eye']" /></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="w-full mt-5">
                    <div class="border-t-2 shadow-lg rounded p-2 mb-3">
                        <div class="w-full">
                            Nenhum artigo disponível
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, toRefs } from 'vue';
import { IArticleState, articleList } from '../../hooks/useArticle';
import Spinner from "../../components/Spinner.vue"
import { Toast } from '../../hooks/useToast';
import router from '../../router';
import ArticleAdminFilter from '../../components/filters/ArticleAdminFilter.vue';
import { getUserRole } from '../../hooks/useAuth';
import ArticleAdvisorFilter from '../../components/filters/ArticleAdvisorFilter.vue';
import Certificate from '../../components/Certificate.vue';
import Export from '../../components/Export.vue';

export default defineComponent({
    async setup() {
        const state: IArticleState = reactive({
            isLoading: false,
            message: '',
        })

        const pageTitle = 'Listagem de artigos'
        const articles = ref()
        const infonotnull = ref(false)
        const infoLoaded = ref(false)
        const currentOrigin = ref()
        const total = ref(0)
        return {
            ...toRefs(state),
            pageTitle,
            articles,
            infonotnull,
            infoLoaded,
            currentOrigin,
            total,
        }
    },
    methods: {
        viewSubmission(articleID, eventID) {
            sessionStorage.setItem('event-id-selected', eventID)
            router.push(`/submit/${articleID}`)
        },
        async loadArticles(filter) {
            this.infoLoaded = false
            this.infonotnull = false
            this.articles = null
            this.currentOrigin = filter.origin

            let result = (await articleList(getUserRole(true).toLowerCase(), filter)).value

            if (result[0] != undefined) {
                this.articles = result
                this.total = Object.keys(this.articles).length
                this.infonotnull = true
            } else {
                Toast().fire({ icon: 'warning', title: 'Nenhum artigo encontrado com os filtros utilizados' })
                this.total = 0
            }
            this.infoLoaded = true
        },
        formatCourseAndAuthors(data, key, extra: Record<string, any> = {}) {
            let _array: string[] = [];
            let _str = ''
            for (let c = 0; c < Object.keys(data).length; c++) {
                try {
                    if (!_array.includes(data[c][key])) {
                        _array.push(data[c][key])
                    }
                } catch (e) { }
            }

            if (key === 'name') {
                Object.values(extra ?? {}).forEach((p: any) => {
                    if (p?.type === 'author' && p.name && !_array.includes(p.name)) {
                        _array.push(p.name)
                    }
                })
            }

            _array.forEach(item => {
                _str += item + ' / '
            });
            return _str.slice(0, -3)
        },
        formatArticleStatus(status) {
            let newStatus = ''
            switch (status) {
                case 'in_submission':
                    newStatus = 'Em submissão'
                    break
                case 'in_revision':
                    newStatus = 'Em revisão'
                    break
                case 'in_correction':
                    newStatus = 'Em correção'
                    break
                case 'approved':
                    newStatus = 'Aprovado'
                    break
                case 'disapproved':
                    newStatus = 'Reprovado'
                    break
                case 'finished':
                    newStatus = 'Finalizado'
                    break
            }
            return newStatus;
        },
        getColorArticleStatus(status) {
            let color = ''
            switch (status) {
                case 'in_submission':
                    color = 'bg-yellow-500'
                    break
                case 'in_revision':
                    color = 'bg-cyan-500'
                    break
                case 'in_correction':
                    color = 'bg-red-500'
                    break
                case 'approved':
                    color = 'bg-green-500'
                    break
                case 'disapproved':
                    color = 'bg-red-500'
                    break
                case 'finished':
                    color = 'bg-gray-900'
                    break
            }
            return color;
        },
        getRole() {
            return getUserRole(true).toLowerCase()
        }
    },
    beforeMount() {
        if (getUserRole(true) == 'ADVISOR') {
            this.pageTitle = 'Submissões aguardando revisão'
        }
        if (getUserRole(true) == 'AUTHOR' || getUserRole(true) == 'EXTERNAL') {
            router.push('/events')
        }
    },
    components: { Spinner, ArticleAdminFilter, ArticleAdvisorFilter, Certificate, Export }
})
</script>