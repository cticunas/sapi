<template>
<div style="padding:10px 20px 10px 10px">
  <div class="vld-parent">
    <loading :active.sync="isLoading" 
    :can-cancel="false" 
    :is-full-page="true"
    color="#1890ff"></loading>
  </div>

  <h4>Planes <a-button type="primary" size="small" @click="add()" title="Nuevo">Nuevo</a-button></h4> 

  <a-table :columns="columns" :row-key="record => record.id"
  :data-source="dataPlan"
  :pagination="pagination"
  :loading="loading"
  @change="handleTableChange">
  <span slot="active" slot-scope="text, record">
    {{record.active == true ? 'Activo':'Inactivo'}}
  </span>
    <span slot="action" slot-scope="text,record">
      <a href="javascript:;" @click="edit(record.id)" title="Editar"><a-icon type="edit" /></a>
      <a-divider type="vertical" />
      <a-popconfirm v-if="dataPlan.length" title="Seguro?" @confirm="() => remove(record.id)" ok-text="Si" cancel-text="No">
        <a href="javascript:;" title="Eliminar"><a-icon type="delete" /></a>
      </a-popconfirm>
      <a-divider type="vertical" />
      <a href="javascript:;" @click="showCategories(record.id)" title="Ver Programas"><a-icon type="unordered-list" /></a>
    </span>
  </a-table>

  <a-modal title="Nuevo plan"  :visible="showModal" cancelText="Cancelar" okText="Guardar"
  @ok="save" 
  @cancel="showModal = false">

    <a-form-model ref="userForm" :model="plan" :label-col="labelCol" :wrapper-col="wrapperCol">
      <a-row>
        <a-col class="gutter-row" :span="24">
          <a-form-model-item label="Codigo" prop="code">
            <a-input v-model="plan.code" />
          </a-form-model-item>

          <a-form-model-item label="Nombre" prop="name">
            <a-input v-model="plan.name" />
          </a-form-model-item>

          <a-form-model-item label="Resolución" prop="resolution">
            <a-input v-model="plan.resolution" />
          </a-form-model-item>

          <a-form-model-item label="Inicio" prop="init">
            <a-input v-model="plan.init" />
          </a-form-model-item>

          <a-form-model-item label="Fin" prop="end">
            <a-input v-model="plan.end" />
          </a-form-model-item>

          <a-form-model-item label="Activo" prop="active">
            <input type="checkbox" v-model="plan.active" @change="onChangeActive" class="checkbox"/>
          </a-form-model-item>
        </a-col> 
      </a-row>
    </a-form-model>
  </a-modal>

  <a-modal :title="'Lineas del plan: '+plan.name"  :visible="showCategoriesModal" cancelText="Cancelar" width="60%" okText="Guardar"
  @ok="saveActiveCategories" 
  @cancel="showCategoriesModal = false">
    <ul v-for="(faculty, index) in programs" :key="faculty.id" style="list-style-type: none; padding:0">
      <li style="margin-bottom:1.5em">
        <a href="javascript:void(0)" class="anchor_faculty" @click="toggleProgram(faculty,index)">
          <a-icon :type="faculty.showlines?'caret-down':'caret-right'" class="anchor_faculty icon"/>
          {{faculty.name}}
        </a>
        <div v-show="faculty.showlines" :key="showlines" class="container_list_programs">
          <ul v-for="group in faculty.children" :key="group.id">
            <li :title="group.id">
              {{group.name}}
                  <ul v-for="line in group.children" :key="line.id" class="container_list_lines list_style">
                    <li style="display:flex; align-items:center" class="item_hover" :title="line.id">
                      <input type="checkbox" class="checkbox" v-model="line.active"> 
                      {{line.name}}
                    </li>
                  </ul>
                
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </a-modal>
</div>
</template>
<script>
import moment from "moment";
import Repository from "../repositories/RepositoryFactory";
const PlanRepository = Repository.get("plan");
const CategoryRepository = Repository.get("category");
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

