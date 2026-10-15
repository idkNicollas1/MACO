import axios from "axios";
import { ref } from "vue";
import { credentials, validSession } from "./useAuth";

const api = await credentials();

export interface IArticleState {
    isLoading: boolean,
    message: String
}

export async function articleList(level, filter = {}) {
    await validSession()

    let path = `/article/list/${level}`
    let char = '?'

    if (filter.status != undefined && filter.status != 0) {
        path = `${path}${char}article_status=${filter.status}`
        char = '&'
    }

    if (filter.origin != undefined && filter.origin != 0) {
        path = `${path}${char}origin=${filter.origin}`
        char = '&'

        if (filter.origin == 'internal' && filter.course != undefined && filter.course != 0) {
            path = `${path}${char}course_id=${filter.course}`
            char = '&'
        } else if (filter.origin == 'external' && filter.schooling != undefined && filter.schooling != 0) {
            path = `${path}${char}schooling_id=${filter.schooling}`
            char = '&'
        }
    }

    if (filter.event != undefined && filter.event != 0) {
        path = `${path}${char}event_id=${filter.event}`
        char = '&'
    }

    const { data } = await axios.get(`${api.url}${path}`, {
        headers: api.authBearer
    })

    return ref<IArticleState[]>(data)
}

export async function submissionsList(level, filter) {
    await validSession()

    const { data } = await axios.get(`${api.url}/article/list/${level}?${filter}`, {
        headers: api.authBearer
    })

    return ref<IArticleState[]>(data)
}

export async function submissionDetails(level, article) {
    await validSession()

    const { data } = await axios.get(`${api.url}/article/list/${level}?article_id=${article}`, {
        headers: api.authBearer
    })

    return ref<IArticleState[]>(data[0])
}

export async function submissionDelete(articleID) {
    await validSession()

    const { data } = await axios.delete(`${api.url}/article/del/${articleID}`, {
        headers: api.authBearer
    })

    return data
}

export async function articleDelAuthor(article, user) {
    await validSession()

    const { data } = await axios.delete(`${api.url}/article/author/del/${article}/${user}`, {
        headers: api.authBearer,
    })

    return data
}

export async function articleDelAdvisor(article, user) {
    await validSession()

    const { data } = await axios.delete(`${api.url}/article/advisor/del/${article}/${user}`, {
        headers: api.authBearer,
    })

    return data
}

export async function articleAdd(params) {
    await validSession()

    const { data } = await axios.post(`${api.url}/article/add`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleEditStatus(article, status) {
    await validSession()

    let params = {
        'article': article,
        'status': status,
    }

    const { data } = await axios.post(`${api.url}/article/edit/status`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleAddComment(article, comment) {
    await validSession()

    let params = {
        'article': article,
        'comment': comment,
    }

    const { data } = await axios.post(`${api.url}/article/add/comment`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleAddAuthor(article, author) {
    await validSession()

    let params = {
        'article': article,
        'author': author,
    }

    const { data } = await axios.post(`${api.url}/article/add/author`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleAddExternalParticipant(article, name, email, type) {
    await validSession()

    const { data } = await axios.post(`${api.url}/article/add/participant`, {
        article: article,
        name: name,
        email: email,
        type: type
    }, {
        headers: api.authBearer
    })

    return data
}

export async function articleDelExternalParticipant(article, participantId) {
    await validSession()

    const { data } = await axios.delete(`${api.url}/article/participant/del/${article}/${participantId}`, {
        headers: api.authBearer
    })

    return data
}

export async function articleAddAdvisor(article, adivisor, coadvisor) {
    await validSession()

    let params = {
        'article': article,
        'advisor': adivisor,
        'coadvisor': coadvisor,
    }

    const { data } = await axios.post(`${api.url}/article/add/advisor`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleEditKeywords(article, keywords) {
    await validSession()

    console.log(keywords)
    let params = {
        'article': article,
        'keywords': keywords,
    }

    const { data } = await axios.post(`${api.url}/article/edit/keywords`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleAddReference(article, reference) {
    await validSession()

    let params = {
        'article': article,
        'reference': reference,
    }

    const { data } = await axios.post(`${api.url}/article/add/reference`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleDelReference(article, referenceID) {
    await validSession()

    const { data } = await axios.delete(`${api.url}/article/reference/del/${article}/${referenceID}`, {
        headers: api.authBearer
    })

    return data
}

export async function articleSubmit(article, title, summary) {
    await validSession()

    let params = {
        'article': article,
        'title': title,
        'summary': summary
    }

    const { data } = await axios.post(`${api.url}/article/edit/data`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleEditReference(article, ref, value) {
    await validSession()

    let params = {
        'article': article,
        'ref_id': ref,
        'ref_str': value,
    }

    const { data } = await axios.post(`${api.url}/article/edit/reference`, params, {
        headers: api.authBearer
    })

    return data
}

export async function articleExport(article) {
    await validSession()

    let params = {
        'article': article,
        'type': 'docx',
    }

    try {
        const { data } = await axios.post(`${api.url}/article/export`, params, {
            headers: api.authBearer
        })

        if (data.status != 'error') {
            window.open(`${api.url}/${data.file}`)
            return true
        }
    } catch { }
    return false
}

