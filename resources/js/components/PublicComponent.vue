<template>
<div style="width:100%; padding:10px">
  <div class="vld-parent">
    
  </div>
  <a-row>
    <a-col :span="6" class="sidebar_menu">
      <a-menu style="width:100%" mode="inline">
        <a-menu-item key="authors" @click="clean();show('authors')" style="display:flex; align-items:center">
          <a-icon type="usergroup-add" style="padding-right: 2px; color: #1890ff;"/>
          <span class="menu_text"> AUTORES </span>
        </a-menu-item>

        <a-menu-item key="research" @click="clean();show('research')" style="display:flex; align-items:center">
          <a-icon type="experiment" theme="twoTone" style="padding-right: 2px"/>
          <span class="menu_text"> INVESTIGACIONES </span>
        </a-menu-item>

        <a-menu-item key="publisheds" @click="clean();show('publisheds')" style="display:flex; align-items:center">
          <a-icon type="file-pdf" theme="twoTone" style="padding-right: 2px"/>
          <span class="menu_text"> PUBLICACIONES </span>
        </a-menu-item>

        <a-menu-item key="thesis" @click="clean();show('thesis')" style="display:flex; align-items:center">
          <a-icon type="container" theme="twoTone" style="padding-right: 2px"/>
          <span class="menu_text"> TESIS </span>
        </a-menu-item>

        <a-menu-item key="externals" @click="clean();show('externals')" style="display:flex; align-items:center">
          <a-icon type="interaction" theme="twoTone" style="padding-right: 2px"/>
          <span class="menu_text"> INVESTIGACIONES EXTERNAS </span>
        </a-menu-item>

        <a-sub-menu key="faculties">
          <div slot="title" style="display:flex; align-items:center"><a-icon type="appstore" theme="twoTone" style="padding-right: 2px"/><span class="menu_text">FACULTADES</span></div>
          
          <a-menu-item :key="faculty.id"  @click="clean();show('programs', {faculty_id:faculty.id})" style="display:flex; align-items:center" v-for="faculty in programs">
            <a-icon type="home" theme="twoTone" style="padding-right: 2px"/>
            <span :title="faculty.name" class="menu_text"> {{faculty.name}} </span>
          </a-menu-item>
        </a-sub-menu>
      </a-menu>
      <p style="padding-left:30px" v-if="!programs.length">Cargando...</p>
    </a-col>

    <a-col :span="18" class="main_section">
      <div id="authors-tab" v-show="showAuthors">
        <div style="display:flex; justify-content:space-between; margin: .5em 0 1em 0">
          <h4> Investigadores(as) de{{ line_selected?(' la linea de '+line_selected.name) : 
                                        group_selected?('l grupo de '+group_selected.name) :
                                        program_selected?('l programa de '+program_selected.name) :
                                        ' toda la Universidad' }}</h4>
          <a-input-search
            style="width:40%"
            v-model="filter.search"
            placeholder="Buscar investigador ..."
            enter-button="Buscar"
            size="large"
            @search="get_authors()"
            allowClear
          />
        </div>
        <div style="background-color:#fafafa;display:flex;flex-wrap:wrap;justify-content:space-between;padding:1.2em">
          <a-card v-for="author in authors" :key="author.id" hoverable style="width: 210px;height:160px;margin-bottom:1.2em" @click="showAuthor(author.id)">
            <img slot="cover" :src="author.photo==''?'images/none_perfil.png':author.photo" style="width:56px;border-radius:50%;margin:18px auto -8px auto;aspect-ratio:1/1"/>
            <a-card-meta :title="author.name+' '+author.lastname" style="font-size:12px;text-align:center">
              <template slot="description">
                <span style="font-size:13px">{{author.email}}</span>
              </template>
            </a-card-meta>
          </a-card>
        </div>
      </div>

      <div id="faculties-tab" v-show="showPrograms">
        <div style="display:flex; justify-content:space-between; margin: .5em 0 1em 0;align-items:center">
          <h4 style="text-transform:uppercase;display:flex"> {{faculty_selected.name}} </h4>
          <a-input-search style="width:40%" v-model="filter_lines" placeholder="Buscar ..." enter-button="Buscar" size="large" @search="searchInPrograms()" allowClear/>
        </div>

        <GChart type="LineChart" :data="activities" :options="chartOptions2" style="width:100%"/>

        <div style="width:100%">
          <div style="padding-top: .2em;">
            <ul v-for="(program, program_index) in faculty_selected.children_" :key="program.id" style="list-style-type: none; padding:0; margin:0">
              <li style="background:#fafafa; border-bottom:1px solid #e7e7e7; padding: .9em"> <!-- PROGRAMAS -->
                <div style="display:flex;align-items:center;justify-content:space-between">
                  <div  @click="toggleProgram(program_index)" style="color:#333; cursor:pointer">
                    <a href="javascript:;"><a-icon :type="program.showgroups?'caret-down':'caret-right'" style="padding-right:10px"/></a>
                    <a :title="program.type"> {{program.type}} de {{program.name}} </a>
                  </div>
                  <div style="display:flex">
                    <div @click="listAuthorsByCategory(program, 'authors')" title="Investigadores" style="margin: 0 10px; cursor:pointer">
                      <a-badge :count="program.author_count" :number-style="{ backgroundColor: 'rgba(25, 144, 255, 0.8)', color:'#fff',fontWeight: 700, minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                        <a-icon type="user" style="font-size:18px"/>
                        <a  class="head-example"></a>
                      </a-badge>
                    </div>
                    <div @click="listResearchByCategory(program, 'research')" title="Investigaciones" style="margin: 0 10px; cursor:pointer">
                      <a-badge :count="program.research_count" :number-style="{ backgroundColor: 'rgba(25, 144, 255, 0.8)', color:'#fff',fontWeight: 700, minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                        <a-icon type="experiment" style="font-size:18px"/>
                        <a class="head-example" ></a>
                      </a-badge>
                    </div>
                    <div @click="listResearchByCategory(program, 'publisheds')" title="Publicaciones" style="margin: 0 10px; cursor:pointer">
                      <a-badge :count="program.published_count" :number-style="{ backgroundColor: 'rgba(25, 144, 255, 0.8)', color:'#fff', minWidth:'15px',fontWeight: 700, height: '15px', lineHeight:'14px' }" >
                        <a-icon type="file-pdf" style="font-size:18px"/>
                        <a class="head-example" ></a>
                      </a-badge>
                    </div>
                    <div @click="listResearchByCategory(program, 'thesis')" title="Tesis" style="margin: 0 10px; cursor:pointer">
                      <a-badge :count="program.thesis_count" :number-style="{ backgroundColor: 'rgba(25, 144, 255, 0.8)', color:'#fff', minWidth:'15px',fontWeight: 700, height: '15px', lineHeight:'14px' }" >
                        <a-icon type="container" style="font-size:18px"/>
                        <a class="head-example" ></a>
                      </a-badge>
                    </div>
                    <div @click="listResearchByCategory(program, 'externals')" title="I. Externas" style="margin: 0 10px; cursor:pointer">
                      <a-badge :count="program.external_count" :number-style="{ backgroundColor: 'rgba(25, 144, 255, 0.8)', color:'#fff', minWidth:'15px',fontWeight: 700, height: '15px', lineHeight:'14px' }" >
                        <a-icon type="interaction" style="font-size:18px"/>
                        <a class="head-example" ></a>
                      </a-badge>
                    </div>
                  </div>
                </div>
                <div v-show="program.showgroups" :key="showgroups" style="padding-top: .6em;">
                  <!-- group_index -->
                  <ul v-for="(group  ) in program.children_" :key="group.id" style="list-style-type: none; padding:0; margin:0">
                    <li style="padding:10px 0 10px 10px">  <!-- GRUPOS -->
                      <div style="display:flex;align-items:center;justify-content:space-between">
                        <!-- @click="toggleGroup(program_index, group_index)" -->
                        <div  style="padding-left:20px; color:#333">
                          <a href="javascript:;"><a-icon :type="group.showlines?'caret-down':'caret-right'" style="padding-right:10px;cursor:default"/></a>
                          <a :title="group.type" style="cursor:default"> {{group.type}} de {{group.name}} </a>
                        </div>
                        <div style="display:flex">
                          <div @click="listAuthorsByCategory(group, 'authors')" title="Investigadores" style="margin: 0 10px; cursor:pointer">
                            <a-badge :count="group.author_count" :number-style="{ backgroundColor: 'rgba(135, 208, 104, 0.8)', color:'#000',fontWeight: 700, minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                              <a-icon type="user" style="font-size:18px"/>
                              <a href="" class="head-example"></a>
                            </a-badge>
                          </div>
                          <div @click="listResearchByCategory(group, 'research')" title="Investigaciones" style="margin: 0 10px; cursor:pointer">
                            <a-badge :count="group.research_count" :number-style="{ backgroundColor: 'rgba(135, 208, 104, 0.8)', color:'#000',fontWeight: 700, minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                              <a-icon type="experiment" style="font-size:18px"/>
                              <a class="head-example" ></a>
                            </a-badge>
                          </div>
                          <div @click="listResearchByCategory(group, 'publisheds')" title="Publicaciones" style="margin: 0 10px; cursor:pointer">
                            <a-badge :count="group.published_count" :number-style="{ backgroundColor: 'rgba(135, 208, 104, 0.8)', color:'#000',fontWeight: 700, minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                              <a-icon type="file-pdf" style="font-size:18px"/>
                              <a class="head-example" ></a>
                            </a-badge>
                          </div>
                          <div @click="listResearchByCategory(group, 'thesis')" title="Tesis" style="margin: 0 10px; cursor:pointer">
                            <a-badge :count="group.thesis_count" :number-style="{ backgroundColor: 'rgba(135, 208, 104, 0.8)', color:'#000',fontWeight: 700, minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                              <a-icon type="container" style="font-size:18px"/>
                              <a class="head-example" ></a>
                            </a-badge>
                          </div>
                          <div @click="listResearchByCategory(group, 'externals')" title="I. Externas" style="margin: 0 10px; cursor:pointer">
                            <a-badge :count="group.external_count" :number-style="{ backgroundColor: 'rgba(135, 208, 104, 0.8)', color:'#000',fontWeight: 700, minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                              <a-icon type="interaction" style="font-size:18px"/>
                              <a class="head-example" ></a>
                            </a-badge>
                          </div>
                        </div>
                      </div>
                      <!-- Está parte queda en desuso, hasta nuevo aviso. 👇 -->
                      <div v-show="group.showlines" :key="showlines" style="padding-top: .6em;">
                        <ul v-for="line in group.children" :key="line.id" style="padding-left:65px">
                          <li style="padding: 10px 0 10px 8px; border-bottom:1px solid #e7e7e7ca" class="line-hoverable">
                            <div style="display:flex;align-items:center;justify-content:space-between;" >
                              <div> <!-- LINEAS -->
                                <span class="line-link"><a> {{line.name}} </a></span>
                              </div>
                              <div style="display:flex">
                                <div @click="show('authors', {line_id:line.id})" title="Investigadores" style="margin: 0 10px; cursor:pointer">
                                  <a-badge count="5" :number-style="{ backgroundColor: 'rgba(104, 104, 104, 0.8)', minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                                    <a-icon type="user" style="font-size:18px"/>
                                    <a href="" class="head-example"></a>
                                  </a-badge>
                                </div>
                                <div @click="listResearchByCategory(line, 'research')" title="Investigaciones" style="margin: 0 10px; cursor:pointer">
                                  <a-badge :count="line.research_count" :number-style="{ backgroundColor: 'rgba(104, 104, 104, 0.8)', minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                                    <a-icon type="experiment" style="font-size:18px"/>
                                    <a class="head-example" ></a>
                                  </a-badge>
                                </div>
                                <div @click="listResearchByCategory(line, 'publisheds')" title="Publicaciones" style="margin: 0 10px; cursor:pointer">
                                  <a-badge count="3" :number-style="{ backgroundColor: 'rgba(104, 104, 104, 0.8)', minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                                    <a-icon type="file-pdf" style="font-size:18px"/>
                                    <a class="head-example" ></a>
                                  </a-badge>
                                </div>
                                <div @click="listResearchByCategory(line, 'thesis')" title="Tesis" style="margin: 0 10px; cursor:pointer">
                                  <a-badge count="3" :number-style="{ backgroundColor: 'rgba(104, 104, 104, 0.8)', minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                                    <a-icon type="container" style="font-size:18px"/>
                                    <a class="head-example" ></a>
                                  </a-badge>
                                </div>
                                <div @click="listResearchByCategory(line, 'externals')" title="I. Externas" style="margin: 0 10px; cursor:pointer">
                                  <a-badge count="3" :number-style="{ backgroundColor: 'rgba(104, 104, 104, 0.8)', minWidth:'15px', height: '15px', lineHeight:'14px' }" >
                                    <a-icon type="interaction" style="font-size:18px"/>
                                    <a class="head-example" ></a>
                                  </a-badge>
                                </div>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div id="research-tab" v-show="showResearch">
        <h4>{{filter.type==ARTICLE_OUTCOME ? 'Publicaciones' : 
              filter.external==1 ? 'Investigaciones Externas' :
              filter.type_research==TESIS_RESEARCH ? 'Tesis' :
              'Investigaciones'}} de{{ line_selected?(' la linea de '+line_selected.name) : 
                                        group_selected?('l grupo de '+group_selected.name) :
                                        program_selected?('l programa de '+program_selected.name) :
                                        ' toda la Universidad' }} </h4>
        <div>
          <GChart type="LineChart" :data="activities" :options="chartOptions1" style="width:100%"/>
          <small v-if="isLoading">Consiguiendo estadisticas de las investigaciones...</small>
        </div>

        <div style="display:flex; justify-content: flex-start; margin-top: 2em; margin-bottom:2em">
          <div style="width: 45%">
            <a-input placeholder="Buscar investigacion" v-model="filter.text" style="width:95%" v-on:keyup.enter="fetch();fetchPublications()"/>
          </div>
          <div>
            <a-range-picker style="margin:0 5px;width:95%" v-model="filter.range" :placeholder="['Fecha de inicio', 'Fecha de fin']"/>
          </div>
          <div>
            <a-button type="primary" icon="search" size="small" @click="fetch();fetchPublications()">Buscar</a-button>
          </div>
        </div>
        <small v-if="loading">Consiguiendo proyectos y artículos, esto podria tardar unos segundos...</small>
        <a-table :columns="columns" :data-source="data" :row-key="record => record.id" :loading="loading" :pagination="pagination" @change="handleTableChange">
          <span slot="research" slot-scope="text, record">
            <h4 style="font-size:15px;color:#06898f;font-weight:600">{{record.name}}</h4>
            <p style="margin-bottom:10px">
              <a-button v-for="author in record.research_authors" :key="author.id" @click="showAuthor(author.id)" style="margin:0 5px 5px 0;height:23px;">
                <span :style="author.role=='TI'?'font-weight:601':''">{{author.fullname}}</span>   
              </a-button>  
            </p>
            <p style="margin-bottom:10px;font-style:italic;color:#7d7d7d">Año: {{get_year(record.date)}}<a-divider type="vertical" />
              <a :href="record.url" target="_blank" title="Click para ver el articulo"><img v-if="record.indexed" :src="'images/'+record.indexed+'.jpg'" style="height:20px"></a> <a-divider type="vertical" />
              <span v-show="record.doi">DOI: {{record.doi}}</span><a-divider type="vertical" />
              <span v-show="record.journal"> {{record.journal}}</span>
            </p>
            <p style="margin-bottom:10px; color:green" v-show="record.url">{{'URL: '+record.url}}</p>
          </span>
          <span slot="state" slot-scope="text, record">
            <a-tag :key="record.id" >{{get_type_outcome(record.type)}}</a-tag>   
          </span>
        </a-table>
      </div>
    </a-col>
  </a-row>

  <a-modal id="modalAuthor" title="INFORMACION DEL INVESTIGADOR"  :visible="showModal" cancelText="Cerrar" @cancel="showModal = false" :footer="null" width="80%">
    <a-row  style="margin-bottom:1.5em">
      <a-col class="gutter-row" :span="4">
        <img :src="author.photo==''?'images/none_perfil.png':author.photo" style="width:150px">
      </a-col> 
      <a-col class="gutter-row" :span="12">
        <h4>{{author.name+' '+author.lastname}}</h4>
        <p>{{author.biography}}</p>
        <p style="margin-bottom:8px"><strong>Lineas de investigacion</strong></p>
        <a-tag v-for="line in author.lines" :key="line.id" style="margin:0 5px 5px 0">{{line.name}}</a-tag>
        <a v-show="author.orcid" :href="author.orcid" target="_blank" rel="noopener noreferrer" style="color:#365f8e;">
          <p style="margin-top:12px"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/ORCID_iD.svg/18px-ORCID_iD.png" style="margin-right:8px"/>{{author.orcid}}</p>
        </a>
        <a v-show="author.email" :href="'mailto:'+author.email" style="color:#365f8e">
          <p style="display:flex;align-items:center;margin-top:12px"><a-icon type="mail" theme="filled" style="margin-right:8px"/> {{author.email}}</p> 
        </a>
      </a-col>
      <a-col class="gutter-row" :span="8">
        <GChart
          type="ColumnChart"
          :data="author_activities"
          :options="chartOptions"
        />
      </a-col>
    </a-row>

    <a-row>
      <a-table :columns="author_columns" :data-source="author.investigations">
        <span slot="research" slot-scope="text, record">
          <p>{{record.title}}</p>
        </span>
        <span slot="date_init" slot-scope="text, record">
          <p>{{moment(record.date_init).format('D/MM/YYYY')}}</p>
        </span>
        <span slot="state" slot-scope="text, record">
          <p>{{record.state}}</p>
        </span>
      </a-table>
    </a-row>
  </a-modal>
