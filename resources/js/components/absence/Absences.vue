<template>
    <div>
        <Absence v-for="absence in absences"
            v-bind:data="absence"
            v-bind:key="'absence_container' + absence.id"
            :absence="absence"
        ></Absence>

        <table class="table table-hover datatable media_table">
            <tbody>
                <tr>
                    <td
                        class="py-2 link-muted text-sm pointer"
                        v-permission="'absence_create'"
                        @click.prevent="open();"
                    >
                        <i class="fa fa-plus px-2 "></i> {{ trans('global.absences.create') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import Absence from '../absence/Absence.vue'
import {useGlobalStore} from "../../store/global";
export default {
    props: {
        subscribable_type: String,
        subscribable_id: Number,
        logbook: {},
        entry: {},
    },
    setup() {
        const globalStore = useGlobalStore();

        return {
            globalStore,
        }
    },
    data() {
        return {
            errors: {},
            absences: Array,
        }
    },
    methods: {
        open() {
            this.globalStore?.showModal('absence-modal',{
                referenceable_type: this.subscribable_type,
                referenceable_id: this.subscribable_id,
            })
        },
        loaderEvent() {
            axios.get('/absences?referenceable_type=' + this.subscribable_type + '&referenceable_id=' + this.subscribable_id)
                .then(response => {
                    this.absences = response.data;
                })
                .catch(e => {
                    console.log(e);
                });
        },
    },
    mounted() {
        this.$eventHub.on('absence-added', function(newContent) {
            this.globalStore?.closeModal('absence-modal');
            this.loaderEvent();
        }.bind(this));

        this.$eventHub.on('absence-updated', function(newContent) {
            this.globalStore?.closeModal('absence-modal');
            this.loaderEvent();
        }.bind(this));
    },
    components: {
        Absence
    },
}
</script>