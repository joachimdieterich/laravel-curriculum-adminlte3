# File (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/file.blade.php`

```html
(at)include ('forms.input.file', [
    "model" => "media", 
    "field" => "path", 
    "label" => false,
    "value" => old('title', isset($media) ? $media->path : '')
])

```

Parameter | Type | required | Description
-----------------  | ------ | ------ | ------ 
model  | string | * | Name of Model e.g: `App\Period`
label  | boolean | * | Show label?
required | boolean |   | If `true` field is required
readonly | boolean |   | If `true` field is readonly
field  | string | * | Name of input field 
value | string |  | Selected option value





