# TinyMCE

---

- [Include TinyMCE in vue component](#section-1)
- [Configuration](#section-2)


<a name="section-1"></a>
## Include TinyMCE in vue
```js
<Editor
    api-key="no-api-key"
    id="description"
    name="description"
    initialValue="Description"
    v-model="form.description"
    :init="editorConfig"
></Editor>
```


<a name="section-2"></a>
## Configuration

```js
<script>
    import Form from 'form-backend-validation';
    import Editor from '@tinymce/tinymce-vue'

    export default {
        data() {
            return {
                form: new Form({
                    'description': '',
                }),
                editorConfig: {
                    menubar: false,
                    branding: false,
                    plugins: [
                      'advlist autolink lists link image charmap print preview anchor',
                      'searchreplace visualblocks code fullscreen',
                      'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar:
                      'undo redo | formatselect | bold italic backcolor | \
                      alignleft aligncenter alignright alignjustify | \
                      bullist numlist outdent indent | removeformat | code | help'
                }
            }
        },

        components: {
            Editor
        },
    }
</script>
```