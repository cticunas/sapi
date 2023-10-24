<template>
<div style="padding:10px 20px 10px 10px">
  <div style="display:flex">
    <h2>Ejemplares</h2>
      <a-radio-group v-model="value" @change="onChange" style="margin-left:2em; margin-top:.4em">
        <div style="display:flex">
          <div style="border:1px solid #ddd; background-color:#eee; padding:0 .6em; border-radius:4px; margin-right:1em">
            <a-radio :style="radioStyle" value="xCollege"> x Escuela</a-radio>
          </div>
          <div style="border:1px solid #ddd; background-color:#eee; padding:0 .6em; border-radius:4px">
            <a-radio :style="radioStyle" value="xAuthor"> x Investigador</a-radio>
          </div>
        </div>
      </a-radio-group>
  </div>
      <a-form-model style="display:flex; width:100%; gap: 10px;padding:8px; background:#333;color:white">
       
        <div style="display:flex" v-if="value=='xCollege'" >
          <label>Esc.: </label>
          <a-select  placeholder="Escuela" v-model="filter.organization_id" style="width:350px" show-search option-filter-prop="children">
            <a-select-option v-for="item in colleges" :key="item.id" :value="item.id" :title="item.name"> 
              {{item.name}}</a-select-option>
          </a-select>
        </div>
         <div style="display:flex" v-if="value=='xAuthor'">
          <label>Investigador:</label>
          <select-people-component  style="width: 350px" :person_id="null" format="full_data" v-on:handleSelectPeople="changeSelectAuthor"/>
        </div>
        <div style="display:flex">
          <label>Index.:</label>
          <a-select  placeholder="Base de Datos" v-model="filter.indexed" style="width:120px" :allowClear="true">
            <a-select-option v-for="item in INDEXED_BDS" :key="item.id" :value="item.id"> 
              {{item.name}}</a-select-option>
          </a-select> 
        </div>
        <div>
          <label>Fecha:</label>
          <a-date-picker v-model="filter.from" placeholder="Desde" style="width:120px" :format="DATEFORMAT"/>
          <a-date-picker v-model="filter.to" placeholder="Hasta" style="width:120px" :format="DATEFORMAT"/>
        </div>
        <a-button type="primary" icon="search" @click="fetch({format:'pdf'})"></a-button>
        <a-button type="primary" icon="file-word" @click="fetch({format:'word'})"></a-button>
      </a-form-model>
     
  <div v-if="report_url">
    <iframe :src="report_url" style="width:100%; height:70vh; border:0"> </iframe>
  </div>
</div>    
</template>
<script>
import moment from "moment";
import Repository from "../../repositories/RepositoryFactory";
const OrganizationRepository = Repository.get("organization");
const {INDEXED_BDS, DATEFORMAT} = require("../../constants");

const default_report_url='/api/outcome/by' 
/*---------- */
export default {
    data(){
        return {
          DATEFORMAT,
          value: 'xCollege',
          filter:{from: (moment().format('YYYY'))+"-01-01", to: moment() },
          pagination: {},
          loading: false,
          report_url:null,
          colleges:[],
          INDEXED_BDS,
          radioStyle: {
              display: 'inline-block', height: '30px',lineHeight: '30px',
            },
        }
    },

    async mounted() {
      this.listColleges();
    },
    methods:{   
      onChange(e) {
    },
      async listColleges(){
      const{data} = await OrganizationRepository.list();
      this.colleges = [];
       data.map( e=>{ 
          e.children.map( e=> { this.colleges.push({id:e.id,name:e.name}) }) 
      });
    },
           async fetch(params = {}){
              try{
                const format = params.format||'pdf';
                if (!this.filter.indexed) throw "Seleccione una base de datos";
                if (!this.filter.from) throw "Seleccione fecha de inicio";
                if (!this.filter.to) throw "Seleccione fecha de fin";
                console.log(this.filter);
                 let from = moment(this.filter.from).format('YYYY-MM-DD');
                let to = moment(this.filter.to).format('YYYY-MM-DD');
                let url = default_report_url + '?from=' + from + '&to=' + to+'&format='+format;
                if( this.value=='xCollege' ) url+='&organization_id='+this.filter.organization_id; 
                else if(this.value=='xAuthor') url += '&author_id='+ this.filter.author_id+"&by_author=true";
                if( this.filter.indexed ) url+='&indexed='+ this.filter.indexed;

                url+= '&r=' + Math.random();
                this.report_url = url;
              }catch(error){
                this.error(error);
            }
        },
         changeSelectAuthor(person_data){
            this.filter.author_id = person_data.id;
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