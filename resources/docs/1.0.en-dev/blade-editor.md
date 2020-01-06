# TinyMCE

---

- [Activate TinyMCE](#section-1)
- [Usage](#section-2)

<a name="section-1"></a>
## Activate TinyMCE 

TinyMCE is imported in /resources/views/layouts/master.blade.php
```html
<script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
<script>
    tinymce.init({
        selector:'textarea',
        branding: false
    });
</script>
```

<a name="section-2"></a>
## Usage

Just use a `<textarea></textarea` in blade. You can also use `/resources/views/forms/input/textarea.blade.php`.


```html
// Example for description fielt on curriculum model
(at)include ('forms.input.textarea', 
          [
            "model" => "curriculum", 
            "field" => "description", 
            "placeholder" => "Description",  
            "rows" => 3, 
            "value" => old('title', isset($curriculum) ? $curriculum->description : '')
           ]) 
```
Attention! Replace (at) with @    