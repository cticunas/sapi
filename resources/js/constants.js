module.exports = {
    DATEFORMAT:"DD/MM/YYYY",
    LOCATION:'Universidad Nacional agraria de la Selva',
    FIN_COMPANY:'Universidad Nacional agraria de la Selva',
    // ROLES
    ADMIN_ROLE:1,
    RESEARCH_ROLE:2,
    UNIT_ROLE:3,
    DGI_ROLE:4,
    COR_ROLE:5,
    //TIPOS ENTREGABLES
    PROJECT_OUTCOME:1,
    ADVANCE_OUTCOME:2,
    FINALINFORM_OUTCOME:3,
    ARTICLE_OUTCOME:4,

    RESEARCH_STATE_NEW: 2,
    RESEARCH_STATE_ANUL: 6,

    RESEARCH_ROLES:[
        {id:'TI', name:'Titular'},
        {id:'AS', name:'Asesor'},
        {id:'OR', name:'Ordinario'},
        {id:'EO', name:'Extra Ordinario'},
        {id:'CO', name:'Consultor'},
        {id:'OT', name:'Otros'},
        {id:'IC', name:'Ini. Cientfica'},],
    OUTCOME_TYPES:[
        {id:1, name:"Proyecto"},
        {id:2, name:"Avance"},
        {id:3, name:"Informe Final"},
        {id:4, name:"Articulo"},
        {id:5, name:"Informe de experiencia"},
        {id:6, name:"Libro"},
    ],
    RESEARCH_TYPES:[
        {id: '1',name: 'Tesis'},
        {id: '2',name: 'Investigacion Docente'},
        {id: '3',name: 'Informe de experiencia'},
        {id: '4',name: 'Proyecto de innovación'}
    ],
    TESIS_RESEARCH:1,
    PROFESSOR_RESEARCH:2,
    EXPERIENCE_RESEARCH:3,
    INNOVATION_RESEARCH:4,
    PERIOD_DEFAULT:'T',
    PERIOD_TYPES:[
        {id:'M', name:'Mensual',active:true, periods:[{id:1, name:'Ene'},{id:2, name:'Feb'},{id:3, name:'Mar'},{id:4, name:'Abr'},{id:5, name:'May'},{id:6, name:'Jun'},{id:7, name:'Jul'},{id:8, name:'Ago'},{id:9, name:'Set'},{id:10, name:'Oct'},{id:11, name:'Nov'},{id:12, name:'Dic'}]},
        {id:'B', name:'Bimestral',active:false,periods:[{id:1, name:'Bi.1'},{id:2, name:'Bi.2'},{id:3, name:'Bi3'},{id:4, name:'Bi4'},{id:5, name:'Bi5'},{id:6, name:'Bi6'}]},
        {id:'T', name:'Trimestral',active:true,periods:[{id:1, name:'Tr.1'},{id:2, name:'Tr.2'},{id:3, name:'Tr.3'},{id:4, name:'Tr.4'}]},
        {id:'S', name:'Semestral',active:false,periods:[{id:1, name:'St.1'},{id:2, name:'St.2'}]},
        {id:'A', name:'Anual',periods:[{id:1, name:'Anual'}]},
    ],
    INDEXED_BDS: [
        {id: 'LatIndex',name: 'LatIndex'},
        {id: 'Scielo',name: 'Scielo'},
        {id: 'Scopus',name: 'Scopus'},
        {id: 'Web of Science',name: 'Web of Science'},
        {id: 'Otros',name: 'Otros'}
    ],
    FINANCINGS:[
        {id:'1', name:'Financiamiento interno'},
        {id:'2', name:'Financiamiento externo'},
        {id:'3', name:'Autofinanciado'}
    ],
    GRADES: [
        {id: '1',name: 'PREGRADO',},
        {id: '2',name: 'POSGRADO',},
    ],
    GENDERS: [
        {id: 'MASCULINO', name: 'MASCULINO'},
        {id: 'FEMENINO', name: 'FEMENINO'},
    ]
}
