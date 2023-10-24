import backend from "./clients/ClientAxios";
const resource = `${layout.APP_URL}/api/research`;
export default {
    list(params){
        return backend.get(`${resource}`,{params})
    },
    list_outcomes(params){
        return backend.get(`${resource}/public_list`,{params})
    },
    list_outcomes_by_year(params){
        return backend.get(`${resource}/public_list_by_year`,{params})
    },
    save(payload){
        if('id' in payload)  return backend.put(`${resource}/${payload.id}`,payload)
        return backend.post(`${resource}`,payload)
    },
    
    listStatus(params){
        return backend.get(`${resource}/list_status`,{params})
    },
    saveStatus(payload){
        return backend.post(`${resource}/save_status`,payload)
    },
    saveOutcome(payload){
        return backend.post(`${resource}/save_outcome`,payload)
    },
    delete(id){
        return backend.delete( `${resource}/${id}` )
    },
    get(id){
        return backend.post(`${resource}/${id}`)
    }
}