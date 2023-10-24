import backend from "./clients/ClientAxios";
const resource = `${layout.APP_URL}/api/file`;
export default {
    list(params){
        return backend.get(`${resource}`,{params})
    },
    save(payload){
        if('id' in payload)  return backend.put(`${resource}/${payload.id}`,payload)
        return backend.post(`${resource}`,payload)
    },
    get(id){
        return backend.post(`${resource}/${id}`)
    },
    getURlUploadImage(){
        return `${layout.APP_URL}/api/file/upload`;
    },
    delete(id){
        return backend.delete( `${resource}/${id}` )
    }
}