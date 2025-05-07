<template >
    <!--  v-if create terminal-->
    <div v-if="type === 'createterminal'"
        class="box box-objective user-select-none pointer"
        :style="{ 'background-color': '#fff'}"
        @click.prevent="openTerminalModal()"
    >
        <h1
            class="h5 position-absolute text-center w-100"
            style="top: 20px;"
        >
            {{ trans("global.terminalObjective.title_singular") }}
        </h1>
        <div style="text-align: center; padding: 25px; font-size: 100px;">+</div>
    </div>

    <!--  v-else-if create enabling-->
    <div v-else-if="type === 'createenabling'"
        class="box box-objective user-select-none pointer"
        :style="{ 'background-color': backgroundcolor }"
        @click.prevent="openEnablingModal()"
    >
        <h1
            class="h5 position-absolute text-center w-100"
            style="top: 20px;"
        >
            {{ trans("global.enablingObjective.title_singular") }}
        </h1>

        <div style="text-align: center; padding: 25px; font-size: 100px;">+</div>
    </div>

    <!--  v-else-if render existing objective-->
    <div v-else
        :id="id"
        class="box box-objective"
        :style="{ 'background-color': backgroundcolor, 'border-color': bordercolor }"
    >
        <!-- don't load Header if it isn't needed -->
        <Header
            :objective="objective"
            :objective_type_id="objective_type_id"
            :type="type"
            :menuEntries="menuEntries"
            :settings="settings"
            :max_id="max_id"
            :textcolor="textcolor"
        />

        <div
            class="panel-body boxwrap pointer"
            @click.prevent="showDetails()"
        >
            <div
                class="boxscroll hide-scrollbars"
                style="background-color: inherit;"
            >
                <div
                    class="boxcontent"
                    :style="{ 'color': textcolor }"
                    v-html="objective.title"    
                ></div>
            </div>
        </div>

        <Footer
            :objective="objective"
            :textcolor="textcolor"
            :type="type"
            :settings="settings"
        />
    </div>
</template>
<script>
import Header from './Header.vue';
import Footer from './Footer.vue';
import {useGlobalStore} from "../../store/global";

export default {
    props: {
        objective: {
            type: Object,
            default: {},
        },
        objective_type_id: {
            type: Number,
            default: null,
        },
        type: {
            type: String,
            default: null,
        },
        referenceable_id: {
            type: Number,
            default: null,
        },
        referenceable_type: {
            type: String,
            default: null,
        },
        color: {
            type: String,
            default: '#000',
        },
        settings: {
            type: Object,
            default: undefined,
        },
        editable: {
            type: Boolean,
            default: false,
        },
        max_id: {
            type: Number,
            default: null,
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        }
    },
    data() {
        return {
            menuEntries:  [
                {
                    title: 'Edit',
                    icon: 'fa fa-pencil-alt',
                    action: 'edit',
                    model: this.type + 'Objective',
                    value: this.type + '-objective-modal',
                },
                {
                    title: 'Move',
                    icon: 'fa fa-repeat',
                    action: 'move',
                    model: this.type + 'Objective',
                    value: 'move-' + this.type + '-objective-modal',
                },
                {
                    hr: true,
                },
                {
                    title: 'Delete',
                    icon: 'fa fa-trash',
                    action: 'delete',
                    model: this.type + 'Objective',
                }
            ],
            visibility: 100,
        }
    },
    methods: {
        openTerminalModal() { 
            this.globalStore?.showModal('terminal-objective-modal', {
                curriculum_id: this.objective.curriculum_id,
                objective_type_id: this.objective_type_id,
            });
        },
        openEnablingModal() {
            this.globalStore?.showModal('enabling-objective-modal', {
                curriculum_id: this.objective.curriculum_id,
                terminal_objective_id: this.objective.terminal_objective_id,
            });
        },
        showDetails() {
            if (this.settings?.achievements === undefined || !this.editable) {
                location.href= '/' + this.type + 'Objectives/' + this.objective.id;
            } else {
                this.globalStore?.showModal('set-achievements-modal', {
                    objective: this.objective,
                    referenceable_id: this.referenceable_id,
                    referenceable_type: this.referenceable_type,
                });
            }
        },
    },
    computed: {
        background: function () {
            return (this.type === 'terminal' ? 'none' : "");
        },
        backgroundcolor: function () {
            return this.type === 'terminal'
                ? this.objective.color + (this.objective.visibility ? 'FF' : '40')
                : "#fff";
        },
        bordercolor: function () {
            return this.type === 'terminal'
                ? this.objective.color + (this.objective.visibility ? 'FF' : '40')
                : this.color + (this.objective.visibility ? 'FF' : '40');
        },
        textcolor: function() {
            if (this.type === 'terminal') {
                return this.$textcolor(this.objective.color);
            } else {
                return "#000";
            }
        },
        id: function() {
            return this.type + '_' + this.objective.id;
        },
        cross_reference: function() {
            if (typeof this.settings !== "undefined") {
                return this.settings.cross_reference_curriculum_id;
            } else {
                return false;
            }
        },
    },
    watch: {
        cross_reference: function() {
            if ((this.settings.cross_reference_curriculum_id !== false) || (this.settings.cross_reference_curriculum_id === "")) { // reset view with x button
                this.visibility = 40;

                if (typeof this.objective.referencing_curriculum_id !== "undefined" ) {
                    if (this.objective.referencing_curriculum_id !== null) {
                        if (this.objective.referencing_curriculum_id.includes(parseInt(this.settings.cross_reference_curriculum_id)))
                        {
                            this.visibility = 100;
                        }
                    }
                }
            } else {
                this.visibility = 100;
            }
        }
    },
    created: function() {
        this.$eventHub.on('deleteObjective', function(deletedObjective) {
            if (this.objective === deletedObjective) {
                this.deleteEvent();
            }
        }.bind(this));
    },
    mounted() {
        // remove move-enabling option, since this option hasn't been implemented yet
        if (this.type === 'enabling') this.menuEntries.splice(1, 1);

        this.$nextTick(() => {
            MathJax.startup.defaultReady();
        });
    },
    components: {
        Header,
        Footer,
    },
}
</script>
