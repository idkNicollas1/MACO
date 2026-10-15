import axios from 'axios';
import { ref } from 'vue';
import Swal from "sweetalert2"

const endpointUrl = import.meta.env.VITE_URL_API

export interface ILoginState {
    isLoading: boolean,
    message: String,
    token: String
}

export async function apiLogin(email, password) {

    localStorage.clear()

    var params = {
        'email': email,
        'password': password
    }

    const { data } = await axios.post(`${endpointUrl}/user/login`, params)

    if (data.token != undefined) {
        localStorage.clear()
        localStorage.setItem('user-token', data.token)
        localStorage.setItem('user-token-expires', data.token_expires)
        localStorage.setItem('user-id', data.user.id)
        localStorage.setItem('user-name', data.user.name)
        localStorage.setItem('user-email', data.user.email)
        localStorage.setItem('user-ra', data.user.ra)
        localStorage.setItem('user-role', data.user.role)
        localStorage.setItem('user-auth-day', (new Date()).getDate().toString())
    }

    const loginData = ref<ILoginState[]>(data);

    return loginData
}

export async function apiLoginAdmin(user) {
    var params = {
        'user': user
    }

    const { data } = await axios.post(`${endpointUrl}/user/login/admin`, params, {
        headers: (await credentials()).authBearer
    })

    if (data.token != undefined) {
        let actual_user = {
            token: localStorage.getItem('user-token'),
            token_expires: localStorage.getItem('user-token-expires'),
            id: localStorage.getItem('user-id'),
            name: localStorage.getItem('user-name'),
            email: localStorage.getItem('user-email'),
            ra: localStorage.getItem('user-ra'),
            role: localStorage.getItem('user-role'),
        }
        localStorage.clear()
        localStorage.setItem('user-backup', JSON.stringify(actual_user))
        localStorage.setItem('user-token', data.token)
        localStorage.setItem('user-token-expires', data.token_expires)
        localStorage.setItem('user-id', data.user.id)
        localStorage.setItem('user-name', data.user.name)
        localStorage.setItem('user-email', data.user.email)
        localStorage.setItem('user-ra', data.user.ra)
        localStorage.setItem('user-role', data.user.role)
        localStorage.setItem('user-auth-day', (new Date()).getDate().toString())
        window.location.href = '/dashboard'
    }
}

export async function backToUser(user) {
    if (user == 'error') {
        return
    }
    localStorage.clear()
    localStorage.setItem('user-token', user.token)
    localStorage.setItem('user-token-expires', user.token_expires)
    localStorage.setItem('user-id', user.id)
    localStorage.setItem('user-name', user.name)
    localStorage.setItem('user-email', user.email)
    localStorage.setItem('user-ra', user.ra)
    localStorage.setItem('user-role', user.role)
    window.location.href = '/dashboard'
}

export function authBasic(to, from, next) {
    const token = localStorage.getItem('user-token') != undefined
    token ? next() : next('/login')
}

export function authAdmin(to, from, next) {
    const token = localStorage.getItem('user-token') != undefined
    const role = String(getUserRole()) == '1:ADMIN'
    token && role ? next() : next('/login')
}

export function authAdvisor(to, from, next) {
    const token = localStorage.getItem('user-token') != undefined
    const role = String(getUserRole()) == '2:ADVISOR'
    token && role ? next() : next('/login')
}

export function authAuthor(to, from, next) {
    const token = localStorage.getItem('user-token') != undefined
    const role = String(getUserRole()) == '4:EXTERNAL' || String(getUserRole()) == '3:AUTHOR'
    token && role ? next() : next('/login')
}

export function authAuthorOnly(to, from, next) {
    const token = localStorage.getItem('user-token') != undefined
    const role = String(getUserRole()) == '3:AUTHOR'
    token && role ? next() : next('/login')
}

export function getUserRole(modestring = false) {
    try {
        let role = atob(localStorage.getItem('user-role') || '')
        if (modestring) {
            role = role.split(':')[1]
        }
        return role
    }
    catch (e) {
        return 'null'
    }
}

export async function credentials() {
    let url = String(endpointUrl)
    let token = String(localStorage.getItem('user-token'))
    let authBearer = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
    }

    return {
        url,
        token,
        authBearer,
    }
}

export async function validSession() {
    const today = new Date()
    const dateEnd = new Date(localStorage.getItem('user-token-expires') ?? today)

    if (today >= dateEnd) {
        let timerInterval;
        Swal.fire({
            title: "Alerta!",
            html: "Sessão expirada",
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                localStorage.clear()
                window.location.href = '/login'
            }
        });
    }
}