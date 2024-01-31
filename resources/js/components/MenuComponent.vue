<template>
    <div v-show="user_object && user_object.id">
        <div style="border-bottom: 1px solid #ccc; background-color: #1064B2">
            <div style="display:flex; justify-content:space-around; text-align:center; padding: 5px 0 5px 0;">
                <div style="display:flex; font-weight: bold; align-items:center">
                    <a-avatar :src="user_object.photo" style="margin-right:.5em"/>
                    <p style="margin: auto 0"> {{ user_object.name + ' ' + user_object.lastname}} </p>
                </div>
                <div style="margin:auto 0">
                    <a-popconfirm title="¿Desea cerrar Sesion?" @confirm="() => logoutButton()" ok-text="Si" cancel-text="No">
                        <a-icon type="poweroff" style="font-size: 16px; cursor:pointer"/>
                    </a-popconfirm>
                </div>
            </div>
            <div style="border-top: 1px dashed #ccc; text-align:center; padding:5px">
                <div style="padding: 0 5px; margin:auto; text-align:center; background:#1890ff; border-radius: 5px; width:80% ; margin:auto;" >
                    {{
                        user_object.role_id == 1 ? 'Administrador'
                        : user_object.role_id == 2 ? 'Investigador'
                        : user_object.role_id == 3 ? "Unidad investigación"
                        : user_object.role_id == 4 ? 'DGI'
                        : user_object.role_id == 5 ? 'Coord. Grupo'
                        : '-----'
                    }}
                </div>
                <div style="text-align:center; color:#fafafa; font-size:11px" v-if="user_object.role_id==2 || user_object.role_id==3||user_object.role_id==5">
                    {{
                        user_object.role_id==2?( user_object.faculty_name||'No definido')
                        : user_object.role_id==3? (user_object.faculty_name||'No definido')
                        : user_object.role_id==5? (user_object.group_name||'No definido')
                        :''
                    }}
                </div>
            </div>
        </div>

        <a-menu style="width: 251px;height:" :default-selected-keys="['1']" :open-keys.sync="openKeys" theme="dark" mode="inline" >
            <a-menu-item key="profile" v-if="user_object.role_id">
                <router-link to="/profile"> <a-icon type="appstore" />Datos Personales</router-link>
            </a-menu-item>

            <a-menu-item key="research" v-if="user_object.role_id">
                <router-link to="/research"> <a-icon type="appstore" />Investigaciones</router-link>
            </a-menu-item>

            <a-menu-item key="users" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                <router-link to="/users"> <a-icon type="appstore" />Personas</router-link>
            </a-menu-item>

            <a-menu-item key="category" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                <router-link to="/category"> <a-icon type="appstore" />Areas</router-link>
            </a-menu-item>

            <a-menu-item key="plan" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                <router-link to="/plan"> <a-icon type="appstore" />Planes de Investigacion</router-link>
            </a-menu-item>

            <a-menu-item key="organizations" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                <router-link to="/organizations"> <a-icon type="appstore" />Organizaciones</router-link>
            </a-menu-item>

            <a-menu-item key="events" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                <router-link to="/events"> <a-icon type="calendar" />Eventos</router-link>
            </a-menu-item>

            <a-menu-item key="documents" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                <router-link to="/documents"> <a-icon type="file-add" />Documentos</router-link>
            </a-menu-item>

            <a-sub-menu v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE || user_object.role_id == UNIT_ROLE">
                <span slot="title" class="submenu-title-wrapper"> <a-icon type="setting" /> Reportes</span>
                <!--
                <a-menu-item key="setting:1" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                    <router-link to="/incentives"> <a-icon type="appstore" />Incentivos</router-link>
                </a-menu-item>
                -->
                <a-menu-item key="setting:2" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                    <router-link to="/py_period"> <a-icon type="appstore" />Proyectos x Periodo</router-link>
                </a-menu-item>
                <a-menu-item key="setting:3" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                    <router-link to="/py_by_author"> <a-icon type="appstore" />Proyectos x Investigador</router-link>
                </a-menu-item>
                <a-menu-item key="setting:7" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                    <router-link to="/py_by_state"> <a-icon type="file" />Proyectos x Estado</router-link>
                </a-menu-item>
                <a-menu-item key="setting:9" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                    <router-link to="/py_by_college"> <a-icon type="file" />Proyectos x Escuela</router-link>
                </a-menu-item>
                <a-menu-item key="setting:4" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                    <router-link to="/sunedu"> <a-icon type="appstore" />Formato SUNEDU</router-link>
                </a-menu-item>
                <a-menu-item key="setting:5" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE || user_object.role_id == UNIT_ROLE">
                    <router-link to="/constancy"> <a-icon type="appstore" />Constancia de Investigador</router-link>
                </a-menu-item>
                <a-menu-item key="setting:6" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE || user_object.role_id == UNIT_ROLE">
                    <router-link to="/certified"> <a-icon type="appstore" />Certificado de Investigador</router-link>
                </a-menu-item>
                <a-menu-item key="setting:8" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE">
                    <router-link to="/out_by_college"> <a-icon type="file" />Ejemplares</router-link>
                </a-menu-item>
                <!--
                <a-menu-item key="setting:10" v-if="user_object.role_id == ADMIN_ROLE || user_object.role_id == DGI_ROLE || user_object.role_id == UNIT_ROLE">
                    <router-link to="/unit_report"> <a-icon type="appstore" />Informe Unidad</router-link>
                </a-menu-item>
                -->
            </a-sub-menu>
        </a-menu>
    </div>
