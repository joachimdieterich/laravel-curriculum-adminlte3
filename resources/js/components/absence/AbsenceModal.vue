<template>
    <modal
        id="absence-modal"
        name="absence-modal"
        height="auto"
        :adaptive=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @opened="opened"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card"
             style="margin-bottom: 0px !important">
            <div class="card-header">
                 <h3 class="card-title">
                     <span v-if="method === 'post'">
                        {{ trans('global.absences.create')  }}
                    </span>
                    <span v-if="method === 'patch'">
                        {{ trans('global.absences.edit')  }}
                    </span>
                 </h3>

                 <div class="card-tools">
                     <button type="button" class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                     </button>
                     <button type="button" class="btn btn-tool" data-widget="remove" @click="close()">
                        <i class="fa fa-times"></i>
                     </button>
                 </div>
            </div>

            <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="form-group "
                    :class="errors.reason ? 'has-error' : ''"
                      >
                    <label for="reason">{{ trans('global.absences.fields.reason') }} *</label>
                    <input
                        type="text" id="reason"
                        name="reason"
                        class="form-control"
                        v-model="reason"
                        placeholder="Fehlgrund"
                        required
                        />
                     <p class="help-block" v-if="errors.reason" v-text="errors.reason[0]"></p>
                </div>

                 <div class="form-group ">
                    <label for="categorie">
                        {{ trans('global.user.title') }}
                    </label>
                    <select name="users[]"
                            id="users"
                            class="form-control select2 "
                            style="width:100%;"
                            multiple=true>
                         <option v-for="(item,index) in user_list" v-bind:value="item.id">{{ item.username }}</option>
                    </select>
                </div>

                <div class="form-group ">
                    <label for="categorie">
                        {{ trans('global.absences.fields.time') }}
                    </label>
                    <input name="time"
                           type="number"
                           id="time"
                           step="5"
                           v-model="time"
                           class="form-control "
                           style="width:100%;"/>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="done" v-model="done" >
                    <label class="form-check-label" for="visibility">{{ trans('global.absences.fields.done') }}</label>
                </div>

            </div>

            <div class="card-footer">
                <span class="pull-right">
<!--                     <button type="button" class="btn btn-primary" data-widget="remove" @click="close()">{{ trans('global.close') }}</button>-->
                     <button class="btn btn-primary" @click="submit()" >{{ trans('global.save') }}</button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {
        data() {
            return {
                params: [],
                user_list: [],
                users: [],
                id: '',
                reason: '',
                done: false,
                time: 0,
                errors: {},
                method: 'post'
            };
        },
        methods: {
            async loadGroupMembers(group_id) {
                try {
                    // todo: if more of one course is enroled loadGroupMemebers-calls should be merged
                    this.user_list = JSON.parse((await axios.get('/groups/'+ group_id)).data.users);
                }
                catch(error) {
                    this.errors = error.response.data.errors;
                }
            },
            async submit() {
                try {
                    this.location = (await axios.post('/absences', {
                        'reason':               this.reason,
                        'absent_user_ids':      $("#users").val(),
                        'referenceable_type':   this.params.referenceable_type,
                        'referenceable_id':     this.params.referenceable_id,
                        'done':                 this.done,
                        'time':                 this.time,
                    })).data.message;
                    location.reload(true);

                } catch(error) {
                    //
                }
            },

            beforeOpen(event) {
                this.params = JSON.parse(event.params);

                // load users from subscribed groups
                Object.entries(this.params.subscriptions).forEach(([key, value]) => {
                    if (value.subscribable_type === "App\\Course"){
                        this.loadGroupMembers(value.subscribable.group_id);
                    }
                    if (value.subscribable_type === "App\\Group"){
                        this.loadGroupMembers(value.subscribable.id);
                    }
                });
                //todo: get users related to logbook

             },
            beforeClose() {
            },
            opened(){
                this.initSelect2();
            },
            initSelect2(){
                $("#users").select2({
                    dropdownParent: $(".v--modal-overlay"),
                    allowClear: true
                });
            },
            close(){
                this.$modal.hide('absence-modal');
            }

        },
        components: {

        }
    }
</script>

