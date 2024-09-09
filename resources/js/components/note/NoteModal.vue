<template>
    <Transition name="modal">
        <div v-if="show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    <span v-if="method === 'post'">
                        {{ trans('global.note.create') }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.note.edit') }}
                    </span>
                </h3>
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            @click="$emit('close')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <Notes :notable_type="this.form.notable_type"
                       :notable_id="this.form.notable_id"
                       :show_tabs="this.form.show_tabs"></Notes>
            </div>

            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="note-save"
                         class="btn btn-primary"
                         @click="$emit('close')" >
                         {{ trans('global.close') }}
                     </button>
                </span>
            </div>
        </div>
    </div>
    </Transition>
</template>
<script>
    import Notes from "./Notes.vue";
    import Form from 'form-backend-validation';

    export default {
        components:{
            Notes,
        },
        props: {
            show: {
                type: Boolean
            },
            params: {
                type: Object
            },
        },
        data() {
            return {
                component_id: this.$.uid,
                form: new Form({
                    'notable_type': false,
                    'notable_id': false,
                    'show_tabs': true,
                }),
            }
        },
        watch: {
            params: function(newVal, oldVal) {
                this.form.reset();
                this.form.populate(newVal);
            },

        },
        methods: {},
        mounted() {},
    }
</script>

