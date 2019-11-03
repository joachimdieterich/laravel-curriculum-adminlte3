@csrf
@include ('forms.input.text', ["model" => "group", "field" => "title", "placeholder" => "Name der Lerngruppe",  "required" => true, "value" => old('title', isset($group) ? $group->title : '')])
           
@include ('forms.input.select', 
                      ["model" => "grade",
                      "show_label" => true,
                      "field" => "grade_id",  
                      "options"=> $grades, 
                      "value" => old('grade_id', isset($group->grade_id) ? $group->grade_id : '') ])                                                          
                
@include ('forms.input.select', 
                      ["model" => "period",
                      "show_label" => true,
                      "field" => "period_id",  
                      "options"=> $periods, 
                      "value" => old('period_id', isset($group->period_id) ? $group->period_id : '') ])                                                          
                      
@include ('forms.input.select', 
                      ["model" => "organization",
                      "show_label" => true,
                      "field" => "organization_id",  
                      "options"=> $organizations, 
                      "value" => old('organization_id', isset($group->organization_id) ? $group->organization_id : '') ])                                                          

<div>
    <input class="btn btn-info" type="submit" value="{{ $buttonText }}">
</div>