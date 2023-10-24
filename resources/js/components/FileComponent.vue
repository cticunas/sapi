<template>
<div>
  <a-form-model v-if="!readOnly">
    <a-form-model-item>
      <a-input v-model="file_name" placeholder="Nombre del Archivo" />

      <a-upload
        name="file"
        :multiple="false"
        action="/api/uploadfile"
        :file-list="fileList"
        :headers="{authorization: mask_token}"
        :before-upload="beforeUpload"
        @change="handleChange" >
        <a-button> <a-icon type="upload" /> Subir Archivo </a-button>
        (.pdf, .doc, .xls, .ppt)
      </a-upload>
    </a-form-model-item>
  </a-form-model>

  <a-table :columns="columns" :data-source="file_list" :row-key="record => record.id">
    <span slot="user" slot-scope="text, record">
      {{record.person_name ? record.person_name + " " + record.person_lastname : "---" }}
    </span>
    <span slot="file" slot-scope="text, record">
      <a :href="record.url" target="_blank" title="Ver archivo"><a-icon type="eye" /></a>
    </span>
    <span slot="action" slot-scope="text, record">
      <a-popconfirm  title="Seguro?" @confirm="() => remove(record.id)" ok-text="Si" cancel-text="No">
			<a-button v-if="!readOnly" size="small" title="Eliminar"><a-icon type="delete" /></a-button>
		  </a-popconfirm>
    </span>
  </a-table>
  
</div>
</template>
<script>
import moment from "moment";
moment.locale('es');
import Repository from "../repositories/RepositoryFactory";
const md5 = require('md5');
const UserRepository = Repository.get("user");
const mask_token = md5(window.CLEAN_TOKEN);

const columns = [
  {title: 'Nombre', dataIndex: 'name', key: 'name',width:'100%'},
  {title: 'Fecha', dataIndex: 'created_at', key: 'created_at', width:'150px'},
  {title: 'Usuario',scopedSlots: { customRender: 'user' }, key: 'user',width:'130px'},
  {title: 'Ver',key: 'file',scopedSlots: { customRender: 'file' },width:'60px'},
  {title: '-',key: 'action',scopedSlots: { customRender: 'action' },},
];

const data = [];

function getBase64(img, callback) {
  const reader = new FileReader();
  reader.addEventListener('load', () => callback(reader.result));
  reader.readAsDataURL(img);
}
export default {
  props: ['files','readOnly'],
  data() {
    return {
      moment,
      file_name:"",
      data,
      columns,
      loading: false,
      file_list: this.files,
      fileList: [],
      mask_token,
      user:{},
      user_logged :{},
    };
  },
  mounted() {
    this.fileList = [];
    this.getUser();
    this.user_logged = JSON.parse(localStorage.getItem("user"));
    },
  methods: {
    async remove(id){ 
      this.file_list=this.file_list.filter(e => e.id != id);
      this.$emit('changeFile',this.file_list);
    },
    async getUser(){
      //  window.USER_ID
      let {data} = await UserRepository.list();
      this.user_id = data.data;
    },
    handleChange(info) {
      if (info.file.status == 'uploading') {
        this.fileList = info.fileList;
      }
      if (info.file.status == 'removed') { 
        this.fileList = [];
      }
      if (info.file.status === 'done') {
        this.$message.success(`${info.file.name} archivo cargado.`);
        let file_name_add =this.file_name ? this.file_name : info.file.name;
        this.file_list.push({
          id: this.file_list.length*-1,
          created_at:moment().format('YYYY-MM-DD'),
          user_id:this.user_logged.id,
          name:file_name_add,
          url:info.file.response.url
        });
        this.$emit('changeFile',this.file_list);
        this.fileList = [];
        this.file_name='';
        this.$message.success('Para realizar cambios no olvide guardar.');
      } else if (info.file.status === 'error') {
        this.$message.error(`${info.file.name} fallo!`);
      }
    },
    beforeUpload(file) {
      const isJpgOrPng = file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type === 'application/pdf' || file.type === 'application/msword' || file.type === 'application/vnd.ms-excel' || file.type === 'application/vnd.ms-powerpoint' || file.type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
      if (!isJpgOrPng) {
        this.$message.error('Solo puede subir documentos pdf, excel, word, ppt!');
      }
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!isLt2M) {
        this.$message.error('El documento debe ser menor de 2MB!');
      }
      return isJpgOrPng && isLt2M;
    },
  },
};
</script>
<style>
.avatar-uploader > .ant-upload {
  width: 83%;
  height: 285px;
}
.ant-upload-select-picture-card i {
  font-size: 32px;
  color: #999;
}

.ant-upload-select-picture-card .ant-upload-text {
  margin-top: 8px;
  color: #666;
}
</style>
