import axios from 'axios';
import { ref } from 'vue';
import { credentials, validSession } from './useAuth';

const api = await credentials()

export interface IUserState {
    isLoading: boolean,
    message: String,
}

export async function userList(mode, filter) {
    await validSession()
    let path = ''
    if (filter.user_info != undefined) {
        path = `&user_info=${filter.user_info}&article_id=${filter.article_id}`
    }
    if (filter.user_info_with_role != undefined) {
        path = `&user_role=${filter.user_role}&user_info=${filter.user_info_with_role}`
    } else if (filter.user_role != undefined) {
        path = `&user_role=${filter.user_role}`
    }

    const { data } = await axios.get(`${api.url}/user/list?mode=${mode}${path}`, {
        headers: api.authBearer
    })

    return ref<IUserState[]>(data)
}

export async function userDetails(id, mode) {
    await validSession()

    const { data } = await axios.get(`${api.url}/user/list?user_id=${id}${mode}`, {
        headers: api.authBearer
    })

    return data[0]
}

export async function userAdd(params) {
    const { data } = await axios.post(`${api.url}/user/register`, params)

    return data
}

export async function userEdit(params) {
    await validSession()

    const { data } = await axios.post(`${api.url}/user/edit`, params, {
        headers: api.authBearer,
    })

    return data
}

export async function userDel(id) {
    await validSession()

    const { data } = await axios.delete(`${api.url}/user/del/${id}`, {
        headers: api.authBearer
    })

    return data
}

export async function userRecoverPassword(params) {
    const { data } = await axios.post(`${api.url}/user/recoverpassword`, params, {
        headers: api.authBearer
    })

    return data
}

export async function generateCertificate(params) {
    await validSession()

    const { data } = await axios.post(`${api.url}/user/certificate/export`, params, {
        headers: api.authBearer
    })

    return data
}


export async function userRemoveCourse(user, course) {
    await validSession()

    const { data } = await axios.delete(`${api.url}/user/remove/course/${user}/${course}`, {
        headers: api.authBearer
    })

    return data
}

export async function userAddCourse(params) {
    await validSession()

    const { data } = await axios.post(`${api.url}/user/add/course`, params, {
        headers: api.authBearer
    })

    return data
}

export function userFormatCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, "")
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4")
}

export function userFormatRA(ra) {
    if (ra) {
        return ra.replace(/^(\d{5})-?(\d{1})$/, "$1-$2")
    }
}

