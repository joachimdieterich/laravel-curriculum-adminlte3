@csrf
@include ('forms.input.text', ["model" => "certificate", "field" => "title", "placeholder" => "Title",  "required" => true, "value" => old('title', isset($certificate) ? $certificate->title : '')])
@include ('forms.input.text', ["model" => "certificate", "field" => "description", "placeholder" => "Description", "value" => old('description', isset($certificate) ? $certificate->description : '')])

{{--@include ('forms.input.textarea', ["model" => "certificate", "field" => "body", "placeholder" => "Body",  "rows" => 3, "value" => old('body', isset($certificate) ? $certificate->body : '')])--}}

<div id="body_form_group" class="form-group {{ $errors->has( 'body' ) ? 'has-error' : '' }}">
    <label for="body">
        {{ trans('global.certificate.fields.body') }}
        *
    </label>
    <textarea
        id="body"
        name="body"
        class="form-control description my-editor "
        rows="30"
        placeholder="Template">
        {{ old('body', isset($certificate) ? $certificate->body : '') }}
    </textarea>

    @if($errors->has('body'))
        <p class="help-block">
            {{ $errors->first('body') }}
        </p>
    @endif
    <p class="helper-block">
        {{ trans('global.certificate.fields.body_helper') }}
    </p>
</div>

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

<input id="progress_reference"  type="hidden"/>
<objective-progress-subscription-modal></objective-progress-subscription-modal>
<div>
    <input class="btn btn-info" type="submit" value="{{ $buttonText }}">
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

            toolbar: " | customDateButton | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | insertFirstname insertLastname organizationTitle organizationStreet organizationPostcode organizationCity certificateDate | usersProgress",
            extended_valid_elements : "span[reference_type|reference_id|min_value]",
            setup: function (editor) {
                editor.ui.registry.addButton('insertFirstname', {
                    text: window.trans.global.user.fields.firstname,
                    onAction: function (_) {
                        editor.insertContent('<span id="firstname" style="background-color: lightgray;">'+ window.trans.global.user.fields.firstname + '</span>&nbsp;');
                    }
                });
                editor.ui.registry.addButton('insertLastname', {
                    text: window.trans.global.user.fields.lastname,
                    onAction: function (_) {
                        editor.insertContent('<span id="lastname" style="background-color: lightgray;">'+ window.trans.global.user.fields.lastname + '</span>');
                    }
                });
                editor.ui.registry.addButton('organizationTitle', {
                    text: window.trans.global.organization.title_singular,
                    onAction: function (_) {
                        editor.insertContent('<span id="organization_title" style="background-color: lightgray;">'+ window.trans.global.organization.title_singular + '</span>');
                    }
                });
                editor.ui.registry.addButton('organizationStreet', {
                    text: window.trans.global.organization.fields.street,
                    onAction: function (_) {
                        editor.insertContent('<span id="organization_street" style="background-color: lightgray;">'+ window.trans.global.organization.fields.street + '</span>');
                    }
                });
                editor.ui.registry.addButton('organizationPostcode', {
                    text: window.trans.global.organization.fields.postcode,
                    onAction: function (_) {
                        editor.insertContent('<span id="organization_postcode" style="background-color: lightgray;">'+ window.trans.global.organization.fields.postcode + '</span>&nbsp;');
                    }
                });
                editor.ui.registry.addButton('organizationCity', {
                    text: window.trans.global.organization.fields.city,
                    onAction: function (_) {
                        editor.insertContent('<span id="organization_city" style="background-color: lightgray;">'+ window.trans.global.organization.fields.city + '</span>');
                    }
                });
                editor.ui.registry.addButton('certificateDate', {
                    text: window.trans.global.date,
                    onAction: function (_) {
                        editor.insertContent('<span id="date" style="background-color: lightgray;">'+ window.trans.global.date + '</span>');
                    }
                });
                editor.ui.registry.addButton('usersProgress', {
                    text: window.trans.global.progress.title_singular,
                    onAction: function (_) {
                        document.querySelector("#app").__vue__.$modal.show('objective-progress-subscription-modal');
                        $('#progress_reference').on('change', function() {
                            const progress_reference = JSON.parse(document.getElementById('progress_reference').value);
                            editor.selection.setContent('<span reference_type="' + progress_reference['referenceable_type'] + '" reference_id="'+ ( Array.isArray(progress_reference['referenceable_id']) ? progress_reference['referenceable_id'].join() : progress_reference['referenceable_id']) +'" min_value="'+ progress_reference['percentage'] +'"/>'   +  tinymce.activeEditor.selection.getContent() + '</span>', {format: 'raw'});

                        });


                    }
                });

            },

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
