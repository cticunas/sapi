<template>
<div style="padding:10px 0">
  <div class="vld-parent">
    <loading :active.sync="isLoading" 
    :can-cancel="false" 
    :is-full-page="true"
    color="#1890ff"></loading>
  </div>
  
  <a-form-model ref="personForm" :model="person" :rules="rules" :label-col="labelCol" :wrapper-col="wrapperCol">
    <a-row>
      <a-col class="gutter-row" :span="12">
        <a-form-model-item label="DNI" prop="dni">
          <a-input v-model="person.dni" />
        </a-form-model-item>


        <a-form-model-item label="Nombre" prop="nombre">
          <a-tooltip>
            <template slot="title">
              Usar mayusculas al principio. <br> Ej: Juan Gabriel
            </template>
            <a-input v-model="person.name" />
          </a-tooltip>
        </a-form-model-item>

        <a-form-model-item label="Apellidos" prop="apellidos">
          <a-tooltip>
            <template slot="title">
              Usar mayusculas al principio. <br> Ej: Perez Salas
            </template>
            <a-input v-model="person.lastname" />
          </a-tooltip>
        </a-form-model-item>

        <a-form-model-item label="F. Nacimiento" prop="fecha">
            <a-date-picker v-model="person.birth" placeholder="Seleccione una fecha" :format="DATEFORMAT"/>
        </a-form-model-item>

        <a-form-model-item label="Género" prop="gender">
          <a-select v-model="person.sex" placeholder="Seleccione un genero">
            <a-select-option v-for="item in GENDERS" :value="item.id" :key="item.id">
              {{item.name}}
            </a-select-option>
          </a-select>
        </a-form-model-item>

        <a-form-model-item label="Direccion" prop="direccion">
          <a-input v-model="person.address" />
        </a-form-model-item>

        <a-form-model-item label="Telefono" prop="telefono">
          <a-input v-model="person.phone" />
        </a-form-model-item>

        <!-- <a-form-model-item label="ID RENACYT" prop="renacyt">
          <a-select v-model="person.renacyt_id" placeholder="Seleccione...">
            <a-select-option v-for="item in renacyt_levels" :value="item.id" :key="item.id" >
              {{item.name}}
            </a-select-option>
          </a-select>
        </a-form-model-item> -->

        <a-form-model-item label="ORCID" prop="orcid">
          <a-input v-model="person.orcid" placeholder="ej. https://orcid.org/xxxx-xxxx-xxxx-xxxx"/>
        </a-form-model-item>

        <a-form-model-item label="Scopus ID" prop="scopus_id">
          <a-input v-model="person.scopus_id" placeholder="ej. 12345678900"/>
        </a-form-model-item>

        <a-form-model-item label="Biografia" prop="biografia">
          <a-textarea v-model="person.biography" />
        </a-form-model-item>
      </a-col>

      <a-col class="gutter-row" :span="12">
        <a-form-model-item label="Email" prop="email">
          <a-input v-model="person.email" disabled/>
        </a-form-model-item>
        
        <a-form-model-item label="Facultad" prop="faculty">
          <a-select show-search option-filter-prop="children" v-model="person.faculty_id" placeholder="Seleccione una Facultad" @change="onChangeColleges()" :disabled="!allowEditFaculty">
            <a-select-option v-for="item in faculties" :value="item.id" :key="item.id" :title="item.name">
              {{item.name}}
            </a-select-option>
          </a-select>
        </a-form-model-item>

        <a-form-model-item label="Escuela" prop="organization">
          <a-select v-model="person.organization_id" placeholder="Seleccione una Escuela" :disabled="!allowEditCollege">
            <a-select-option v-for="item in colleges" :value="item.id" :key="item.id" :title="item.name">
              {{item.name}}
            </a-select-option>
          </a-select>
        </a-form-model-item>

        <a-form-model-item label="Grupo" prop="group">
          <a-select show-search option-filter-prop="children" v-model="person.group_id" placeholder="Seleccione un grupo" @change="onChangeGroup()" :disabled="!allowEditGroup">
            <a-select-option v-for="item in groups" :value="item.id" :key="item.id" >
              {{item.name}}
            </a-select-option>
          </a-select>
        </a-form-model-item>

      <a-form-model-item label="Linea de Invest." prop="line_research">
        <a-select show-search option-filter-prop="children" v-model="person.line_id" placeholder="Seleccione una linea">
          <a-select-option v-for="item in lines" :value="item.id" :key="item.id" >
            {{item.name}}
          </a-select-option>
        </a-select>
      </a-form-model-item>

        <a-form-model-item label="Imagen" prop="photo">
          <picture-component :photo="person.photo" v-on:changeImage="onChangePhoto" :key="pictureKey"/>
        </a-form-model-item>
      </a-col>
    </a-row>
    <a-row>
      <div class="btn_save"> <a-button type="primary" size="large" @click="save()">Guardar</a-button></div>
    </a-row>
  </a-form-model>
