# Select (Blade)

---

- [Usage](#section-1)

<a name="section-1"></a>
## Usage

Definition `/resources/views/forms/input/select.blade.php`

```html
(at)include ('forms.input.select', 
            ["model" => "state", 
            "show_label" => true,
            "field" => "state_id",  
            "options"=> $states, 
            "option_label" => "lang_de",    
            "option_id" => "code",    
            "optgroup" => $countries,    
            "optgroup_label" => "lang_de",
            "optgroup_id" => "alpha2",
            "optgroup_reference_field" => "country",    
            "value" =>  old('state_id', isset($organizationtype->state_id) ? $organizationtype->state_id : '')])         
```

Parameter | Type | Description
-----------------  | ------ | ------ 
model  | string | Name of Model e.g: `App\State`
show_label | boolean | If `true` label is shown
label | string | If label is set it is used, if not fallback is used `trans("global.{$model}.title_singular")`,  
field  | string | Name of input field 
css | string | Additional CSS for `form-group`
class_left | string | Additional CSS for `<label>`
class_right | string | Additional CSS for `form-control`
style | string | Additional styles for `form-control`
multiple | boolean | If `true` select/deselect all is shown
onchange | string | If set, `onchange="{ { $onchange } }"` is added to select field
options | array | Array of options
option_id | string | If set value of `option_id` is used, if not fallback is `id`
option_label | string | If set value of `option_label` is used, if not fallback is value of `$option[i]->title`
optgroup | array | Array of optiongroup
optgroup_id | string | If set value of `optgroup_id` is used, if not fallback is `id`
optgroup_label | string | If set value of `optgroup_label` is used, if not fallback is value of `$optgroup[i]->title`
optgroup_reference_field | string | Reference field `$options[i]->optgroup_reference_field` which is related to `optgroup_id`
value | string | Selected option value



