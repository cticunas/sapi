<template>
    <div style="padding:10px 20px 10px 10px">
        <div class="vld-parent">
            <loading :active.sync="isLoading"
            :can-cancel="false"
            :is-full-page="true"
            color="#1890ff"></loading>
        </div>

        <h4>Areas por Facultad </h4>

        <div>
            <vue-tree-list
                :model="data"
                default-tree-node-name="Parent"
                default-leaf-node-name="Children"
                v-bind:default-expanded="false"
            >
                <template v-slot:leafNameDisplay="slotProps">
                    <span>
                        <span :style=" slotProps.model.comment=='create from research'?'background:#eee':'' " class="names">{{ slotProps.model.name }}</span>
                        <span style="color:#7CA9FF" class="icon" @click="add(slotProps.model)"><a-icon type="plus" title="Añadir"/></span>
                        <span style="color:#7CA9FF" class="icon" v-if="slotProps.model.type" @click="edit(slotProps.model)"><a-icon type="edit" title="Editar"/></span>
                        <a-popconfirm v-if="slotProps.model.type" title="¿Realmente deseas eliminar?" ok-text="Si" cancel-text="No" @confirm="() => remove(slotProps.model)">
                            <a-icon title="Quitar" style="color:#7CA9FF" class="icon" type="delete" />
                        </a-popconfirm>
                    </span>
                </template>

                <template v-slot:addTreeNodeIcon="">
                    <span class="icon" style="display:none">📂</span>
                </template>
                <template v-slot:addLeafNodeIcon="">
                    <span class="icon" style="display:none">＋</span>
                </template>
                <template v-slot:editNodeIcon="slotProps">
                    <span class="icon" style="display:none" @click="edit(slotProps.model)">📃</span>
                </template>
                <template v-slot:delNodeIcon="slotProps">
                    <span class="icon" style="display:none" @click="remove(slotProps.model)">✂️</span>
                </template>
            </vue-tree-list>
        </div>

        <a-modal :title="category.type == 'Programa' ? 'Programa' : category.type == 'Grupo' ? 'Grupo' : category.type == 'Linea' ? 'Linea' : ''" :visible="showModal" cancelText="Cancelar" :key="showModal" okText="Guardar"
            @ok="save"
            @cancel="showModal = false">
            <a-form-model ref="userForm" :model="category" :label-col="labelCol" :wrapper-col="wrapperCol">
                <a-row>
                <a-col class="gutter-row" :span="24">
                    <h5 style="margin-bottom:1em">{{ parent?('Para: '+ parent.name):'' }}</h5>

                    <a-form-model-item label="Código" prop="code">
                    <a-input v-model="category.code" />
                    </a-form-model-item>

                    <a-form-model-item label="Nombre" prop="name">
                    <a-input v-model="category.name" />
                    </a-form-model-item>
                </a-col>
                </a-row>
            </a-form-model>
        </a-modal>

    </div>
</template>
<script>
import { VueTreeList, Tree} from 'vue-tree-list'
import Repository from "../repositories/RepositoryFactory";
const CategoryRepository = Repository.get("category");
// const OrganizationRepository = Repository.get("organization");
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

/*---------- */
export default {
    components: {
        VueTreeList,
        Loading,
    },
    data(){
        return {
        isLoading: false,
        parent,
        // filter: {search:null},
        labelCol: { span: 7 },
        wrapperCol: { span: 14 },
        category: { },
        pagination: {},
        loading: false,
        showModal:false,
        data: new Tree([{ children: [{}], }]),
        }
    },
    async mounted() {
        this.isLoading=true;
        await this.fetch();
        this.isLoading=false;
    },
    methods:{
        async fetch(params = {}){
            // if(this.filter.search) params.search=this.filter.search;
            let {data} =  await CategoryRepository.list(params);
            // console.log(data);
            for(let i=0; i<data.length; i++){
                data[i].dragDisabled = true;
                data[i].addTreeNodeDisabled = true;
                data[i].addLeafNodeDisabled = true;
                data[i].editLeafNodeDisabled = false;
                data[i].editNodeDisabled = true;
                data[i].delNodeDisabled = true;
                for(let j=0; j<data[i].children.length; j++){
                    data[i].children[j].dragDisabled = true;
                    data[i].children[j].isLeaf = true;
                    for (let k=0; k<data[i].children[j].children.length; k++) {
                        data[i].children[j].children[k].dragDisabled = true;
                        data[i].children[j].children[k].isLeaf = true;
                        for (let s = 0; s < data[i].children[j].children[k].children.length; s++) {
                            data[i].children[j].children[k].children[s].dragDisabled = true;
                            data[i].children[j].children[k].children[s].isLeaf = true;
                        }
                    }
                }
            }
            this.data = new Tree(data);
        },
        error (message) {
            this.$message.error(message||'Error al procesar');
        },
        success (message) {
            this.$message.success(message||'Proceso Correcto');
        },
        add(parent){
            try {
                // console.log(parent )
                this.parent=parent;
                this.category={};
                // if( parent.type ){ //es un category
                //     this.category.parent_id=parent.id;
                //     if (parent.type == 'Programa') this.category.type='Grupo';
                //     else if (parent.type == 'Grupo') this.category.type='Linea';
                //     else if (parent.type == 'Linea') throw ('No se puede agregar mas categorias en este nivel');
                // }else{
                //     this.category.type='Grupo';
                //     this.category.organization_id = parent.id;
                // }
                if(parent.type){
                    this.category.parent_id=parent.id;
                    // if (parent.type == 'Programa') this.category.type='Area';
                    if (parent.type == 'Area') this.category.type='Grupo';
                    else if (parent.type == 'Grupo') this.category.type='Linea';
                    else if (parent.type == 'Linea') throw ('No se puede agregar mas categorias en este nivel');
                } else {
                    this.category.type='Area';
                    this.category.organization_id = parent.id;
                }
                this.showModal=true;
            } catch (error) {
                this.error(error)
            }
        },
        async save(){
            try {
                if (!this.category.code) throw ('El código es requerido');
                if (!this.category.name) throw ('El nombre es requerido');

                this.showModal=false;
                this.loading=true;
                let payload = this.category;
                await CategoryRepository.save(payload)
                this.success();
                this.fetch();
            } catch (error) {
                this.error(error);
            }
        },
        edit(model){
            // console.log(model)
            this.category = {
                id: model.id,
                name:model.name,
                code: model.code,
                type: model.type,
                organization_id: model.organization_id,
                parent_id: model.parent_id,
                status: model.status,
            };
            this.showModal = true;
        },
        async remove(model){
            try {
                this.loading=true;
                await CategoryRepository.delete(model.id)
                this.success();
                this.fetch();
            } catch (error) {
                this.error(error)
            }
        },
    }
}
</script>

<style scoped>
    .icon{
        padding: 0 8px;
        margin: 0;
    }
    .icon:hover {
        cursor: pointer;
    }
    .names{
        padding-left: .6em;
    }
    .muted {
        color: gray;
        font-size: 80%;
    }
    .vtl .vtl-drag-disabled {
        background-color: #d0cfcf;
    }
    .vtl .vtl-drag-disabled:hover {
        background-color: #d0cfcf;
    }
    .vtl-disabled {
        background-color: #d0cfcf;
    }
</style>
