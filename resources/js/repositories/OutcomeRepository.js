import backend from "./clients/ClientAxios";
import moment from "moment";
const {PERIOD_TYPES, PERIOD_DEFAULT} = require("../constants");
const resource = `${layout.APP_URL}/api/outcome`;

export default {
    suggestPeriod(period_type) {
        const sems={'1':1,'2':1,'3':1,'4':1,'5':1,'6':1, '7':2, '8':2, '9':2, '10':2,'11':2, '12':2 }
        const bims={'1':1,'2':1,'3':2,'4':2,'5':3,'6':3, '7':4, '8':4, '9':5, '10':5,'11':6 , '12':6 }
        return period_type.id == 'M' ? moment().month() + 1 :
                (period_type.id == 'T' ? moment().quarter() :
                    period_type.id == 'S' ? sems[(moment().month()+1)+''] :
                    period_type.id == 'B' ? bims[(moment().month()+1)+''] :
                    period_type.periods[0].id
                );
    },
    getPeriods(period_type_id) {
        let p = [];
        if(period_type_id) p = PERIOD_TYPES.find(e=>e.id==period_type_id).periods;
        return p;
    },
    getPeriodType(period_type_id){
        return PERIOD_TYPES.find(e=>e.id == (period_type_id ? period_type_id : PERIOD_DEFAULT));
    },
    list(params){
        return backend.get(`${resource}`,{params})
    },
    incentives_list(params){
        return backend.get(`${resource}/incentives_list`,{params})
    },
    py_trimester_list(params){
        return backend.get(`${resource}/py_trimester_list`,{params})
    },
    save_outcome(payload){
        if('id' in payload)  return backend.put(`${resource}/${payload.id}`,payload)
        return backend.post(`${resource}`,payload)
    },
    save_pending_outcomes(payload){
        return backend.post(`${resource}/approved`,payload)
    },
    save_reviewed_pending_outcomes(payload){
        return backend.post(`${resource}/reviewed`,payload)
    },
    delete(id){
        return backend.delete( `${resource}/${id}` )
    },
    get(id){
        return backend.post(`${resource}/${id}`)
    }
}
