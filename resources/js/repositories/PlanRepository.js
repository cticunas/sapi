import backend from "./clients/ClientAxios";
const resource = `${layout.APP_URL}/api/plan`;
export default {
    list(params){
        return backend.get(`${resource}`,{params})
    },
    save(payload){
        if('id' in payload)  return backend.put(`${resource}/${payload.id}`,payload)
        return backend.post(`${resource}`,payload)
    },
    list_lines(params){
        return backend.get(`${resource}/list_lines`,{params})
    },
    save_line_actives(payload){
        return backend.post(`${resource}/save_line_actives`,payload)
    },
    delete(id){
        return backend.delete( `${resource}/${id}` )
    },
    get(id){
        return backend.post(`${resource}/${id}`)
    }
}