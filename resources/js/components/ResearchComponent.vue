<template>
    <div style="padding:10px 20px 10px 10px">
        <div class="vld-parent">
            <loading :active.sync="isLoading"
            :can-cancel="false"
            :is-full-page="true"
            color="#1890ff"></loading>
        </div>

        <a-row>
            <a-col :span="6">
                <h4>Investigaciones <a-button type="primary" size="small" @click="newResearch()">Nuevo</a-button></h4>
            </a-col>
            <a-col :span="5">
                <div style="display:flex; align-items:center" v-if="user_logged.role_id == ADMIN_ROLE || user_logged.role_id == UNIT_ROLE || user_logged.role_id == COR_ROLE" >
                    <label style="margin: auto .5em">Mis Investigaciones:  <input class="checkbox" type="checkbox" v-model="filter.own_research" @change="onChangeOwnResearch" /> </label>
                </div>
            </a-col>
            <a-col :span="6">
                <div style="display: flex; justify-content: flex-end; align-items: center"
                v-if="user_logged.role_id == ADMIN_ROLE || user_logged.role_id == UNIT_ROLE">
                    <a-tooltip style="width:120px">
                        <template slot="title">
                            Investigaciones que deben ser cambiados a estado Ejecución.
                        </template>
                        <label style="margin: auto .5em">I. a Ejecutar:  <input class="checkbox" type="checkbox" v-model="filter.in_work" @change="onChangeResearchInWork" /> </label>
                    </a-tooltip>
                    <a-tooltip style="width:120px">
                        <template slot="title">
                            Investigaciones que tienen Artículo y deben ser cambiados a estado Culminado.
                        </template>
                        <label style="margin: auto .5em">I. a Culminar:  <input class="checkbox" type="checkbox" v-model="filter.in_finish" @change="onChangeResearchInFinish" /> </label>
                    </a-tooltip>
                </div>
            </a-col>
            <a-col :span="6">
                <div style="display: flex; justify-content: flex-end; align-items: center"
                v-if="user_logged.role_id == ADMIN_ROLE || user_logged.role_id == UNIT_ROLE || user_logged.role_id == COR_ROLE">
                    <h6>Entregables x aprobar: <a-button type="primary" size="small" @click="listPendingOutcomes(); fetchAllPendingOutcomes()">Ver</a-button></h6>
                </div>
            </a-col>
        </a-row>

        <div class="header_nav">
            <a-col :span="8">
                <a-input placeholder="Buscar ..." v-model="filter.text" style="width:95%" v-on:keyup.enter="fetch()"/>
            </a-col>
            <a-col :span="6">
                <a-range-picker v-model="filter.range" :placeholder="['Fecha de inicio', 'Fecha de fin']" :format="DATEFORMAT" />
            </a-col>
            <a-col :span="3">
                <a-select style="width:120px; margin:0 5px;" v-model="filter.state" placeholder="Estado" :allowClear='true'>
                    <a-select-option v-for="item in states" :value="item.id" :key="item.id">
                    {{item.name}}
                    </a-select-option>
                </a-select>
            </a-col>
            <a-col :span="5">
                <a-select style="width:200px; margin:0 5px;" v-model="filter.type_research" placeholder="Tipo"  :allowClear='true'>
                    <a-select-option v-for="item in type_research" :value="item.id" :key="item.id" >
                    {{item.name}}
                    </a-select-option>
                </a-select>
            </a-col>
            <a-col :span="1">
                <a-button type="primary" icon="search" size="small" @click="fetch()">Buscar</a-button>
            </a-col>
        </div>

        <a-table :columns="columns" :row-key="record => record.id"
        :data-source="data"
        :pagination="pagination"
        :loading="loading"
        @change="handleTableChange"
        >
            <span slot="date_init" slot-scope="text,record">
                {{record.date_init}} {{record.date_end}}
            </span>
            <div slot="stateResearch" slot-scope="text,record" style="text-align:center">
            {{getStatusName(record.research_state_id)}}  <br />
                <span v-if="getPendingTime(record.date_end)<0 && ! /Culminado|Anulado|Suspendido/. test(getStatusName(record.research_state_id))" style="pointer:default">
                    <a-tooltip>
                        <template slot="title">
                            Esta Investigacion debió concluir el  {{record.date_end }}
                        </template>
                        🔔
                    </a-tooltip>
                </span>
            </div>
            <span slot="typeResearch" slot-scope="text,record">
                <span>
                    <a-tooltip>
                        <template slot="title">
                            {{record.type_research==TESIS_RESEARCH ? 'Tesis' : record.type_research==PROFESSOR_RESEARCH ? 'I. docente' : record.type_research==EXPERIENCE_RESEARCH ? 'Experiencia' : record.type_research==INNOVATION_RESEARCH ? 'Innovacion' : ''}}
                        </template>
                        {{getStatusIcon(record.type_research)}}
                    </a-tooltip>
                </span>
            </span>
            <span slot="authorResearch" slot-scope="text,record">
                <a-tag v-for="author, index in record.research_authors" :key="index" :color=" author.role == 'TI' ? 'cyan':''"> {{author.fullname}} </a-tag>
            </span>
            <span slot="action" slot-scope="text,record">
                <a href="javascript:;" title="Estados" v-if="record.research_state_id != RESEARCH_STATE_ANUL" @click="changeStatus(record.id)"><a-icon type="check-circle" /></a>
                <a-divider type="vertical" />
                <a href="javascript:;" title="Entregables" v-if="record.research_state_id != RESEARCH_STATE_ANUL" @click="showOutcomeModal(record)"><a-icon type="folder-open" /></a>

                <a href="javascript:;" title="Editar" v-if=" record.research_state_id != RESEARCH_STATE_ANUL && ( checkOwner(record) || user_logged.role_id == ADMIN_ROLE )" @click="editResearch(record.id)"><a-icon type="edit" /></a>

                <a-divider type="vertical" v-if="record.research_state_id == RESEARCH_STATE_NEW"></a-divider>
                <a-popconfirm v-if="record.research_state_id == RESEARCH_STATE_NEW" title="Seguro?" @confirm="() => remove(record.id)" ok-text="Si" cancel-text="No">
                    <a href="javascript:;" title="Eliminar"><a-icon type="delete"/></a>
                </a-popconfirm>

                <a-divider type="vertical" v-if="record.research_state_id != RESEARCH_STATE_ANUL &&  !checkOwner(record)"></a-divider>
                <a href="javascript:;" title="Ver" v-if="record.research_state_id != RESEARCH_STATE_ANUL &&  !checkOwner(record)" @click="editResearch(record.id, {readOnly:true})"><a-icon type="eye" /></a>
            </span>
        </a-table>

        <p>Total: {{pagination.total?pagination.total:0}} registros. </p>

        <a-modal :title="'Investigación '+(research.id?research.code:'Nueva')" :visible="showResearchModal" cancelText="Cancelar" :key="showResearchModal" :okText="research.readOnly?'Cerrar':'Guardar'"
        @ok="research.readOnly? closeResearch(): save()"
        @cancel="showResearchModal = false"
        width="90%">
            <a-form-model ref="userForm" :model="research" :label-col="labelCol" :wrapper-col="wrapperCol">
                <div class="container_title_form">
                    <label class="title_form" for="title">Titulo:</label>
                    <a-input id="title" placeholder="Titulo" v-model="research.title" :disabled="(user_logged.role_id != ADMIN_ROLE && research.research_state_id > RESEARCH_STATE_NEW)  || research.readOnly" />
                </div>

                <div>
                    <div v-if="!research.readOnly">
                        <label style="margin-right: .5em">Objetivo: </label>
                        <a-input v-model="newObjective" placeholder="+ Objetivo" style="width: 88%; margin-right: 1em;" v-on:keyup.enter="addObjective()" :disabled="user_logged.role_id != ADMIN_ROLE && research.research_state_id > RESEARCH_STATE_NEW"/>
                        <a-button class="btn_author" type="primary" title="Añadir objetivo" size="small" @click="addObjective()" :disabled="user_logged.role_id != ADMIN_ROLE && research.research_state_id > RESEARCH_STATE_NEW">+</a-button>
                    </div>

                    <table style="width:88%; margin:1em 0 2.5em 4.5em">
                        <tr style="background-color: #eee;">
                            <th style="padding-left: 1em">Objetivos</th>
                            <th style="width:35px; text-align:center">-</th>
                        </tr>

                        <tr v-for="objective in research.objectives" :key="objective.id">
                            <td style="display:flex; align-items:center; border-bottom:1px dashed #ccc;">
                                <a-icon type="arrow-right" v-show="objective.id != 1" style="margin-left:.5em;margin-right:.5em"/>
                                <a-input v-model="objective.name" style="border:0 " :disabled="(user_logged.role_id != ADMIN_ROLE && research.research_state_id > RESEARCH_STATE_NEW)|| research.readOnly"/>
                            </td>
                            <td style="width:35px; text-align:center">
                                <a v-if="!research.readOnly" href="javascript:;" @click="removeObjective(objective.id)" title="No puede realizar esta acción" v-show="objective.id != 1" style="display:none" :disabled="user_logged.role_id != ADMIN_ROLE && research.research_state_id > RESEARCH_STATE_NEW"><a-icon type="delete" /></a>
                            </td>
                        </tr>
                    </table>
                </div>

                <a-row>
                    <a-col :span="12" >
                        <a-form-model-item label="Tipo de investigación" prop="tipo">
                            <a-select v-model="research.type_research" @change="filterGrade" :disabled="research.readOnly">
                                <a-select-option v-for="item in type_research" :value="item.id" :key="item.id" >
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Grado" prop="grade" v-show="research.type_research==1" >
                            <a-select v-model="research.grade" placeholder="Seleccione un grado" :disabled="research.readOnly">
                                <a-select-option v-for="item in grades" :value="item.id" :key="item.id">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Fecha de Inicio" prop="fechaInicio">
                            <a-date-picker v-model="research.date_init" placeholder="Fecha de inicio" :format="DATEFORMAT" :disabled="research.readOnly"/>
                        </a-form-model-item>

                        <a-form-model-item label="Fecha de Finalizacion" prop="fechaFinalizacion">
                            <a-date-picker v-model="research.date_end" placeholder="Fecha de fin" :format="DATEFORMAT" :disabled="research.readOnly"/>
                        </a-form-model-item>

                        <a-form-model-item label="Tipo de Financiamiento" prop="fin_type">
                            <a-select v-model="research.fin_type" placeholder="Seleccione un tipo" :disabled="research.readOnly">
                                <a-select-option v-for="item in financing" :value="item.id" :key="item.id" >
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Entidad que Financia" prop="fin_company">
                            <a-input v-model="research.fin_company" :disabled="research.readOnly" />
                        </a-form-model-item>

                        <a-form-model-item label="Presupuesto" prop="budget">
                            <a-input-number v-model="research.budget" :min="0" :max="999999999999" :default-value="0" :disabled="research.readOnly" style="width:80%"/>
                        </a-form-model-item>

                        <a-form-model-item label="Autor(es)" prop="author">
                            <div class="container_select_pople" v-if="!research.readOnly">
                                <select-people-component style="width: 80%" :person_id="author.id" format="full_data" v-on:handleSelectPeople="changeSelectAuthor"/>
                                <span class="container_btn_author">
                                    <a-button class="btn_author" type="primary" title="Añadir autor" size="small" @click="addAuthor()">+</a-button>
                                </span>
                            </div>
                        </a-form-model-item>

                        <div class="container_list">
                            <table class="author_table">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Autor</th>
                                    <th>Quitar</th>
                                </tr>
                                <tr v-for="author, index in research_authors" :key="index">
                                    <td>
                                        <a-select v-model="author.role" style="width: 90px" :disabled="research.readOnly">
                                            <a-select-option v-for="item in roles" :key="item.id">
                                            {{item.name}}
                                            </a-select-option>
                                        </a-select>
                                    </td>
                                    <td>
                                        <p> {{author.fullname}} </p>
                                    </td>
                                    <td>
                                        <a v-if="!research.readOnly" href="javascript:;" title="Quitar" @click="removeAuthor(author.id)"><a-icon type="delete"/></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </a-col>

                    <a-col :span="12">
                        <a-form-model-item :label="isExternal=='true'? 'Contrato' : 'N° de Resolución'" prop="document">
                            <a-input v-model="research.document" :disabled="research.readOnly" />
                        </a-form-model-item>

                        <a-form-model-item label="Facultad" prop="department">
                            <a-select v-model="research.faculty_id" placeholder="Seleccione una Facultad" @change="onChangeFaculties()" :disabled="research.readOnly || !research.allowEdit">
                                <a-select-option v-for="item in faculties" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Escuela" prop="organization">
                            <a-select v-model="research.organization_id" placeholder="Seleccione una Escuela" :disabled="research.readOnly || !research.allowEdit">
                                <a-select-option v-for="item in professionalSchoolsOfAFaculty" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Plan de Investigación" prop="plan">
                            <a-select v-model="research.plan" placeholder="Seleccione un plan" @change="onChangePlan()" :disabled="research.readOnly || !research.allowEdit">
                                <a-select-option v-for="item in plans" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Areaa" prop="area">
                            <a-select show-search option-filter-prop="children" v-model="research.area_id" placeholder="Seleccione un Area" @change="onChangeArea()" :disabled="research.readOnly || research.notAllowEditResearchUnit">
                                <a-select-option v-for="item in areas" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}sdsdfds
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Grupo" prop="group">
                            <a-select show-search option-filter-prop="children" v-model="research.group_id" placeholder="Seleccione un Grupo" @change="onChangeGroup()" :disabled="research.readOnly || research.notAllowEditResearchUnit">
                                <a-select-option v-for="item in groups" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Linea de Investigación" prop="line_research">
                            <a-select show-search option-filter-prop="children" v-model="research.line_id" placeholder="Seleccione una linea" @change="onChangeLine" :key="line_select_key" :disabled="research.readOnly">
                                <a-select-option v-for="item in lines" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Lugar de ejecucion" prop="location">
                            <a-input v-model="research.location" :disabled="research.readOnly"/>
                        </a-form-model-item>

                        <a-form-model-item label="Incentivo" prop="incentive"> <!--  v-if="research.grade != 2" -->
                            <input class="checkbox" type="checkbox" v-model="research.incentive" @change="onChangeIncentive" :disabled="research.readOnly"/>
                        </a-form-model-item>

                        <a-form-model-item label="Investigacion Externa" prop="external">
                            <input class="checkbox" type="checkbox" v-model="research.external" @change="onChangeExternal" :disabled="research.readOnly" />
                        </a-form-model-item>
                    </a-col>
                </a-row>
            </a-form-model>
        </a-modal>

        <a-modal title="Historial de Estados"  :visible="showListStatusModal" width="70%" cancelText="Cerrar" @cancel="showListStatusModal=false" >
            <template slot="footer">
                <a-button key="back" @click="showListStatusModal = false">
                    Cerrar
                </a-button>
            </template>
            <div class="btnStatus">
                <a-button type="primary" v-if="user_logged.role_id == ADMIN_ROLE || user_logged.role_id == UNIT_ROLE" @click="addStatus()">
                    Agregar
                </a-button>
            </div>

            <div><p>(<span style="color:#f00">*</span>) Para cambiar el estado necesita adjuntar: resoluciones, acuerdos, cartas u otros.</p></div>

            <div class="card_logs">
                <div v-for="(status,index) in research_logs" :key="status.id" class="container_log_arrow">
                    <button class="card_log" @click="editStatus(status)">
                        <p> {{ getStatusName(status.new_status_id) }} </p>
                        <p><a-icon type="clock-circle" /> {{ status.date_at }} </p>
                        <p>
                            <a-tooltip placement="bottom">
                                <template slot="title"> Usuario que realizó el cambio</template>
                                <a-icon type="user" /> {{ status.user_name }}
                            </a-tooltip>
                        </p>
                    </button>
                    <a-icon v-if="research_logs.length != (index+1)  " type="arrow-right" style="padding: 0 5px 0 5px;"/>
                </div>
            </div>
        </a-modal>

        <a-modal title="Estado de la Investigación"  :visible="showEditStatusModal" cancelText="Cancelar" okText="Guardar"
        @ok="saveStatus"
        @cancel="showEditStatusModal = false"
        width="50%">
            <a-form-model ref="userForm" :model="status" :label-col="labelCol" :wrapper-col="wrapperCol">
                <a-row>
                    <a-col :span="24">
                        <div v-if="user_logged.role_id == ADMIN_ROLE || user_logged.role_id == UNIT_ROLE">
                            <a-form-model-item label="Cambiar Estado" prop="stateResearch">
                                <a-select v-model="status.new_status_id"  placeholder="Seleccione un estado.">
                                    <a-select-option v-for="item in states" :value="item.id+''" :key="item.id" >
                                    {{item.name}}
                                    </a-select-option>
                                </a-select>
                            </a-form-model-item>

                            <div><p>(<span style="color:#f00">*</span>) Para cambiar el estado necesita adjuntar: resoluciones, acuerdos, cartas u otros.</p></div>

                            <file-component v-model="status.file" :files='status_files' @changeFile="changeStatusFileList" :key="status_files_key">
                            </file-component>
                        </div>

                    </a-col>
                </a-row>
            </a-form-model>
        </a-modal>

        <a-modal title="Listado de Entregables" :visible="showListOutcomeModal" width="80%" cancelText="Cancelar" @cancel="showListOutcomeModal = false">
            <template slot="footer">
                <a-button key="back" @click="showListOutcomeModal = false">
                    Cerrar
                </a-button>
            </template>
            <div class="btnOutcome">
                <a-button type="primary" v-if="checkOwner(research) || user_logged.role_id == ADMIN_ROLE" @click="addOutcome()" title="Agregar"> Agregar </a-button>
            </div>

            <div><p>(<span style="color:#f00">*</span>) Para añadir un entregable necesita adjuntar: Informes, Material de Avance, Diapositivas u otros.</p></div>

            <table class="outcome_table">
                <tr>
                    <th class="column_action1" title="Entregable">Entregable</th>
                    <th class="column_action1" title="Autor(es)">Autores</th>
                    <th class="column_action2" title="Fecha del Entregable">F. Entrega</th>
                    <th class="column_action2" title="Fecha Fin de Investigacion">Fin. Inv.</th>
                    <th class="column_action2" title="Periodo">Periodo</th>
                    <th class="column_action3" title="Tipo de Entregable">Tipo</th>
                    <th class="column_action5" title="Entregables revisados">Revisado</th>
                    <th class="column_action5" title="Entregables aprobados">Aprobado</th>
                    <th class="column_action4" title="Acciones">-</th>
                </tr>
                <tr v-for="outcome in outcomes" :key="outcome.id">
                    <td>{{outcome.name}}</td>
                    <td>
                        <a-tag v-for="author, index in outcome.outcome_authors" :key="index" :color=" author.role == 'TI' ? 'cyan':''">{{author.fullname}}</a-tag>
                    </td>
                    <td>{{ outcome.date }}</td>
                    <td :style="getPendingTime(outcome.research_date_end)<0?'background: rgba(255,255,0,0.1)':'' "> {{outcome.research_date_init}} al {{outcome.research_date_end}}</td>
                    <td>{{ outcome.period_type }}{{ outcome.period }} - {{ formatYear(outcome.date) }}</td>
                    <td><span :title="outcome.public?'Privado':''" >{{outcome.public ? '' : '🔒 '}}</span>
                        {{ getOutcomeType(outcome.type) }} {{ outcome.type==ADVANCE_OUTCOME?outcome.count:''}}
                    </td>
                    <td style="text-align:center">{{ formatDate(outcome.reviewed_date) }} ({{ outcome.reviewed_by_user ? outcome.reviewed_by_user : '--'}})</td>
                    <td style="text-align:center">{{ formatDate(outcome.approved_date) }} ({{ outcome.approved_by_user ? outcome.approved_by_user : '--'}})</td>
                    <td>
                        <a href="javascript:;" title="Editar" v-if="checkOwner(research) || user_logged.role_id == ADMIN_ROLE" @click="editOutcome(outcome.id)"><a-icon type="edit" /></a>
                        <a-divider type="vertical" />
                        <a-popconfirm title="Seguro?" v-if="checkOwner(research) || user_logged.role_id == ADMIN_ROLE" @confirm="() => removeOutcome(outcome.id)" ok-text="Si" cancel-text="No">
                            <a href="javascript:;" title="Eliminar"><a-icon type="delete" /></a>
                        </a-popconfirm>
                    </td>
                </tr>
            </table>
            <div style="margin-top:10px">Total: {{outcomes.length}} registros.</div>
        </a-modal>

        <a-modal title="Artefactos" :visible="showEditOutcomeModal" cancelText="Cancelar" okText="Guardar"
        @ok="saveOutcome"
        @cancel="showEditOutcomeModal = false"
        width="90%">
            <a-form-model ref="userForm" :model="outcome" :label-col="labelCol" :wrapper-col="wrapperCol">
                <a-row>
                    <a-col :span="12">
                        <a-form-model-item label="Tipo">
                            <a-select v-model="outcome.type" placeholder="Seleccione un tipo" >
                                <a-select-option v-for="item in outcome_types" :value="item.id" :key="item.id" >
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Nombre">
                            <a-input v-model="outcome.name"></a-input>
                        </a-form-model-item>

                        <a-form-model-item label="Fecha">
                            <a-date-picker v-model="outcome.date" :format="DATEFORMAT"></a-date-picker>
                        </a-form-model-item>

                        <a-form-model-item label="T. Entrega">
                            <a-select v-model="outcome.period_type" placeholder="Tipo Entrega" @change="onChangePeriodType" >
                                <a-select-option v-for="item in PERIOD_TYPES" :value="item.id" :key="item.id" >
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Periodo">
                            <a-select v-model="outcome.period" placeholder="Seleccione un periodo" >
                                <a-select-option v-for="item in getPeriods(outcome.period_type)" :value="item.id" :key="item.id" >
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <!-- <a-form-model-item label="Publico" prop="public">
                            <input class="checkbox" type="checkbox" v-model="outcome.public" @change="onChangePublic" />
                        </a-form-model-item> -->
                    </a-col>

                    <a-col :span="12">
                        <a-form-model-item label="URL"  v-show="outcome.type==4">
                            <a-input v-model="outcome.url"></a-input>
                        </a-form-model-item>

                        <a-form-model-item label="DOI" v-show="outcome.type==4">
                            <a-input v-model="outcome.doi"></a-input>
                        </a-form-model-item>

                        <a-form-model-item label="Journal"  v-show="outcome.type==4">
                            <a-input v-model="outcome.journal"></a-input>
                        </a-form-model-item>

                        <a-form-model-item label="Indexado"  v-show="outcome.type==4">
                            <a-select v-model="outcome.indexed" placeholder="Seleccione un indexador">
                                <a-select-option v-for="item in indexers" :value="item.id" :key="item.id" >
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Especifique" v-show="outcome.indexed == '5'">
                            <a-input v-model="outcome.other_indexed"></a-input>
                        </a-form-model-item>

                        <a-form-model-item label="Autor(es)" prop="author">
                            <div class="container_select_pople">
                                <select-people-component style="width: 80%" :person_id="author.id" format="full_data" v-on:handleSelectPeople="changeSelectAuthor"/>
                                <span class="container_btn_author">
                                    <a-button class="btn_author" type="primary" size="small" @click="addOutcomeAuthor()">+</a-button>
                                </span>
                            </div>
                        </a-form-model-item>

                        <div class="container_list" style="display:flex; justify-content:center">
                            <table class="author_table">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Autor</th>
                                    <th>Quitar</th>
                                </tr>
                                <tr v-for="author, index in outcome.outcome_authors" :key="index">
                                    <td>
                                        <a-select v-model="author.role" style="width: 90px">
                                            <a-select-option v-for="item in roles" :key="item.id">
                                                {{item.name}}
                                            </a-select-option>
                                        </a-select>
                                    </td>
                                    <td>
                                        <p> {{author.fullname}} </p>
                                    </td>
                                    <td>
                                        <a href="javascript:;" title="Quitar" @click="removeOutcomeAuthor(author.id)"><a-icon type="delete" /></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </a-col>
                </a-row>
                <div><p>(<span style="color:#f00">*</span>) Para añadir un entregable necesita adjuntar: Informes, Material de Avance, Diapositivas u otros.</p></div>
                <file-component :files='outcome_files' @changeFile="changeOutcomeFileList" :key="outcome_files_key">
                </file-component>
            </a-form-model>
        </a-modal>

        <a-modal title="Archivos del artefacto" :visible="showOutcomeFilesModal" width="70%" @cancel="showOutcomeFilesModal=false" cancelText="Cerra">
            <template slot="footer">
                <a-button key="back" @click="showOutcomeFilesModal = false">
                    Cerrar
                </a-button>
            </template>
            <a-form-model ref="userForm" :model="outcome" :label-col="labelCol" :wrapper-col="wrapperCol">
                <a-row>
                    <file-component :files='outcome_files' :readOnly="true" :key="outcome_files_key">
                    </file-component>
                </a-row>
            </a-form-model>
        </a-modal>

        <a-modal title="Entregables por aprobar" :visible="showAllOutcomesModal" cancelText="Cancelar" okText="Guardar"
        @ok="savePendingOutcomes"
        @cancel="showAllOutcomesModal = false"
        width="90%">
            <div class="header_nav" style="justify-content:flex-end">
                <a-col :span="4">
                    <div style="display:flex; margin-right: 2em">
                        <p style="margin-right: .5em">Año:</p>
                        <a-select v-model="outcome_filter.year" style="width: 90%" @change="onChangeYearFilter" :allowClear=true>
                            <a-select-option :value="(new Date()).getFullYear()">{{ (new Date()).getFullYear() }}</a-select-option>
                            <a-select-option :value="(new Date()).getFullYear()-1">{{ (new Date()).getFullYear()-1 }}</a-select-option>
                            <a-select-option :value="(new Date()).getFullYear()-2">{{ (new Date()).getFullYear()-2 }}</a-select-option>
                            <a-select-option :value="(new Date()).getFullYear()-3">{{ (new Date()).getFullYear()-3 }}</a-select-option>
                        </a-select>
                    </div>
                </a-col>
                <a-col :span="8">
                    <div style="display:flex; margin-right: 2em" v-show="outcome_filter.year>0">
                        <p style="margin-right: .5em">Periodo:</p>
                        <a-select v-model="outcome_filter.period_type" style="width: 90%" @change="onChangPeriodTypesOutcomeFilter" :allowClear=true>
                            <a-select-option v-for="item in PERIOD_TYPES" :value="item.id" :key="item.id" >
                                {{item.name}}
                            </a-select-option>
                        </a-select>
                        -
                        <a-select v-model="outcome_filter.period" style="width: 90%" :allowClear=true>
                            <a-select-option v-for="item in getPeriods(outcome_filter.period_type)" :value="item.id" :key="item.id">
                                {{item.name}}
                            </a-select-option>
                        </a-select>
                    </div>
                </a-col>
                <a-col :span="5">
                    <div style="display:flex; margin-right: 2em">
                        <p style="margin-right: .5em">Estados: </p>
                        <a-select v-model="outcome_filter.approved" style="width: 90%">
                            <a-select-option value="0">Pendiente</a-select-option>
                            <a-select-option value="1">Aprobado</a-select-option>
                            <a-select-option value="-1">Todos</a-select-option>
                        </a-select>
                    </div>
                </a-col>
                <a-col :span="5">
                    <div style="display:flex; margin-right: 2em">
                    <p style="margin-right: .5em">Incentivo: </p>
                    <a-select v-model="outcome_filter.incentive" style="width: 90%">
                        <a-select-option value="0">Sin incentivo</a-select-option>
                        <a-select-option value="1">Con Incentivo</a-select-option>
                        <a-select-option value="-1">Todos</a-select-option>
                    </a-select>
                    </div>
                </a-col>

                <a-col>
                    <a-button type="primary" size="small" @click="fetchPendingOutcomes">Buscar</a-button>
                </a-col>
            </div>

            <div class="outcome_pending_table">
                <table class="outcome_table" >
                    <tr>
                        <th title="Titulo del entregable">Entregable</th>
                        <th title="Autor(es)">Autores</th>
                        <th title="Fecha de Entrega">F. Entrega</th>
                        <th title="Fecha fin de la Investigación">Fin. Inv.</th>
                        <th title="Fecha de Entrega">Tipo</th>
                        <th title="Trimestre y Año">Periodo</th>
                        <th title="Cuando y quien aprobó este entregable.">Revisado</th>
                        <th title="Cuando y quien aprobó este entregable.">Aprobado</th>
                        <th title="Revisar - Coordinador">-</th>
                        <th title="Aprobar - Unidad">-</th>
                    </tr>
                    <tr v-for="outcome in outcomes" :key="outcome.id">
                        <td>
                            <a-tooltip>
                                <template slot="title">
                                {{outcome.title}}
                                </template>
                                {{outcome.name}}
                            </a-tooltip>
                        </td>
                        <td>
                            <a-tag v-for="author, index in outcome.outcome_authors" :key="index" :color=" author.role == 'TI' ? 'cyan':''">{{author.fullname}}</a-tag>
                        </td>
                        <td>{{outcome.date}}</td>
                        <td :style="getPendingTime(outcome.research_date_end)<0?'background: rgba(255,255,0,0.1)':'' "> {{outcome.research_date_init}} {{outcome.research_date_end}}
                            <span v-if="checkOutcomeInTime(outcome.research_date_end, outcome.date)<0 && outcome.type==ADVANCE_OUTCOME" title="Segun fecha fin, deberia presentar informe final" style="cursor: default"> 🔔 </span>
                        </td>
                        <td>
                            <a href="javascript:;" title="Click para ver archivos" style="text-decoration:underline" @click="viewOutcomeFiles(outcome)"> {{ getOutcomeType(outcome.type) }} {{ outcome.type==ADVANCE_OUTCOME?outcome.count:'' }} </a>
                        </td>
                        <td>{{outcome.period_type }}{{outcome.period}}-{{ formatYear(outcome.date) }} </td>
                        <td style="text-align:center">{{ formatDate(outcome.reviewed_date) }} ({{ outcome.reviewed_by_user ? outcome.reviewed_by_user : '--' }})</td>
                        <td style="text-align:center">{{ formatDate(outcome.approved_date) }} ({{ outcome.approved_by_user ? outcome.approved_by_user : '--' }})</td>
                        <td>
                            <span>
                            <input class="checkbox" type="checkbox" v-model="outcome.reviewed" @change="onChangeReviewed" v-if="outcome.reviewed == 0 || outcome.reviewed === true" title="Revisar"/>
                            </span>
                        </td>
                        <td>
                            <span v-if="user_logged.role_id == UNIT_ROLE || user_logged.role_id == ADMIN_ROLE">
                            <input class="checkbox" type="checkbox" v-model="outcome.approved" @change="onChangeApproved" v-if="outcome.approved == 0 || outcome.approved === true" title="Aprobar"/>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="margin-top:10px"> Total: {{outcomes.length}} registros. </div>
        </a-modal>

    </div>
