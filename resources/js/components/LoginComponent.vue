<template>
<div class="cover_image">
  <div class="vld-parent">
    <loading :active.sync="isLoading" 
    :can-cancel="false" 
    :is-full-page="true"
    color="#1890ff"></loading>
  </div>

  <div style="height:86%;display:flex" class="media">
    <div class="card">
      <div class="content">
        <div class="image">
          <img src="/images/logounas.png" alt="logo de la UNAS" width="110px" height="110px">
        </div>

        <div class="form">
          <h3 class="title"><b>Sistema de Apoyo a la Investigaci&oacute;n</b></h3>
          <button class="btn" type="submit" @click="officeLogin()">
            <img src="/images/outlook.png" alt="Logo de Outlook" width="24px" height="24px">
            <p class="txt_button">Inicia sesi&oacute;n con Outlook</p>
          </button>
        </div>

        <div class="description">
          <p class="text_description">Inicie sesi&oacute;n con su correo institucional @unas.edu.pe. En caso no tenga una, p&oacute;ngase en contacto con la OTIC para activar su correo.</p>
        </div>
      </div>
    </div>

    <div class="container_table">
      <div class="box_header">
        <span><a-icon type="schedule" style="font-size:32px;margin-right:.6em;color:#2196f3"/></span>
        <span><h4 style="margin:1em auto;text-align:center;font-weight:600">FECHAS IMPORTANTES</h4></span>
      </div>
      <a-table :columns="columns" :data-source="dataEvent" :row-key="record => record.id" :pagination="false" style="background-color:#fff;">
        <span slot="date_init" slot-scope="text,record">
          <a-tag color="green" style="margin-right:5px">{{ formatDate(record.date_init) }}</a-tag>
        </span>
        <span slot="date_end" slot-scope="text,record">
          <a-tag color="red" style="margin-right:5px">{{ formatDate(record.date_end) }}</a-tag>
        </span>
      </a-table>
    </div>
  </div>

  <footer class="footer">
    <div class="footer_top">
      <p style="margin:0;text-align:center;color:#fff">Soporte: 
        <a href="mailto:sgi.administrador@unas.edu.pe" style="margin-left:.5em;text-decoration:underline;color:#80c2ff;">sgi.administrador@unas.edu.pe</a>
      </p>
    </div>
    <div class="footer_bottom">
      <div>
        <a href="ocda.unas.edu.pe" target="_blank" style="color:#0f75bc;font-weight:600;">Unidad de Gesti&oacute;n de la Investigaci&oacute;n</a>
        <p style="margin:0">Derechos Reservados © 2021</p>
      </div>
      <div>
        <a href="www.unas.edu.pe" target="_blank" style="color:#1abb9c;font-weight:600;">Universidad Nacional Agraria de la Selva</a>
        <p style="text-align:end;margin:0">UNAS - Tingo María - Per&uacute;</p>
      </div>
    </div>
  </footer>
</div>
</template>

<script>

import moment from "moment";
import Repository from "../repositories/RepositoryFactory";
const EventRepository = Repository.get("event");
const UserRepository = Repository.get("user");
import GoogleLogin from 'vue-google-login';
import * as msal from "@azure/msal-browser";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

const msalConfig = {
  auth: {
    clientId: 'a892d3bb-91a7-4f69-8f0b-210713c6fd90',
    authority: 'https://login.microsoftonline.com/common/',
  }
};
const msalInstance = new msal.PublicClientApplication(msalConfig);

