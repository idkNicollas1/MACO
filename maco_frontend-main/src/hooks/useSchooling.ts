import axios from 'axios';
import { ref } from 'vue';
import { credentials, validSession } from './useAuth';

const api = await credentials()

export interface ISchoolingState {
    isLoading: boolean,
    message: String,
}

export async function schoolingList(path = '/') {
    if (path != '/public/') {
        await validSession()
    }

    const { data } = await axios.get(`${api.url}${path}schooling/list`, {
        headers: api.authBearer
    })

    return ref<ISchoolingState[]>(data)
}

export async function schoolingListByUser() {
    await validSession() 

    let id = localStorage.getItem('user-id')
    const { data } = await axios.get(`${api.url}/schooling/list?user_id=${id}`, {
        headers: api.authBearer
    })

    return ref<ISchoolingState[]>(data)
}

export async function schoolingDetails(id) {
    await validSession() 

    const { data } = await axios.get(`${api.url}/schooling/list?course_id=${id}`, {
        headers: api.authBearer
    })

    return ref<ISchoolingState[]>(data[0])
}

export async function schoolingAdd(infos) { 
    await validSession() 

    let params = {        
        'name': infos.name,
        'status': infos.status ? 1 : 0,        
    }  

    const { data } = await axios.post(`${api.url}/schooling/add`, params, {
        headers: api.authBearer
    })

    return data
}

export async function schoolingEdit(infos) {
    await validSession() 
    
    let params = {
        'id': infos.id,
        'name': infos.name,
        'status': infos.status ? 1 : 0,        
    } 

    const { data } = await axios.post(`${api.url}/schooling/edit`, params, {
        headers: api.authBearer
    })

    return data
}