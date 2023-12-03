<template>
    <div style="padding:10px 20px 10px 10px">
        <div class="vld-parent">
            <loading :active.sync="isLoading"
                    :can-cancel="false"
                    :is-full-page="true"
                    color="#1890ff"
            ></loading>
        </div>

        <div style="margin-bottom: 1.8em; display: flex; align-items: center">
            <a-col :span="14">
                <div>
                    <h4>Personas <a-button type="primary" size="small" @click="newPerson()" title="Nuevo">Nuevo</a-button></h4>
                </div>
            </a-col>
            <a-col :span="10">
                <a-input-search
                    style="width:100%"
                    v-model="filter.search"
                    placeholder="Buscar ..."
                    enter-button="Buscar"
                    size="large"
                    @search="fetch()"
                    allowClear
                />
            </a-col>
        </div>

        <a-table :columns="columns" :row-key="record => record.id"
            :data-source="data"
            :pagination="pagination"
            :loading="loading"
            @change="handleTableChange">

            <span slot="type" slot-scope="text,record" :title="record.id">
                {{ getTypeName(record.type) }} {{ record.status==0?'(Inactivo)':'' }}
            </span>
            <span slot="action" slot-scope="text,record">
                <a href="javascript:;" @click="viewDetails(record.id)" title="Usuario" v-if="record.user_id && user_logged.role_id==ADMIN_ROLE"><a-icon type="user" /></a>
                <a-divider type="vertical" />
                <a href="javascript:;" @click="edit(record.id)" title="Editar"><a-icon type="edit" /></a>
                <a-divider type="vertical" />
                <a-popconfirm v-if="data.length" title="Seguro?" @confirm="() => remove(record.id)" ok-text="Si" cancel-text="No">
                    <a href="javascript:;" title="Eliminar"><a-icon type="delete" /></a>
                </a-popconfirm>
            </span>
        </a-table>
        <p>Total: {{pagination.total?pagination.total:0}} registros. </p>

        <a-modal title="Persona" :visible="showModal" cancelText="Cancelar" :key="showModal" okText="Guardar" @ok="save" @cancel="showModal = false" width="70%">
            <a-form-model ref="userForm" :model="person" :label-col="labelCol" :wrapper-col="wrapperCol">
                <a-row>
                    <a-col class="gutter-row" :span="12">
                        <a-form-model-item label="Nombre" prop="name">
                            <a-input v-model="person.name" />
                        </a-form-model-item>

                        <a-form-model-item label="Apellidos" prop="lastname">
                            <a-input v-model="person.lastname" />
                        </a-form-model-item>

                        <a-form-model-item label="DNI" prop="dni">
                            <a-input v-model="person.dni"/>
                        </a-form-model-item>

                        <a-form-model-item label="Email" prop="email">
                            <a-input v-model="person.email"/>
                        </a-form-model-item>

                        <a-form-model-item label="Rol" prop="role">
                            <a-select v-model="person.role_id" placeholder="Seleccione un rol">
                                <a-select-option v-for="item in roles" :value="item.id" :key="item.id">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Estamento" prop="statement">
                            <a-select v-model="person.type" placeholder="Seleccione un estamento" @change="filterStatement">
                                <a-select-option v-for="item in types" :value="item.id" :key="item.id">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Condición" prop="condition">
                            <a-select v-model="person.condition" placeholder="Seleccione una condicion">
                                <a-select-option v-for="item in conditions" :value="item.id" :key="item.id">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>
                    </a-col>

                    <a-col class="gutter-row" :span="12">
                        <a-form-model-item label="Facultad" prop="faculties">
                            <a-select show-search option-filter-prop="children" v-model="person.faculty_id" placeholder="Seleccione una Facultad"  @change="onChangeFaculties()">
                                <a-select-option v-for="item in faculties" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Escuela" prop="organization">
                            <a-select v-model="person.organization_id" placeholder="Seleccione una Escuela" >
                                <a-select-option v-for="item in professionalSchoolsOfAFaculty" :value="item.id" :key="item.id" :title="item.name">
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Area" prop="area">
                            <a-select show-search option-filter-prop="children" v-model="person.area_id" placeholder="Seleccione un area" @change="onChangeArea()">
                                <a-select-option v-for="item in areas" :value="item.id" :key="item.id" >
                                    {{item.name}}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Grupo" prop="group">
                            <a-select show-search option-filter-prop="children" v-model="person.group_id" placeholder="Seleccione un grupo" @change="onChangeGroup()">
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

                        <a-form-model-item label="Grado" prop="grade">
                            <a-select v-model="person.degree" placeholder="Seleccione una grado">
                                <a-select-option v-for="item in degrees" :value="item.id" :key="item.id">
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Especifique" prop="other" v-show="person.degree == 'O'">
                            <a-input v-model="person.other_degree" />
                        </a-form-model-item>

                        <a-form-model-item label="Activo" prop="status">
                            <input class="checkbox" type="checkbox" v-model="person.status" @change="onChangePersonStatus" />
                        </a-form-model-item>
                    </a-col>
                </a-row>
            </a-form-model>
        </a-modal>

        <a-modal title="Detalles" :visible="showUserDetailsModal" cancelText="Cancelar" okText="Guardar" @ok="saveDetails" @cancel="showUserDetailsModal = false">
            <a-form-model ref="userForm" :model="user" :label-col="labelCol" :wrapper-col="wrapperCol">
                <a-row>
                    <a-col class="gutter-row" :span="24">
                        <a-form-model-item label="Nombre" prop="condition">
                            <a-input v-model="user.fullname" disabled/>
                        </a-form-model-item>
                        <a-form-model-item label="Email" prop="email">
                            <a-input v-model="user.email" disabled/>
                        </a-form-model-item>
                        <a-form-model-item label="Outlook ID:" prop="outlook_id">
                            <a-input v-model="user.outlook_id" disabled/>
                        </a-form-model-item>
                        <a-form-model-item label="Activo" prop="active">
                            <input class="checkbox" type="checkbox" v-model="user.active" @change="onChangeActive" />
                        </a-form-model-item>
                        <a-form-model-item label="Estado" prop="status">
                            <input class="checkbox" type="checkbox" v-model="user.status" @change="onChangeUserStatus" />
                        </a-form-model-item>
                    </a-col>
                </a-row>
            </a-form-model>
        </a-modal>

    </div>
