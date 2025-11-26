<template>
    <div>
        <div
            :id="id + '_form_group'"
            class="form-group"
            :class="[(typeof css != 'undefined') ? css : '' ]"
            :style="id == 'permissions' ? { 'margin-bottom': '200px' } : ''"
        >
            <label v-if="showLabel"
                :for="id"
                :class="[(typeof classLeft != 'undefined') ? classLeft : 'p-0 col-sm-12' ]"
            >
                <span v-if="label != ''" :class="{'full-line': buttonNewLine}">{{ label }}</span>
                <span v-else>
                    {{ trans('global.' + model + '.title_singular') }}
                </span>
                <span v-if="multiple">
                    <slot name="buttons"></slot>
                    <span class="btn btn-info deselect-all pull-right" :class="buttonSizeClass" @click="deselectAll">
                        {{ trans("global.deselect_all") }}
                    </span>
                </span>
            </label>

            <slot name="pre-dropdown"></slot>

            <select
                :name="id + '[]'"
                :id="id"
                class="form-control select2"
                :class="[(typeof classRight != 'undefined') ? classRight : 'col-sm-12' ]"
                :style="styles"
                :multiple="multiple"
                :disabled="readOnly"
                :placeholder="placeholder"
            >
                <option v-if="list" disabled selected value>{{ placeholder }}</option>
                <option v-if="list"
                    v-for="item in list"
                    :value="item[option_id]"
                    :data-icon="option_icon"
                >
                    {{ item[option_label] }}
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
            required: true,
        },
        list: {
            type: [Array, Object],
            default: null,
        },
        url: {
            type: String,
            default: '',
        },
        model: {
            type: String,
            required: true,
        },
        error: {
            type: Array,
            default: null,
        },
        css: {
            type: String,
            default: '',
        },
        styles: {
            type: String,
            default: 'width: 100%;',
        },
        showLabel: {
            type: Boolean,
            default: true,
        },
        classLeft: {
            type: String,
            default: 'p-0 col-sm-12',
        },
        classRight: {
            type: String,
            default: 'col-sm-12',
        },
        label: {
            type: String,
            default: '',
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        readOnly: {
            type: Boolean,
            default: false,
        },
        option_id: {
            type: String,
            default: 'id',
        },
        option_icon: {
            type: String,
            default: '',
        },
        option_label: {
            type: String,
            default: 'title',
        },
        placeholder: {
            type: String,
            default: window.trans.global.pleaseSelect,
        },
        allowClear: {
            type: Boolean,
            default: false,
        },
        selected: {
            default: false,
        },
        term: {
            type: String,
            default: '',
        },
        additional_query_param: {
            type: Object,
            default: {},
        },
        buttonSizeClass: {
            type: String,
            default: 'btn-xs'
        },
        buttonNewLine: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            componentId: this.$.uid,
            componentInstance: null,
            currentSelection: [],
            values1: []
        }
    },
    methods: {
        deselectAll() {
            this.componentInstance.trigger({
                type: 'select2:clear',
                params: {}
            });
        },
        formatText(icon) {
            return $('<span class="' + $(icon.element).data('class') + '"><i class="fas ' + $(icon.element).data('icon') + '"></i> ' + icon.text + '</span>');
        },
        loader() {
            if (this.list === null) { // ajax
                this.componentInstance = $('#' + this.id).select2({
                    language: window.select2Translation,
                    placeholder: this.placeholder,
                    dropdownParent: $('#' + this.id).parent(),
                    allowClear: this.allowClear,
                    closeOnSelect: !this.multiple,
                    ajax: {
                        delay: 250,
                        url: this.url,
                        dataType: 'json',
                        data: function(params) {
                            let query = {
                                term: params.term || this.term,
                                page: params.page || 1
                            };
                            Object.keys(this.additional_query_param).forEach(parameterName => {
                                query[parameterName] = this.additional_query_param[parameterName];
                            })

                            return query;
                        }.bind(this),
                        cache: true,
                    },

                    templateSelection: this.formatText,
                    templateResult: this.formatText,
                });
            } else { // this.list is set
                this.componentInstance = $('#' + this.id).select2({
                    language: window.select2Translation,
                    placeholder: this.placeholder,
                    dropdownParent: $('#' + this.id).parent(),
                    allowClear: this.allowClear,
                });
            }

            this.componentInstance.on("select2:select", function(e) {
                this.$emit("selectedValue", this.componentInstance.select2("data").map(i => i['id']));
            }.bind(this))
            .on("select2:clear", function() {
                this.$emit("cleared");
                this.componentInstance.val(null).trigger('change');
            }.bind(this))
            .on("select2:unselect", function(e) {
                this.$emit("selectedValue", this.componentInstance.select2("data").map(i => i[this.option_id]));
                // prevent toggling the dropdown after removing an option
                e.params.originalEvent.stopPropagation();
            }.bind(this));

            if (typeof this.selected === 'object' || this.selected !== false) {
                let selectedParam = '';
                this.componentInstance.val(null).trigger('change'); //reset selection

                if (typeof (this.selected) === 'string'
                    || typeof (this.selected) === 'number'
                    || typeof (this.selected) === 'object'
                ) {
                    selectedParam = encodeURIComponent(this.selected);
                } else if(typeof (this.selected) === 'array') {
                    selectedParam = encodeURIComponent(this.selected.join());
                } else {
                    console.log(typeof this.selected);
                    console.log(this.selected);
                }

                if (this.url !== '' && selectedParam != '' && selectedParam !== null) {
                    // cut off parameters for this request
                    axios.get(this.url.split('?')[0] + "?selected=" + selectedParam)
                        .then((res) => {
                            res.data.forEach((entry) => {
                                let label = entry[this.option_label];
                                if ((typeof label) === 'undefined') {
                                    label = entry['firstname'] + ' ' + entry['lastname'];
                                }

                                let option = new Option(label, entry[this.option_id], true, true);
                                this.componentInstance.append(option).trigger('change');
                            });
                        })
                        .catch((e) => { console.log(e); })
                } else {
                    this.componentInstance.val(this.selected).trigger('change');
                }
            }
        },
    },
    watch: {
        url: function() {
            this.loader();
        },
        list: function() {
            this.loader();
        },
        selected: {
            handler() {
                this.loader();
                this.componentInstance.trigger('change');
            },
            deep: true
        },
    },
    mounted() {
        this.loader();
    }
}
</script>
<style>
.full-line {
    width: 100%;
    display: inline-block;
}
.additional-button {
    margin-right: 1em;
}
.select2-container .select2-selection--single {
    min-height: 38px;
    height: auto!important;
    padding: auto;
}
.select2-selection__rendered {
    margin-top: 0 !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: normal!important;
}
.select2-container .select2-selection--single .select2-selection__rendered {
    white-space: normal!important;
}
</style>
