# Checkbox (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/checkbox.blade.php`

```html
(at)include ('forms.input.checkbox', [
    "model" => null, 
    "field" => "show_pw", 
    "value" => ""
])

```

Parameter | Type | Description
-----------------  | ------ | ------ 
model  | string | Name of Model e.g: `App\State`, nullable
field  | string | Name of input field 
show_helper | string | Show help text
class | string | Additional CSS
onclick | string | If set, `onclick="{ { $onclick } }"` is added to the button
icon | string | Example `fa fa-user`
label | string | Label of button




