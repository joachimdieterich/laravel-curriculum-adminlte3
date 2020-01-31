# Date-Picker

---

- [Usage](#section-1)


<a name="section-1"></a>
## Usage

Registered in .vue

```js
<script>
    import Form from 'form-backend-validation'
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';

    export default {
        components: { DatePicker },
        data() {
          return {
            time: null,
            form: new Form({
                'id':'',
                'logbook_id':'',
                'title': '',
                'description': '',
                'begin': '',
                'end': '',
            }),
          };
    },
    methods: {
        async submit(method) {
            try {
                this.form.begin = this.time[0];
                this.form.end = this.time[1];
                this.location = (await axios.post('/logbookEntries', this.form)).data.message;
                location.reload(true);
            } catch(error) {
                this.form.errors = error.response.data.form.errors;
            }
        },  
    }   
  };
</script>
```

### Include date-picker
```html
 <date-picker 
    class="w-100"
    v-model="time" 
    type="datetime" range
    valueType="YYYY-MM-DD HH:mm:ss">
</date-picker>
```
