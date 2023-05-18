<template>
    <span style="position:relative;">
        <chrome-picker
            style="position: absolute; left:0;bottom:0;"
            v-if="displayPicker"
            :value="colors"
            @input="updateFromPicker" />
        <div ref="colorpicker" class="input-group">
            <input
                v-model="colorValue"
                type="text"
                class="form-control"
                @focus="showPicker()"
                @input="updateFromInput"/>
            <div class="input-group-append">
                <span class="input-group-text">
                    <i  class="fas fa-square"
                        :style="'color: ' + colorValue"
                        @click="togglePicker()" >
                    </i>
                </span>
            </div>
        </div>

    </span>

</template>

<script>
const VueColor =
    () => import('vue-color');
    //import VueColor from 'vue-color';

    export default {
        props: ["color"],
	data() {
            return {
                colors: {
                    hex: "#000000"
                },
                colorValue: "",
                displayPicker: false
            };
	},
	watch: {
            colorValue(val) {
                if (val) {
                    this.updateColors(val);
                    this.$emit("input", val);
                }
            }
	},
	mounted() {
            this.setColor(this.color || "#000000");
	},
	destroyed() {
            this.hidePicker();
	},
	methods: {
            setColor(color) {
                this.updateColors(color);
                this.colorValue = color;
            },
            updateColors(val) {
                this.colors = val;
            },
            showPicker() {
                document.addEventListener("click", this.documentClick);
                this.displayPicker = true;
            },
            hidePicker() {
                document.removeEventListener("click", this.documentClick);
                this.displayPicker = false;
            },
            togglePicker() {
                this.displayPicker ? this.hidePicker() : this.showPicker();
            },
            updateFromInput() {
                this.updateColors(this.colorValue);
            },
            updateFromPicker(color) {
                this.colors = color;
                if (color.rgba.a === 1) {
                    this.colorValue = color.hex;
                } else {
                    // eslint-disable-next-line
                    this.colorValue =
                        "rgba(" +
                        color.rgba.r +
                        ", " +
                        color.rgba.g +
                        ", " +
                        color.rgba.b +
                        ", " +
                        color.rgba.a +
                        ")";
                }
            },
            documentClick(e) {
                const el = this.$refs.colorpicker;
                const target = e.target;
                if (el !== target && !el.contains(target)) {
                        this.hidePicker();
                }
            }
	},
        components: {
            'chrome-picker': VueColor.Chrome
        }

    }
</script>
