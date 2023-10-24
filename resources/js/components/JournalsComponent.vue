<template>
<div style="padding:10px 20px 10px 10px">
  <h4>Fuentes  <a-button type="primary" size="small" @click="newo()">Nuevo</a-button> </h4>
  <a-table :columns="columns" :row-key="record => record.id"
    :data-source="data"
    :pagination="pagination"
    :loading="loading"
    @change="handleTableChange"
    >
    <span slot="type" slot-scope="text,record">
    {{record.type=='CO'?'Conferencia':'Revista'}}
    </span>
    <span slot="action" slot-scope="text,record">
      <a href="javascript:;" @click="edit(record.id)" title="Editar"><a-icon type="edit" /></a>
      <a-divider type="vertical" />
      <a-popconfirm v-if="data.length" title="Seguro?" @confirm="() => remove(record.id)" ok-text="Si" cancel-text="No">
        <a href="javascript:;" title="Eliminar"><a-icon type="delete" /></a>
      </a-popconfirm>
     </span>
    </a-table>

  <a-modal title="Revista/Conferencia"  :visible="showModal" cancelText="Cancelar" :key="showModal" okText="Guardar"
    @ok="save" 
    @cancel="showModal = false" 
    >

    <a-form-model ref="userForm" :model="journal" :label-col="labelCol" :wrapper-col="wrapperCol">
        <a-row>
          <a-col class="gutter-row" :span="24">
              <a-form-model-item label="Codigo" prop="codigo">
				<a-input v-model="journal.code" />
				</a-form-model-item>
            <a-form-model-item label="Nombre" prop="nombre">
				<a-input v-model="journal.name" />
			</a-form-model-item>
       <a-form-model-item label="Tipo" prop="tipo">
          <a-select  v-model="journal.type" placeholder="Seleccione un tipo" style="width: 120px">
              <a-select-option v-for="item in journal_types" :value="item.id" :key="item.id">
                {{item.name}}
              </a-select-option>
          </a-select>
       </a-form-model-item>
      <a-form-model-item label="Indexado" prop="indexado">
          <a-select v-model="journal.indexed" placeholder="Seleccione la indexacion" style="width: 120px">
              <a-select-option v-for="item in indexed_types" :value="item.name" :key="item.id">
                {{item.name}}
               </a-select-option>
          </a-select>
       </a-form-model-item>
           <a-form-model-item label="URL" prop="url">
              <a-input v-model="journal.url" />
            </a-form-model-item>
          </a-col>
        </a-row>
        
    </a-form-model>
     
    </a-modal>

</div>    
</template>
<script>
import moment from "moment";
import Repository from "../repositories/RepositoryFactory";
const JournalRepository = Repository.get("journal");
const {RT_COBRA,CA_CASHB,RT_AUXIL} = require("../constants");//roles
const journal_types= [{id:'CO', name:'Conferencia'},{id:'Rev',name:'Revista'}];
const indexed_types= [{id:'1', name:'LatIndex'},{id:'2',name:'Scopus'},{id:'3',name:'Scielo'},{id:'4',name:'WOS'}, {id:'5',name:'Otros'}];
/*---------- */
const columns = [
  {title: 'Codigo',dataIndex: 'code', width: '100px'},
  { title: 'Nombre',dataIndex: 'name',},
  {title: 'Tipo',key: 'type', scopedSlots: { customRender: "type" },   width: '100px'},
  {title: 'Indexado',dataIndex: 'indexed', width: '100px'},
  {title:'-',key:'action',scopedSlots: { customRender: "action" }, width: '100px' }
];

export default {
  
  data(){
    return {
      labelCol: { span: 7 },
      wrapperCol: { span: 14 },
      journal: { },
      data:[],
      pagination: {},
      loading: false,
      columns,
      journal_types,
      indexed_types,
      showModal:false,
    }
  },
  async mounted() {
    this.fetch();
  },
  methods:{        
    async fetch(params = {}){
        //params.iam_id=window.USER_ID;
        try {
          this.loading = true;
          const {data} =  await JournalRepository.list(params)
          this.loading = false;
          this.data = data.data;
        } catch (error) {
          this.error(error)
        }
    },
    error (message) {
    this.$message.error(message||'Error al procesar');
    },
    success (message) { 
        this.$message.success(message||'Proceso Correcto');
    },
    save(){
      try {        
        this.showModal=false;
        this.loading=true;
        let payload = this.journal;
        JournalRepository.save(payload).then((data)=>{
          this.success();
          this.fetch();
        }).catch(err=>{ this.error();});
      } catch (error) {
        this.error(error);
      }
    },
    newo(){
      this.showModal=true;
      this.journal={};
    },
    edit(id){
      let d = this.data.find(item => item.id === id);
      this.journal = {...d};
      this.showModal = true;
    },
    async remove(id){
      let payload = this.data.find(item => item.id === id);
      this.loading=true;
      JournalRepository.delete(payload.id).then((data)=>{
        this.success();
        this.fetch();
      }).catch(err=>{ this.error();});
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

</style>
