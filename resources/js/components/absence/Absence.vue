<template>
    <ul class="todo-list" data-widget="todo-list">
        <li class="d-flex flex-row">
            <span class="flex-fill">
                <div class="icheck-primary d-inline mx-2 ">
                    <input
                        type="checkbox"
                        value="absence.done"
                        name="'absence_'+absence.id"
                        id="'absence_'+absence.id"
                        @click="complete(absence.id)"
                        v-bind:checked="absence.done">
                </div>
                <strong>
                    {{ absence.absent_user.firstname }} {{ absence.absent_user.lastname }}
                </strong>
                <div class="pl-4 ml-2">
                    {{ absence.reason }}
                    <span v-if="absence.time !== 0"> ({{ absence.time }} {{ trans('global.minutes') }})</span><br>
                    <small>{{ absence.owner.firstname }} {{ absence.owner.lastname }}</small>
                </div>
            </span>
            <span class="pull-right">
                <!-- General tools such as edit or delete-->

                <div class="tools">
                    <!--<a @click="edit()" >
                        <i class="pl-2 fa fa-pencil-alt text-muted"></i>
                    </a>-->
                    <a @click="destroy()">
                        <i class="pl-2 fas fa-trash"></i>
                    </a>

                </div><br>

                <small
                    class="badge  p-1 mt-1"
                    :class="[absence.done === 1 ? 'badge-success' : 'badge-danger']">
                    <i class="far fa-clock"></i>
                    <span v-dompurify-html="absence.created_at"></span>
                </small><br>
                <small class="badge badge-success  p-1 mt-1"
                       v-if="(absence.updated_at !== absence.created_at) && (absence.done === 1)">
                    <i class="fa fa-check"></i>
                    <span v-dompurify-html="absence.updated_at"></span>
                </small>
            </span>
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            'absence': '',
        },
        data() {
            return {

            }
        },

        methods: {
           async complete(absence_id) {
                try {
                    this.absence.done = (await axios.patch('/absences/'+absence_id, {'done': (1 - this.absence.done)})).data.done;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
           },
            /*edit(){
                this.$modal.show('absence-modal', JSON.stringify(_.merge({ 'method': 'patch'}, this.absence)));
            },*/
           async destroy() {
                try {
                    this.location = (await axios.delete('/absences/'+this.absence.id)).data.message;
                } catch(error) {
                    this.errors = error.response.data.errors;
                }
                location.reload(true);
            },
        },

        mounted(){

        }

    }
</script>
