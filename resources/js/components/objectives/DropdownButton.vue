<template>
    <div class="btn-group">
        <button
            type="button"
            class="dropdown-toggle border-0"
            style="background-color: transparent;"
            :style="{ 'color': textcolor }"
            data-toggle="dropdown"
            aria-label="Dropdown menu"
            aria-expanded="false"
        >
            <span class="caret"></span>
        </button>
        <div
            class="dropdown-menu position-absolute"
            x-placement="top-start"
        >
            <span v-for="entry in menuEntries">
                <hr v-if="entry.hr" style="margin: 0.4rem 0;">
                <button v-else-if="entry.action === 'edit'"
                    class="dropdown-item"
                    @click="editObjective(entry)"
                >
                    <i
                        class="mr-4"
                        :class="entry.icon"
                    ></i>
                    {{ trans('global.' + entry.model + '.edit') }}
                </button>
                <button v-else-if="entry.action === 'move'"
                    class="dropdown-item"
                    @click="moveObjective(entry)"
                >
                    <i
                        class="mr-4"
                        :class="entry.icon"
                    ></i>
                    {{ trans('global.terminalObjective.move_to_curriculum') }}
                </button>
                <button v-else-if="entry.action === 'resetOrderIds'"
                    class="dropdown-item"
                    @click="resetOrderIds(entry)"
                >
                    <i
                        class="mr-4"
                        :class="entry.icon"
                    ></i>
                    {{ entry.title }}
                </button>
                <button v-else-if="entry.action === 'delete'"
                    class="dropdown-item text-danger"
                    @click="emitDeleteEvent(entry)"
                >
                    <i
                        class="mr-4"
                        :class="entry.icon"
                    ></i>
                    {{ trans('global.' + entry.model + '.delete') }}
                </button>
                <button v-else
                    class="dropdown-item"
                    @click="action(entry);"
                >
                    <i
                        class="mr-4"
                        :class="entry.icon"
                    ></i>
                    {{ entry.title }}
                </button>
            </span>
        </div>
    </div>
</template>
<script>
import { useGlobalStore } from '../../store/global';

export default {
    props: {
        menuEntries: {
            type: Object,
            default: null,
        },
        objective: {
            type: Object,
            default: null,
        },
        textcolor: {
            type: String,
            default: '#000',
        },
    },
    setup() {
        const globalStore = useGlobalStore();
        return {
            globalStore,
        };
    },
    data() {
        return {
            entries: [],
            errors: {},
        }
    },
    methods: {
        editObjective(entry) {
            this.globalStore.showModal(entry.value, this.objective);
        },
        moveObjective(entry) {
            this.globalStore.showModal(entry.value, this.objective);
        },
        emitDeleteEvent(entry) {
            this.$eventHub.emit('confirm-objective-delete', {
                objective: this.objective,
                model: entry.model,
            });
        },
        action(entry) {
            this.$modal.show(entry.value);
        }
    },
}
</script>