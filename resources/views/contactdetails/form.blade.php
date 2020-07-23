@csrf

@include ('forms.input.text', [
    "model" => "contactdetail", 
    "field" => "email", 
    "placeholder" => trans('global.contactdetail.fields.email'),  
    "required" => true, 
    "value" => old('email', isset($contactdetail) ? $contactdetail->email : '')])

@include ('forms.input.text', [
    "model" => "contactdetail", 
    "field" => "phone", 
    "placeholder" => trans('global.contactdetail.fields.phone'),  
    "type" => "text", 
    "value" => old('phone', isset($contactdetail) ? $contactdetail->phone : '')])

@include ('forms.input.text', [
    "model" => "contactdetail", 
    "field" => "mobile", 
    "placeholder" => trans('global.contactdetail.fields.mobile'),  
    "type" => "text", 
    "value" => old('mobile', isset($contactdetail) ? $contactdetail->mobile : '')])
    
@include ('forms.input.textarea', [
    "model" => "contactdetail", 
    "field" => "notes", 
    "rows" => 3, 
    "value" => old('notes', isset($contactdetail) ? $contactdetail->notes : '')]) 
                                         
<div>
    <input 
        id="plan-save"
        class="btn btn-info" 
        type="submit" 
        value="{{ $buttonText }}">
</div>