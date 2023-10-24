require('./bootstrap');
import Antd from 'ant-design-vue'
import VueRouter from 'vue-router'
import 'ant-design-vue/dist/antd.css';
import { ADMIN_ROLE, RESEARCH_ROLE, UNIT_ROLE, COR_ROLE } from './constants';
import moment from 'moment';
window.Vue = require('vue');
Vue.use(Antd);
Vue.use(VueRouter);

Vue.mixin({
   methods: {
        asset(path) {
            var base_path = window._asset || '';
			var route = '';
			if(base_path.includes('localhost'))  route = base_path + path;
			else route = base_path+'/'+ path;
            return route;
        }
    }
});

//Vue.component('profile-component', require('./components/ProfileComponent.vue').default);
Vue.component('menu-component', require('./components/MenuComponent.vue').default);
Vue.component('file-component', require('./components/FileComponent.vue').default);
Vue.component('picture-component', require('./components/PictureComponent.vue').default);
Vue.component('login-component', require('./components/LoginComponent.vue').default);
Vue.component('select-people-component', require('./components/SelectPeopleComponent.vue').default);
Vue.component('public-component', require('./components/PublicComponent.vue').default);

const EventsComponent = Vue.component('events-component', require('./components/EventsComponent.vue').default);
const DocumentsComponent = Vue.component('documents-component', require('./components/DocumentsComponent.vue').default);
const ForbiddenComponent = Vue.component('forbidden-component', require('./components/ForbiddenComponent.vue').default);
const ProfileComponent = Vue.component('profile-component', require('./components/ProfileComponent.vue').default);
const ResearchComponent = Vue.component('research-component', require('./components/ResearchComponent.vue').default);
const CategoryComponent = Vue.component('category-component', require('./components/CategoryComponent.vue').default);
const PlanComponent = Vue.component('plan-component', require('./components/PlanComponent.vue').default);
const UsersComponent = Vue.component('users-component', require('./components/UsersComponent.vue').default);
const OrganizationsComponent = Vue.component('organizations-component', require('./components/OrganizationsComponent.vue').default);
const JournalsComponent = Vue.component('journals-component', require('./components/JournalsComponent.vue').default);
//Zona de Reportes
const IncentivesComponent = Vue.component('incentives', require('./components/reports/IncentivesComponent.vue').default);
const Py_PeriodComponent = Vue.component('py_period', require('./components/reports/Py_PeriodComponent.vue').default);
const Py_AuthorComponent = Vue.component('py_by_author', require('./components/reports/Py_AuthorComponent.vue').default);
const Py_StateComponent = Vue.component('py_by_state', require('./components/reports/Py_StateComponent.vue').default);
const Py_CollegeComponent = Vue.component('py_by_college', require('./components/reports/Py_CollegeComponent.vue').default);
const Out_CollegeComponent = Vue.component('out_by_college', require('./components/reports/Out_CollegeComponent.vue').default);
const Out_JournalComponent = Vue.component('out_in_journal', require('./components/reports/Out_JournalComponent.vue').default);
const Out_AuthorComponent = Vue.component('out_author', require('./components/reports/Out_AuthorComponent.vue').default);
const SuneduComponent = Vue.component('sunedu', require('./components/reports/SuneduComponent.vue').default);
const ConstancyComponent = Vue.component('constancy', require('./components/reports/ConstancyComponent.vue').default);
const CertifiedComponent = Vue.component('certified', require('./components/reports/CertifiedComponent.vue').default);
const UnitComponent = Vue.component('unit_report', require('./components/reports/UnitComponent.vue').default);

const routes = [
    { path: '/403',name:'forbidden', component: ForbiddenComponent },
    { path: '/profile',name:'profile', component: ProfileComponent },
    { path: '/research',name:'research', component: ResearchComponent },
    { path: '/category',name:'category', component: CategoryComponent },
    { path: '/plan',name:'plan', component: PlanComponent },
    { path: '/users',name:'users', component: UsersComponent },
    { path: '/organizations',name:'organizations', component: OrganizationsComponent },
    
    { path: '/events',name:'events', component: EventsComponent },
    { path: '/documents',name:'documents', component: DocumentsComponent },

    { path: '/journals',name:'journals', component: JournalsComponent },
    { path: '/incentives',name:'incentives', component: IncentivesComponent },
    { path: '/py_period',name:'py_period', component: Py_PeriodComponent },
    { path: '/py_by_author',name:'py_by_author', component: Py_AuthorComponent },
    { path: '/py_by_state',name:'py_by_state', component: Py_StateComponent },
    { path: '/py_by_college',name:'py_by_college', component: Py_CollegeComponent },
    { path: '/out_by_author',name:'out_by_author', component: Out_AuthorComponent },
    { path: '/out_by_college',name:'out_by_college', component: Out_CollegeComponent },
    { path: '/out_in_journal',name:'out_in_journal', component: Out_JournalComponent },
    { path: '/sunedu',name:'sunedu', component: SuneduComponent },
    { path: '/constancy',name:'constancy', component: ConstancyComponent },
    { path: '/certified',name:'certified', component: CertifiedComponent },
    { path: '/unit_report',name:'unit_report', component: UnitComponent },
];

const router = new VueRouter({
    mode: 'history',
    routes
  })


router.beforeEach((to, from, next) => {
    const publicPages = ["/login", "/","/public", "/403"];
    const authRequired = !publicPages.includes(to.path);
    let loggedIn = localStorage.getItem("user");
    const researchPages = ["/research", "/profile",'/home'];
    const coorPages = ["/research", "/profile",'/home'];
    const unitPages = ["/research", "/profile",'/home', "/unit_report", "/constancy", "/certified"];

    if(loggedIn) {
        const userLogged = JSON.parse(loggedIn);
        //si la fecha actual > expire : eliminar user_data
        if( loggedIn &&  moment() > moment(userLogged.expire_session) )  loggedIn = null;
        //console.log(" Horas restantes",  moment.duration( moment(userLogged.expire_session).diff( moment() ) ).asHours()  );
    }
  
    if (authRequired && !loggedIn) {  next("/login"); }
    if (loggedIn && authRequired ) {
        const userLogged = JSON.parse(loggedIn);
        if (userLogged.role_id == RESEARCH_ROLE && !researchPages.includes(to.path)) {
            next("/403");
        }else if (userLogged.role_id == UNIT_ROLE && !unitPages.includes(to.path)) {
            next("/403");
        }else if (userLogged.role_id == COR_ROLE && !coorPages.includes(to.path)) {
            next("/403");
        }
    }
    
    next();
});

const app = new Vue({
    el: '#app',
    router,
});
$(document).ready(function () {
     $('#sidebarCollapse').on('click', function () {
         $('#sidebar').toggleClass('active');
     });
});