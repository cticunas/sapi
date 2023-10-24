<template>
<div style="padding:10px 20px 10px 10px">
    <div style="display:flex">
      <h2>Investigaciones por Periodo</h2>
      <div style="padding-left:1em;">
        <a-tooltip>
          <template slot="title">
            <span>Las investigaciones de este reporte se basan en la fecha que cambió de estado según el estado cambiado</span>
          </template>
          <a-icon type="question-circle" theme="twoTone" style="font-size: 20px; padding-top:.4em"/>
        </a-tooltip>
      </div>
    </div>

      <div style="display:flex; gap: 5px;padding:5px; background:#333;color:white">
        <div>
          <label>Escuela: </label>
          <a-select show-search option-filter-prop="children" placeholder="Escuela" v-model="filter.organization_id" style="width:300px">
            <a-select-option v-for="item in colleges" :key="item.id" :value="item.id" :title="item.name">{{item.name}}</a-select-option>
          </a-select>
        </div>
        <div>
          <label>Tipo:</label>
          <a-select v-model="filter.type_research" style="width:110px">
            <a-select-option :value="1">Tesis</a-select-option>
            <a-select-option :value="2">Inv. Docente</a-select-option>
            <a-select-option :value="3">Experiencia</a-select-option>
            <a-select-option :value="4">Innovación</a-select-option>
          </a-select>
        </div>
        <div v-if= "filter.type_research==1">
          <label>Grado:</label>
          <a-select  placeholder="Seleccione Nivel" v-model="filter.grade" style="width:100px">
            <a-select-option :value="1"> Pregrado </a-select-option>
            <a-select-option :value="2"> Posgrado  </a-select-option>
          </a-select>
        </div>
        <div>
          <label>Estado:</label>
          <a-select  placeholder="Seleccione Estado" v-model="filter.research_state_id" style="width:100px">
            <a-select-option :value="2"> Nuevo </a-select-option>
            <a-select-option :value="3"> En ejecucion  </a-select-option>
            <a-select-option :value="4"> Culminado </a-select-option>
            <a-select-option :value="5"> Suspendido </a-select-option>
            <a-select-option :value="6"> Anulado</a-select-option>
          </a-select>
        </div>
        <div>
          <label>Año:</label>
            <a-select  v-model="filter.year" style="width:80px">
                <a-select-option :value="(new Date()).getFullYear()">{{(new Date()).getFullYear()}}</a-select-option>
                <a-select-option :value="(new Date()).getFullYear()-1">{{(new Date()).getFullYear()-1}}</a-select-option>
                <a-select-option :value="(new Date()).getFullYear()-2">{{(new Date()).getFullYear()-2}}</a-select-option>
                <a-select-option :value="(new Date()).getFullYear()-3">{{(new Date()).getFullYear()-3}}</a-select-option>
            </a-select>
        </div>
        <div style="text-align: center">
          <label>Periodo:</label>
          <div style="display:flex; justify-content:flex-end; gap: .2em">
            <a-select  placeholder="T periodo" v-model="filter.period_type" style="width:100px" @change="onChangeTPeriod">
              <a-select-option v-for="item in PERIOD_TYPES" :key="item.id" :value="item.id">{{item.name}}</a-select-option>
            </a-select> 
            
            <a-select  placeholder="Periodo" v-model="filter.period" style="width:80px">
              <a-select-option v-for="item in getPeriods(filter.period_type)" :key="item.id" :value="item.id">{{item.name}}</a-select-option>
            </a-select>
          </div>
        </div>
        <a-button type="primary" icon="search" @click="fetch({format:'pdf'})" title="Buscar"></a-button>
        <a-button type="primary" icon="file-word" @click="fetch({format:'word'})" title="Exportar"></a-button>
      </div>
  <div v-if="report_url">
    <iframe :src="report_url" style="width:100%; height:70vh; border:0">
    </iframe>
  </div>
</div>    

</template>
<script>
import moment from "moment";
import Repository from "../../repositories/RepositoryFactory";
const OutcomeRepository = Repository.get("outcome");
const OrganizationRepository = Repository.get("organization");
const {PERIOD_TYPES} = require("../../constants");//roles
const default_report_url='/api/research/py_by_period' 

/*---------- */
export default {
    data(){
        return {
          filter:{},
          pagination: {},
          incentive_list:[],
          colleges:[],
          loading: false,
          report_url:null,
          PERIOD_TYPES
        }
    },

    async mounted() {
     const period_type = OutcomeRepository.getPeriodType();
     this.filter = {type_research:1, year:moment().format('YYYY'),research_state_id:3,grade:1, period_type:period_type.id, period: this.suggestPeriod(period_type) };
          // this.fetch();
     this.listColleges();
    },

    methods:{      
              suggestPeriod(period_type ){
              return OutcomeRepository.suggestPeriod(period_type);
            },
              onChangeTPeriod(e){
                  this.filter.period = this.suggestPeriod({id:this.filter.period_type});
              },
            getPeriods(period_type){
             if(period_type)  return OutcomeRepository.getPeriods(period_type);
            },  
            async listColleges(){
              const{data} = await OrganizationRepository.list();
              this.colleges = [];
              data.map( e=>{  e.children.map( e=> { this.colleges.push({id:e.id,name:e.name}) })  });
            },
           async fetch(params = {}){
              try{
                if (!this.filter.organization_id) throw "Seleccione una escuela";
                const format = params.format||'pdf';
                params={...this.filter};
                this.report_url=default_report_url+'?type_research='+params.type_research+'&year='+ params.year+'&period_type='+params.period_type+'&period='+params.period+'&research_state_id='+ params.research_state_id+'&organization_id='+ params.organization_id+"&format="+format+"&r="+Math.random()+(params.type_research==1?'&grade='+params.grade:'');
              }catch(error){
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