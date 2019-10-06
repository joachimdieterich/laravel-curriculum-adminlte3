@csrf
@include ('forms.input.text', 
                    ["model" => "navigator", 
                    "field" => "title", 
                    "placeholder" => "Title",  
                    "required" => true, 
                    "value" => old('title', isset($navigator) ? $navigator->title : '')])

                                                                   
@include ('forms.input.select', 
                      ["model" => "organization",
                      "show_label" => true,
                      "field" => "organization_id",  
                      "options"=> $organizations, 
                      "value" => old('organization_id', isset($navigator->organization_id) ? $navigator->organization_id : '') ])                                                          

<div>
    <input class="btn btn-info" type="submit" value="{{ $buttonText }}">
</div>