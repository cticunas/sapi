<template>
<div style="padding:10px 20px 10px 10px">
    <div style="display:flex">
      <h2>Acreedores a Incentivo</h2>
      <div style="padding-left:1em;">
      <a-tooltip>
        <template slot="title">
          <span>Las investigaciones deben tener un entregable aprobado en el periodo seleccionado, y deben tener el check de incentivo activo. <br> Además, la investigaciones debe estar en estado ejecución o culminado.</span>
        </template>
        <a-icon type="question-circle" theme="twoTone" style="font-size: 20px; padding-top:.4em"/>
      </a-tooltip>
    </div>
    </div>
      <div style="display:flex; gap: 15px;padding:5px; background:#333;color:white">
        <div>
          <label>Tipo:</label>
          <a-select v-model="filter.incentive_type" style="width:100px">
              <a-select-option value="FEDU">FEDU</a-select-option>
              <a-select-option value="IC">Inic. Cientifica</a-select-option>
          </a-select>
        </div>
        <div>
          <label>Año:</label>
            <a-select   v-model="filter.year" style="width:80px">
                <a-select-option :value="(new Date()).getFullYear()">{{(new Date()).getFullYear()}}</a-select-option>
                <a-select-option :value="(new Date()).getFullYear()-1">{{(new Date()).getFullYear()-1}}</a-select-option>
                <a-select-option :value="(new Date()).getFullYear()-2">{{(new Date()).getFullYear()-2}}</a-select-option>
                <a-select-option :value="(new Date()).getFullYear()-3">{{(new Date()).getFullYear()-3}}</a-select-option>
            </a-select>
        </div>
        <div>
          <label>Periodo:</label>
          <a-select  placeholder="T periodo" v-model="filter.period_type" style="width:100px" @change="onChangeTPeriod">
                            <a-select-option v-for="item in PERIOD_TYPES" :key="item.id" :value="item.id">{{item.name}}</a-select-option>
                         </a-select> 
                         -
                         <a-select  placeholder="Periodo" v-model="filter.period" style="width:80px">
                            <a-select-option v-for="item in getPeriods(filter.period_type)" :key="item.id" :value="item.id">{{item.name}}</a-select-option>
                         </a-select> 
        </div>
        <a-button type="primary" icon="search" @click="fetch({format:'pdf'})" title="Buscar"></a-button>
        <a-button type="primary" icon="file-word" @click="fetch({format:'word'})" title="Exportar"></a-button>
      </div>
  <div v-if="report_url">
    <iframe id="" :src="report_url" style="width:100%; height:70vh; border:0">
    </iframe>
  </div>
</div>    

</template>
<script>
import moment from "moment";
import Repository from "../../repositories/RepositoryFactory";
const OutcomeRepository = Repository.get("outcome");
const {PERIOD_TYPES, PERIOD_DEFAULT} = require("../../constants");//roles
const default_report_url='/api/outcome/incentives' 

/*---------- */
export default {
    data(){
        return {
          filter:{},
          pagination: {},
          incentive_list:[],
          loading: false,
          report_url:null,
          PERIOD_TYPES
        }
    },

    async mounted() {
      const period_type = OutcomeRepository.getPeriodType();
     this.filter = {incentive_type:'FEDU', year:moment().format('YYYY'), period_type:period_type.id, period: this.suggestPeriod(period_type) };
    },
    methods:{     
      suggestPeriod(period_type ){
        return OutcomeRepository.suggestPeriod(period_type);
      },

      onChangeTPeriod(e){
          this.filter.period = this.suggestPeriod(OutcomeRepository.getPeriodType(this.filter.period_type));
      },
      
      getPeriods(period_type){
        if(period_type)  return OutcomeRepository.getPeriods(period_type);
    },
          async fetch(params = {}){
              try{
                const format = params.format||'pdf';
                this.report_url=default_report_url+'?incentive_type='+this.filter.incentive_type+'&year='+ this.filter.year+'&period_type='+this.filter.period_type+"&period="+this.filter.period+'&format='+format+"&r="+Math.random();
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
        
  }
}
</script>
<style scoped>
</style>