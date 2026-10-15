import axios from 'axios';
import { credentials, validSession } from './useAuth';

const api = await credentials()

export async function logsList() {
    await validSession()

    const { data } = await axios.get(`${api.url}/report/logs`, {
        headers: api.authBearer
    })

    return data
}

export async function submissionsByEventList() {
    await validSession()
    
    const { data } = await axios.get(`${api.url}/report/submissions-by-event`, {
        headers: api.authBearer
    })

    return data
}