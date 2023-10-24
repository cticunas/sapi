import backend from "./clients/ClientAxios";
const resource = `${layout.APP_URL}/api/user`;
export default {
    list(params){
        return backend.get(`${resource}`,{params});
    },
    loginByGoogle(params){
      return backend.get(`${resource}/loginbygoogle`,{params});
    },
    loginByOffice(params){
        return backend.get(`${resource}/loginbyoffice`,{params});
      },
    listRoles(params={}){
        return backend.get(`${resource}/role`,{params});
    },
    save(payload){
        if('id' in payload)  return backend.put(`${resource}/${payload.id}`,payload)
        return backend.post(`${resource}`,payload)
    },
    delete(id){
        return backend.delete(`${resource}/${id}`)
    },
    get(id){
        return backend.get(`${resource}/${id}`)
    },
}