</template>
<script>
    const { ADMIN_ROLE } = require("../constants");//roles
    import Repository from "../repositories/RepositoryFactory";
    const UserRepository = Repository.get("user");
    const PersonRepository = Repository.get("person");
    const OrganizationRepository = Repository.get("organization");
    const CategoryRepository = Repository.get("category");
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    const levels = [
        {id: '1',name: 'PREGRADO',},
        {id: '2',name: 'POSGRADO',},
    ];
    const conditions = [
        {id: 'N',name: 'NOMBRADO',},
        {id: 'C',name: 'CONTRATADO',},
        {id: 'O',name: 'OTRO',}
    ];
    const degrees = [
        {id: 'B',name: 'BACHILLER',},
        {id: 'T',name: 'TITULO',},
        {id: 'M',name: 'MAESTRIA',},
        {id: 'D',name: 'DOCTOR',},
        {id: 'O',name: 'NO DEFINIDO',},
    ];
    const types = [
        {id: 'E',name: 'ESTUDIANTE',},
        {id: 'D',name: 'DOCENTE',},
        {id: 'A',name: 'ADMINISTRATIVO',},
        {id: 'O',name: 'OTRO',},
    ];
    const STUDENT='E';

    const columns = [
        {title: 'Nombre', dataIndex: 'fullname', scopedSlots: { customRender: "fullname" }, width: '35%'},
        {title: 'Escuela', dataIndex: 'organization', scopedSlots: { customRender: "organization_id" },width: '35%'},
        {title: 'Estamento', dataIndex:'type', scopedSlots: { customRender: "type" }, width: '8%', align: 'center'},
        {title: 'Rol', dataIndex:'role', width: '10%', align: 'center'},
        {title: '-', key:'action', scopedSlots: { customRender: "action" }, align: 'center'},
    ];

    /*---------- */
    export default {
        data() {
            return {
                ADMIN_ROLE,
                isLoading: false,
                parent: null,
                roles_allowed: [],
                pictureKey: 0,
                filter: { search: null },
                labelCol: { span: 7 },
                wrapperCol: { span: 14 },
                person: {},
                user: {},
                data: [],
                // groups_full: [],
                groups: [],
                areas: [],
                lines: [],
                levels,
                conditions,
                degrees,
                types,
                faculties: [],
                professionalSchoolsOfAFaculty: [], // escuelas profesionales de una facultad
                facultiesWithCategoriesChildren: [], // facultades con sus categorias y subcategoria
                // colleges: [],
                roles: [],
                users: [],
                columns,
                pagination: {},
                loading: false,
                showModal:  false,
                showUserDetailsModal: false,
                user_logged: {},
            }
        },
        components: {
            Loading
        },
        async mounted() {
            this.user_logged = JSON.parse(localStorage.getItem("user"));
            this.isLoading = true;
            await this.listRoles();
            await this.listCollege(); // obtengo las facultades y escuelas
            await this.listgroupsFulls(); // obtengo las facultades, areas, grupos y lineas
            await this.fetch(); // lista de personas
            this.isLoading = false;
        },
        methods: {
            async fetch(params = {}) {
                params.all_status = true;
                this.loading = true;
                if(this.filter.search) params.search=this.filter.search;
                const { data } =  await PersonRepository.list(params)
                this.data = data.data;
                this.loading = false;

                const pagination = { ...this.pagination };
                pagination.total = data.total;
                this.pagination = pagination;
            },
            async listgroupsFulls() {
                try {
                    let {data} =  await CategoryRepository.list({});
                    this.facultiesWithCategoriesChildren = data;
                } catch (error) { this.error(error); }
            },
            filterStatement() {
            //if( this.person.type == STUDENT ) this.person.degree = 'O'
            },
            async listCollege() {
                const{ data } = await OrganizationRepository.list();
                this.faculties = data;
            },
            onChangeFaculties() {
                this.listSchoolsOfAFaculty();
                this.listAreasOfAFaculty();
                this.person.organization_id = null;
                this.person.area_id = null;
                this.person.group_id = null;
                this.person.line_id = null;
                this.groups = [];
                this.lines = [];
            },
            onChangeArea() {
                this.listGroupsOfAFaculty();
                this.person.group_id = null;
                this.person.line_id = null;
                this.lines = [];
            },
            onChangeGroup() {
                this.listLinesOfAFaculty();
                this.person.line_id = null;
            },
            async listRoles() {
                const{ data } = await PersonRepository.list_roles();
                this.roles = data;
            },
            async getUser(user_id) {
                const{ data } = await UserRepository.get(user_id);
                this.user = data;
                this.user.fullname = this.person.fullname;
            },
            getTypeName(id) {
                let type = '';
                switch (id) {
                    case 'E': type = 'Est'; break;
                    case 'D': type = 'Doc'; break;
                    case 'A': type = 'Adm'; break;
                    case 'O': type = 'Otr'; break;
                }
                return type;
            },
            error (message) {
                this.$message.error(message||'Error al procesar');
            },
            success (message) {
                this.$message.success(message||'Proceso Correcto');
            },
            viewDetails(id) {
                let dataPerson = this.data.find(item => item.id == id);
                this.person = { ...dataPerson };
                this.getUser(this.person.user_id);
                this.showUserDetailsModal = true;
            },
            async saveDetails() {
                try {
                    this.loading=true;
                    let payload = this.user;
                    payload.active = this.user.active ? 1 : 0;
                    await UserRepository.save(payload);
                    this.showUserDetailsModal = false;
                    this.loading = false;
                    this.success();
                } catch (error) {
                    this.error(error);
                }
            },
            async save() {
                try {
                    if (!this.person.name) throw ("Nombre es obligatorio");
                    if (!this.person.lastname) throw ("Apellido es obligatorio");
                    if (!this.person.email) throw ("Email es obligatorio");
                    if (!this.person.type) throw ("Tipo es obligatorio");
                    if (!this.person.role_id) throw ("Rol es obligatorio");
                    if (!this.person.condition) throw ("Condicion es obligatorio");
                    if (!this.person.faculty_id) throw ("Facultad es obligatorio");
                    if (!this.person.organization_id) throw ("Escuela es obligatorio");
                    if (!this.person.degree) throw ("Grado es obligatorio");

                    this.loading=true;
                    let payload = { ...this.person };
                    payload.status = this.person.status ? 1 : 0;
                    delete payload.faculty_id;
                    delete payload.fullname;
                    await PersonRepository.save(payload)
                    this.showModal=false;
                    this.success();
                    this.fetch();
                    this.loading=false;
                } catch (error) {
                    if( error.response && error.response.data ) error = error.response.data.message;
                    this.error(error);
                }
            },
            newPerson() {
                this.person = { status: 1,
                    faculty_id: null,
                    organization_id: null,
                    area_id: null,
                    group_id: null,
                    line_id: null
                };
                this.areas = [];
                this.groups = [];
                this.lines = [];
                this.professionalSchoolsOfAFaculty = [];
                this.showModal=true;
            },
            listSchoolsOfAFaculty() { // obtengo las escuelas de una facultad
                this.professionalSchoolsOfAFaculty = this.faculties.find( e => e.id == this.person.faculty_id).children.map(e=>({id:e.id,name:e.name}));
            },
            listAreasOfAFaculty() { // obtengo las areas de una facultad
                this.areas = this.facultiesWithCategoriesChildren.find( e => e.id == this.person.faculty_id).children;
            },
            listGroupsOfAFaculty() { // obtengo los grupos de una facultad
                this.groups = this.areas.find( e => e.id == this.person.area_id).children;
            },
            listLinesOfAFaculty() {  // obtengo las lineas de una facultad
                this.lines = this.groups.find( e => e.id == this.person.group_id).children;
            },
            edit(id) {
                let dataPerson = this.data.find(item => item.id == id);
                this.person = { ...dataPerson };

                if (this.person.organization_id) {
                    this.person.faculty_id = this.faculties.find(faculty =>
                        faculty.children.some(child => child.id === this.person.organization_id)
                    ).id;
                }
                if (this.person.faculty_id && this.person.organization_id) {
                    this.listSchoolsOfAFaculty(); // obtengo las escuelas de una facultad
                    this.listAreasOfAFaculty(); // obtengo las areas de una facultad
                }
                if (this.person.faculty_id && this.person.area_id) {
                    this.listGroupsOfAFaculty(); // obtengo los grupos de una facultad
                }
                if (this.person.faculty_id && this.person.group_id) {
                    this.listLinesOfAFaculty(); // obtengo las lineas de una facultad
                }
                this.showModal = true;
            },
            async remove(id){
                try {
                    let payload = this.data.find(item => item.id === id);
                    this.loading = true;
                    await PersonRepository.delete(payload.id)
                    this.success();
                    this.fetch();
                    this.loading = false;
                } catch (error) {
                    this.error(error);
                }
            },
            onChangeActive(e){
                return e.target.checked+"";
            },
            onChangeUserStatus(e){
                return e.target.checked+"";
            },
            onChangePersonStatus(e){
                return e.target.checked+"";
            },
            // filterOption(input, option) {
            //     return ( option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0 );
            // },
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
        }
    }
</script>

<style scoped>
    .checkbox {
        width: 1.3em;
        height: 1.3em;
    }
</style>
