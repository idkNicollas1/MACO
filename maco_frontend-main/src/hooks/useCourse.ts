import axios from 'axios';
import { ref } from 'vue';
import { credentials, validSession } from './useAuth';

const api = await credentials()

export interface ICourseState {
    isLoading: boolean,
    message: String,
}

export async function courseList(path = '/') {
    if (path != '/public/') {
        await validSession()
    }

    const { data } = await axios.get(`${api.url}${path}course/list`, {
        headers: api.authBearer
    })

    return ref<ICourseState[]>(data)
}

export async function courseListByUser() {
    await validSession() 

    let id = localStorage.getItem('user-id')
    const { data } = await axios.get(`${api.url}/course/list?user_id=${id}`, {
        headers: api.authBearer
    })

    return ref<ICourseState[]>(data)
}

export async function courseDetails(id) {
    await validSession() 

    const { data } = await axios.get(`${api.url}/course/list?course_id=${id}`, {
        headers: api.authBearer
    })

    return ref<ICourseState[]>(data[0])
}

export async function courseAdd(infos) { 
    await validSession() 

    let params = {        
        'name': infos.name,
        'description': infos.description,
        'status': infos.status ? 1 : 0,        
    }  

    const { data } = await axios.post(`${api.url}/course/add`, params, {
        headers: api.authBearer
    })

    return data
}

export async function courseEdit(infos) {
    await validSession() 
    
    let params = {
        'id': infos.id,
        'name': infos.name,
        'description': infos.description,
        'status': infos.status ? 1 : 0,        
    } 

    const { data } = await axios.post(`${api.url}/course/edit`, params, {
        headers: api.authBearer
    })

    return data
}