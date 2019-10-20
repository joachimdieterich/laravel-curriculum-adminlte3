@csrf
@include ('forms.input.text', ["model" => "certificate", "field" => "title", "placeholder" => "Title",  "required" => true, "value" => old('title', isset($certificate) ? $certificate->title : '')])
@include ('forms.input.text', ["model" => "certificate", "field" => "description", "placeholder" => "Description", "value" => old('description', isset($certificate) ? $certificate->description : '')])

@include ('forms.input.textarea', ["model" => "certificate", "field" => "body", "placeholder" => "Body",  "rows" => 3, "value" => old('body', isset($certificate) ? $certificate->body : '')]) 
                
@include ('forms.input.select', 
                      ["model" => "curriculum",
                      "show_label" => true,
                      "field" => "curriculum_id",  
                      "options"=> $curricula, 
                      "value" => old('curriculum_id', isset($certificate->curriculum_id) ? $certificate->curriculum_id : '') ])                                                          
                
@include ('forms.input.select', 
                      ["model" => "organization",
                      "show_label" => true,
                      "field" => "organization_id",  
                      "options"=> $organisations, 
                      "value" => old('organization_id', isset($certificate->organization_id) ? $certificate->organization_id : '') ])                                                          
                
                                                      
      
<div>
    <input class="btn btn-info" type="submit" value="{{ $buttonText }}">
</div>