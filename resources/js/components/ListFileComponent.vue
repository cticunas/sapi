<template>
  <a-upload
    name="file"
    :multiple="false"
    action="/api/uploadfile"
    :file-list="fileList"
    :headers="{authorization: 'authorization-text'}"
    @change="handleChange"
>
    <a-button> <a-icon type="upload" /> Subir Archivo </a-button>
</a-upload>
</template>
<script>
function getBase64(img, callback) {
  const reader = new FileReader();
  reader.addEventListener('load', () => callback(reader.result));
  reader.readAsDataURL(img);
}
export default {
  props: ['file'],
  data() {
    return {
      loading: false,
      fileUrl: this.file,
      fileList: []
    };
  },
  mounted() {
      this.fileUrl = this.file;
      if(this.file) {
        this.fileList = [];
        let tmp = this.file.split('/');
        let fileName = tmp[tmp.length - 1];

        this.fileList.push({uid: '-1', name:fileName, status: 'done', url: this.file}); // value in component
      }
    },
  methods: {
        handleChange(info) {
        if (info.file.status == 'uploading') {
            
            this.fileList = info.fileList;
        }
        
        if (info.file.status == 'removed') {
            this.fileList = [];
        }
        if (info.file.status === 'done') {
            
            this.$message.success(`${info.file.name} file uploaded successfully`);
            
            this.$emit('changeFile',info.file.response.url);
        } else if (info.file.status === 'error') {
            this.$message.error(`${info.file.name} file upload failed.`);
        }
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
