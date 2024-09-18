<template>
    <Transition name="modal">
        <div v-if="globalStore.modals[$options.name]?.show"
             class="modal-mask"
        >
        <div class="modal-container">
            <div class="card-header">
                <h3 class="card-title">
                    {{ trans('global.plan.select_users') }}
                </h3>
                <div class="card-tools">
                    <button type="button"
                            class="btn btn-tool"
                            @click="globalStore?.closeModal($options.name)">
                        <i class="fa fa-times"></i>
                    </button>
                 </div>
            </div>
            <div class="card-body overflow-auto">
                <table class="table m-0 border-top-0"
                       style="border-top: 0"
                       v-if="this.users.length"
                       v-permission="'achievement_access'"
                >
                    <thead class=" border-top-0">
                    <tr class="border-top-0">
                        <th class="border-top-0">{{trans('global.name')}}</th>
                        <!-- <th class="border-top-0">{{trans('global.notes')}}</th> -->
                        <th class="border-top-0">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users">
                        <td>{{ user.firstname }} {{ user.lastname }}</td>
                        <!-- <td v-if="currentUser(user.id).achievements[0]">
                            <i style="font-size:18px;"
                                class="far fa-sticky-note text-muted pointer"
                                @click.prevent="$modal.show('note-modal', {'method': 'post', 'notable_type': 'App\\Achievement', 'notable_id': currentUser(user.id).achievements[0].id,'show_tabs': false}) ">
                            </i>
                        </td>
                        <td v-else></td> -->
                        <td>
                            <AchievementIndicator
                                v-permission="'achievement_create'"
                                :objective="objective[user.id] ?? objective.default"
                                :type="'enabling'"
                                :users="[user.id]"
                                :settings="{'achievements' : false, 'edit': false}">
                            </AchievementIndicator>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                 <span class="pull-right">
                     <button
                         id="certificate-cancel"
                         type="button"
                         class="btn btn-default"
                         @click="globalStore?.closeModal($options.name)">
                         {{ trans('global.cancel') }}
                     </button>
                     <button
                         id="certificate-save"
                         class="btn btn-primary"
                         @click="submit()" >
                         {{ trans('global.save') }}
                     </button>
                </span>
            </div>
        </div>
    </div>
    </Transition>
</template>
<script>
    import AchievementIndicator from './../objectives/AchievementIndicator.vue';
    import {useGlobalStore} from "../../store/global";

    export default {
        name: 'set-achievements-modal',
        components:{
            AchievementIndicator
        },
        props: {
            users:{},
        },
        setup () {
            const globalStore = useGlobalStore();
            return {
                globalStore,
            }
        },
        data() {
            return {
                component_id: this.$.uid,
                objective: {},
                search: '',
            }
        },
        methods: {
             submit() {
                 this.globalStore?.closeModal($options.name)
            },
        },
        mounted() {
            this.globalStore.registerModal(this.$options.name);
            this.globalStore.$subscribe((mutation, state) => {
                if (mutation.events.key === this.$options.name){

                    const params = state.modals[this.$options.name].params;
                    console.log(params);
                    const eventObj = params.objective;
                    const obj = {}; // if we add attributes directly to 'this.objective' it won't work
                    obj.default = { id: eventObj.id };

                    eventObj.achievements.forEach(achievement => {
                        // only add attributes that are actually needed
                        obj[achievement.user_id] = {
                            id: eventObj.id,
                            achievements: [achievement],
                        };
                    });

                    this.objective = obj;
                }
            });
        },
    }
</script>
