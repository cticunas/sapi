<template>
<div style="padding:10px 20px 10px 10px">
    
      <h2>Ejemplares en Revista</h2>
      <a-form-model style="display:flex; width:100%; gap: 10px;padding:8px; background:#333;color:white">
       
        <div style="display:flex">
          <label>Escuela:</label>
          <a-select  placeholder="Escuela" v-model="filter.organization_id" style="width:300px">
            <a-select-option v-for="item in colleges" :key="item.id" :value="item.id"> 
              {{item.name}}</a-select-option>
          </a-select> 
        </div>
        
        <a-button type="primary" icon="search" @click="fetch()">Visualizar</a-button>
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
const default_report_url='/api/outcome/in_journal' 

/*---------- */
export default {
    data(){
        return {
          filter:{ },
          pagination: {},
          loading: false,
          report_url:null,
          colleges:[],
        }
    },

    async mounted() {
      this.listColleges();
    },
    methods:{   
          
      async listColleges(){
      const{data} = await OrganizationRepository.list();
      this.colleges = [];
       data.map( e=>{ 
          e.children.map( e=> { this.colleges.push({id:e.id,name:e.name}) }) 
      });
    },
           async fetch(params = {}){
              try{
                params.organization_id=this.filter.organization_id;
                this.report_url=default_report_url+'?organization_id='+ params.organization_id + '&r=' + Math.random();
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