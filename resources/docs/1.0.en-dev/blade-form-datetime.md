# Datetime (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/datetime.blade.php`

```html
(at)include ('forms.input.datetime', [
    "model" => "period", 
    "field" => "end", 
    "placeholder" => "2020-11-03 13:15:00",  
    "value" => old('date', isset($period) ? $period->end : '')])
])

```

Parameter | Type | Description
-----------------  | ------ | ------ 
model  | string | Name of Model e.g: `App\Period`
required | boolean | If `true` field is required
readonly | boolean | If `true` field is readonly
field  | string | Name of input field 
placeholder  | string | Placeholder for input field 
value | string | Selected option value





