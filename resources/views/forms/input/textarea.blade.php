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
        @if(isset($placeholder))
            placeholder="{{ __($placeholder) }}"
        @endif
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
    <!--script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script-->
@endsection
@section('scripts')
@parent
<script>
    var editor_config_plugins =
        @if(isset($editor_config_plugins))
            @json($editor_config_plugins)
    @else
            [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table directionality",
                "emoticons template paste textpattern"
            ]
    @endif
    ;
    var editor_config_toolbar =
      @if(isset($editor_config_toolbar))
          "{{ $editor_config_toolbar }}"
      @else
          "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media"
      @endif
    ;

  var editor_config = {
    path_absolute : "/",
    selector: "textarea.my-editor",
    branding:false,
    language: 'de',
    plugins: editor_config_plugins,
    toolbar: editor_config_toolbar,
    relative_urls: false,
  };

  tinymce.init(editor_config);
    tinymce.PluginManager.add('example', function(editor, url) {
        var openDialog = function () {
            document.querySelector("#app").__vue__.$modal.show('medium-create-modal', {'public': 1 });
            $('#medium_id').on('change', function() {
                //reload thumbs
                editor.insertContent('<img src="/media/'+ document.getElementById('medium_id').value +'" width="500">', {format: 'raw'});
            });
        };

        // Add a button that opens a window
        editor.ui.registry.addButton('example', {
            text: 'Medien',
            onAction: function ()  {
                // Open window
                openDialog();
            }
        });

        return {
            getMetadata: function () {
                return  {
                    name: 'Curriculum Media Plugin',
                    url: 'http://curriculumonline.de'
                };
            }
        };
    });
</script>
@endsection
