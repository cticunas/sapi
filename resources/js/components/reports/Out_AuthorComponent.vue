<template>
<div style="padding:10px 20px 10px 10px">
    
      <h2>Ejemplares segun Investigador</h2>
      <a-form-model style="display:flex; width:100%; gap: 10px;padding:8px; background:#333;color:white">

        <div style="display:flex">
          <label>Investigador:</label>
          <select-people-component :person_id="filter.author_id" format="full_data" v-on:handleSelectPeople="changeSelectAuthor" style="width: 240px"/>
        </div>
        
        <a-button type="primary" icon="search" @click="fetch()">Visualizar</a-button>
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
const OutcomeRepository = Repository.get("research");
const default_report_url='/api/outcome/by_author' 

/*---------- */
export default {
    data(){
        return {
          filter:{},
          pagination: {},
          loading: false,
          report_url:null,
        }
    },

    async mounted() {

    },
    methods:{        
           async fetch(params = {}){
              try{
                params.author_id=this.filter.author_id;
                this.report_url=default_report_url+'?author_id='+ params.author_id + '&r=' + Math.random();
              }catch(error){
                this.error(error);
            }
        },

        changeSelectAuthor(person_data){
          this.filter.author_id=person_data.id;
          this.authorSelected = {...person_data, fullname:person_data.name + ' ' + person_data.lastname};
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