<template>
<div style="padding:10px 20px 10px 10px">
      <div style="display:flex">
        <h2>Informe de Unidad</h2>
        <div style="padding-left:1em;">
          <a-tooltip>
            <template slot="title">
              <span>Asegurese que las investigaciones estén con el check de incentivo y tengan entregables aprobados en el periodo seleccionado, además, Docente y Administrativos estén en condición de Nombrado. <br>
              Los alumnos de iniciación cientifica, deben contar con el check de incentivo.</span>
            </template>
            <a-icon type="question-circle" theme="twoTone" style="font-size: 20px; padding-top:.4em"/>
          </a-tooltip>
        </div>
      </div>

      <div style="display:flex; gap: 5px;padding:5px; background:#333;color:white">
        <div>
          <label>Facultad:</label>
          <a-select show-search option-filter-prop="children" placeholder="Facultad" v-model="filter.organization_id" style="width:300px" :disabled="user_logged.role_id == UNIT_ROLE">
            <a-select-option v-for="item in faculties" :key="item.id" :value="item.id" :title="item.name"> 
              {{item.name}}</a-select-option>
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
        <div style="display:flex; gap: .2em">
          <label>Periodo:</label>
          <div style="">
            <a-select  placeholder="T periodo" v-model="filter.period_type" style="width:100px" @change="onChangeTPeriod">
              <a-select-option v-for="item in PERIOD_TYPES" :key="item.id" :value="item.id">{{item.name}}</a-select-option>
            </a-select> 
            
            <a-select  placeholder="Periodo" v-model="filter.period" style="width:80px">
              <a-select-option v-for="item in getPeriods(filter.period_type)" :key="item.id" :value="item.id">{{item.name}}</a-select-option>
            </a-select>
          </div>
        </div>
        <a-button type="primary" icon="search" @click="fetch({format:'pdf'})" title="Visualizar"></a-button>
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
import  {PERIOD_TYPES, UNIT_ROLE } from "../../constants";
const OrganizationRepository = Repository.get("organization");
const OutcomeRepository = Repository.get("outcome");
const default_report_url='/api/outcome/r_for_unit' 

/*---------- */
export default {
    data(){
        return {
          filter:{ },
          pagination: {},
          loading: false,
          report_url:null,
          faculties:[],
          PERIOD_TYPES,
          UNIT_ROLE,
          user_logged:{}
        }
    },

    async mounted() {
       this.user_logged = JSON.parse(localStorage.getItem("user"));
      const period_type = OutcomeRepository.getPeriodType();
      const organization_id = this.user_logged.role_id == UNIT_ROLE ? this.user_logged.faculty_id:'';
      this.filter = {organization_id:organization_id, year:moment().format('YYYY'), period_type:period_type.id, period: this.suggestPeriod(period_type) };
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
      this.faculties = data;
    },
           async fetch(params = {}){
              try{
                if (!this.filter.organization_id) throw 'Seleccione una facultad';
                const format = params.format||'pdf';
               params={...this.filter};
                this.report_url=default_report_url+'?year='+params.year+'&period_type='+params.period_type+'&period='+params.period+'&faculty_id='+ params.organization_id +'&format='+format+'&r='+Math.random();
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