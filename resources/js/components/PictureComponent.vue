<template>
  <a-upload
    name="file"
    list-type="picture-card"
    class="avatar-uploader"
    :show-upload-list="false"
    :disabled="Boolean(this.isdisabled)"
    action="/api/uploadfile"
    :before-upload="beforeUpload"
    :headers="{authorization: mask_token}"
    @change="handleChange"
  >
    <img style="width: 83%" v-if="imageUrl" :src="imageUrl" alt="Image" />
    <div v-else>
      <a-icon :type="loading ? 'loading' : 'plus'" />
      <div class="ant-upload-text">
        Subir imagen
      </div>
    </div>
  </a-upload>
</template>
<script>
const md5 = require('md5');
const mask_token = md5(window.CLEAN_TOKEN);
function getBase64(img, callback) {
  const reader = new FileReader();
  reader.addEventListener('load', () => callback(reader.result));
  reader.readAsDataURL(img);
}
export default {
  props: ['photo','isdisabled'],
  data() {
    return {
      loading: false,
      imageUrl: this.photo,
      mask_token,
    };
  },
  mounted() {
      this.imageUrl = this.photo;
    },
  methods: {
    
    handleChange(info) {
      if (info.file.status === 'uploading') {
        this.loading = true;
        return;
      }
      if (info.file.status === 'done') {
        // Get this url from response in real world.
        getBase64(info.file.originFileObj, imageUrl => {
          this.imageUrl = imageUrl;
          this.loading = false;
          this.$emit('changeImage',this.imageUrl);
        });
      }
    },
    beforeUpload(file) {
      const isJpgOrPng = file.type === 'image/jpeg' || file.type === 'image/png';
      if (!isJpgOrPng) {
        this.$message.error('Solo puede subir imagenes JPG!');
      }
      const isLt1M = file.size / 1024 / 1024 < 1;
      if (!isLt1M) {
        this.$message.error('Imagen debe ser menor de 1MB!');
      }
      return isJpgOrPng && isLt1M;
    },
  },
};
</script>
<style>
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
