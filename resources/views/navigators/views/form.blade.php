@csrf
@include ('forms.input.text', 
                    ["model" => "navigator_view", 
                    "field" => "title", 
                    "placeholder" => "Title",  
                    "required" => true, 
                    "value" => old('title', isset($view) ? $view->title : '')])
@include ('forms.input.textarea', 
            ["model" => "navigator_view", 
            "field" => "description", 
            "placeholder" => "Description",  
            "rows" => 3, 
            "value" => old('title', isset($view) ? $view->description : '')]) 

<div>
    <input id="navigator_id" name="navigator_id" type="hidden" value="{{ (null !== Request::get('navigator')) ? Request::get('navigator') : $view->navigator_id }}">
    <input 
        id="navigator-view-save"
        class="btn btn-info" 
        type="submit" 
        value="{{ $buttonText }}">
</div>