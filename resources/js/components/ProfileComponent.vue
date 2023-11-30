<template>
    <div style="padding:10px 0">
        <div class="vld-parent">
            <loading :active.sync="isLoading"
            :can-cancel="false"
            :is-full-page="true"
            color="#1890ff"></loading>
        </div>
        <a-form-model ref="personForm" :model="person" :rules="rules" :label-col="labelCol" :wrapper-col="wrapperCol">
            <a-row>
                <a-col class="gutter-row" :span="12">
                    <a-form-model-item label="DNI" prop="dni">
                        <a-input v-model="person.dni" />
                    </a-form-model-item>

                    <a-form-model-item label="Nombre" prop="nombre">
                        <a-tooltip>
                            <template slot="title">Usar mayusculas al principio. <br> Ej: Juan Gabriel</template>
                            <a-input v-model="person.name" />
                        </a-tooltip>
                    </a-form-model-item>

                    <a-form-model-item label="Apellidos" prop="apellidos">
                        <a-tooltip>
                            <template slot="title">Usar mayusculas al principio. <br> Ej: Perez Salas</template>
                            <a-input v-model="person.lastname" />
                        </a-tooltip>
                    </a-form-model-item>

                    <a-form-model-item label="F. Nacimiento" prop="fecha">
                        <a-date-picker v-model="person.birth" placeholder="Seleccione una fecha" :format="DATEFORMAT"/>
                    </a-form-model-item>

                    <a-form-model-item label="Género" prop="gender">
                        <a-select v-model="person.sex" placeholder="Seleccione un genero">
                            <a-select-option v-for="item in GENDERS" :value="item.id" :key="item.id">
                            {{item.name}}
                            </a-select-option>
                        </a-select>
                    </a-form-model-item>

                    <a-form-model-item label="Direccion" prop="direccion">
                        <a-input v-model="person.address" />
                    </a-form-model-item>

                    <a-form-model-item label="Telefono" prop="telefono">
                        <a-input v-model="person.phone" />
                    </a-form-model-item>

                    <!-- <a-form-model-item label="ID RENACYT" prop="renacyt">
                    <a-select v-model="person.renacyt_id" placeholder="Seleccione...">
                        <a-select-option v-for="item in renacyt_levels" :value="item.id" :key="item.id" >
                        {{item.name}}
                        </a-select-option>
                    </a-select>
                    </a-form-model-item> -->

                    <a-form-model-item label="ORCID" prop="orcid">
                        <a-input v-model="person.orcid" placeholder="ej. https://orcid.org/xxxx-xxxx-xxxx-xxxx"/>
                    </a-form-model-item>

                    <a-form-model-item label="Scopus ID" prop="scopus_id">
                        <a-input v-model="person.scopus_id" placeholder="ej. 12345678900"/>
                    </a-form-model-item>

                    <a-form-model-item label="Biografia" prop="biografia">
                        <a-textarea v-model="person.biography" />
                    </a-form-model-item>
                </a-col>

                <a-col class="gutter-row" :span="12">
                    <a-form-model-item label="Email" prop="email">
                        <a-input v-model="person.email" disabled/>
                    </a-form-model-item>

                    <a-form-model-item label="Facultad" prop="faculty">
                        <a-select show-search option-filter-prop="children" v-model="person.faculty_id" placeholder="Seleccione una Facultad" @change="onChangeFaculties()" :disabled="!allowEditFaculty">
                            <a-select-option v-for="item in faculties" :value="item.id" :key="item.id" :title="item.name">
                                {{item.name}}
                            </a-select-option>
                        </a-select>
                    </a-form-model-item>

                    <a-form-model-item label="Escuela" prop="organization">
                        <!-- <a-select v-model="person.organization_id" placeholder="Seleccione una Escuela" :disabled="!allowEditCollege"> --->
                        <a-select v-model="person.organization_id" placeholder="Seleccione una Escuela">
                            <a-select-option v-for="item in professionalSchoolsOfAFaculty" :value="item.id" :key="item.id" :title="item.name">
                                {{item.name}}
                            </a-select-option>
                        </a-select>
                    </a-form-model-item>

                    <a-form-model-item label="Area" prop="area">
                        <a-select show-search option-filter-prop="children" v-model="person.area_id" placeholder="Seleccione un area" @change="onChangeArea()" :disabled="!allowEditArea">
                            <a-select-option v-for="item in areas" :value="item.id" :key="item.id" >
                                {{item.name}}
                            </a-select-option>
                        </a-select>
                    </a-form-model-item>

                    <a-form-model-item label="Grupo" prop="group">
                        <a-select show-search option-filter-prop="children" v-model="person.group_id" placeholder="Seleccione un grupo" @change="onChangeGroup()" :disabled="!allowEditGroup">
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

                    <a-form-model-item label="Imagen" prop="photo">
                        <picture-component :photo="person.photo" v-on:changeImage="onChangePhoto" :key="pictureKey"/>
                    </a-form-model-item>
                </a-col>
            </a-row>

            <a-row>
                <div class="btn_save"><a-button type="primary" size="large" @click="save()">Guardar</a-button></div>
            </a-row>
        </a-form-model>
    </div>
