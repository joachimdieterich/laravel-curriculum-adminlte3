<template>
    <div class="dropdown">
        <button
            class="btn btn-default d-flex align-items-center justify-content-center"
            style="width: 40px; height: 40px;"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <i :class="buttonIcon"></i>
        </button>
        <div
            id="iconPicker"
            class="dropdown-menu dropdown-menu-end"
            style="min-width: min(370px, 80vw);"
        >
            <div v-if="showSearch"
                class="iconPicker__header"
            >
                <input
                    type="text"
                    class="form-control"
                    :placeholder="trans('global.select_icon')"
                    @keyup="filterIcons($event)"
                >
            </div>
            <div class="iconPicker__body">
                <div class="iconPicker__icons">
                    <button v-for="icon in icons"
                        class="btn btn-default d-flex align-items-center justify-content-center"
                        :class="selected === icon.name && 'selected'"
                        type="button"
                        :key="icon.value"
                        @click="selectIcon(icon.value, icon.name)"
                    >
                        <i :class="'fas fa-'+icon.name"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import icons from '../../icons';

export default {
    name: 'FontAwesomePicker',
    emits: ['selectIcon'],
    props: {
        buttonIcon: {
            type: String,
            default: 'fa fa-book',
            description: 'The icon to display on the button that opens the icon picker',
        },
        showSearch: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            selected: '',
            icons,
        };
    },
    mounted() {
        this.selected = this.buttonIcon.replace('fa fa-', '');
    },
    methods: {
        selectIcon(icon, key) {
            this.selected = key;
            this.$emit('selectIcon', {
                className: key,
                cssValue: icon.toUpperCase(),
            });
        },
        filterIcons(event) {
            const search = event.target.value.trim();
            let filter = [];

            if (search.length > 3) {
                filter = icons.filter((item) => {
                    const regex = new RegExp(search, 'gi');
                    return item.name.match(regex);
                });
            } else if (search.length === 0) {
                this.icons = icons;
            }

            if (filter.length > 0) {
                this.icons = filter;
            }
        },
    },
};
</script>
<style>
.iconPicker__header {
    padding: 0 0 1em 0;
}
.iconPicker__header input {
    width: 100%;
    padding: 1em;
}
.iconPicker__body {
    max-height: 250px;
    overflow: auto;
}
.iconPicker__icons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}
.iconPicker__icons > * {
    width: 40px;
    height: 40px;
    padding: 12px;
    margin: 5px;
    box-shadow: 0 0 0 1px #ddd;
}
.iconPicker__icons > *.selected {
    background: #ccc;
}
</style>