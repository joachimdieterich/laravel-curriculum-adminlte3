# TinyMCE

---

- [Include TinyMCE in vue component](#section-1)
- [Configuration](#section-2)


<a name="section-1"></a>
## Include TinyMCE in vue
```js
<textarea
    id="content" 
    name="content" 
    class="form-control description my-editor "                  
    v-model="form.content"
></textarea>
```


<a name="section-2"></a>
## Configuration

```js
<script>
    methods: {

        submit() { 
            this.form.title = tinyMCE.get('title').getContent();
        },
        opened(){
            this.$initTinyMCE();
        }
    }
</script>
```