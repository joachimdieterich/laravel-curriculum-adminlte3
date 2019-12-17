@csrf
@include ('forms.input.text', 
            ["model" => "organizationtype", 
            "field" => "title", 
            "placeholder" => "curriculum Headquarter",  
            "required" => true, 
            "value" => old('title', isset($organizationtype) ? $organizationtype->title : '')])

@include ('forms.input.text', 
            ["model" => "organizationtype", 
            "field" => "external_id", 
            "value" => old('street', isset($organizationtype) ? $organizationtype->external_id : '')])

@include ('forms.input.select', 
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

<div>
    <input 
        id="organizationtype-save"
        class="btn btn-info" 
        type="submit" 
        value="{{ $buttonText }}"
    >
</div>