<template>
    <div style="padding:10px 20px 10px 10px">
        <div style="display:flex">
            <h2>Investigaciones segun Investigador</h2>
            <div style="padding-left:1em;">
                <a-tooltip>
                    <template slot="title">
                        <span>Aqui se listan las investigaciones en estado 'Ejecución' o 'Culminado'. Además esas investigaciones NO deben estar marcados como 'Externas'</span>
                    </template>
                    <a-icon type="question-circle" theme="twoTone" style="font-size: 20px; padding-top:.4em"/>
                </a-tooltip>
            </div>
        </div>

        <a-form-model style="display:flex; width:100%; gap: 10px;padding:8px; background:#333;color:white; flex-wrap: wrap;">
            <div style="display:flex; align-items:center">
                <label style='margin: 0; padding-right: 5px;'>Inv.:</label>
                <select-people-component :person_id="filter.author_id" format="full_data" v-on:handleSelectPeople="changeSelectAuthor" style="width: 250px"/>
            </div>
            <div>
                <label style='margin: 0; padding-right: 5px;'>Tipo:</label>
                <a-select  v-model="filter.type_research" style="width:95px">
                    <a-select-option :value="1">Tesis</a-select-option>
                    <a-select-option :value="2">I. Docente</a-select-option>
                    <a-select-option :value="3">Experiencia</a-select-option>
                </a-select>
            </div>
            <div v-if="filter.type_research==1">
                <label style='margin: 0; padding-right: 5px;'>Nivel:</label>
                <a-select  v-model="filter.grade" style="width:100px">
                    <a-select-option :value="1"> Pregrado</a-select-option>
                    <a-select-option :value="2"> Posgrado </a-select-option>
                </a-select>
            </div>
            <div>
                <label style='margin: 0; padding-right: 5px;'>Fechas:</label>
                <a-date-picker v-model="filter.from" placeholder="Desde" style="width:120px" :format="DATEFORMAT"/>
                <a-date-picker v-model="filter.to" placeholder="Hasta" style="width:120px" :format="DATEFORMAT"/>
            </div>
            <a-button type="primary" icon="search" @click="fetch({format:'pdf'})" title="Buscar"></a-button>
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
// const OutcomeRepository = Repository.get("research");
const default_report_url='/api/research/py_by_author';
import {DATEFORMAT} from "../../constants"

/*---------- */
export default {
    data() {
        return {
            DATEFORMAT,
            filter:{grade:1,type_research:1,author_id:'', from: (moment().format('YYYY'))+"-01-01", to: moment() },
            pagination: {},
            loading: false,
            report_url:null,
        }
    },

    async mounted() {
    },
    methods: {
        async fetch(params = {}) {
            try {
                const format = params.format||'pdf';
                if (!this.filter.author_id) throw "Debe seleccionar un autor";
                if (!this.filter.from) throw "Debe seleccionar una fecha desde";
                if (!this.filter.to) throw "Debe seleccionar una fecha hasta";
                params = { ...this.filter };
                if (params.type_research == 2) params.grade = '';
                let from = moment(params.from).format('YYYY-MM-DD');
                let to = moment(params.to).format('YYYY-MM-DD');
                this.report_url = default_report_url+'?type_research='+params.type_research+(params.grade>0?'&grade='+params.grade : '')+'&author_id='+ params.author_id + '&from='+from+'&to='+to+'&format='+format+'&r=' + Math.random();
            }catch(error) {
                this.error(error);
            }
        },
        changeSelectAuthor(person_data) {
            this.filter.author_id=person_data.id;
            this.authorSelected = {...person_data, fullname:person_data.name + ' ' + person_data.lastname};
		},
        error (message) {
            this.$message.error(message||'Error al procesar');
        },
        success (message) {
            this.$message.success(message||'Proceso Correcto');
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
