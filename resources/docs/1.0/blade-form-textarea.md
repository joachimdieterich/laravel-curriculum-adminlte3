# Textarea (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/textarea.blade.php`

```html
(at)include ('forms.input.textarea', [
    "model" => "certificate", 
    "field" => "body", 
    "placeholder" => "Body",  
    "rows" => 3, 
    "value" => old('body', isset($certificate) ? $certificate->body : '')
]) 
```

Parameter | Type | Description
-----------------  | ------ | ------ 
model  | string | Name of Model e.g: `App\certificate`
field  | string | Name of input field 
required | boolean | If `true` field is required
rows  | string | Lines of the Textarea
placeholder  | string | Placeholder for input field 
value | string | Selected option value

## [TinyMCE](/{{route}}/{{version}}/blade-editor)