</div>
</template>
<script>
import Repository from "../repositories/RepositoryFactory";
const {ADMIN_ROLE,RESEARCH_ROLE,UNIT_ROLE,DGI_ROLE, COR_ROLE, DATEFORMAT, GENDERS} = require("../constants");
const PersonRepository = Repository.get("person");
const RenacytRepository = Repository.get("renacyt");
const OrganizationRepository = Repository.get("organization");
const CategoryRepository = Repository.get("category");
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';


/*---------- */
export default {
  data(){
    return {
      DATEFORMAT,
      GENDERS,
      isLoading: false,
      roles_allowed:[],
      pictureKey:0,
      roles:[],
      labelCol: { span: 7 },
      wrapperCol: { span: 14 },
      person:{ birth:null },
      rules:{ },
      organizations: [],
      groups_full :[],
      groups:[],
      lines:[],
      faculties:[],
      colleges:[],
      profile:[],
      user_logged: {},
      pagination: {},
      loading: false,
      renacyt_levels:[],
      ADMIN_ROLE, RESEARCH_ROLE, UNIT_ROLE, DGI_ROLE, COR_ROLE,
      allowEditFaculty:false,
      allowEditCollege:false,
      allowEditGroup:false,
    }
  },
  components: {
    Loading
  },
  async mounted() {
    // this.listRenacytLevels();
    this.user_logged = JSON.parse(localStorage.getItem("user"));
    this.isLoading=true;

    await this.listgroupsFulls();
    await this.fetch();
    await this.listCollege();
    this.pictureKey++;
    for(let i=0;i<this.faculties.length;i++){
      for(let j=0; j<this.faculties[i].children.length;j++){
        if(this.person.organization_id == this.faculties[i].children[j].id){
          this.person.faculty_id = this.faculties[i].id;
        }
      }
    }

    if (this.person.faculty_id == null) this.allowEditFaculty = true;
    if (this.person.organization_id == null) this.allowEditCollege = true;
    if (this.person.group_id == null) this.allowEditGroup = true;

    const p = {...this.person};
    if(this.person.faculty_id) {
      this.onChangeColleges();
      this.person.group_id = p.group_id;
      this.onChangeGroup();
      this.person.line_id = p.line_id;
    }else delete this.person.organization_id;
    this.isLoading=false;
  },
  methods:{
    /* async listRenacytLevels(){
      try {
        const {data}= await RenacytRepository.list({});
        this.renacyt_levels=data.data;
      } catch (error) {
        this.error(error);
      }
    }, */
    async listgroupsFulls(){
      try {
        let {data} =  await CategoryRepository.list({});
        this.groups_full=data;
      } catch (error) { this.error(error); }  
    },
    async fetch(){
      try{
        this.loading = true;
        const person_id = this.user_logged.people_id;
        const {data} =  await PersonRepository.get(person_id);
        this.person = data;
        this.loading = false;
      }catch(error){
        this.error(error);
      }
    },
    async listCollege(){
      const{data} = await OrganizationRepository.list();
      this.faculties = data;
    },
    onChangeColleges(){
      this.colleges = this.faculties.find(e=>e.id == this.person.faculty_id).children.map(e=>({id:e.id,name:e.name}));
      this.person.organization_id = this.colleges[0].id;
      const fact = this.groups_full.find(e=>e.id == this.person.faculty_id);
      this.groups = fact?fact.children : this.groups_full[0].children;
      this.person.group_id = this.groups[0].id;
      this.onChangeGroup();
    },
    onChangeGroup(){
      try {
        if (!this.groups) throw ("No existen grupos");
        const group =this.groups.find(e=>e.id==this.person.group_id); 
        this.lines = group?group.children:this.groups[0].children;
        this.person.line_id = this.lines[0].id;
      } catch (error) {
        this.error(error)
      }
    },
    error (message) {this.$message.error(message||'Error al procesar');},
    success (message) {  this.$message.success(message||'Proceso Correcto');},
    async save(){
      try {
        if (!this.person.name) throw ("Nombre es obligatorio");
        if (!this.person.lastname) throw ("Apellido es obligatorio");
        if (!this.person.organization_id) throw ("Escuela es obligatoria");
        if (!this.person.group_id) throw ("Grupo es obligatorio"); 

        this.loading=true;
        let payload = this.person;
        this.user_logged.faculty_id = payload.faculty_id;
        this.user_logged.college_id = payload.organization_id;
        this.user_logged.group_id = payload.group_id;
        this.user_logged.line_id = payload.line_id;
        await PersonRepository.save(payload);
        localStorage.setItem("user",JSON.stringify(this.user_logged));
        this.success();
        this.loading=false;
      } catch (error) {
        this.error(error);
      }
    },
    filterOption(input, option) {
      return ( option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0 );
    },
    onChangePhoto(data){
      this.person.photo = data;
      this.pictureKey++;
    },
  }
}
</script>
<style scoped>
.checkbox{
  padding-left: .5em;
}
.btn_save{
  display: flex;
  justify-content: flex-end;
  padding-right: 4em;
}
</style>