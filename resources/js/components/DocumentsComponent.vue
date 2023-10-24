<template>
  <div>
    <div class="vld-parent">
    <loading :active.sync="isLoading" 
    :can-cancel="false" 
    :is-full-page="true"
    color="#1890ff"></loading>
  </div>
  
    <div>
      <div style="display:flex;margin-bottom:1em">
        <h4>Archivos Públicos </h4>
      </div>

      <a-table :columns="columns" :row-key="record => record.id" :data-source="dataDocs" :pagination="false" :loading="loading">
        <span slot="action" slot-scope="text,record">
          <a href="javascript:;" @click="edit(record.id)" title="Editar"><a-icon type="edit" /></a>
        </span>
      </a-table>
    </div>

    <a-modal title="Subir Archivos" :visible="showDocumentsModal" width="50%" cancelText="Cancelar" okText="Guardar" @cancel="showDocumentsModal = false" @ok="save">
      <file-component v-model="document.file" :files='document_files' @changeFile="onChangeDocumentFileList" :key="document_files_key"></file-component>
    </a-modal>
  </div>
</template>

<script>
import Repository from "../repositories/RepositoryFactory";
import FileComponent from "./FileComponent";
const FileRepository = Repository.get("file");
const DocumentRepository = Repository.get('document');
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

const columns = [
  {title:'Nombre', dataIndex:'name', width:'70%'},
  {title:'-', key:'action',scopedSlots: { customRender: "action" }, width:'10px', align:'left'},
];

export default {
  components: { FileComponent, Loading },
  data() {
    return {
      isLoading: false,
      showDocumentsModal:false,
      document_files_key:0,
      document_files:[],
      document,
      dataDocs:[],
      columns,
      document_files:[],
      newTutosFiles:[],
      loading:false,
      pagination:{},
    }
  },
  async mounted() {
    this.isLoading=true;
    await this.fetch();
    this.isLoading=false;
  },
  methods: {
    async fetch(params={}){
      try {
        this.loading=true;
        const {data} = await DocumentRepository.list(params)
        this.dataDocs = data.data;
        this.loading=false;
      } catch (error) {
        this.error(error);
      }
    },
    async save(){
      try {
        this.showDocumentsModal=false;
        this.loading=true;
        let payload = this.document;
        payload.files = this.document_files;
        await DocumentRepository.save(payload)
        this.saveDocumentFiles();
        // this.saveTutorialFiles();
        this.success();
        this.fetch();
      } catch (error) {
        this.error(error);
      }
    },
    edit(id){
      let d = this.dataDocs.find(item => item.id === id);
      this.document={...d};
      this.listDocumentFiles(id);
      this.showDocumentsModal = true;
    },
    async remove(id){
      try {
        let payload = this.dataDocs.find(item => item.id === id);
        this.loading=true;
        await DocumentRepository.delete(payload.id)
        this.success();
        this.fetch();
      } catch (error) {
        this.error(error);
      }
    },
    async listDocumentFiles(reference_id){
      try {
        let {data} = await FileRepository.list({reference_id:reference_id});
        this.document_files = data.data;
        this.document_files_key++;
      } catch (error) {
        this.error(error);
      }
    },
    async saveDocumentFiles(){
      for( var i=0; i<this.document_files.length;i++){
        let file = this.document_files[i];
        file.reference_id = this.document.id;
        file.type = "docs";
        delete file.id;
        await FileRepository.save(file);
      }
    },
    async saveTutorialFiles(){
      for( var i=0; i<this.document_files.length;i++){
        let file = this.document_files[i];
        file.reference_id = this.document.id;
        file.type = "tutos";
        delete file.id;
        await FileRepository.save(file);
      }
    },
    onChangeDocumentFileList(filelist) {
      // this.document_files = [];
      // this.document_files_key++;
      this.resolveDocumentDeletedFiles(filelist);
      this.document_files = filelist.filter(e=>e.id < 1);
    },
    async resolveDocumentDeletedFiles(updated_list){
      const current_list = updated_list.map(e=>e.id);
      const old_list = this.document_files;
      for(var i =0; i<old_list.length;i++ ){
        if( ! current_list.includes(old_list[i].id) ) {
          await FileRepository.delete(old_list[i].id);
        }
      }
    },
    error (message) { this.$message.error(message||'Error al procesar');},
    success (message) { this.$message.success(message||'Proceso Correcto');},
  },
}
</script>