/* ------------ */
const columns = [
  {title: 'Codigo',dataIndex: 'code',width: '60px'},
  {title: 'Plan',dataIndex: 'name', width: '40%'},
  {title: 'Inicio',dataIndex: 'init',width: '60px'},
  {title: 'Fin',dataIndex: 'end',width: '60px'},
  {title: 'Activo',key: 'active',scopedSlots: { customRender: "active" },width: '80px'},
  {title:'-',key:'action',scopedSlots: { customRender: "action" },width: '100px' }
];
/*---------- */
export default {
  data(){
    return {
      isLoading: false,
      labelCol: { span: 7 },
      wrapperCol: { span: 14 },
      plan: { },
      dataPlan:[],
      programs:[],
      showlines:0,
      pagination: {},
      loading: false,
      columns,
      showModal:false,
      showCategoriesModal:false,
    }
  },
  components: {
    Loading
  },
  async mounted() {
    this.isLoading=true;
    await this.fetch();
    await this.listCategories();
    this.isLoading=false;
  },
  methods:{
    onChangeActive(e){
      return e.target.checked;
    },
    async fetch(params = {}){
      try {
        this.loading = true;
        const {data} =  await PlanRepository.list(params);
        this.dataPlan = data.data;
        this.loading = false;

        const pagination = {...this.pagination};
        pagination.total=data.total;
        this.pagination=pagination;
      } catch (error) {
        this.error(error);
      }
    },
    async listCategories(){
      let {data} = await CategoryRepository.list();
      this.programs = data;
    },
    toggleProgram(program, index){
      this.showlines ++;
      this.programs[index].showlines=!this.programs[index].showlines;
    },
    error (message) {
    this.$message.error(message||'Error al procesar');
    },
    success (message) { 
        this.$message.success(message||'Proceso Correcto');
    },
    async save(){
      try {
        if (!this.plan.code) throw ('El codigo es requerido');
        if (!this.plan.name) throw ('El nombre es requerido');
        if (!this.plan.resolution) throw ('La resolución es requerida');
        if (!this.plan.init) throw ('La fecha de inicio es requerida');
        if (!this.plan.end) throw ('La fecha de fin es requerida');

        this.showModal=false;
        this.loading=true;
        let payload = this.plan;
        payload.active = this.plan.active ? 1 : 0;
        await PlanRepository.save(payload)
        this.success();
        this.fetch();
      } catch (error) {
        this.error(error);
      }
    },
    add(){
      this.showModal=true;
      this.plan={active:1};
    },
    edit(id){
      let d = this.dataPlan.find(item => item.id === id);
      this.plan={...d};
      this.showModal = true;
    },
    async remove(id){
      try {
        let payload = this.dataPlan.find(item => item.id === id);
        this.loading=true;
        await PlanRepository.delete(payload.id)
        this.success();
        this.fetch();
      } catch (error) {
        this.error(error);
      }
    },
    async showCategories(id){
      this.plan= this.dataPlan.find(item => item.id == id);
      const {data} = await PlanRepository.list_lines({plan_id:id});
      let lines_active = data.map(item => item.category_id);
      this.programs.forEach(faculty => {
        faculty.children.forEach(program => {
         // program.children.forEach(group => {
            program.children.forEach(line => {
              if( lines_active.includes(line.id) ){
                line.active = 1;
              }else{
                line.active = 0;
              }
            });
          //});
        });
      });
      this.showCategoriesModal = true;
    },
    saveActiveCategories(){
      let active_lines = [];
      this.showCategoriesModal = false;
      this.programs.forEach(faculty => {
        faculty.children.forEach(program => {
          program.children.forEach(group => {
            group.children.forEach(line => {
              if(line.active){
                active_lines.push(line.id);
              }
            });
          });
        });
      });
      PlanRepository.save_line_actives({plan_id:this.plan.id,lines:active_lines});
      this.success();
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
  .checkbox {
    margin-right: .8em;
  }
  /* List Faculties, Programs, Groups and Lines. */
  .anchor_faculty{
    margin:0 18px; 
    color:#333; 
    cursor:pointer; 
    display:flex; 
    align-items:center
  }
  .anchor_faculty .icon{
    margin-left:-18px; 
    margin-right:.8em
  }
  .container_list_programs, .container_list_groups, .container_list_lines{
    margin-top: .7em;
    margin-bottom: .7em;
  }
  .list_style{
    list-style-type: none;
    padding-left: 1.5em;
  }
  .item_hover:hover{
    background-color: #f0f0f0;
  }
  .item_hover{
    padding: 4px;
  }
</style>
