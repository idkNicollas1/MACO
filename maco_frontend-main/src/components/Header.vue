<template>
  <header class="flex items-center justify-between px-6 py-4 bg-white border-b-2 border-gray mx-5 my-3 rounded-md">
    <div class="flex items-center">
      <button @click="isOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" fill="rgb(0,0,0)">
          <path
            d="M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z" />
        </svg>
      </button>

      <div class="flex items-center justify-center ml-3">
        <p class="text-gray-500 font-semibold text-xl">Maco - <i>{{ roleUser }}</i></p>
      </div>

    </div>

    <div class="flex items-center">

      <div class="relative">
        <button @click="dropdownOpen = !dropdownOpen"
          class="relative z-10 block w-9 h-9 overflow-hidden rounded-full shadow focus:outline-none text-bold bg-blue-700 text-white">
          {{ nameUser }}
        </button>

        <div v-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 z-10 w-full h-full"></div>

        <transition enter-active-class="transition duration-150 ease-out transform"
          enter-from-class="scale-95 opacity-0" enter-to-class="scale-100 opacity-100"
          leave-active-class="transition duration-150 ease-in transform" leave-from-class="scale-100 opacity-100"
          leave-to-class="scale-95 opacity-0">
          <div v-show="dropdownOpen" class="absolute right-0 z-20 w-48 py-2 mt-2 bg-white rounded-md shadow-xl">
            <a v-if="userInBackup()" href="#" @click="backUser()"
              class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-400 hover:bg-opacity-30 hover:text-gray-900">Voltar
              para "{{ getNameUserBackup() }}"</a>
            <hr>
            <a href="#" @click="profile"
              class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-400 hover:bg-opacity-30 hover:text-gray-900">Meu
              perfil</a>
            <hr>
            <a href="#" @click="logout"
              class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-400 hover:bg-opacity-30 hover:text-gray-900">Sair</a>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import { useSidebar } from "../hooks/useSidebar";
import { backToUser, getUserRole } from "../hooks/useAuth";
import router from "../router";

export default defineComponent({
  data() {
    return {
      nameUser: '',
    }
  },
  setup(_, { emit }) {
    const dropdownOpen = ref(false);
    const { isOpen } = useSidebar();
    const roleUser = ref('');

    return {
      isOpen,
      dropdownOpen,
      roleUser,
    };
  },
  methods: {
    profile() {
      router.push(`/user/${this.getIdUserLogged()}`)
    },
    logout() {
      localStorage.clear();
      sessionStorage.clear();
      router.push('/')
    },
    loadUser() {
      let name = localStorage.getItem('user-name')
      if (name != null) {
        let name_split = name.split(' ')
        let char1 = name_split[0].substr(0, 1)
        let char2 = name_split[name_split.length - 1].substr(0, 1)

        return (char1 + char2).toUpperCase()
      }
      return ''
    },
    getIdUserLogged() {
      return window.localStorage.getItem('user-id')
    },
    userInBackup() {
      let user = localStorage.getItem('user-backup')
      if (user != '' && user != undefined && user != null) {
        return true
      }
      return false
    },
    getUserBackup() {
      try {
        let user = localStorage.getItem('user-backup')
        user = JSON.parse(user || '')
        return user
      } catch {
        return 'error'
      }
    },
    getNameUserBackup() {
      let user = this.getUserBackup()
      let name = user?.name
      let chars_name = ''
      if (name != null) {
        let name_split = name.split(' ')
        name_split.forEach(element => {
          chars_name += element.substr(0, 1)
        });
      }
      return chars_name
    },
    async backUser() {
      await backToUser(this.getUserBackup())
    }
  },
  beforeMount() {
    let role = getUserRole(true)
    if (role == 'ADMIN') {
      role = 'ADMINISTRADOR'
    } else if (role == 'AUTHOR') {
      role = 'AUTOR'
    } else if (role == 'ADVISOR') {
      role = 'REVISOR'
    } else if (role == 'EXTERNAL') {
      role = 'EXTERNO'
    }
    this.roleUser = role
    this.nameUser = this.
    loadUser()
  },
});
</script>