const columns = [
  { title: 'Inicio',scopedSlots: { customRender: "date_init" }, width:'22%', align: 'center' },
  { title: 'Fin',scopedSlots: { customRender: "date_end" }, width:'22%', align: 'center' },
  { title: 'Descripción', dataIndex: 'description',},
];
/* ------------ */
export default {
  data(){
    return {
      isLoading: false,
      labelCol: { span: 7 },
      cardCol: { span: 14 },
      dataEvent:[],
      columns,
      login: { },
      message:null,
      loading: false,
      showModal:false,
      params: {
        client_id: "984418871646-leigk55aj36pggks17pomhfin458c4d4.apps.googleusercontent.com"
      },
      renderParams: {
        width: 250,
        height: 48,
        longtitle: true
      }
    }
  },
  components: {
    GoogleLogin, Loading
  },
  async mounted() {
    this.getEvents();
  },
  methods:{
    formatDate(date){
      return date ? moment(date).format('DD/MM/YY') : '';
    },
    async getEvents(){
      try {
        const {data} = await EventRepository.list();
        let dataEvent = data.data;
        this.dataEvent = dataEvent;
      } catch (error) {
        this.error(error)
      }
    },
    onSuccess(googleUser) {
      this.googleLogin(googleUser);
    },
    onFailure(error){
      return error;
    },
    async googleLogin(googleUser){
      try {
        let profile = googleUser.getBasicProfile();
        let name = profile.getGivenName();
        let lastname = profile.getFamilyName();
        let email = profile.getEmail();
        let photo = profile.getImageUrl();
        let google_id = profile.getId();
        
        if(email.indexOf("@unas.edu.pe")==-1 ) throw ("Necesita un correo @unas.edu.pe");
        const {data} = await UserRepository.loginByGoogle({name,lastname, email,photo, google_id});
        localStorage.setItem("user", JSON.stringify(data));
        this.$router.push('research');
        location.reload();
      } catch (error) {
        let message = '';
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          message = error.response.data.message;
        } else if (error.request) {
          // The request was made but no response was received
          // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
          // http.ClientRequest in node.js
          message = error.request;
        } else {
          // Something happened in setting up the request that triggered an Error
          message = error.message;
        }
        this.message = message;
        this.error(message) 
      }
      
    },
    async officeLogin(){
      try {
        const  profileResponse = await msalInstance.loginPopup({});
        let name = profileResponse.account.name;
        let lastname = '.';
        let email = profileResponse.account.username;
        let outlook_id = profileResponse.account.homeAccountId;
        if(email.indexOf("@unas.edu.pe")==-1 ) throw ("Necesita un correo @unas.edu.pe");
        this.isLoading=true;
        const {data} = await UserRepository.loginByOffice({name, email, lastname, outlook_id});
        let user = data;
        user.expire_session = moment().add(1,'d');
        localStorage.setItem("user", JSON.stringify(user));
        this.$router.push('research');
        location.reload();
        this.isLoading=false;
      } catch (err) {
        this.error(err);
      }
    },
    error (message) {
      this.$message.error(message||'Error al procesar');
    },
    success (message) { 
      this.$message.success(message||'Proceso Correcto');
    }
  }
}
</script>

<style scoped>
  .cover_image{
    margin:0;
    padding:0;
    width:100%;
    background-image:url('/images/cover-login.jpg');
    background-size: cover;
    position: absolute;
    width: 100%;
    height: 100%;
  }
  .container_table{
    margin: auto;
    width:380px;
    height:440px;
    background-color:rgba(255, 255, 255, 0.8);
    box-shadow: 4px 4px 6px rgba(0, 0, 0, 0.35);
    padding:2em 18px;
    border-radius:8px;
  }
  .box_header{
    display:flex;
    align-items:center;
    justify-content: center;
  }
  .card{
    background-color: rgba(255, 255, 255, 0.7);
    box-shadow: 4px 4px 6px rgba(0, 0, 0, 0.35);
    margin: auto;
    width: 380px;
    height: 440px;
    flex-direction: column;
    border-radius: 8px;
  }
  .content{
    margin: auto 2.5em;
    display: flex;
    flex-direction: column;
    justify-items: center;
  }
  .image{
    text-align: center;
  }
  .form{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 2em 0;
  }
  .text_description{
    margin: 0;
    padding: 0;
    text-align:justify;
    font-size:13px;
    line-height:20px;
    font-weight:500;
  }
  .title{
    margin-bottom: .8em;
    text-align: center;
  }
  .btn .txt_button{
    padding-left: 1.2rem;
    margin: auto 0;
  }
  .btn{
    box-shadow: 0 2px 4px 0 rgba(144, 144, 144, 0.25);
    width: 250px;
    height: 45px;
    display: flex;
    align-items: center;
    color: #050038; /* #1790FC */
    background: #fff;
    border: 2px solid #2196f3;
    transition: all 1s ease;
  }
  .btn:hover{
    background: rgba(235, 235, 235, 0.8);
    color: #050038;
  }
  .footer{
    position:fixed;
    bottom:0;
    width:100%;
    z-index:999;
  }
  .footer_top{
    margin:0;
    padding:0;
    height:40px;
    background-color:#2f4050;
    display:flex;
    align-items:center;
    justify-content:center;
  }
  .footer_bottom{
    display:flex;
    justify-content:space-between;
    background-color:#f3f3f4;
    padding: .5em 2em;
  }

  @media screen and (max-width: 840px) {
    .media{
      flex-direction: column;
    }
    .card{
      margin: 3em auto;
    }
    .content{
      margin: 2em 2em;
    }
    .container_table{
      margin: 0 auto;
    }
  }
  @media screen and (max-width: 480px) {
    .footer{
      font-size: 12px;
    }
    .card{
      margin: 3em auto;
    }
    .container-table{
      margin: 3em auto;
    }
  }
  @media screen and (max-height: 320px) {
    .footer{
      display: none;
    }
    .container-table{
      margin: 3em auto 3em auto;
    }
  }
</style>
