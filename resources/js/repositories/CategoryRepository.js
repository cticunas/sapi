import backend from "./clients/ClientAxios";
const resource = `${layout.APP_URL}/api/category`;
export default {
    list(params){
        return backend.get(`${resource}`,{params})
    },
    list_programs_and_lines(params){
        return backend.get(`${resource}/list_programs_and_lines`,{params})
    },
    save(payload){
        if('id' in payload)  return backend.put(`${resource}/${payload.id}`,payload)
        return backend.post(`${resource}`,payload)
    },
    delete(id){
        return backend.delete( `${resource}/${id}` )
    },
    get(id){
        return backend.post(`${resource}/${id}`)
    },
    get_members(id){
        return backend.get(`${resource}/${id}/members`)
    }

}