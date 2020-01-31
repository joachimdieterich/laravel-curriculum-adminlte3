@csrf
@include ('forms.input.text', 
                    ["model" => "logbook", 
                    "field" => "title", 
                    "placeholder" => "Title",  
                    "required" => true, 
                    "value" => old('title', isset($logbook) ? $logbook->title : '')])

@include ('forms.input.textarea', 
                    ["model" => "logbook", 
                    "field" => "description", 
                    "placeholder" => "Description",  
                    "rows" => 3, 
                    "value" => old('description', isset($logbook) ? $logbook->description : '')])                                                                                                                          

<div>
    <input 
        id="logbook-save"
        class="btn btn-info" 
        type="submit" 
        value="{{ $buttonText }}">
</div>