</template>
<script>
    import moment from "moment";
    const {PERIOD_TYPES, GRADES,EXPERIENCE_RESEARCH, TESIS_RESEARCH,PROFESSOR_RESEARCH, INNOVATION_RESEARCH, FINANCINGS, FIN_COMPANY, LOCATION, INDEXED_BDS, RESEARCH_TYPES, OUTCOME_TYPES, RESEARCH_ROLES, ADMIN_ROLE,RESEARCH_ROLE,UNIT_ROLE,DGI_ROLE, COR_ROLE, FINALINFORM_OUTCOME, ARTICLE_OUTCOME, ADVANCE_OUTCOME, PROJECT_OUTCOME,PERIOD_DEFAULT, RESEARCH_STATE_ANUL, RESEARCH_STATE_NEW, DATEFORMAT} = require("../constants");//roles
    import Repository from "../repositories/RepositoryFactory";
    const ResearchRepository = Repository.get("research");
    const CategoryRepository = Repository.get("category");
    const OrganizationRepository = Repository.get("organization");
    const OutcomeRepository = Repository.get("outcome");
    const FileRepository = Repository.get("file");
    const PlanRepository = Repository.get("plan");
    const MasterRepository = Repository.get("master");
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    const outcome_types = OUTCOME_TYPES;
    const grades =GRADES;
    const type_research = RESEARCH_TYPES;
    const indexers =INDEXED_BDS;
    let gradeXType_research = [];
    const financing= FINANCINGS;
    const roles=RESEARCH_ROLES;

    const columns = [
        {title: 'Titulo',dataIndex: 'title',},
        {title: 'Autor',key: 'authorResearch', scopedSlots:{customRender: "authorResearch"},width:'220px'},
        {title: 'Fecha',key:'date_init',scopedSlots: { customRender: "date_init" },width:'110px'},
        {title: 'Tipo',key:'typeResearch', scopedSlots:{customRender: "typeResearch"},width:'40px', align:'center'},
        {title: 'Estado',key:'stateResearch', scopedSlots:{customRender: "stateResearch"},width:'84px'},
        {title: '-',key:'action',scopedSlots: { customRender: "action" },width:'65px', align:'left'},
    ];
    /*---------- */
    export default {
        data() {
            return {
                DATEFORMAT,
                isLoading: false,
                labelCol: { span: 7 },
                wrapperCol: { span: 12 },
                // :models:"xxxxx"
                research: { },
                researchlog: { },
                data:[],
                faculties: [],
                // colleges:[],
                plans: [],
                states: [],
                filter: {own_research:1},
                // consts xxxxx[]
                research_authors: [],
                objectives: [],
                outcomes: [],
                outcome_types,
                grades,
                roles,
                type_research,
                indexers,
                status,
                columns,
                financing,
                gradeXType_research,
                outcome: [],
                author: [],
                programs: [],
                listOfLinesOfAPlan: [],
                areas: [],
                groups: [],
                lines: [],
                pagination: {},
                loading: false,
                showListStatusModal:false,
                showEditStatusModal:false,
                showResearchModal:false,
                showEditOutcomeModal: false,
                showListOutcomeModal: false,
                showAllOutcomesModal: false,
                showOutcomeFilesModal: false,
                authorSelected: [],
                filter_researchs:"",
                research_logs: [],
                status:{},
                outcome_files_key:0,
                line_select_key:0,
                outcome_files:[],
                status_files_key:0,
                status_files:[],
                user_logged :{},
                TESIS_RESEARCH,EXPERIENCE_RESEARCH,PROFESSOR_RESEARCH,INNOVATION_RESEARCH,
                LOCATION,FIN_COMPANY, RESEARCH_STATE_ANUL, RESEARCH_STATE_NEW,
                PERIOD_TYPES,
                PERIOD_DEFAULT,
                newObjective: "",
                newOutcomeFiles: [],
                newStatusFiles: [],
                ADMIN_ROLE, RESEARCH_ROLE, UNIT_ROLE, DGI_ROLE, COR_ROLE,
                ARTICLE_OUTCOME,
                FINALINFORM_OUTCOME,
                ADVANCE_OUTCOME,
                outcome_filter:{},
                isExternal: false,
                hasIncentive: false,
                facultiesWithCategoriesChildren: [], // facultades con sus categorias y subcategoria
                professionalSchoolsOfAFaculty: [], // escuelas profesionales de una facultad
            }
        },
        components: {
            Loading
        },
        async mounted() {
            this.user_logged = JSON.parse(localStorage.getItem("user"));
            this.isLoading = true;
            await this.listgroupsFulls(); // obtengo las facultades, areas, grupos y lineas
            await this.listFaculties(); // obtengo las facultades
            await this.listPlans();
            // await this.listPrograms(this.plans[0].id);
            await this.listStates();
            await this.fetch();
            this.isLoading = false;
        },
        methods:{
            onChangeYearFilter(e) {
                if(this.outcome_filter.year==0) {
                    this.outcome_filter.year = null;
                    this.outcome_filter.period = null;
                    this.outcome_filter.period_type = null;
                }
            },
            viewOutcomeFiles(outcome, outcome_id) {
                this.outcome = outcome;
                this.listOutcomeFiles(this.outcome.id);
                this.showOutcomeFilesModal = true;
            },
            getPeriods(period_type) {
                let p = [];
                if(period_type) p = this.PERIOD_TYPES.find(e=>e.id==period_type).periods;
                return p;
            },
            async fetch(params = {}) {
                try {
                    if( this.filter.own_research ) params.author_id = this.user_logged.people_id;
                    if( this.filter.in_finish ) params.in_finish = this.filter.in_finish;
                    if( this.filter.in_work ) params.in_work = this.filter.in_work;
                    if (this.user_logged.role_id == RESEARCH_ROLE) params.author_id = this.user_logged.people_id;
                    else if (this.user_logged.role_id == UNIT_ROLE && !this.filter.own_research) params.faculty_id = this.user_logged.faculty_id;
                    else if (this.user_logged.role_id == COR_ROLE && !this.filter.own_research ) params.group_id = this.user_logged.group_id;
                    if (this.user_logged.role_id ==DGI_ROLE) delete params.author_id;

                    params.text=this.filter.text;
                    if(this.filter.range?.length) {
                    params.from=this.filter.range[0].format("YYYY-MM-DD");
                    params.to=this.filter.range[1].format("YYYY-MM-DD");
                    }
                    if(this.filter.state==0) delete params.state;
                    else params.state=this.filter.state;
                    if(this.filter.type_research==0) delete params.type_research;
                    else params.type_research=this.filter.type_research;

                    this.loading = true;
                    let {data} =  await ResearchRepository.list(params);
                    this.loading = false;
                    for(let i = 0; i < data.data.length; i++) {
                        data.data[i].research_authors=[];
                        let authors = data.data[i].authors;
                        let authors_ = authors.split(",");
                        for(let j = 0; j < authors_.length; j++) {
                            let author_ = authors_[j].split("|");
                            let id=author_[0];
                            let fullname=author_[1];
                            let role=author_[2];
                            let author={ id:id, fullname:fullname, role:role };
                            data.data[i].research_authors.push(author);
                        }
                    }
                    this.data = data.data;

                    const pagination = {...this.pagination};
                    pagination.total=data.total;
                    this.pagination=pagination;
                } catch (error) {
                    this.error(error);
                }
            },
            getDurationTime(date_init, date_end) {
                let time = moment.duration( moment(date_end).diff( moment(date_init) ) ).asMonths();
                return time;
            },
            getPendingTime(date_end) {
                let time = moment.duration( moment(date_end).diff( moment() ) ).asMonths();
                return time;
            },
            checkOutcomeInTime(outcome_date, research_date_end) {
                let time = this.getDurationTime(research_date_end, outcome_date);
                return time;
            },
            onChangeResearchInWork(a) {
                this.filter.in_work = a.target.checked ? 1 : 0;
                this.fetch();
            },
            onChangeResearchInFinish(a) {
                this.filter.in_finish = a.target.checked ? 1 : 0;
                this.fetch();
            },
            onChangeOwnResearch(a) {
                this.filter.own_research = a.target.checked ? 1 : 0;
                this.fetch();
            },
            checkOwner(research){
                return (research.research_authors && research.research_authors.find(e=>e.id==this.user_logged.people_id)) ? true : false
            },
            filterOption(input, option) {
                return (
                    option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
                );
            },
            suggestPeriod(period_type ) {
                return period_type.id == 'M' ? moment().month()+1 : (period_type.id =='T' ? moment().quarter() : period_type.periods[0].id);
            },
            onChangPeriodTypesOutcomeFilter(a) {

                const period_type = this.PERIOD_TYPES.find(e=>e.id == this.outcome_filter.period_type);
                const period = period_type?this.suggestPeriod(period_type):null;
                let filter = { ...this.outcome_filter };
                filter.period = period;
                this.outcome_filter = filter;
            },
            fetchAllPendingOutcomes(){
                this.outcome_filter = {year:new Date().getFullYear(), approved:'0'};
                this.outcome_filter.external = 0;
                const params = { ...this.outcome_filter, xpage: 100 };
                if (this.user_logged.role_id == COR_ROLE) params.group_id = this.user_logged.group_id;
                this.listoutcomes(params);
            },
            fetchPendingOutcomes() {
                if (this.outcome_filter.approved == -1) delete this.outcome_filter.approved;
                if (this.outcome_filter.incentive == -1) delete this.outcome_filter.incentive;
                this.outcome_filter.public = 1;
                this.outcome_filter.external = 0;
                const params = {...this.outcome_filter, xpage:100};
                if (this.user_logged.role_id == COR_ROLE) params.group_id = this.user_logged.group_id;
                this.listoutcomes(params);
            },
            listPendingOutcomes() {
                let period_type = PERIOD_TYPES.find(e=>e.id == this.PERIOD_DEFAULT);
                const period =  this.suggestPeriod(period_type);
                this.outcome_filter = {year:new Date().getFullYear(), period_type:period_type.id, period:period, approved:'-1', public:1, incentive:'-1'};
                this.outcome_filter.external = 0;
                const params = {...this.outcome_filter, xpage:100};
                this.listoutcomes(params);
                this.fetchAllPendingOutcomes();
                this.showAllOutcomesModal = true;
            },
            async savePendingOutcomes() {
                this.loading = true;
                let payload = {outcomes:this.outcomes.filter(e => e.approved === true).map(e => { return {id:e.id, approved_by:this.user_logged.people_id} })};
                let payloadReviewed = {outcomes:this.outcomes.filter(e => e.reviewed === true).map(e => { return {id:e.id, reviewed_by:this.user_logged.people_id} })};
                if (payload.outcomes.length > 0)  await OutcomeRepository.save_pending_outcomes(payload);
                if (payloadReviewed.outcomes.length > 0) await OutcomeRepository.save_reviewed_pending_outcomes(payloadReviewed);
                this.showAllOutcomesModal = false;
                this.loading = false;
                this.success();
            },
            async listStates() {
                try {
                    let { data } = await MasterRepository.list_states({});
                    this.states = data;
                } catch (error) {
                    this.error(error);
                }
            },
            filterGrade(){
                if(this.research.type_research == TESIS_RESEARCH || this.research.type_research == EXPERIENCE_RESEARCH){
                    this.gradeXType_research = grades;
                    for (let i = 0; i < gradeXType_research.length; i++) {
                    this.research.grade = this.gradeXType_research[i].id;
                    }
                }else{
                    this.gradeXType_research = [];
                    this.research.grade  = undefined;
                }
            },
            changeSelectAuthor(person_data){
                this.authorSelected = {...person_data, fullname:person_data.name + ' ' + person_data.lastname};
            },
            formatDate(date){
                return date ? moment(date, 'DD/MM/YYYY').format('DD/MM/YYYY') : '';
            },
            formatYear(date){
                return date ? moment(date, 'DD/MM/YYYY').format('YYYY') : '--';
            },
            onChangeExternal(a){
                this.isExternal = a.target.checked+"";
                return this.isExternal;
            },
            onChangePeriodType(a){
                const period_type = this.PERIOD_TYPES.find(e=>e.id == this.outcome.period_type);
                this.outcome.period = this.suggestPeriod(period_type);
            },
            onChangeIncentive(e){
                this.hasIncentive = e.target.checked+"";
                return this.hasIncentive;
            },
            onChangeApproved(e){
                return e.target.checked+"";
            },
            onChangeReviewed(e){
                return e.target.checked+"";
            },
            onChangePublic(e) {
                return e.target.checked+"";
            },
            addAuthor() {
                try {
                    if (this.authorSelected == "") throw ("Seleccione un autor");
                    if (this.research_authors.filter(e => e.id == this.authorSelected.id).length > 0) throw ("El autor ya esta agregado");
                    this.research_authors.push({
                        id: this.authorSelected.id+"",
                        fullname: this.authorSelected.name + ' ' +this.authorSelected.lastname,
                        role:'OR',
                    });
                } catch (error) {
                    this.error(error);
                }
            },
            addObjective() {
                try {
                    if (this.newObjective == "") throw ("Escriba un objetivo");
                    let latestObjective_id = this.research.objectives.length?this.research.objectives[ this.research.objectives.length-1 ].id:0;
                    this.research.objectives.push({
                        id:latestObjective_id+1,
                        name:this.newObjective
                    })
                    this.newObjective = "";
                } catch (error) {
                    this.error(error);
                }
            },
            removeAuthor(id) {
                let authors = [...this.research_authors];
                this.research_authors = authors.filter(e=>e.id != id);
            },
            removeObjective(id) {
                this.research.objectives = authors.filter(e=>e.id != id);
            },
            async onChangeFaculties() {
                this.areas = [];
                this.groups = [];
                this.lines = [];
                this.research.organization_id = null;
                this.research.area_id = null;
                this.research.group_id = null;
                this.research.line_id = null;
                this.listSchoolsOfAFaculty(); // obtengo las escuelas de una facultad
                try {
                    // this.professionalSchoolsOfAFaculty = this.faculties.find(e=>e.id == this.research.faculty_id).children.map(e=>({id:e.id,name:e.name}));
                    // this.research.organization_id = this.professionalSchoolsOfAFaculty[0].id;
                    // await this.onChangePlan();
                    // this.onChangeGroup();
                    await this.selectAreas();
                } catch (error) {
                    this.error(error);
                }
            },
            async onChangePlan() {
                // await this.listPrograms(this.research.plan);
                // const r = {...this.research};
                // if (this.groups.length) r.group_id = this.groups[0].id;
                // else r.group_id = null;
                // this.research = r;
                this.areas = [];
                this.groups = [];
                this.lines = [];
                this.research.area_id = null;
                this.research.group_id = null;
                this.research.line_id = null;
                try {
                    await this.selectAreas();
                } catch (error) {
                    this.error(error);
                }
            },
            async listPlans() {
                const { data } = await PlanRepository.list({xpage:50});
                this.plans = data.data;
            },
            async listFaculties() {
                const{data} = await OrganizationRepository.list();
                this.faculties = data;
            },
            onChangeArea() {
                this.groups = [];
                this.lines = [];
                this.research.group_id = null;
                this.research.line_id = null;
                this.selectGroups();
            },
            onChangeGroup() {
                try {
                    this.lines = [];
                    this.research.line_id = null;
                    this.selectLines();
                } catch (error) {
                    this.error(error)
                }
            },
            onChangeLine(e) {
                this.line_select_key++;
            },
            async listgroupsFulls() {
                try {
                    let { data } =  await CategoryRepository.list({});
                    this.facultiesWithCategoriesChildren = data;
                } catch (error) { this.error(error); }
            },
            async listPrograms(plan_id) {
                const { data } = await CategoryRepository.list_programs_and_lines({plan_id});
                this.listOfLinesOfAPlan = data;
            },
            getOutcomeType(id){
                let outcomeTypeData = this.outcome_types.find(e=>e.id==id);
                let name = outcomeTypeData ? outcomeTypeData.name : "---";
                return name;
            },
            getStatusName(id){
                let stateData = this.states.find(item => item.id == id);
                let name = stateData ? stateData.name : "";
                return name;
            },
            getStatusIcon(id){
                let icon = '';
                switch (id) {
                    case '1':
                    icon = '🎓';
                    break;
                    case '2':
                    icon = '👨‍🔬';
                    break;
                    case '3':
                    icon = '✍️';
                    break;
                    case '4':
                    icon = '🚀';
                    break;
                }
                return icon;
            },
            filterOption(input, option) {
                return (
                    option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
                );
            },
            error (message) {
                this.$message.error(message||'Error al procesar');
            },
            success (message) {
                this.$message.success(message||'Proceso Correcto');
            },
            suggestOutcomeType(outcomes, period_type) {
                let type = null;
                if(outcomes.length ){
                    const last = outcomes[0];
                    if(last.type == PROJECT_OUTCOME) type =ADVANCE_OUTCOME;
                    else if (last.type == FINALINFORM_OUTCOME) type = ARTICLE_OUTCOME;
                    else if( period_type.id=='M'  ) type = outcomes.filter(e=>e.type == ADVANCE_OUTCOME ).length==11? FINALINFORM_OUTCOME: ADVANCE_OUTCOME;
                    else if (period_type.id=='T') type =  outcomes.filter(e=>e.type == ADVANCE_OUTCOME ).length==3? FINALINFORM_OUTCOME: ADVANCE_OUTCOME;
                    else type=null;
                }else{
                    type = PROJECT_OUTCOME;
                }
                return type;
            },
            addOutcome() {
                this.showEditOutcomeModal = true;
                const research = this.data.find(e=> e.id == this.research.id);
                const period_type = this.PERIOD_TYPES.find( e=>e.id ==this.PERIOD_DEFAULT );
                const period =  period_type.id =='M' ? moment().month()+1 : (period_type.id =='T' ? moment().quarter() : period_type.periods[0].id);
                let outcome_authors = research.research_authors;
                this.outcome_files = [];
                this.outcome_files_key++;
                this.outcome = {
                    type: this.suggestOutcomeType(this.outcomes, period_type),
                    name:research.title,
                    period_type: period_type.id,
                    date:moment().format("YYYY-MM-DD"),
                    period: period,
                    public:true,
                    outcome_authors: outcome_authors,
                }
            },
            addOutcomeAuthor(){
                try {
                    if (this.authorSelected == "") throw ("Seleccione un autor");
                    this.outcome.outcome_authors.push({
                        id:this.authorSelected.id+"",
                        fullname: this.authorSelected.name + ' ' +this.authorSelected.lastname,
                        role:'OR',
                    });
                } catch (error) {
                    this.error(error);
                }
            },
            removeOutcomeAuthor(id){
                this.outcome.outcome_authors = this.outcome.outcome_authors.filter(e=>e.id != id);
            },
            async listoutcomes(params){
                if (!this.user_logged.role_id == RESEARCH_ROLE) params.public = 0;
                else if (this.user_logged.role_id == UNIT_ROLE) params.faculty_id = this.user_logged.faculty_id;

                const {data} = await OutcomeRepository.list(params);
                for(let i = 0; i < data.data.length; i++){
                    data.data[i].outcome_authors=[];
                    let authors = data.data[i].authors;
                    let authors_ = authors.split(",");
                    for(let j = 0; j < authors_.length; j++){
                        let author_ = authors_[j].split("|");
                        let id=author_[0];
                        let fullname=author_[1];
                        let role=author_[2];
                        let author={ id:id, fullname:fullname, role:role };
                        data.data[i].outcome_authors.push(author);
                    }
                }
                this.outcomes = data.data;
            },
            async showOutcomeModal(research){
                this.research = research;
                this.outcomes=[];
                this.listoutcomes({research_id:this.research.id, xpage:100});
                this.showListOutcomeModal = true;
            },
            async saveOutcome() {
                try {
                    if (!this.outcome.type) throw ("Tipo es obligatorio");
                    if (!this.outcome.name) throw ("Nombre es obligatorio");
                    if (!this.outcome.period) throw ("Periodo es obligatorio");
                    if (this.outcome.outcome_authors.filter(e=>!e.role).length > 0) throw ("Rol del autor es obligatorio");
                    if (this.outcome.outcome_authors.filter(e=>e.role=='TI').length!=1) throw ("Debe haber un Titular");
                    if (!this.outcome_files.length) throw ("Necesitas agregar un archivo");
                    this.showEditOutcomeModal = false;
                    this.loading = true;
                    let payload = this.outcome;
                    payload.research_id = this.research.id;
                    payload.files = this.newOutcomeFiles;
                    payload.outcome_authors = this.outcome.outcome_authors;
                    payload.public = this.outcome.public ? 1 : 0;
                    delete payload.authors;
                    delete payload.approved_date;
                    delete payload.reviewed_date;
                    const response =  await OutcomeRepository.save_outcome(payload);
                    this.outcome= {id: response.data.id};
                    this.saveOutcomeFiles();
                    this.showOutcomeModal(this.research);
                    this.success();
                    this.loading = false;
                } catch (error) {
                    this.error(error);
                }
            },
            async editOutcome(id) {
                this.outcome = { ...this.outcomes.find(e=>e.id==id) };
                this.outcome.date = moment(this.outcome.date, 'DD/MM/YYYY');
                this.outcome_authors = this.outcome.outcome_authors;
                await this.listOutcomeFiles(this.outcome.id)
                this.showEditOutcomeModal = true;
            },
            async saveOutcomeFiles() {
                for(let i = 0; i < this.newOutcomeFiles.length; i++) {
                    let file = this.newOutcomeFiles[i];
                    file.reference_id = this.outcome.id;
                    file.type = "outcome";
                    delete file.id;
                    await FileRepository.save(file);
                }
            },
            async removeOutcome(id){
                let payload = this.outcomes.find(item => item.id == id);
                this.loading = true;
                await OutcomeRepository.delete(payload.id);
                this.success();
                this.showOutcomeModal(this.research);
                this.loading = false;
            },
            async listOutcomeFiles(outcome_id){
                try {
                    let { data } = await FileRepository.list({reference_id: outcome_id, type:"outcome" });
                    this.outcome_files = data.data;
                    this.outcome_files_key++;
                } catch (error) {
                    this.error(error);
                }
            },
            async changeOutcomeFileList(filelist){
                this.resolveOutcomeDeletedFiles(filelist)
                this.newOutcomeFiles = filelist.filter(e=>e.id < 1);
            },
            async resolveOutcomeDeletedFiles(updated_list){
                const current_list = updated_list.map(e=>e.id);
                const old_list = this.outcome_files;
                for(let i = 0; i < old_list.length; i++){
                    if( ! current_list.includes(old_list[i].id)){
                        await FileRepository.delete(old_list[i].id);
                    }
                }
            },
            async changeStatus(id) {
                let researchData = this.data.find(item => item.id == id);
                this.research = { ...researchData };
                this.showListStatusModal=true;
                const {data} = await ResearchRepository.listStatus({research_id:id});
                this.research_logs = data;
            },
            addStatus(){
                this.showEditStatusModal = true;
                this.status = {};
                this.status_files = [];
                this.status_files_key++;
            },
            async saveStatus() {
                try {
                    if (this.user_logged.role_id == RESEARCH_ROLE) throw ("No tiene permisos para esta accion");
                    if (!this.status.new_status_id) throw ("Necesitas seleccionar un estado");
                    if (!this.status_files.length) throw ("Necesitas agregar un archivo");
                    this.showEditStatusModal = false;
                    let payload = {research_id:this.research.id, new_status_id:this.status.new_status_id};
                    if(this.status.id) payload.id = this.status.id;
                    payload.user_id = this.user_logged.id;
                    const response = await ResearchRepository.saveStatus(payload);
                    this.status = {id: response.data.id};
                    this.saveStatusFiles();
                    this.success();
                    this.changeStatus(this.research.id);
                    this.loading = false;
                    this.fetch();
                } catch (error) {
                    this.error(error);
                }
            },
            async changeStatusFileList(filelist){
                this.resolveStatusDeletedFiles(filelist);
                this.newStatusFiles = filelist.filter(e=>e.id < 1);
            },
            async resolveStatusDeletedFiles(updated_list){
                const current_list = updated_list.map(e=>e.id);
                const old_list = this.status_files;
                for(let i = 0; i < old_list.length; i++){
                    if(!current_list.includes(old_list[i].id)) {
                        await FileRepository.delete(old_list[i].id);
                    }
                }
            },
            async saveStatusFiles(){
                for(let i=0; i<this.newStatusFiles.length;i++){
                    let file = this.newStatusFiles[i];
                    file.reference_id = this.status.id;
                    file.type = "status";
                    delete file.id;
                    await FileRepository.save(file);
                }
            },
            async editStatus(status){
                this.status = status;
                this.status_files = [];
                this.status_files_key++;
                this.status.new_status_id += "";
                this.showEditStatusModal = true;
                await this.listStatusFiles(this.status.id);
            },
            async listStatusFiles(status_id){
                try {
                    let { data } = await FileRepository.list({reference_id: status_id, type:"status" });
                    this.status_files = data.data;
                    this.status_files_key++;
                } catch (error) {
                    this.error(error);
                }
            },
            async newResearch(){
                try {
                    if (this.user_logged.faculty_id == null) throw ("No tiene facultad asignada, contacte a su administrador.");
                    if (this.user_logged.group_id == null) throw ("No tiene grupo asignado, contacte a su administrador.");
                    let allowEdit = !(this.user_logged.role_id == RESEARCH_ROLE || this.user_logged.role_id==UNIT_ROLE) ? true : false;
                    let notAllowEditResearchUnit = this.user_logged.role_id == UNIT_ROLE;

                    this.research = {
                        type_research:this.user_logged.type=='D'?"2":"1",
                        // date_init: moment().format('YYYY-MM-DD'),
                        date_init: moment(),
                        faculty_id:this.user_logged.faculty_id,
                        // date_end: moment().add(1,'y').format('YYYY-MM-DD'),
                        date_end: moment().add(1,'y'),
                        research_authors: [],
                        objectives: [],
                        plan:this.plans.filter(e=>e.active==1)[0].id,
                        location: this.LOCATION,
                        fin_company: "Universidad Nacional Agraria de la Selva",
                        fin_type: '3',
                        incentive: this.user_logged.type == 'D' ? 1 : 0,
                        grade: '1',
                        budget: 0,
                        allowEdit: allowEdit,
                        notAllowEditResearchUnit: notAllowEditResearchUnit,
                    };
                    this.research_authors = [{
                        id:this.user_logged.people_id+"",
                        fullname: this.user_logged.name + ' ' +this.user_logged.lastname,
                        role:'TI',
                    }];
                    this.listSchoolsOfAFaculty();
                    await this.selectAreas();
                    this.showResearchModal=true;
                } catch (error) {
                    this.error(error);
                }
            },
            closeResearch() {
                this.showResearchModal=false;
            },
            async save() {
                try {
                    if (this.research.readOnly) throw ("No tiene permisos para cambiar datos");
                    if (this.user_logged.faculty_id == null) throw ("Necesitas estar registrado a una escuela, contacte a su administrador");
                    if (this.isExternal=='true' && this.hasIncentive=='true') throw ("No puede tener incentivo si es una investigación externa");
                    if (this.research.grade == 2 && this.hasIncentive=='true') throw ("No puede tener incentivo si es una investigación de Posgrado");
                    if (!this.research.title) throw ("Titulo es obligatorio");
                    if (!this.research.objectives.length) throw ("Necesitas por lo menos un objetivo");
                    if (!this.research.organization_id) throw ("Escuela es obligatorio");
                    if (!this.research.area_id) throw ("Area es obligatorio");
                    if (!this.research.group_id) throw ("Grupo es obligatorio");
                    if (!this.research.line_id) throw ("Linea es obligatorio");
                    if (this.research_authors.filter(e=>!e.role).length > 0) throw ("Rol del autor es obligatorio");
                    if (this.research_authors.filter(e=>e.role=='TI').length!=1) throw ("Debe haber un Titular");
                    this.showResearchModal=false;
                    this.loading=true;
                    let payload = this.research;
                    payload.objectives = JSON.stringify(this.research.objectives);
                    payload.user_id = this.user_logged.id;
                    payload.research_authors = this.research_authors;
                    payload.incentive = this.research.incentive ? 1 : 0;
                    payload.external = this.research.external ? 1 : 0;
                    delete payload.authors;
                    await ResearchRepository.save(payload);
                    this.loading=false;
                    this.success();
                    this.fetch()
                } catch (error) {
                    this.error(error);
                }
            },
            listSchoolsOfAFaculty() { // obtengo las escuelas de una facultad
                this.professionalSchoolsOfAFaculty = this.faculties.find( e => e.id == this.research.faculty_id).children.map(e=>({id:e.id,name:e.name}));
            },
            async editResearch(id, params={}) {
                if(this.user_logged.role_id == RESEARCH_ROLE || this.user_logged.role_id == ADMIN_ROLE) params.author_id = this.user_logged.people_id;
                if(params.readOnly) this.research.readOnly = true;
                let allowEdit = !(this.user_logged.role_id == RESEARCH_ROLE || this.user_logged.role_id==UNIT_ROLE) ? true : false ;
                let notAllowEditResearchUnit = this.user_logged.role_id == UNIT_ROLE;

                let d = this.data.find(item => item.id == id);
                this.research = { ...d };
                this.research.allowEdit = allowEdit;
                this.research.notAllowEditResearchUnit = notAllowEditResearchUnit;
                this.research.date_init = moment(d.date_init, 'DD/MM/YYYY');
                this.research.date_end = moment(d.date_end, 'DD/MM/YYYY');
                this.isExternal = d.external ? 'true' : 'false';
                this.hasIncentive = d.incentive ? 'true' : 'false';

                this.research.plan = parseInt(this.research.plan);
                this.research.objectives = this.research.objectives && this.research.objectives.indexOf('[')>=0 ? JSON.parse(this.research.objectives) : [{id:1, name:this.research.objectives}];
                this.research_authors= this.research.research_authors;
                //
                this.research.faculty_id = this.faculties.find(e=>e.children.find(f=>f.id==this.research.organization_id)).id;
                this.listSchoolsOfAFaculty(); // obtengo las escuelas de una facultad
                this.selectAreasGroupsAndLines();
                this.showResearchModal = true;
            },
            async remove(id) {
                try {
                    let payload = this.data.find(item => item.id == id);
                    this.loading = true;
                    await ResearchRepository.delete(payload.id)
                    this.success();
                    this.fetch();
                } catch (error) {
                    this.error(error);
                }
            },
            handleTableChange(pagination, filters, sorter) {
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
            selectLines() {
                const group = this.groups.find(e=>e.id == this.research.group_id);
                group.children.forEach(line => {
                    const isPartOfTheLine = this.listOfLinesOfAPlan.some(lineOfAPlan =>
                        lineOfAPlan.id === line.id
                    );
                    if (isPartOfTheLine) {
                        this.lines.push(line);
                    }
                });
            },
            selectGroups() {
                const area = this.areas.find(e=>e.id == this.research.area_id);
                area.children.forEach(group => {
                    const isPartOfTheGroup = group.children.some(line =>
                        this.listOfLinesOfAPlan.some(lineOfAPlan =>
                            lineOfAPlan.id === line.id
                        )
                    );
                    if (isPartOfTheGroup) {
                        this.groups.push(group);
                    }
                });
            },
            async selectAreas() {
                const faculty = this.facultiesWithCategoriesChildren.find(e=>e.id == this.research.faculty_id);
                await this.listPrograms(this.research.plan); // obtengo las lineas de un plan
                const researchAreaId = this.research.area_id;
                faculty.children.forEach(area => {
                    const isPartOfTheArea = area.children.some(group =>
                        group.children.some(line =>
                            this.listOfLinesOfAPlan.some(lineOfAPlan =>
                                lineOfAPlan.id === line.id
                            )
                        )
                    );
                    if (isPartOfTheArea || area.id === researchAreaId) {
                        this.areas.push(area);
                    }
                });
            },
            async selectAreasGroupsAndLines() {
                let faculty = this.facultiesWithCategoriesChildren.find(e=>e.id == this.research.faculty_id);
                await this.listPrograms(this.research.plan); // obtengo las lineas de un plan
                this.areas = [];
                this.groups = [];
                this.lines = [];
                let isPartOfTheArea = false;
                let count = 0;
                faculty.children.forEach(area => {
                    for(let i = 0; i < area.children.length; i++) {
                      isPartOfTheArea = false;
                      for(let j = 0; j < area.children[i].children.length; j++) {
                        const foundLine = this.listOfLinesOfAPlan.find(line=>line.id==area.children[i].children[j].id);
                        if(foundLine && area.children[i].id === this.research.group_id) {
                          this.lines.push({id:area.children[i].children[j].id, name:area.children[i].children[j].name, type:area.children[i].children[j].type});
                          if(count == 0) {
                            this.areas.push(area);;
                            count++;
                          }
                        } else if(foundLine && area.id != this.research.area_id) {
                          this.areas.push(area);
                          isPartOfTheArea = true;
                          break;
                        }
                      }
                      if (area.id === this.research.area_id) {
                        this.groups.push(area.children[i]);
                      }
                      if(area.id != this.research.area_id && isPartOfTheArea){
                        break;
                      }
                    }
                });
            }
        }
    }
</script>

<style scoped>
    .list_objectives{
        margin-bottom: 1em;
    }
    .checkbox {
        width: 1.3em;
        height: 1.3em;
    }
    .header_nav{
        display: flex;
        margin-bottom: 1.8em;
        margin-top: 1em;
        align-items: center;
    }

    .btnOutcome{
        display: flex;
        justify-content: flex-end;
        padding: 0 0 2em 0;
    }
    .btnStatus{
        display: flex;
        justify-content: flex-end;
        padding: 0 0 2em 0;
    }
    /* Inicio de estilos para el Scrollbar de la tabla*/
    .outcome_pending_table{
        max-height: 500px;
        overflow-y: scroll;
    }
    /* Tamaño del scroll */
    .outcome_pending_table::-webkit-scrollbar {
        width: 8px;
    }

    /* Estilos barra (thumb) de scroll */
    .outcome_pending_table::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .outcome_pending_table::-webkit-scrollbar-thumb:active {
        background-color: #999999;
    }

    .outcome_pending_table::-webkit-scrollbar-thumb:hover {
        background: #b3b3b3;
        box-shadow: 0 0 2px 1px rgba(0, 0, 0, 0.2);
    }

    /* Estilos track de scroll */
    .outcome_pending_table::-webkit-scrollbar-track {
        background: #e1e1e1;
        border-radius: 4px;
    }

    .outcome_pending_table::-webkit-scrollbar-track:hover,
    .outcome_pending_table::-webkit-scrollbar-track:active {
        background: #d4d4d4;
    }
    /* Fin de estilos para el ScrollBar */
    .outcome_table{
        width: 100%;
    }
    .outcome_table tr{
        border:1px solid rgb(230, 230, 230);
    }
    .outcome_table tr td{
        align-items: center;
        align-content: center;
    }
    .outcome_table tr th{
        background: #eee;

    }
    .outcome_table tr td,.outcome_table tr th {
        padding-left: 16px;
        line-height: 25px;
    }
    .column_action4{
        align-content: flex-start;
    }
    .main_header{
        padding-left: 1em;
    }
    .container_title_form{
        display: flex;
        align-items: center;
        padding-bottom: 2.5em;
        padding-top: 2em;
    }
    .title_form{
        font-family: inherit;
        padding-right: 1em;
    }

    .container_location_form{
        display: flex;
        align-items: center;
        padding-bottom: 2em;
        padding-top: 1em;
    }
    .location_form{
        font-family: inherit;
        padding-right: 1em;
    }

    .container_select_pople{
        display: flex;
        flex-direction: row;
    }
    .container_btn_author{
        padding-left: 1em;
    }
    .list{
        list-style: none;
        margin: auto 0;
    }
    .list_item{
        margin: 0 auto;
    }

    .container_list{
        display:flex;
        justify-content:center;
    }
    .author_table{
        width:85%;
    }
    .author_table tr{
        border:1px solid rgb(230, 230, 230);
    }
    .author_table tr th{
        background: #eee;
    }
    .author_table tr td,.author_table tr th {
        padding-left: 8px;
        line-height: 35px;
    }
    .author_table tr td p{
        margin: auto;
    }
    .header_table{
        display: flex;
        justify-content: center;
    }
    .card_logs {
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        align-items: center;
        background: #eee;
        padding: 5px;
    }
    .container_log_arrow{
        display:flex;
        align-items:center;
    }
    .card_logs p{
        margin-bottom: 0;
        text-align: start;
    }
    .card_log{
        display: inline;
        color: #0586b9;
        background: #fff;
        flex-wrap: wrap;
        width: 200px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 1px 1px 3px #ccc;
        padding: 10px 0 10px 10px;
        background: #ffffff;
        transition: all 1s ease;
    }
    .carg_log:hover{
        background: #05CAF1;
        color: #000;
    }
</style>
