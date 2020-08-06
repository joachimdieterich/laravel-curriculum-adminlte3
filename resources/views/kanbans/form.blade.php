@csrf
@include ('forms.input.text', 
                    ["model" => "kanban", 
                    "field" => "title", 
                    "placeholder" => trans('global.kanban.fields.title'),  
                    "required" => true, 
                    "value" => old('title', isset($kanban) ? $kanban->title : '')])

@include ('forms.input.textarea', 
                    ["model" => "kanban", 
                    "field" => "description", 
                    "placeholder" => trans('global.kanban.fields.description'),  
                    "rows" => 3, 
                    "value" => old('description', isset($logbook) ? $kanban->description : '')])                                                                                                                          
@include ('forms.input.file', 
            ["model" => "media", 
            "field" => "path", 
            "label" => false,
            "value" => old('path', isset($media->path) ? '/laravel-filemanager'.$media->relativePath() : '')])
<div class="pt-3">
    <input 
        id="logbook-save"
        class="btn btn-info" 
        type="submit" 
        value="{{ $buttonText }}">
</div>
                    
@section('scripts')
@parent
<script>
$(document).ready( function () {                     
    $('#lfm').filemanager('files');
});
</script>
@endsection