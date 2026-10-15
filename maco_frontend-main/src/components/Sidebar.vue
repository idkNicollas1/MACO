<template>
    <div class="flex">
        <!-- Backdrop -->
        <div :class="isOpen ? 'block' : 'hidden'" @click="isOpen = false"
            class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>
        <!-- End Backdrop -->

        <div :class="isOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
            class="fixed inset-y-0 left-0 z-30 w-72 overflow-y-auto transition duration-300 transform bg-white lg:translate-x-0 lg:static lg:inset-0 border-r-2 border-gray">
            <div class="flex items-center justify-center mt-8">
                <div class="flex items-center">
                    <!-- Logo -->
                    <img src="/logo_maco.png" title="Maco: Magnoli & Cordeiro" alt="Logo Maco" width="150" class="rounded-full">                    
                </div>
            </div>

            <nav class="mt-5 px-2">
                <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                    :class="[$route.name === 'Dashboard' ? activeClass : inactiveClass]" to="/dashboard">
                    <font-awesome-icon :icon="['fas', 'chart-line']" />
                    <span class="mx-4 font-semibold">Dashboard</span>
                </router-link>
                <div v-if="roleUser == '1:ADMIN'">
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Submissions' ? activeClass : inactiveClass]" to="/submissions">
                        <font-awesome-icon :icon="['fas', 'newspaper']" />
                        <span class="mx-4 font-semibold">Artigos</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Courses' ? activeClass : inactiveClass]" to="/courses">
                        <font-awesome-icon :icon="['fas', 'scroll']" />
                        <span class="mx-4 font-semibold">Cursos</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Schoolings' ? activeClass : inactiveClass]" to="/schoolings">
                        <font-awesome-icon :icon="['fas', 'book']" />
                        <span class="mx-4 font-semibold">Escolaridades</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Users' ? activeClass : inactiveClass]" to="/users">
                        <font-awesome-icon :icon="['fas', 'users']" />
                        <span class="mx-4 font-semibold">Usuários</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Configurações de evento' ? activeClass : inactiveClass]" to="/settings">
                        <font-awesome-icon :icon="['fas', 'gear']" />
                        <span class="mx-4 font-semibold">Configurações</span>
                    </router-link>
                </div>
                <div v-if="roleUser == '2:ADVISOR'">
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Submissions' ? activeClass : inactiveClass]" to="/submissions">
                        <font-awesome-icon :icon="['fas', 'newspaper']" />
                        <span class="mx-4 font-semibold">Submissões</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'AdvisorCourses' ? activeClass : inactiveClass]" to="/mycourses">
                        <font-awesome-icon :icon="['fas', 'graduation-cap']" />
                        <span class="mx-4 font-semibold">Meus cursos</span>
                    </router-link>
                </div>
                <div v-if="roleUser == '3:AUTHOR'">
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Events' ? activeClass : inactiveClass]" to="/events">
                        <font-awesome-icon :icon="['fas', 'file-circle-plus']" />
                        <span class="mx-4 font-semibold">Submeter artigo</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Corrections' ? activeClass : inactiveClass]" to="/corrections">
                        <font-awesome-icon :icon="['fas', 'file-pen']" />
                        <span class="mx-4 font-semibold">Correções pendentes</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Historic' ? activeClass : inactiveClass]" to="/historic">
                        <font-awesome-icon :icon="['fas', 'newspaper']" />
                        <span class="mx-4 font-semibold">Meus artigos</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'MyCertificates' ? activeClass : inactiveClass]" to="/mycertificates">
                        <font-awesome-icon :icon="['fas', 'certificate']" />
                        <span class="mx-4 font-semibold">Meus certificados</span>
                    </router-link>
                </div>
                <div v-if="roleUser == '4:EXTERNAL'">
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Events' ? activeClass : inactiveClass]" to="/events">
                        <font-awesome-icon :icon="['fas', 'file-circle-plus']" />
                        <span class="mx-4 font-semibold">Submeter artigo</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'Historic' ? activeClass : inactiveClass]" to="/historic">
                        <font-awesome-icon :icon="['fas', 'newspaper']" />
                        <span class="mx-4 font-semibold">Meus artigos</span>
                    </router-link>
                    <router-link class="flex items-center px-4 py-2 mt-2 duration-200"
                        :class="[$route.name === 'MyCertificates' ? activeClass : inactiveClass]" to="/mycertificates">
                        <font-awesome-icon :icon="['fas', 'certificate']" />
                        <span class="mx-4 font-semibold">Meus certificados</span>
                    </router-link>
                </div>
            </nav>        
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import { useSidebar } from "../hooks/useSidebar";
import { getUserRole } from "../hooks/useAuth";

export default defineComponent({
    async setup() {
        const { isOpen } = useSidebar();
        const activeClass = ref(
            "bg-gray-400 bg-opacity-30 text-gray-700 rounded-md"
        );
        const inactiveClass = ref(
            "border-gray-900 text-gray-500 hover:bg-gray-600 hover:bg-opacity-25 hover:text-gray-100 rounded-md"
        );
        const roleUser = ref('')

        return {
            isOpen,
            activeClass,
            inactiveClass,
            roleUser,
        };
    },
    beforeMount() {
        this.roleUser = getUserRole()
    },    
});
</script>