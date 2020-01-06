# Button (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/button.blade.php`

```html
(at)include ('forms.input.button', [
    "onclick" => "resetPassword()", 
    "field" => "confirmed", 
    "type" => "button", 
    "class" => "btn btn-default pull-right mt-3", 
    "icon" => "fa fa-lock", 
    "label" => "Passwort zurücksetzen"
])

```

Parameter | Type | Description
-----------------  | ------ | ------ 
field  | string | Name of input field 
type | string | Possible types `text`, `submit`
class | string | Additional CSS
onclick | string | If set, `onclick="{ { $onclick } }"` is added to the button
icon | string | Example `fa fa-user`
label | string | Label of button




