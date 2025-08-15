<template>

        <div class="btn-group" >
            <button type="button"
                    class="dropdown-toggle border-0 "
                    style="background-color: transparent;"
                    v-bind:style="{ 'color': textcolor }"
                    data-toggle="dropdown"
                    aria-label="Dropdown menu"
                    aria-expanded="false">
              <span class="caret"></span>
            </button>
            <div class="dropdown-menu"
                 x-placement="top-start"
                 style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -2px, 0px);">
                <span v-for="entry in Entries">
                    <hr v-if="entry.hr === true" style="margin: 0.4rem 0;">
                    <button v-else-if="entry.action === 'edit' "
                            class="dropdown-item"
                            @click="editObjective(entry)">
                        <i class="mr-4"
                           v-bind:class="entry.icon"></i>
                        {{ trans('global.terminalObjective.edit') }}
                    </button>
                    <button v-else-if="entry.action === 'move' "
                            class="dropdown-item"
                            @click="moveObjective(entry)">
                        <i class="mr-4"
                           v-bind:class="entry.icon"></i>
                        {{ trans('global.terminalObjective.move') }}
                    </button>
                    <button v-else-if="entry.action === 'resetOrderIds' "
                            class="dropdown-item"
                            @click="resetOrderIds(entry)">
                        <i class="mr-4"
                           v-bind:class="entry.icon"></i>
                        {{ entry.title }}
                    </button>
                    <button v-else-if="entry.action === 'delete' "
                            class="dropdown-item text-danger"
                            @click="emitDeleteEvent()">
                        <i class="mr-4" v-bind:class="entry.icon"></i>
                        {{ trans('global.terminalObjective.delete') }}
                    </button>
                    <button v-else
                            class="dropdown-item"
                            @click="action(entry);">
                        <i class="mr-4"
                           v-bind:class="entry.icon"></i>
                        {{ entry.title }}
                    </button>
                </span>

            </div>
          </div>

</template>


<script>
    import Form from 'form-backend-validation';
    export default {
        props: {
            'menuEntries': {},
            'objective': {
                default: null,
            },
            'textcolor': {default: '#000'},
        },
        data() {
            return {
                Entries: [],
                errors: {}
            }
        },
        methods: {
            editObjective(entry) {
                this.$modal.show(entry.value, {'objective': this.objective, 'method': 'PATCH' });
            },
            moveObjective(entry) {
                this.$modal.show(entry.value, {'objective': this.objective, 'method': 'PATCH' });
            },
            emitDeleteEvent() {
                this.$eventHub.$emit('deleteObjective', this.objective);
                //this.$parent.$emit('eventDelete', this.objective)
            },
            action(entry) {
                this.$modal.show(entry.value);
            }
        },
        mounted(){

            this.Entries = this.menuEntries;
        }

    }
</script>
