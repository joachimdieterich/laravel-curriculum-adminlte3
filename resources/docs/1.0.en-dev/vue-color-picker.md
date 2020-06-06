# Date-Picker

---

- [Dependency](#section-1)
- [Usage](#section-2)

<a name="section-1"></a>
## Dependency
Curriculum uses [vue-color](https://github.com/xiaokaike/vue-color)

<a name="section-2"></a>
## Usage

Registered in .vue

```js
<script>
    import ColorPicker from '../uiElements/ColorPicker';

    export default {
        components: { DatePicker },
        data() {
          return {
               ...
                form: new Form({
                    ...
                    'color': '#008000',
                }),
                colors: {
                    hex: '#194d33',
                    hsl: { h: 150, s: 0.5, l: 0.2, a: 1 },
                    hsv: { h: 150, s: 0.66, v: 0.30, a: 1 },
                    rgba: { r: 25, g: 77, b: 51, a: 1 },
                    a: 1
                  }
            }
    },
    ...
  };
</script>
```

### Include color-picker
```html
<ColorPicker 
    :color="form.color" 
    v-model="form.color">
</ColorPicker>
```