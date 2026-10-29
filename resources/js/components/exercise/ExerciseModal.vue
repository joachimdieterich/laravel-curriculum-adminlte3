<template>
    <Modal
        model="exercise"
        modalName="exercise-modal"
        :form="form"
        :require-title="true"
        :disable-save-button="!form.recommended_iterations"
    >
        <template #general-extended>
            <div class="mt-3">
                <Editor
                    id="exercise-description"
                    licenseKey="gpl"
                    :init="tinyMCE"
                    v-model="form.description"
                />
            </div>

            <div class="mt-3">
                <label for="exercise-recommended-iterations" class="form-label">
                    {{ trans('global.exercise.fields.recommended_iterations') }} *
                </label>
                <input
                    type="number"
                    min="1"
                    id="exercise-recommended-iterations"
                    class="form-control"
                    v-model="form.recommended_iterations"
                    :placeholder="trans('global.exercise.fields.recommended_iterations_short')"
                    required
                />
            </div>
        </template>
    </Modal>
</template>
<script>
import Modal from '../uiElements/Modal.vue';
import Form from 'form-backend-validation';
import Editor from "@tinymce/tinymce-vue";

export default {
    name: 'exercise-modal',
    components: {
        Modal,
        Editor,
    },
    data() {
        return {
            component_id: this.$.uid,
            form: new Form({
                id: null,
                training_id: null,
                title: '',
                description: '',
                recommended_iterations: '',
                order_id: 0,
            }),
            tinyMCE: this.$initTinyMCE(
                [
                    "autolink", "link", "autoresize",
                ],
                {
                    'callback': 'insertContent',
                    'callbackId': this.component_id
                },
                "bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist | curriculummedia link",
                ""
            ),
        }
    },
}
</script>