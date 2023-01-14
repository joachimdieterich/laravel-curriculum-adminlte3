<template>
    <div class="card">
        <div class="card-body px-3 py-2">
            <div class="form-group">
                <input
                    type="text" id="title"
                    name="title"
                    class="form-control"
                    v-model.trim="form.title"
                    placeholder="Titel"
                    required
                    />
                <p class="help-block" v-if="form.errors.title" v-text="form.errors.title[0]"></p>
             </div>

            <button
                name="kanbanStatusCancel"
                @click="$emit('status-canceled')"
                type="reset"
                class="btn btn-default"
              >
                {{ trans('cancel') }}
            </button>
            <button
                name="kanbanStatusSave"
                type="submit"
                class="btn btn-primary pull-right"
                @click="submit"
              >
                {{ trans('save') }}
            </button>
        </div>
    </div>
</template>

<script>
import Form from 'form-backend-validation';
export default {

    props: {
      kanban_id: Number,
      order_id: Number
    },
    data() {
        return {
            form: new Form({
                    'title':'',
                    'kanban_id': '',
                    'order_id': 0
                }),
        };
    },
    mounted() {
          this.form.kanban_id = this.kanban_id;
          this.form.order_id = this.order_id;
    },
    methods: {
        submit() {
            axios.post('/kanbanStatuses', this.form)
                .then(res => { // Tell the parent component we've added a new task and include it
                    this.$emit("status-added", res.data.message);
                })
                .catch(error => { // Handle the error returned from our request
                     this.form.errors = error.response.data.errors;
                });
        },

    }
};
</script>
