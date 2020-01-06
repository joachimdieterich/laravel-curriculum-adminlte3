# Password Input (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/password.blade.php`

```html
(at)include ('forms.input.password', [
    "model" => "user", 
    "field" => "password", 
    "placeholder" => "New Password", 
    "type" => "password", 
    "value" => ""
])

```

Parameter | Type | Description
-----------------  | ------ | ------ 
model  | string | Name of Model e.g: `App\User`
field  | string | Name of input field 
required | boolean | If `true` field is required
readonly | boolean | If `true` field is readonly
placeholder  | string | Placeholder for input field 
value | string | Selected option value




