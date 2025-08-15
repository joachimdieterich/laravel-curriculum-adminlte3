# Medium (Vue)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/js/components/media/MediumForm.vue`

```vue
<template >
    <MediumForm 
        :id="component_id"
        :medium_id="form.medium_id"
        accept="image/*"/>
    
    <button @click="submit()">
        {{ trans('global.save') }}
    </button>
</template>
<script>
import MediumForm from "../media/MediumForm";

export default {
    data() {
      return {
        component_id: this._uid,
        form: new Form({
          'medium_id': null,
        }),
      }
    },
    mounted() {
        // Set eventlistener for Media
        this.$eventHub.$on('addMedia', (e) => {
            if (this.component_id == e.id) {
                this.form.medium_id = e.selectedMediumId;
            }
        });
    },
    components: {
        MediumForm,
    },
}
</script>
```

Parameter | Type   | required | Description
-----------------  |--------| ------ | ------ 
id  | int    | * | uID
medium_id | int    |   | id of medium
accept | string |   | e.g. "image/*"
model  | string | * | Name of input field 
form | array  |  | to get validation errors





