import backend from "./clients/ClientAxios";
const resource = `${layout.APP_URL}/api/person`;
export default {
    list(params){
        return backend.get(`${resource}`,{params})
    },
    list_roles(params){
        return backend.get(`${resource}/roles`,{params})
    },
    
    save(payload){
        if('id' in payload)  return backend.put(`${resource}/${payload.id}`,payload)
        return backend.post(`${resource}`,payload)
    },

    delete(id){
       return backend.delete( `${resource}/${id}` )
    },
    get(id){
        return backend.get(`${resource}/${id}`)
    },
    get_photo(id){
        return backend.get(`${resource}/${id}`)
    },
    get_author(id){
        return backend.get(`${resource}/author/${id}`)
    },
    get_authors(params){
        return backend.get(`${resource}/get_authors`,{params})
    },
    get_author_activity(id){
        return backend.get(`${resource}/author_activity/${id}`)
    }
}