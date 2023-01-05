<template>
    <div>
        <div :id="this.id + '_form_group'"
             class="form-group"
             :class="[(typeof this.css != 'undefined') ? this.css : '' ]">
            <label v-if="this.showLabel"
                :for="this.id"
                :class="[(typeof this.classLeft != 'undefined') ? this.classLeft : 'p-0 col-sm-12' ]">
                <span v-if="this.label != ''">
                    {{ this.label }}
                </span>
                <span v-else>
                    {{ trans('global.' + this.model + '.title_singular') }}
                </span>
                <span v-if="multiple">
                    <span class="btn btn-info btn-xs deselect-all pull-right">{{ trans("global.deselect_all") }}</span>
                    <span class="btn btn-info btn-xs select-all pull-right mr-1">{{ trans("global.select_all") }}</span>
                </span>
            </label>

            <select
                :name="this.id+'[]'"
                :id="this.id"
                 class="form-control select2"
                :class="[(typeof this.classRight != 'undefined') ? this.classRight : 'col-sm-12' ]"
                :style="this.styles"
                :multiple="this.multiple"
                :disabled="this.readOnly">
                <option v-for="(item,index) in list"
                        :value="item[this.option_id]"
                        :data-icon="this.option_icon" >
                    <span v-if="this.option_field != ''">
                        {{ item['this.option_field'] }}
                    </span>
                    <span v-else>
                        {{ item.title }}
                    </span>
                </option>
            </select>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            id: {
                type: String,
                default: 'select2',
                required: true
            },
            list: {
                type: Array,
            },
            url: {
                type: String,
                default: '',
                required: true
            },
            model: {
                type: String,
                required: true
            },
            field: {
                type: String,
                default: 'title'
            },
            error: {
                type: Array
            },
            css: {
                type: String,
                default: ''
            },
            styles: {
                type: String,
                default: 'width:100%;'
            },
            showLabel: {
                type: Boolean,
                default: true
            },
            classLeft: {
                type: String,
                default: 'p-0 col-sm-12'
            },
            classRight: {
                type: String,
                default: 'col-sm-12'
            },
            label: {
                type: String,
                default: ''
            },
            multiple: {
                type: Boolean,
                default: false
            },
            readOnly: {
                type: Boolean,
                default: false
            },
            option_id: {
                type: String,
                default: 'id'
            },
            option_icon: {
                type: String,
                default: ''
            },
            option_field: {
                type: String,
                default: ''
            },
            placeholder: {
                type: String,
                default: ''
            },
            allowClear: {
                type: Boolean,
                default: false
            },
            selected: {
            },

        },
        created () {
        },
        methods: {

            getItem(item){
                if (this.optionKey){
                    return item[this.optionKey];
                }
                return item;
            },
            formatText(icon) {
                return $('<span class="' + $(icon.element).data('class') + '"><i class="fas ' + $(icon.element).data('icon') + '"></i> ' + icon.text + '</span>');
            }
        },
        mounted() {
            $('#' + this.id).select2({
                placeholder: this.placeholder,
                dropdownParent: $('#' + this.id).parent(),
                allowClear: this.allowClear,
                ajax: {
                    delay: 250,
                    url: this.url,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true,
                },
                templateSelection: this.formatText,
                templateResult: this.formatText,
            }).on("select2:select", function(e){
                this.selected = e.params.data.id;
                this.$emit("selectedValue", e.params.data.id);
            }.bind(this))
             .val(this.selected).trigger('change');
        }
    }
</script>
