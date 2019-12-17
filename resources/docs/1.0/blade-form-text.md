# Text (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/text.blade.php`

```html
(at)include ('forms.input.text', [
    "model" => "certificate", 
    "field" => "title", 
    "placeholder" => "Title",  
    "required" => true, 
    "value" => old('title', isset($certificate) ? $certificate->title : '')
])
```

Parameter | Type | Description
-----------------  | ------ | ------ 
model  | string | Name of Model e.g: `App\certificate`
field  | string | Name of input field 
required | boolean | If `true` field is required
readonly | boolean | If `true` field is readonly
placeholder  | string | Placeholder for input field 
value | string | Selected option value




