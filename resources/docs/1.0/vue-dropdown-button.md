# Dropdown

---

- [Usage](#section-1)


<a name="section-1"></a>
## Usage

VModal is registered in `resources/js/app.js`

```js
Vue.component('dropdown-button', require('./components/uiElements/DropdownButton.vue').default);
```

The Vue-Component is stored at `resources/js/components/DropdownButton.vue`

### Include dropdown-button 
```html
 <dropdown-button 
    label="LABEL" 
    model="{ { class_basename($curriculum->media[0]) } }"
    :entries="{ { $model->title } }"
></dropdown-button> 
```

### Button Groups 
Get nice button groups (without double borders):
```html
<div class="btn-group">
    <dropdown-button 
        label="LABEL" 
        model="{ { class_basename($curriculum->media[0]) } }"
        :entries="{ { $curriculum->media } }"
    ></dropdown-button> 
    <dropdown-button 
        label="LABEL" 
        model="{ { class_basename($objective->media[0]) } }"
        :entries="{ { $objective->media } }"
        styles="border-left:0px"
    ></dropdown-button> 
</div>
```