<template>
    <div class="boxheader row"
         v-bind:style="{ 'color': textcolor }">
        <span class="mr-auto "
              v-can="'curriculum_edit'"
              v-if="edit_settings === true">
            <span v-if="(type == 'terminal' && objective.order_id != 0)"
                  class="fa fa-arrow-up mr-1"
                  @click="$emit('eventSort','-1')">
            </span>
            <span v-if="(type == 'enabling'  && objective.order_id != 0)"
                  class="fa fa-arrow-left mr-1 "
                  @click="$emit('eventSort','-1')">
            </span>
            <span v-if="(type == 'terminal' && max_id != objective.id)"
                  class="fa fa-arrow-down  "
                  @click="$emit('eventSort','1')">
            </span>
            <span v-if="(type == 'enabling' && max_id != objective.id)"
                  class="fa fa-arrow-right  "
                  @click="$emit('eventSort','1')">
            </span>
        </span>

        <span v-if="(type == 'enabling' && objective.level != null)">
            <button type="button"
                    class="btn btn-block btn-xs"
                    v-bind:class="objective.level.css_color"
                    >
                {{ objective.level.title }}
            </button>
        </span>

        <span
            v-can="'curriculum_edit'"
            v-if="edit_settings === true"
            class="ml-auto">
            <DropdownButton v-if="type == 'terminal'"
                            :menuEntries="menuEntries"
                            :objective.sync="objective"
                            :textcolor="textcolor"/>
            <DropdownButton v-else-if="type == 'enabling'"
                            :menuEntries="menuEntries"
                            :objective.sync="objective"
                            :textcolor="textcolor"/>
        </span>
    </div>
</template>

<script>
const DropdownButton =
    () => import('./DropdownButton');
    //import DropdownButton from './DropdownButton'
    export default {
        props: ['objective', 'type', 'menuEntries', 'settings', 'textcolor', 'max_id'],

        methods: {

        },
        computed: {
            edit_settings: function() {
                if (typeof this.settings !== "undefined"){
                    return this.settings.edit;
                } else {
                    return false;
                }
            }
        },
        mounted() {
            //console.log(this.settings);
        },

        components: {
            DropdownButton,
        },
    }
</script>
