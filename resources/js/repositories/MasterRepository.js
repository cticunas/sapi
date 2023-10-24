import backend from "./clients/ClientAxios";
const resource = `${layout.APP_URL}/api/master`;
export default {
    list_states(params){
        return backend.get(`${resource}/list_states`,{params})
    }
}