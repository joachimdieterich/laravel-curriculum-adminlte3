@csrf
@include ('forms.input.text', 
            ["model" => "grade", 
            "field" => "title", 
            "placeholder" => "Title",  
            "required" => true, 
            "value" => old('title', isset($grade) ? $grade->title : '')])

@include ('forms.input.text', 
            ["model" => "grade", 
            "field" => "external_begin", 
            "placeholder" => "1",  
            "required" => true, 
            "value" => old('title', isset($grade) ? $grade->external_begin : '')])

@include ('forms.input.text', 
            ["model" => "grade", 
            "field" => "external_end", 
            "placeholder" => "4",  
            "required" => true, 
            "value" => old('title', isset($grade) ? $grade->external_end : '')])
                      
@include ('forms.input.select', 
            ["model" => "organizationtype",
            "show_label" => true,
            "field" => "organization_type_id",  
            "options"=> $organization_types, 
            "value" => old('organization_id', isset($grade->organization_type_id) ? $grade->organization_type_id : '') ])    

                                         

<div>
    <input 
        id="grade-save"
        class="btn btn-info" 
        type="submit" 
        value="{{ $buttonText }}">
</div>