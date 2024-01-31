<template>
    <div style="padding:10px 20px 10px 10px">
        <h2>Proyectos por Escuela</h2>
        <a-form-model style="display:flex; align-items:center; width:100%; gap: 15px;padding:8px; background:#333; color:white">
            <div style="display:block">
                <label title="Escuela">Esc.:</label>
                <div>
                    <a-select  placeholder="Escuela" v-model="filter.organization_id" style="width:350px" show-search option-filter-prop="children">
                    <a-select-option v-for="item in colleges" :key="item.id" :value="item.id" :title="item.name">
                        {{item.name}}</a-select-option>
                    </a-select>
                </div>
            </div>
            <div style="display:block">
                <label>Tipo:</label>
                <div>
                    <a-select  placeholder="Seleccione tipo" v-model="filter.type_research" style="width:110px">
                        <a-select-option :value="1"> Tesis</a-select-option>
                        <a-select-option :value="2">Inv. Docente</a-select-option>
                    </a-select>
                </div>
            </div>
            <div style="display:block"  v-if= "filter.type_research==1">
                <label>Grado:</label>
                <div>
                    <a-select  placeholder=" Nivel" v-model="filter.grade" style="width:100px">
                    <a-select-option :value="1"> Pregrado </a-select-option>
                    <a-select-option :value="2"> Posgrado  </a-select-option>
                    </a-select>
                </div>
            </div>
            <div style="text-align:center">
                <label>Fechas:</label>
                <div style="display:flex; justify-content:flex-end; gap: .2em">
                    <a-date-picker v-model="filter.from" placeholder="Desde" style="width:120px" :format="DATEFORMAT"/>
                    <a-date-picker v-model="filter.to" placeholder="Hasta" style="width:120px" :format="DATEFORMAT"/>
                </div>
            </div>
            <a-button title="Visualizar" type="primary" icon="search" @click="fetch({format:'pdf'})"></a-button>
            <a-button title="Exportar" type="primary" icon="file-word" @click="fetch({format:'excel'})"></a-button>

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
const OrganizationRepository = Repository.get("organization");
const default_report_url='/api/research/py_by_college' ;
import {DATEFORMAT} from "../../constants";

/*---------- */
export default {
    data(){
        return {
            DATEFORMAT,
            filter:{type_research:1,grade:1, from: (moment().format('YYYY'))+"-01-01", to: moment() },
            pagination: {},
            loading: false,
            report_url:null,
            colleges:[],
        }
    },
    async mounted() {
        this.listColleges();
    },
    methods: {
        async listColleges() {
            const { data } = await OrganizationRepository.list();
            this.colleges = [];
            data.map( e=>{
                e.children.map( e=> { this.colleges.push({id:e.id,name:e.name}) })
            });
        },
        async fetch(params = {}) {
            try{
                const format = params.format || 'pdf';
                if (!this.filter.organization_id) throw 'Seleccione una escuela';
                if (!this.filter.from) throw 'Seleccione fecha de inicio';
                if (!this.filter.to) throw 'Seleccione fecha de fin';
                params.type_research = this.filter.type_research;
                params.grade = this.filter.grade;
                params.organization_id = this.filter.organization_id;
                let from = moment(this.filter.from).format('YYYY-MM-DD');
                let to = moment(this.filter.to).format('YYYY-MM-DD');
                this.report_url = default_report_url+'?type_research='+params.type_research+'&organization_id='+ params.organization_id +'&from='+from+'&to='+to +"&format="+format+ '&r=' + Math.random()+(params.type_research==1?'&grade='+params.grade:'');
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
        filterOption(input, option) {
            return (
                option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
            );
        },
    }
}
</script>
<style scoped>
</style>