</div>

</template>
<script>
import moment from "moment";
const {ARTICLE_OUTCOME, TESIS_RESEARCH} = require("../constants");
import Repository from "../repositories/RepositoryFactory";
import { GChart } from 'vue-google-charts';
const PersonRepository = Repository.get("person");
const ResearchRepository = Repository.get("research");
const CategoryRepository = Repository.get("category");
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

const outcome_types = [ {id:0, name:"Todos"}, {id:1, name:"Proyecto"}, {id:4, name:"Articulo"} ];

const columns = [
  {
    title: 'INVESTIGACIONES',
    key:"investigacion",
    scopedSlots: { customRender: 'research' },
  },
  {
    title: 'TIPO',
    key: 'state',
    scopedSlots: { customRender: 'state' },
    align: 'center',
  },
];

const author_columns=[
{
    title: 'INVESTIGACIONES',
    key:"investigacion",
    scopedSlots: { customRender: 'research' },
  },
  {
    title: 'Inicio',
    key: 'date_init',
    scopedSlots: { customRender: 'date_init' },
    width: '100px'
  },
  {
    title: 'Estado',
    key: 'state',
    scopedSlots: { customRender: 'state' },
    width: '100px'
  },
];

/*---------- */
export default {
  data(){
    return {
      moment,
      isLoadingGraph:false,
      isLoading: false,
        faculty_selected:{},
        program_selected:null,
        group_selected:null,
        line_selected:null,
        showResearch:true,
        showAuthors:false,
        showPrograms:false,
        showThesis:false,
        ARTICLE_OUTCOME,
        TESIS_RESEARCH,
        labelCol: { span: 7 },
        wrapperCol: { span: 14 },
        data:[],
        filter:{text:null, range:null, state:null},
        author:{},
        authors:[],
        showModal:false,
        // showModalmember:false,
        // members:[],
        authors:[],
        fullprograms:[],
        programs:[],
        program:{},
        columns,
        author_columns,
        outcome_types,
        pagination: {},
        loading: false,
        renacyt_levels:[],
        showlines:0,
        showprograms:0,
        showgroups:0,
        filter_lines:"",
        author_activities: [
          ['Año', 'Cantidad'],
          [0,0]
        ],
        chartOptions: {
          chart: {
            title: 'Investigaciones',
          }
        },
        activities: [
          ['Año', 'Investigaciones'],
          [0,0]
        ],
        chartOptions1: {
          legend:{
            display:true,
            position:'top',
          },
          chart: {
            title: 'Investigaciones por año',
          }
        },
        faculty_activities: [
          ['Año', 'Investigaciones'],
          [0,0]
        ],
        chartOptions2: {
          legend:{
            display:true,
            position:'top',
          },
          chart: {
            title: 'Investigaciones por año, de la Facultad',
          }
        }
      }
    },
    components: { GChart, Loading },
    async mounted() {
      await this.fetch();
      this.isLoading=true;
      await this.fetchPublications();
      this.isLoading=false;
      await this.fetchLines();
    },
    methods:{
      clean(){
        this.program_selected=null;
        this.group_selected=null;
        this.line_selected=null;

        this.filter.program_id=null;
        this.filter.group_id=null;
        this.filter.line_id=null;
      },
      show(menu, params){
        this.showResearch = false;
        this.showAuthors = false;
        this.showPrograms = false;
        this.showThesis = false;

        this.filter.type = null;
        this.filter.type_research = null;
        this.filter.external = null;

        if (menu == 'authors') {
          this.showAuthors = true;
          this.get_authors();
        } else if (menu == 'programs') {
          this.faculty_selected = this.programs.find(e => e.id == params.faculty_id);
          this.filter_lines='';
          this.searchInPrograms();
          this.fetchPublications({faculty_id:this.faculty_selected.id});
          this.showPrograms = true;
        } else if (menu == 'research') {
          this.fetchPublications();
          this.fetch();
          this.showResearch = true;
        } else if (menu == 'publisheds') {
          this.filter.type = ARTICLE_OUTCOME;
          this.fetchPublications();
          this.fetch();
          this.showResearch = true;
        } else if (menu == 'externals') {
          this.filter.external = 1;
          this.fetchPublications({external:1});
          this.fetch({external:1});
          this.showResearch = true;
        }else if (menu == 'thesis') {
          this.filter.type_research = TESIS_RESEARCH;
          this.fetchPublications();
          this.fetch();
          this.showResearch = true;
        }
      },
      async get_authors(params = {}){
        this.loading = true;
        if(this.filter.search) params.search=this.filter.search;
        if(this.filter.program_id) params.program_id=this.filter.program_id;
        if(this.filter.group_id) params.group_id=this.filter.group_id;
        if(this.filter.line_id) params.line_id=this.filter.line_id;
        params.xpage = 20;
        const {data} = await PersonRepository.get_authors(params);
        this.authors = data.data;
        this.loading = false;
      },
      searchInPrograms(){
        let groups_=  this.faculty_selected.children;
        
        let new_groups_ = [];
        for(var i=0; i<groups_.length;i++){
          let line_found=false;
          let match_lines = groups_[i].children.filter(el=> el.name.toLowerCase().indexOf(this.filter_lines)>=0 );
          if(match_lines.length>0){
            line_found=true;
            let g = {...groups_[i]};
            g.children_=match_lines;
            new_groups_.push(g);
          }
           if(!line_found){
            if( groups_[i].name.toLowerCase().indexOf(this.filter_lines)>=0 ) new_groups_.push({...groups_[i]});
          } 
        }
        let fac = {...this.faculty_selected};
        fac.children_ = new_groups_;
        this.faculty_selected = fac;
      },
      async fetchLines(){
        try {
          let {data} = await CategoryRepository.list({inc_counts:true}); 
          data.forEach(e => {//fac
            e.children.forEach(el => {//grupos
              let authors = el.children.reduce( (s,l) => s+ (l.author_count||''), '' );
             let total_authors = 0;
              if(authors!='') total_authors = [...new Set(authors.split(","))].length;
              el.author_count = total_authors;
              el.children.forEach( line => {  
                line.author_count  = (line.author_count?line.author_count.split(','):[]).length;  } );
              el.research_count = el.children.reduce((s,l)=> s+l.research_count,0);
              el.published_count = el.children.reduce((s,l)=> s+l.published_count,0);
              el.thesis_count = el.children.reduce((s,l)=> s+l.thesis_count,0);
              el.external_count = el.children.reduce((s,l)=> s+l.external_count,0);
            });
          });
          this.programs=data;
          this.fullprograms=data;
        } catch (error) {
          this.error("Los datos no se cargaron correctamente");
        }
      },
      async fetchPublications(params={}){
        try {
          params.text=this.filter.text;
          if(this.filter.range?.length){
            params.from=this.filter.range[0].format("YYYY-MM-DD");
            params.to=this.filter.range[1].format("YYYY-MM-DD");
          }
          
          params.type_research=this.filter.type_research;
          params.type=this.filter.type;
          params.line_id=this.filter.line_id;
          params.group_id=this.filter.group_id;
          params.program_id=this.filter.program_id;
          const {data} = await ResearchRepository.list_outcomes_by_year(params); 
          this.activities=[['Año', 'Investigaciones'],["0",0]];
            for(var $i=0;$i<data.length;$i++){
              this.activities.push([data[$i].year+'',data[$i].total]);
          }
        } catch (error) {
          this.error("Los datos no se cargaron correctamente");
        }
      },
      toggleFaculty( index){
          this.showprograms ++;
          this.programs[index].showprograms=!this.programs[index].showprograms;
      },
      toggleProgram( index){
        this.showgroups ++;
        this.faculty_selected.children_[index].showgroups=!(this.faculty_selected.children_[index].showgroups||false);
      },
      toggleGroup(  program_index, group_index ){
          this.showlines ++;
          this.faculty_selected.children[program_index].children[group_index].showlines=!(this.faculty_selected.children[program_index].children[group_index].showlines||false);
      },
      onSelect(selectedKeys, info) {
          return selectedKeys, info;
      },
      async showAuthor(id){
        this.showModal = true;
        try {
          const {data} = await PersonRepository.get_author(id);
          this.author=data.author;
          this.author.investigations=data.research;
          this.author.lines=data.lines;
          this.get_author_activity(id)
        } catch (error) {
          this.error("Los datos no se cargaron correctamente");
        }
      },
      async get_author_activity(author_id){
        try {
          const {data} = await PersonRepository.get_author_activity(author_id); 
          this.author_activities=[['Año', 'Cantidad']];
          for(var $i=0;$i<data.length;$i++){
            this.author_activities.push([data[$i].year+'',data[$i].q]);
          }
        } catch (error) {
          this.error("Los datos no se cargaron correctamente");
        }
      },
      get_type_outcome($type){
        let $name_type="";
        switch($type){
          case 1:$name_type="Proyecto";break;
          case 2:$name_type="Avance";break;
          case 3:$name_type="Informe";break;
          case 4:$name_type="Articulo";break;
        }
        return $name_type;
      }, 
      get_year($date){
        let $d="";
        $d=new Date($date);
        return $d.getFullYear();
      },
      parseAuthor(list){
        for(var i=0; i<list.length;i++){
          list[i].research_authors=[];
          let authors = list[i].authors;
          let authors_ = authors.split(",");
          for( var j=0;j<authors_.length;j++ ){
            let author_ = authors_[j].split("|");
            let id=author_[0];
            let fullname=author_[1];
           let role=author_[2]; //main_author is role, now
            let photo=author_[3];
            let author={ id, fullname,role, photo }; //main_author is role, now
            list[i].research_authors.push(author);
          }
        }
        return list;
      },
      listResearchByCategory(category, type){
        this.showPrograms = false;
        this.showResearch = true;
        this.program_selected=null;
        this.group_selected=null;
        this.line_selected=null;
        this.filter.program_id = null;
        this.filter.group_id = null;
        this.filter.line_id = null;
        if (category.type == 'Linea') { this.line_selected = category; this.filter.line_id=category.id;}
        else if (category.type == 'Grupo') {this.group_selected = category; this;this.filter.group_id=category.id;}
        else if (category.type == 'Programa') {this.program_selected = category; this.filter.program_id=category.id;}
        
        if (type=='research') this.show("research");
        else if (type=='publisheds') this.show("publisheds");
        else if (type=='thesis') this.show("thesis");
        else if (type=='externals') this.show("externals");
      },
      listAuthorsByCategory(category, type){
        this.showPrograms = false;
        this.showResearch = false; 
        this.program_selected=null;
        this.group_selected=null;
        this.line_selected=null;
        this.filter.program_id = null;
        this.filter.group_id = null;
        this.filter.line_id = null;
        if (category.type == 'Linea') { this.line_selected = category; this.filter.line_id=category.id;}
        else if (category.type == 'Grupo') {this.group_selected = category; this;this.filter.group_id=category.id;}
        else if (category.type == 'Programa') {this.program_selected = category; this.filter.program_id=category.id;}
        this.show("authors");
      },
      async fetch(params = {}){
        try{
          params.text=this.filter.text;
          if(this.filter.range?.length){
            params.from=this.filter.range[0].format("YYYY-MM-DD");
            params.to=this.filter.range[1].format("YYYY-MM-DD");
          }
          params.type_research=this.filter.type_research;
          params.type=this.filter.type;
          params.line_id=this.filter.line_id;
          params.group_id=this.filter.group_id;
          params.program_id=this.filter.program_id;
          this.loading = true;
          const {data} =  await ResearchRepository.list_outcomes(params);
          this.data=this.parseAuthor(data.data);

          const pagination = {...this.pagination};
          pagination.total=data.total;
          this.pagination=pagination;
          this.loading = false;
        }catch(error){
          this.error("Los datos no se cargaron correctamente");
        }
      },
      error (message) {
        this.$message.error(message||'Error al procesar');
      },
      success (message) { 
        this.$message.success(message||'Proceso Correcto');
      },
      onChangePhoto(data){
        this.people.photo = data;
        this.pictureKey+=1;
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

<style>
  .sidebar_menu{
    padding-right:10px
  }
  .ant-table-tbody > tr > td{
    border-bottom:1px solid #ccc;
  }
  .line-link:hover{
    text-decoration: underline;
  }
  .line-hoverable:hover{
    background-color: #f5f5f5;
  }
  @media screen and (max-width: 577px){
    .sidebar_menu{
      display:none;
    }
    .main_section{
      width: 100%;
    }
    .menu_text{
      font-size: 13px;
    }
  }
</style>