</template>

<script>
    const {ADMIN_ROLE,RESEARCH_ROLE,UNIT_ROLE,DGI_ROLE, COR_ROLE} = require("../constants");//roles

    export default {
        props: ['baseUrl','currentUrl','user'],
        data() {
            return {
                permissions:[],
                collapsed: false,
                dataPeople:[],
                url: this.currentUrl.replace(this.baseUrl,''),
                current: ['mail'],
                openKeys: ['sub1'],
                user_object:{ },
                ADMIN_ROLE,
                UNIT_ROLE,
                RESEARCH_ROLE,
                DGI_ROLE,
                COR_ROLE
            }
        },
        mounted() {
            //this.$isLoading(true);
            this.verifyUser();

            // this.permission = access[this.role];
            if(this.user_object && this.user_object.id) this.fetch();
        },
        methods: {
            async fetch() {
            //const {data} = await UserRepository.listAccess({role_id:this.user_object.role_id});
            //this.permissions = data;
            },
            verifyUser() {
                try {
                    this.user_object = JSON.parse(localStorage.getItem("user"));
                    if(!this.user_object) {
                    this.user_object = {};
                    // this.logoutButton();
                    //location.reload();
                    this.$router.go(this.$router.currentRoute);
                    } else document.getElementById("app").style.display = 'block';
                } catch (e) {
                    alert("Se ha agotado su sesion");
                }
            },
            logoutButton() {
                localStorage.removeItem("user");
                this.$router.push('/login');
                this.$router.go(this.$router.currentRoute);
                //location.reload();
            },
            toggleCollapsed() {
                this.collapsed = !this.collapsed;
            },
        }
    }
</script>
<style>
    #sidebar.active {
        margin-left: -261px;
    }
    #sidebar ul li a {
        padding: 0;
        font-size: .9rem;
    }
    #sidebar ul li:hover {
        background: #08979C;
    }

    #sidebar ul li a:hover {
        background:none;
    }
    #sidebar {
        overflow-y: scroll;
        min-width: 260px;
        scrollbar-color: rgba(242, 242, 242, 0.5) rgb(0, 21, 41);
        scrollbar-width: thin;
    }
    .btnlogout{
        background-color: #E83447;
        border: 0;
        padding: 8px 0 8px 0;
        border-radius: 5px;
        display:flex;
        width:50%;
        justify-content:space-around;
        margin-top: 1em;
        margin-left: 1em;
        align-items:center
    }
    .btnlogout p{
        margin: auto 0;
    }
</style>
