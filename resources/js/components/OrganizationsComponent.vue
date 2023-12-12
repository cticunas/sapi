<template>
    <div style="padding: 10px 20px 10px 10px">
        <div class="vld-parent">
            <loading
                :active.sync="isLoading"
                :can-cancel="false"
                :is-full-page="true"
                color="#1890ff"
            ></loading>
        </div>

        <h4>
            Organizaciones
            <a-button
                type="primary"
                size="small"
                @click="add('faculty', null)"
                title="Nuevo"
                >Nuevo</a-button
            >
        </h4>

        <div>
            <vue-tree-list
                :model="data"
                default-tree-node-name="Parent"
                default-leaf-node-name="Children"
                v-bind:default-expanded="false"
            >
                <template v-slot:leafNameDisplay="slotProps">
                    <span>
                        <span
                            :style="
                                slotProps.model.comment ==
                                'create from research'
                                    ? 'background:#eee'
                                    : ''
                            "
                            >{{ slotProps.model.name }}</span
                        >
                        <span
                            style="color: #7ca9ff"
                            class="icon"
                            @click="add('college', slotProps.model)"
                            ><a-icon type="plus"
                        /></span>
                        <span
                            style="color: #7ca9ff"
                            class="icon"
                            @click="edit(slotProps.model)"
                            ><a-icon type="form"
                        /></span>
                        <a-popconfirm
                            title="¿Realmente deseas eliminar?"
                            ok-text="Si"
                            cancel-text="No"
                            @confirm="() => remove(slotProps.model)"
                        >
                            <a-icon
                                style="color: #7ca9ff"
                                class="icon"
                                type="delete"
                            />
                        </a-popconfirm>
                    </span>
                </template>

                <template v-slot:editNodeIcon="">
                    <span class="icon" style="display: none">🚒</span>
                </template>
                <template v-slot:delNodeIcon="">
                    <span class="icon" style="display: none">✂️</span>
                </template>
            </vue-tree-list>
        </div>

        <a-modal
            :title="parent ? 'Escuela' : 'Facultad'"
            :visible="showModal"
            cancelText="Cancelar"
            :key="showModal"
            okText="Guardar"
            @ok="save"
            @cancel="showModal = false"
        >
            <a-form-model
                ref="userForm"
                :model="typeOrganization"
                :label-col="labelCol"
                :wrapper-col="wrapperCol"
            >
                <a-row>
                    <a-col class="gutter-row" :span="24">
                        <h5 style="margin-bottom: 1em">
                            {{ parent ? "Para: " + parent.name : "" }}
                        </h5>

                        <a-form-model-item label="Código" prop="code">
                            <a-input
                                v-model="typeOrganization.code"
                                @input="
                                    typeOrganization.code =
                                        $event.target.value.toUpperCase()
                                "
                            />
                        </a-form-model-item>

                        <a-form-model-item label="Grado" prop="level">
                            <a-select
                                v-model="typeOrganization.level"
                                placeholder="Seleccione un grado"
                            >
                                <a-select-option
                                    v-for="item in level"
                                    :value="item.id"
                                    :key="item.id"
                                >
                                    {{ item.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-model-item>

                        <a-form-model-item label="Nombre" prop="name">
                            <a-input v-model="typeOrganization.name" />
                        </a-form-model-item>

                        <a-form-model-item
                            label="Documento creación"
                            prop="creation"
                        >
                            <a-input v-model="typeOrganization.creation" />
                        </a-form-model-item>
                    </a-col>
                </a-row>
            </a-form-model>
        </a-modal>
    </div>
</template>
<script>
// import moment from "moment";
import { VueTreeList, Tree, TreeNode } from "vue-tree-list";
import Repository from "../repositories/RepositoryFactory";
const OrganizationRepository = Repository.get("organization");
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";

const level = [
    { id: "1", name: "PREGRADO" },
    { id: "2", name: "POSGRADO" },
];
/*---------- */
export default {
    components: {
        VueTreeList,
        Loading,
    },
    data() {
        return {
            isLoading: false,
            parent: null,
            children: true,
            labelCol: { span: 7 },
            wrapperCol: { span: 14 },
            typeOrganization: {},
            pagination: {},
            level,
            loading: false,
            showModal: false,
            data: new Tree([{ children: [{}] }]),
        };
    },
    async mounted() {
        this.isLoading = true;
        await this.fetch();
        this.isLoading = false;
    },
    methods: {
        async fetch(params = {}) {
            this.loading = true;
            let { data } = await OrganizationRepository.list(params);
            for (let i = 0; i < data.length; i++) {
                data[i].dragDisabled = true;
                data[i].addTreeNodeDisabled = true;
                data[i].addLeafNodeDisabled = true;
                data[i].editLeafNodeDisabled = false;
                data[i].editNodeDisabled = true;
                data[i].delNodeDisabled = true;
                for (let j = 0; j < data[i].children.length; j++) {
                    data[i].children[j].dragDisabled = true;
                    data[i].children[j].isLeaf = true;
                    // for (let k = 0; k < data[i].children[j].children.length; k++) {
                    //     data[i].children[j].children[k].dragDisabled = true;
                    //     data[i].children[j].children[k].isLeaf = true;
                    // }
                }
            }
            this.loading = false;
            this.data = new Tree(data);
        },
        error(message) {
            this.$message.error(message || "Error al procesar");
        },
        success(message) {
            this.$message.success(message || "Proceso Correcto");
        },
        add(type, parent) {
            try {
                this.parent = parent;
                this.typeOrganization = { level: "1" };
                if (this.parent) this.typeOrganization.parent_id = parent.id;
                if (parent && parent.parent_id)
                    throw "No se puede agregar mas niveles";
                this.showModal = true;
            } catch (error) {
                this.error(error);
            }
        },
        async save() {
            try {
                if (!this.typeOrganization.code) throw "El codigo es requerido";
                if (!this.typeOrganization.level) throw "El grado es requerido";
                if (!this.typeOrganization.name) throw "El nombre es requerido";
                if (!this.typeOrganization.abbreviation)
                    throw "La abreviatura es requerida";
                if (!this.typeOrganization.creation)
                    throw "La fecha de creación es requerida";

                this.showModal = false;
                this.loading = true;
                let payload = this.typeOrganization;
                await OrganizationRepository.save(payload);
                this.success();
                this.fetch();
            } catch (error) {
                this.error(error);
            }
        },
        edit(model) {
            this.typeOrganization = {
                id: model.id,
                code: model.code,
                level: model.level,
                name: model.name,
                abbreviation: model.abbreviation,
                creation: model.creation,
            };
            this.showModal = true;
        },
        async remove(model) {
            this.loading = true;
            await OrganizationRepository.delete(model.id);
            this.success();
            this.fetch();
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
    },
};
</script>

<style scoped>
.icon {
    padding: 0 8px;
    margin: 0;
}
.icon:hover {
    cursor: pointer;
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