</template>
<script>
    import Repository from "../repositories/RepositoryFactory";
    const {ADMIN_ROLE,RESEARCH_ROLE,UNIT_ROLE,DGI_ROLE, COR_ROLE, DATEFORMAT, GENDERS} = require("../constants");
    const PersonRepository = Repository.get("person");
    // const RenacytRepository = Repository.get("renacyt");
    const OrganizationRepository = Repository.get("organization");
    const CategoryRepository = Repository.get("category");
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';


    /*---------- */
    export default {
        data() {
            return {
                DATEFORMAT,
                GENDERS,
                isLoading: false,
                roles_allowed: [],
                pictureKey: 0,
                roles: [],
                labelCol: { span: 7 },
                wrapperCol: { span: 14 },
                person: { birth:null },
                rules: {},
                facultiesWithCategoriesChildren: [], // facultades con sus categorias y subcategoria
                areas: [],
                groups: [],
                lines: [],
                faculties: [],
                professionalSchoolsOfAFaculty: [], // escuelas profesionales de una facultad
                profile: [],
                user_logged: {},
                pagination: {},
                loading: false,
                renacyt_levels: [],
                ADMIN_ROLE, RESEARCH_ROLE, UNIT_ROLE, DGI_ROLE, COR_ROLE,
                allowEditFaculty: false,
                // allowEditCollege: false,
                allowEditArea: false,
                allowEditGroup: false,
            }
        },
        components: {
            Loading
        },
        async mounted() {
            // this.listRenacytLevels();
            this.user_logged = JSON.parse(localStorage.getItem("user"));
            this.isLoading=true;

            await this.listgroupsFulls(); // obtengo las facultades, areas, grupos y lineas
            await this.fetch(); // informacion del usuario
            await this.listCollege(); // obtengo las facultades
            this.pictureKey++;

            // para evaluar a persona que aun no se ha registrado
            /*
            this.person.faculty_id = null;
            this.person.organization_id = null;
            this.person.area_id = null;
            this.person.group_id = null;
            this.person.line_id = null;*/

            // en this.person.organization_id se guarda la escuela no la facultad
            if (this.user_logged.faculty_id) {
                this.person.faculty_id = this.user_logged.faculty_id;
            } else if (this.person.organization_id) {
                this.person.faculty_id = this.faculties.find(faculty =>
                    faculty.children.some(child => child.id === this.person.organization_id)
                ).id;
            }

            if (this.person.faculty_id == null) this.allowEditFaculty = true;
            // if (this.person.organization_id == null) this.allowEditCollege = true;
            if (this.person.area_id == null) this.allowEditArea = true;
            if (this.person.group_id == null) this.allowEditGroup = true;

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
            this.isLoading=false;
        },
        methods:{
            /* async listRenacytLevels(){
                    try {
                        const {data}= await RenacytRepository.list({});
                        this.renacyt_levels=data.data;
                    } catch (error) {
                        this.error(error);
                    }
                },
            */
            async listgroupsFulls() {
                try {
                    let { data } =  await CategoryRepository.list({});
                    this.facultiesWithCategoriesChildren = data;
                } catch (error) { this.error(error); }
            },
            async fetch() {
                try {
                    this.loading = true;
                    const person_id = this.user_logged.people_id;
                    const { data } =  await PersonRepository.get(person_id);
                    this.person = data;
                    this.loading = false;
                } catch(error) {
                    this.error(error);
                }
            },
            async listCollege(){
                const { data } = await OrganizationRepository.list();
                this.faculties = data;
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
            onChangeFaculties() {
                console.log(this.person.faculty_id);
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
                console.log(this.person.area_id);
                this.listGroupsOfAFaculty();
                this.person.group_id = null;
                this.person.line_id = null;
                this.lines = [];
            },
            onChangeGroup() {
                this.listLinesOfAFaculty();
                this.person.line_id = null;
            },
            error (message) { this.$message.error(message||'Error al procesar'); },
            success (message) { this.$message.success(message||'Proceso Correcto'); },
            async save(){
                try {
                    if (!this.person.name) throw ("Nombre es obligatorio");
                    if (!this.person.lastname) throw ("Apellido es obligatorio");
                    if (!this.person.organization_id) throw ("Escuela es obligatoria");
                    if (!this.person.area_id) throw ("Area es obligatoria");
                    if (!this.person.group_id) throw ("Grupo es obligatorio");

                    this.loading = true;
                    let payload = this.person;
                    this.user_logged.faculty_id = payload.faculty_id;
                    this.user_logged.college_id = payload.organization_id;
                    this.user_logged.area_id = payload.area_id;
                    this.user_logged.group_id = payload.group_id;
                    this.user_logged.line_id = payload.line_id;
                    await PersonRepository.save(payload);
                    localStorage.setItem("user",JSON.stringify(this.user_logged));
                    this.success();
                    this.loading = false;
                } catch (error) {
                    this.error(error);
                }
            },
            // filterOption(input, option) {
            //     return ( option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0 );
            // },
            onChangePhoto(data){
                this.person.photo = data;
                this.pictureKey++;
            },
        }
    }
</script>
<style scoped>
    .checkbox{
        padding-left: .5em;
    }
    .btn_save{
        display: flex;
        justify-content: flex-end;
        padding-right: 4em;
    }
</style>
