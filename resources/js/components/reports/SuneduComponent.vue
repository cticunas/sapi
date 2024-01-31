<template>
    <div style="padding:10px 20px 10px 10px">
        <div style="display:flex">
            <h2>Formato SUNEDU</h2>
            <div style="padding-left:1em;">
                <a-tooltip>
                    <template slot="title">
                        <span>Aqui se listan las investigaciones en estado 'Ejecución' o 'Culminado'. Además esas investigaciones NO deben estar marcados como 'Externas'</span>
                    </template>
                    <a-icon type="question-circle" theme="twoTone" style="font-size: 20px; padding-top:.4em"/>
                </a-tooltip>
            </div>
        </div>

        <div style="display:flex; gap: 10px;padding:8px; background:#333;color:white">
            <div>
                <label>Tipo Persona:</label>
                <a-select  placeholder="Seleccione Tipo" v-model="filter.type" style="width:150px">
                    <a-select-option value="D"> Docente </a-select-option>
                    <a-select-option value="E"> Estudiante  </a-select-option>
                    <a-select-option value="A"> Administrativo  </a-select-option>
                </a-select>
            </div>
            <div>
                <label>Estado:</label>
                <a-select  placeholder="Seleccione Nivel" v-model="filter.research_state_id" style="width:150px">
                    <a-select-option value="3"> En ejecucion </a-select-option>
                    <a-select-option value="4"> Culminado </a-select-option>
                </a-select>
            </div>
            <div>
                <label>Fechas:</label>
                <a-date-picker v-model="filter.from" placeholder="Desde" style="width:120px" :format="DATEFORMAT"/>
                <a-date-picker v-model="filter.to" placeholder="Hasta" style="width:120px" :format="DATEFORMAT"/>
            </div>
            <a-button type="primary" icon="search" @click="fetch({format:'html'})"></a-button>
            <a-button type="primary" icon="file-excel" @click="fetch({format:'excel'})"></a-button>
        </div>
        <div v-if="report_url">
            <iframe id="" :src="report_url" style="width:100%; height:70vh; border:0">
            </iframe>
        </div>
    </div>
</template>
<script>
import moment from "moment";
// import Repository from "../../repositories/RepositoryFactory";
// import ResearchRepository from '../../repositories/ResearchRepository';
// const OutcomeRepository = Repository.get("research");
const default_report_url = '/api/research/sunedu' ;
import { DATEFORMAT } from "../../constants";

/*---------- */
export default {
    data() {
        return {
            DATEFORMAT,
            filter:{research_state_id:'4'+'',type:'D', from: (moment().format('YYYY'))+"-01-01", to: moment()},
            //pagination: {},
            loading: false,
            report_url:null,
        }
    },

    async mounted() {
      // this.fetch();
    },
    methods: {
        async fetch(params={}) {
            try {
                const format = params.format || 'html';
                if (!this.filter.from) throw "Debe seleccionar una fecha desde";
                if (!this.filter.to) throw "Debe seleccionar una fecha hasta";
                params = { ...this.filter };
                params.from = this.filter.from;
                params.to = this.filter.to;
                let from = moment(params.from).format('YYYY-M-D');
                let to = moment(params.to).format('YYYY-MM-DD');
                //this.report_url=default_report_url+'?type_research='+params.type_research+'&grade='+params.grade+'&author_id='+ params.author_id + '&from='+from+'&to='+to+'&r=' + Math.random();
                this.report_url = default_report_url+'?research_state_id='+params.research_state_id+'&type='+ params.type+'&from='+from+'&to='+to +'&format='+format+'&r='+Math.random();
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
        handleTableChange(pagination, filters, sorter) {
            const pager = { ...this.pagination };
            pager.current = pagination.current;
            this.pagination = pager;
            this.fetch({
                results: pagination.pageSize,
                page: pagination.current,
                sortField: sorter.field,
                sortOrder: sorter.order,
                ...filters,
            });
        },
    }
}
</script>
<style scoped>
</style>
