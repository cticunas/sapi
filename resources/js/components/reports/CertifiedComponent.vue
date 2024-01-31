<template>
    <div style="padding:10px 20px 10px 10px">
        <div style="display:flex">
            <h2>CERTIFICADO DE TRABAJOS DE INVESTIGACION</h2>
            <div style="padding-left:1em;">
                <a-tooltip>
                    <template slot="title">
                        <span>Aqui se listan las investigaciones en estado 'Culminado'. Además esas investigaciones NO deben estar marcados como 'Externas'</span>
                    </template>
                    <a-icon type="question-circle" theme="twoTone" style="font-size: 20px; padding-top:.4em"/>
                </a-tooltip>
            </div>
        </div>

        <a-form-model style="display:flex; width:100%; gap: 10px;padding:8px; background:#333;color:white; align-items:center; flex-wrap: wrap;">
            <div style="display:flex">
                <label style="margin: 0; padding-right: 5px;">Investigador: </label>
                <select-people-component :person_id="filter.author_id" format="full_data" v-on:handleSelectPeople="changeSelectAuthor" style="width: 240px"/>
            </div>
            <div style="display:flex">
                <label style="margin: 0; padding-right: 5px;">Tipo: </label>
                <a-select  placeholder="Seleccione tipo" v-model="filter.type_research">
                    <a-select-option :value="1">Tesis</a-select-option>
                    <a-select-option :value="2">Inv. Docente</a-select-option>
                </a-select>
            </div>
            <div style="display:flex" v-if="filter.type_research == 1">
                <label style="margin: 0; padding-right: 5px;">Grado: </label>
                <a-select v-model="filter.grade" placeholder="Seleccione grado" style="width:125px">
                    <a-select-option :value="1">PREGRADO</a-select-option>
                    <a-select-option :value="2">POSGRADO</a-select-option>
                </a-select>
            </div>
            <div style="display:flex">
                <label style="display:flex; align-items:center; margin: 0; padding-right: 5px;">Desde:</label>
                <div>
                    <a-date-picker v-model="filter.from" placeholder="Desde" style="width:120px;" :format="DATEFORMAT"/>
                    <a-date-picker v-model="filter.to" placeholder="Hasta" style="width:120px;" :format="DATEFORMAT"/>
                </div>
            </div>
            <a-button type="primary" icon="search" @click="fetch({format:'pdf'})" title="Visualizar"></a-button>
            <a-button type="primary" icon="file-word" @click="fetch({format:'word'})" title="Exportar"></a-button>
        </a-form-model>
        <div v-if="report_url">
            <iframe id="" :src="report_url" style="width:100%; height:70vh; border:0">
            </iframe>
        </div>
    </div>
</template>
<script>
import moment from "moment";
import Repository from "../../repositories/RepositoryFactory";
// import ResearchRepository from '../../repositories/ResearchRepository';
const OutcomeRepository = Repository.get("research");
const default_report_url='/api/research/certified' ;
import {DATEFORMAT} from "../../constants";

/*---------- */
export default {
    data() {
        return {
            DATEFORMAT,
            filter:{author_id:'', type_research:2, from: (moment().format('YYYY'))+"-01-01", to: moment() },
            //pagination: {},
            loading: false,
            report_url:null,
        }
    },
    async mounted() {

    },
    methods: {
        async fetch(params={}) {
            try {
                if(!this.filter.author_id) throw("Seleccione un investigador");
                if(!this.filter.from) throw ("Seleccione fecha de inicio");
                if(!this.filter.to) throw ("Seleccione fecha de fin");
                if(this.filter.type_research ==1 && !this.filter.grade) throw ("Seleccione grado");
                let format = params.format;
                params = { ...this.filter }
                if(params.type_research == 2) params.grade = '';
                let from = moment(params.from).format('YYYY-M-D');
                let to = moment(params.to).format('YYYY-MM-DD');
                this.report_url = default_report_url+'?author_id='+params.author_id+'&type_research='+params.type_research+'&format='+format+(params.grade>0?'&grade='+params.grade : '')+'&from='+from+'&to='+to +'&r='+Math.random();
            } catch(error) {
                this.error(error);
            }
        },
        async word(params = {}) {
            try {
                params.author_id=this.filter.author_id;
                this.report_url=default_report_url+'?author_id='+params.author_id+'&type_research='+params.type_research+'&date_init='+params.date_init+'&date_end='+params.date_end +'&format=word'+'&r='+Math.random();
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

        changeSelectAuthor(person_data){
            this.filter.author_id=person_data.id;
		},
        handleTableChange(pagination, filters, sorter){
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
