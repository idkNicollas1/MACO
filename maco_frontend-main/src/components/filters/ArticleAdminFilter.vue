<template>
    <button @click="filter = !filter"
        class="relative z-10 block h-9 text-bold bg-blue-700 text-white ps-4 pe-4 rounded">
        <font-awesome-icon :icon="['fas', 'filter']" /> <span class="hidden md:inline">Filtros</span>
    </button>
    <div v-show="filter" @click="filter = false" class="fixed inset-0 z-10 w-full h-full">
    </div>
    <transition enter-active-class="transition duration-150 ease-out transform" enter-from-class="scale-95 opacity-0"
        enter-to-class="scale-100 opacity-100" leave-active-class="transition duration-150 ease-in transform"
        leave-from-class="scale-100 opacity-100" leave-to-class="scale-95 opacity-0">
        <div v-show="filter" class="absolute right-10 z-20 p-3 mt-2 bg-white rounded-md shadow-xl border-2">
            <p class="mt-2">Filtro por status</p>
            <select v-model="status" @change="$emit('someEvent', emitFilters())"
                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full">
                <option value="1">Artigos em submissão</option>
                <option value="2">Artigos em revisão</option>
                <option v-if="origin != 'external'" value="3">Artigos em correção</option>
                <option value="4">Artigos aprovados</option>
                <option value="5">Artigos finalizados</option>
                <option value="6">Artigos reprovados</option>
            </select>
            <br>
            <p>Filtro por origem</p>
            <select v-model="origin" @change="handleOriginChange()"
                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full">
                <option value="" disabled>Origens</option>
                <option v-for="item in origins" :value="item.id">{{ item.name }}</option>
            </select>
            <br>
            <template v-if="origin == 'internal'">
                <p>Filtro por curso</p>
                <select v-model="course" @change="$emit('someEvent', emitFilters())"
                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full">
                    <option value="0" disabled>Todos os cursos</option>
                    <option v-for="item in courses" :value="item.id">{{ item.name }}</option>
                </select>
                <br>
            </template>

            <template v-if="origin == 'external'">
                <p>Filtro por Escolaridade</p>
                <select v-model="schooling" @change="$emit('someEvent', emitFilters())"
                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full">
                    <option value="0" disabled>Todas as escolaridades</option>
                    <option v-for="item in schoolings" :value="item.id">{{ item.name }}</option>
                </select>
                <br>
            </template>
            <p>Filtro por evento</p>
            <select v-model="event" @change="$emit('someEvent', emitFilters())"
                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full">
                <option value="0" selected>Todos os eventos</option>
                <option v-for="item in events" :value="item.id">{{ item.name }}</option>
            </select>
        </div>
    </transition>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { courseList } from '../../hooks/useCourse';
import { schoolingList } from '../../hooks/useSchooling';
import { eventList } from '../../hooks/useEvent';

export default defineComponent({
    emits: ['someEvent'],
    async setup() {
        const events = await eventList()
        const saved = JSON.parse(sessionStorage.getItem('admin-article-filters') || 'null')

        const filter = ref(false);
        const status = ref(saved?.status ?? 5)
        const course = ref(saved?.course ?? 0)
        const schooling = ref(saved?.schooling ?? 0)
        const origin = ref(saved?.origin ?? 'all')
        const event = ref(saved?.event ?? events.value[0]['id'])
        const courses = ref()
        const schoolings = ref()
        const origins = ref()

        return {
            filter,
            status,
            course,
            schooling,
            origin,
            event,
            events,
            courses,
            schoolings,
            origins,
        }
    },
    methods: {
        async loadCourses() {
            const result = await courseList()
            this.courses = result
        },
        async loadSchooling() {
            const result = await schoolingList()
            this.schoolings = result
        },
        loadOrigin() {
            this.origins = [
                { id: 'all', name: 'Todos' },
                { id: 'internal', name: 'Interno' },
                { id: 'external', name: 'Externo' }
            ]
        },
        handleOriginChange() {
            if (this.origin == 'external' && this.status == 3) {
                this.status = 2
            }
            this.$emit('someEvent', this.emitFilters())
        },
        emitFilters() {
            const filters = {
                status: this.status,
                origin: this.origin, 
                course: this.origin == 'internal' ? this.course : 0,
                schooling: this.origin == 'external' ? this.schooling : 0,
                event: this.event
            }
            sessionStorage.setItem('admin-article-filters', JSON.stringify(filters))
            return filters
        }
    },
    beforeMount() {
        this.loadCourses()
        this.loadSchooling()
        this.loadOrigin()
        this.$emit('someEvent', this.emitFilters())
    }
})
</script>