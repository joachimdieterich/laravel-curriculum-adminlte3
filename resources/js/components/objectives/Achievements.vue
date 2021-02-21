<template >
<div>
    <div v-if="this.groups.length !== 0"
         class="form-group p-2" >
        <label for="achievements_group">
            {{ trans('global.group.title_singular') }}
        </label>
        <select name="achievements_group[]"
                id="achievements_group"
                class="form-control select2 "
                style="width:100%;"
                multiple=false
                v-model="groups">
            <option v-for="(item,index) in groups" v-bind:value="item.id">{{ item.title }}</option>
        </select>
    </div>


    <table class="table m-0 border-top-0"
           style="border-top: 0"
           v-if="(this.objectiveWithAchievement.achievements.length !== 0)">
        <thead class=" border-top-0">
        <tr class="border-top-0">
            <th class="border-top-0">{{trans('global.name')}}</th>
            <th class="border-top-0">{{trans('global.created_at')}}</th>
            <th class="border-top-0">{{trans('global.updated_at')}}</th>
            <th class="border-top-0">{{trans('global.teacher')}}</th>
            <th class="border-top-0">Status</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="(item,index) in users" >
                <td>{{ item.firstname }} {{ item.lastname }}</td>
                <td>{{ currentUser(item.id).achievements[0].created_at }}</td>
                <td>{{ currentUser(item.id).achievements[0].updated_at }}</td>
                <td>{{ currentUser(item.id).achievements[0].owner.firstname }} {{ currentUser(item.id).achievements[0].owner.lastname }}</td>
                <td>
                    <AchievementIndicator
                        v-can="'achievement_create'"
                        :objective="currentUser(item.id)"
                        :type="type"
                        :users="[item.id]"
                        :settings="{'achievements' : false, 'edit': false}">
                    </AchievementIndicator>
                </td>
            </tr>
        </tbody>
    </table>

</div>

</template>


<script>
    import AchievementIndicator from './AchievementIndicator';

    export default {
        props: {
            objective: {},
            type:{},
            settings:{}
        },
        data() {
            return {
                objectiveWithAchievement : {},
                groups: [],
                users: {},
                selectedGroup: null,
               // currentUsersObjective: [],
                errors: {},

            }
        },
        methods: {
            loaderEvent(){
                axios.get('/enablingObjectives/' + this.objective.id + '/achievements/' + this.selectedGroup)
                    .then(response => {
                        this.processResponse(response);
                    }).catch(e => {
                    this.errors = e.response.data.errors;
                });
            },

            currentUser(id)
            {
                let currentUsersObjective = JSON.parse(JSON.stringify(this.objectiveWithAchievement));
                const achievement = currentUsersObjective.achievements.find(e => e.user_id == id);
                currentUsersObjective.achievements = [achievement];

                return currentUsersObjective;
            },
            processResponse(response){
                this.objectiveWithAchievement = response.data.objective;
                this.groups = response.data.groups;
                this.users  = response.data.users;
                if (this.selectedGroup == null){
                    this.$nextTick(() => {
                        $("#achievements_group").select2({
                            dropdownParent: $("#achievements_group").parent(),
                            allowClear: false
                        }).on('select2:select', function () {
                            this.selectedGroup = $("#achievements_group").val();
                            this.loaderEvent();
                        }.bind(this))
                        .on('select2:unselect', function () {
                            this.selectedGroup = null;
                            this.objectiveWithAchievement.achievements = [];
                        }.bind(this));
                    });
                }

            }
        },

        mounted() {

        },
        components: {
            AchievementIndicator
        }

    }
</script>
