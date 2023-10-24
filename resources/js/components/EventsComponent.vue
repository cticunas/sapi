<template>
  <div style="padding:10px 20px 10px 10px">
    <div class="vld-parent">
      <loading :active.sync="isLoading" 
      :can-cancel="false" 
      :is-full-page="true"
      color="#1890ff"></loading>
    </div>

    <div style="display:flex">
      <h4 style="margin-right:.5em">Eventos </h4>
      <a-button type="primary" size="small" title="Agregar" @click="addEvent">Nuevo</a-button>
    </div>

    <a-table :columns="columns" :row-key="record => record.id"
    :data-source="dataEvent"
    :pagination="pagination"
    :loading="loading"
    @change="handleTableChange">
      <span slot="date_init" slot-scope="text,record">
        {{ formatDate(record.date_init) }}
      </span>
      <span slot="date_end" slot-scope="text,record">
        {{ formatDate(record.date_end) }}
      </span>
      <span slot="action" slot-scope="text,record">
        <a href="javascript:;" @click="editEvent(record.id)" title="Editar"><a-icon type="edit" /></a>
        <a-divider type="vertical" />
        <a-popconfirm title="Seguro?" @confirm="() => removeEvent(record.id)" ok-text="Si" cancel-text="No">
          <a href="javascript:;" title="Eliminar"><a-icon type="delete" /></a>
        </a-popconfirm>
      </span>
    </a-table>

    <a-modal title="Estado de la Investigación"  :visible="showEventModal" cancelText="Cancelar" okText="Guardar" @ok="saveEvent" @cancel="showEventModal = false" width="50%">
      <a-form-model ref="userForm" :model="event" :label-col="labelCol" :wrapper-col="wrapperCol">
        <a-row>
          <a-col :span="24">
            <a-form-model-item label="Titulo">
              <a-input v-model="event.title" placeholder="Titulo del evento"></a-input>
            </a-form-model-item>

            <div style="display:flex;justify-content:space-around">
              <div style="width:50%;display:flex;justify-content:center">
                <a-form-model-item label="Inicio">
                  <a-date-picker v-model="event.date_init" placeholder="Fecha de inicio" :format="DATEFORMAT"/>
                </a-form-model-item>
              </div>
              <div style="width:50%">
                <a-form-model-item label="Fin">
                  <a-date-picker v-model="event.date_end" placeholder="Fecha de fin" :format="DATEFORMAT"/>
                </a-form-model-item>
              </div>
            </div>

            <a-form-model-item label="Descripcion">
              <a-textarea v-model="event.description" placeholder="Ingrese una descripción..."/>
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
const EventRepository = Repository.get("event");
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import {DATEFORMAT} from "../constants";

const columns = [
  {title:'Titulo', dataIndex:'title', width:'25%'},
  {title:'Fecha Inicio',scopedSlots: { customRender: "date_init" }, width:'30px'},
  {title:'Fecha Fin',scopedSlots: { customRender: "date_end" }, width:'30px'},
  {title:'Descripcion', dataIndex:'description', width:'45%'},
  {title:'-', key:'action',scopedSlots: { customRender: "action" }, width:'20px'},
];

export default {
  data() {
    return {
      DATEFORMAT,
      isLoading: false,
      labelCol: { span: 4 },
      wrapperCol: { span: 19 },
      showEventModal: false,
      columns,
      event:{},
      dataEvent:[],
      loading: false,
      pagination:{},
    }
  },
  components: {
    Loading
  },
  async mounted() {
    this.isLoading=true;
    await this.fetch();
    this.isLoading=false;
  },
  methods: {
    formatDate(date){
      return date ? moment(date).format('DD-MM-YYYY') : '';
    },
    async fetch(){
      try {
        this.loading = true;
        const {data} =  await EventRepository.list();
        this.dataEvent = data.data;
        this.loading = false;

        const pagination = {...this.pagination};
        pagination.total=data.total;
        this.pagination=pagination;
      } catch (error) {
        this.error(error);
      }
    },
    async saveEvent() {
      try {
        if (!this.event.description) throw 'Ingresar una descripción';
        this.showEventModal=false;
        this.loading=true;
        let payload = this.event;
        await EventRepository.save(payload)
        this.success();
        this.fetch();
      } catch (error) {
        this.error(error);
      }
    },
    addEvent(){
      this.showEventModal = true;
      this.event = {};
    },
    editEvent(id){
      let d = this.dataEvent.find(item => item.id === id);
      this.event={...d};
      this.showEventModal = true;
    },
    async removeEvent(id){
      try {
        let payload = this.dataEvent.find(item => item.id === id);
        this.loading=true;
        await EventRepository.delete(payload.id)
        this.success();
        this.fetch();
      } catch (error) {
        this.error(error);
      }
    },
    error (message) {
      this.$message.error(message||'Error al procesar');
    },
    success (message) { 
      this.$message.success(message||'Proceso Correcto');
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
  },
}
</script>