<div id="{{ $field }}_form_group" class="form-group {{ $errors->has( $field ) ? 'has-error' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif
    </label>
    <textarea
        id="{{ $field }}" 
        name="{{ $field }}" 
        class="form-control description my-editor "
        rows="{{ $rows }}"
        placeholder="{{ __( $placeholder ) }}" 
        @if(isset($required)) 
         required 
        @endif
        >
        {{ $value }}
    </textarea>
    
    @if($errors->has($field))
        <p class="help-block">
            {{ $errors->first($field) }}
        </p>
    @endif
    <p class="helper-block">
        {{ trans('global.'.$model.'.fields.'.$field.'_helper') }}
    </p>
</div>

@section('styles')
@parent
    <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
@endsection
@section('scripts')
@parent
<script>
  var editor_config = {
    path_absolute : "/",
    selector: "textarea.my-editor",
    branding:false,
    plugins: [
      "advlist autolink lists link image charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen",
      "insertdatetime media nonbreaking save table directionality",
      "emoticons template paste textpattern"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
    relative_urls: false,
    file_browser_callback : function(field_name, url, type, win) {
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

      var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
//      if (type == 'image') {
//        cmsURL = cmsURL + "&type=Images";
//      } else {
        cmsURL = cmsURL + "&type=Files";
      //}

      tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        title : 'Filemanager',
        width : x * 0.8,
        height : y * 0.8,
        resizable : "yes",
        close_previous : "no"
      });
    }
  };

  tinymce.init(editor_config);
</script>
@endsection