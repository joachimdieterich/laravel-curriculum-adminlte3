# Modal

---

- [Dependency](#section-1)
- [Usage](#section-2)


<a name="section-1"></a>
## Dependency
Curriculum uses [vue-js-modal](https://github.com/euvl/vue-js-modal#readme)


<a name="section-2"></a>
## Usage

VModal is imported in `resources/js/app.js`

```js
import VModal from 'vue-js-modal'

Vue.use(VModal)

/* Register modal*/
Vue.component('example-modal', require('./components/ExampleModal.vue').default);
```

Create Vue-Component `resources/js/components/ExampleModal.vue`
```html
<template>
    <modal
        id="example-modal"
        name="example-modal"
        height="auto"
        :adaptive=true
        :scrollable=true
        draggable=".draggable"
        :resizable=true
        @before-open="beforeOpen"
        @before-close="beforeClose"
        style="z-index: 1100">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    --> Title
                </h3>

                <div class="card-tools">
                    <button type="button" 
                            class="btn btn-tool draggable" >
                        <i class="fa fa-arrows-alt"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-tool" 
                            data-widget="remove" 
                            @click="close()">
                        <i class="fa fa-times"></i>
                    </button>
                 </div>
            </div>
            <div class="card-body" 
                 style="max-height: 80vh; overflow-y: auto;">
                --> here goes the content
            </div>
            <div class="card-footer">
                <span class="pull-right">
                    <button id="example-cancel"
                            type="button" 
                            class="btn btn-info" 
                            data-widget="remove" 
                            @click="close()">
                        {{ trans('global.cancel') }}
                    </button>
                </span>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {
        data() {
            return {
                id: null, 
            }
        },
        methods: {
            beforeOpen(event) {
                if (event.params.id){  //example: process params
                    this.id = event.params.id 
                }
            },
            beforeClose() {
                console.log('close')
            },
            close(){
                this.$modal.hide('example-modal');
            }
        },
    }
</script>
```

Include component in `blade` or `vue`
```html
<example-modal></example-modal>
```

Open modal 
```js
this.$modal.show('example-modal');
```

Open modal with parameters
```js
this.$modal.show('example-modal', { id: '10' });
```

Close modal
```js
this.$modal.hide('example-modal');
```