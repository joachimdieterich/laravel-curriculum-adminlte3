@csrf
@include ('forms.input.select', 
        ["model" => "plan",
        "show_label" => true,
        "label" => trans('global.plan.fields.type'),
        "field" => "type_id",  
        "options"=> $types, 
        "option_id" => "id",
        "option_label"=> "title", 
        "required" => true, 
        "value" => old('type_id', isset($plan->type_id) ? $plan->type_id : '') ])
@include ('forms.input.text', [
    "model" => "plan", 
    "field" => "title", 
    "placeholder" => trans('global.plan.title_singular'),  
    "required" => true, 
    "value" => old('title', isset($plan) ? $plan->title : '')])

@include ('forms.input.textarea', [
    "model" => "plan", 
    "field" => "description", 
    "rows" => 3, 
    "value" => old('description', isset($plan) ? $plan->description : '')]) 

@include ('forms.input.datetime', 
    ["model" => "plan", 
    "field" => "begin", 
    "placeholder" => "2019-11-03 13:14:00",  
    "value" => old('begin', isset($plan) ? $plan->begin : '')])

@include ('forms.input.datetime', 
    ["model" => "plan", 
    "field" => "end", 
    "placeholder" => "2020-11-03 13:15:00",  
    "value" => old('end', isset($plan) ? $plan->end : '')])

@include ('forms.input.text', [
    "model" => "plan", 
    "field" => "duration", 
    "type" => "number", 
    "value" => old('duration', isset($plan) ? $plan->duration : '')])
                                         
<div>
    <input 
        id="plan-save"
        class="btn btn-info" 
        type="submit" 
        value="{{ $buttonText }}">
</div>