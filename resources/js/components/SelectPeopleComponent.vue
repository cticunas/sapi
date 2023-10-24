<template>
  <div>
     <a-select :size="this.size||'default'" show-search :value="person_identified" placeholder="Seleccione..."
    :default-active-first-option="false" :show-arrow="true" :filter-option="false" :not-found-content="null"
    @search="handleSearchPersons"
    @focus="handleFocus"
    @change="handleChangePersons" :disabled="disabled">
      <a-select-option v-for="item in cmb_persons" :key="item.value" :title="item.text">
        <a-avatar size="small" v-if="!item.photo" src="/images/photo1.png" />
        <a-avatar size="small" v-if="item.photo" :src="item.photo+'?'+Math.random()" />
      {{item.text}}
      </a-select-option>
      
    </a-select>
    <div v-if="person.id && format=='full_data'" class="person-data"></div>
    
  </div>
  
</template>
<script>
import Repository from "../repositories/RepositoryFactory";
import querystring from 'querystring';
import { log } from 'util';
const PersonRepository = Repository.get("person");

let timeout;
let currentValue;
let temporal_list;

function fetchPersons(value,type, callback) {
  if (timeout) {
    clearTimeout(timeout);
    timeout = null;
  }
  currentValue = value;

  async function fake() {
    // Observación, la busqueda de la persona se hace por id, puede funcionar mejor por dni
    const params = type=='byid'?{id:currentValue}:{search:currentValue};
      
    const {data} = await PersonRepository.list(params);
    if (currentValue === value) {
      const result = data.data;
      const tmp_data = [];
      temporal_list = result;
			result.forEach(item => {
			  tmp_data.push({value:item.id, text:`${item.name.toUpperCase()} ${item.lastname.toUpperCase()}`,photo:item.photo});
			});
			callback(tmp_data);
		}

  }
	timeout = setTimeout(fake, 300);
}



export default {
  props: ['person_id','format','size', 'disabled'], //si es un componente de persona. no se debe forzar a ser client id
  data() {
    return { person_identified: this.person_id, cmb_persons:[], person:{} };
  },
  async mounted() {
      this.person_identified = this.person_id;
      if(this.person_id) await this.handleSearchPersons(this.person_id,'byid');
       else await this.handleSearchPersons('','');
      //await this.handleSearchPersons(this.person_identified,this.person_identified);
    },
  methods: {
    async handleSearchPersons(value,type) { 
      await fetchPersons(value,type, data => (this.cmb_persons = data)); 
    },
		async handleChangePersons(value) {
      if(value) this.person_identified = value;
      await fetchPersons(value,'',data => (this.cmb_persons = data));
      //agregue un campo mas a la funcion fetchPersons de id para ubicar bien a la persona
      let data_result = await temporal_list.find(e => e.id == this.person_identified);
      this.$emit('handleSelectPeople',data_result);
    },
    handleFocus() {
      fetchPersons("","", data => (this.cmb_persons = data));
    },
  },
};
</script>
<style scoped>
.person-data{
		border: 1px solid #ccc;
		border-radius: 5px;
		padding:5px;
	}
	.person-data p{
		padding:0;
		margin:0;
		color:#888;
	}

.avatar-uploader > .ant-upload {
  width: 83%;
  height: 253px;
}
.ant-upload-select-picture-card i {
  font-size: 32px;
  color: #999;
}

.ant-upload-select-picture-card .ant-upload-text {
  margin-top: 8px;
  color: #666;
}

.ant-upload.ant-upload-select-picture-card {
  background: #ffffff;
}

.ant-upload.ant-upload-select-picture-card.ant-upload-disabled {
  border: none;
}
</style>
