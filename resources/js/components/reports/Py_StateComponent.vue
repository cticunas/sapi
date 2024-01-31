<template>
    <div style="padding:10px 20px 10px 10px">
        <h2>Proyectos por Estado</h2>
        <a-form-model style="display:flex; gap: 10px; padding:8px; background:#333; color:white;">
            <div>
                <label>Tipo:</label>
                <a-select  placeholder="Seleccione tipo" v-model="filter.type_research" style="width:120px">
                    <a-select-option :value="1">Tesis</a-select-option>
                    <a-select-option :value="2">Inv. Docente</a-select-option>
                </a-select>
            </div>
            <div div style="display:flex; align-items: center;" v-if="filter.type_research == 1">
                <label style='margin: 0; padding-right: 5px;'>Grado:</label>
                <a-select  placeholder=" Nivel" v-model="filter.grade" style="width:110px">
                    <a-select-option :value="1"> Pregrado </a-select-option>
                    <a-select-option :value="2"> Posgrado  </a-select-option>
                </a-select>
            </div>
            <div style="display:flex; align-items: center;">
                <label style='margin: 0; padding-right: 5px;'>Estado:</label>
                <a-select  placeholder="Estado" v-model="filter.research_state_id" style="width:130px">
                    <a-select-option v-for="item in states" :key="item.id" :value="item.id"> {{item.name}} </a-select-option>
                </a-select>
            </div>
            <div>
                <label style='margin: 0; padding-right: 5px;'>Fechas:</label>
                <a-date-picker v-model="filter.from" placeholder="Desde" style="width:120px" :format="DATEFORMAT"/>
                <a-date-picker v-model="filter.to" placeholder="Hasta" style="width:120px" :format="DATEFORMAT"/>
            </div>
            <a-button type="primary" icon="search" @click="fetch({format:'pdf'})" title="Buscar"></a-button>
            <a-button type="primary" icon="file-excel" @click="fetch({format:'excel'})" title="Exportar"></a-button>
        </a-form-model>

        <div v-if="report_url">
            <iframe :src="report_url" style="width:100%; height:70vh; border:0">
            </iframe>
        </div>
    </div>
</template>
<script>
import moment from "moment";
import Repository from "../../repositories/RepositoryFactory";
const MasterRepository = Repository.get("master");
const default_report_url = '/api/research/py_by_state' ;
import { DATEFORMAT } from "../../constants";

/*---------- */
export default {
    data() {
        return {
            DATEFORMAT,
            filter:{type_research:1,grade:1, research_state_id:1,from: (moment().format('YYYY'))+"-01-01", to: moment()  },
            pagination: {},
            loading: false,
            report_url:null,
            states:[],
        }
    },
    async mounted() {
        this.listStates();
    },
    methods: {
        async listStates() {
            try {
                let { data } = await MasterRepository.list_states({});
                this.states = data;
                console.log(this.states);
            } catch (error) {
                this.error(error);
            }
        },
        async fetch(params = {}) {
            try {
                const format = params.format || 'pdf';
                if (!this.filter.from) throw 'Debe seleccionar una fecha de inicio';
                if (!this.filter.to) throw 'Debe seleccionar una fecha de fin';
                params.type_research = this.filter.type_research;
                params.grade = this.filter.grade;
                params.research_state_id = this.filter.research_state_id;
                params.from = this.filter.from;
                params.to = this.filter.to;
                let from = moment(params.from).format('YYYY-M-D');
                let to = moment(params.to).format('YYYY-MM-DD');
                this.report_url = default_report_url+'?type_research='+params.type_research+'&research_state_id='+ params.research_state_id +'&format='+format+'&from='+from+'&to='+to+'&r=' + Math.random()+(params.type_research==1?'&grade='+params.grade:'');
            } catch(error) {
                this.error(error);
            }
        },
        error (message) {
            this.$message.error(message||'Error al procesar');
        },
        success (message) {
            this.$message.success(message||'Proceso Correcto');
        },
    }
}
</script>
<style scoped>
</style>
