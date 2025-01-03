<template>
    <div>
        <div
            :id="this.id + '_form_group'"
            class="form-group"
            :class="[(typeof this.css != 'undefined') ? this.css : '' ]"
        >
            <label v-if="this.showLabel"
                :for="this.id"
                :class="[(typeof this.classLeft != 'undefined') ? this.classLeft : 'p-0 col-sm-12' ]"
            >
                <span v-if="this.label != ''">
                    {{ this.label }}
                </span>
                <span v-else>
                    {{ trans('global.' + this.model + '.title_singular') }}
                </span>
                <span v-if="multiple">
                    <span class="btn btn-info btn-xs deselect-all pull-right">
                        {{ trans("global.deselect_all") }}
                    </span>
                    <span class="btn btn-info btn-xs select-all pull-right mr-1">
                        {{ trans("global.select_all") }}
                    </span>
                </span>
            </label>

            <select
                :name="this.id + '[]'"
                :id="this.id"
                class="form-control select2"
                :class="[(typeof this.classRight != 'undefined') ? this.classRight : 'col-sm-12' ]"
                :style="this.styles"
                :multiple="this.multiple"
                :disabled="this.readOnly"
                :placeholder="this.placeholder"
            >
                <option v-if="list"
                    v-for="item in list"
                    :value="item[this.option_id]"
                    :data-icon="this.option_icon"
                >
                    <span>{{ item[this.option_label] }}</span>
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
            type: [Array, Object],
            default: null
        },
        url: {
            type: String,
            default: '',
        },
        model: {
            type: String,
            required: true
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
        option_label: {
            type: String,
            default: 'title'
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
            default: false
        },
        term: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            componentId: this.$.uid,
            componentInstance: null,
            currentSelection: []
        }
    },
    methods: {
        formatText(icon) {
            return $('<span class="' + $(icon.element).data('class') + '"><i class="fas ' + $(icon.element).data('icon') + '"></i> ' + icon.text + '</span>');
        },
        loader() {
            if (this.list === null) { // ajax
                this.componentInstance = $('#' + this.id).select2({
                    placeholder: this.placeholder,
                    dropdownParent: $('#' + this.id).parent(),
                    allowClear: this.allowClear,
                    closeOnSelect: !this.multiple,
                    ajax: {
                        delay: 250,
                        url: this.url,
                        dataType: 'json',
                        data: function(params) {
                            return {
                                term: params.term || this.term,
                                page: params.page || 1
                            }
                        }.bind(this),
                        cache: true,
                    },

                    templateSelection: this.formatText,
                    templateResult: this.formatText,
                })
            } else { // this.list is set
                this.componentInstance = $('#' + this.id).select2({
                    placeholder: this.placeholder,
                    dropdownParent: $('#' + this.id).parent(),
                    allowClear: this.allowClear,
                });
            }

            this.componentInstance.on("select2:select", function(e) {
                this.$emit("selectedValue", this.componentInstance.select2("data").map(i => i[this.option_id]));
            }.bind(this))
            .on("select2:unselect", function(e) {
                this.$emit("selectedValue", this.componentInstance.select2("data").map(i => i[this.option_id]));
                // prevent toggling the dropdown after removing an option
                e.params.originalEvent.stopPropagation();
            }.bind(this))
                // .val(this.selected).trigger('change'); //wont work for ajax -> data isn't present yet

            if (typeof this.selected === 'object' || this.selected !== false) {
                let selectedParam = '';
                this.componentInstance.val(null).trigger('change'); //reset selection
                switch (typeof (this.selected)) {
                    case 'string':
                    case 'number': selectedParam = encodeURIComponent(this.selected);
                        break;
                    case 'array': selectedParam = encodeURIComponent(this.selected.join());
                        break;
                    case 'object': selectedParam = encodeURIComponent(this.selected);
                    break;
                    default:
                        console.log(typeof this.selected);
                        console.log(this.selected);
                        break;
                }

                if (this.url !== '' && selectedParam != '') {
                    axios.get(this.url + "?selected=" + selectedParam)
                        .then((res) => {
                            //console.log(res);
                            res.data.forEach((entry) => {
                                let label = entry[this.option_label];
                                //console.log(this.option_label);
                                if ((typeof label) === 'undefined') {
                                    label = entry['firstname'] + ' ' + entry['lastname'];
                                }
                                //console.log(label +'('+this.option_label + ') _' + entry[this.option_id] +' '+label);
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
        }
    },
    mounted() {
        this.loader();
    }
}
</script>
<style>
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
