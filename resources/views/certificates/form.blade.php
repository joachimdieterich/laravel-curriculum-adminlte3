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
<medium-create-modal></medium-create-modal>
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
            language: 'de',
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table directionality",
                "emoticons template paste textpattern example"
            ],

            toolbar: " | customDateButton | insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | example link image media | insertFirstname insertLastname organizationTitle organizationStreet organizationPostcode organizationCity certificateDate | usersProgress",

            extended_valid_elements : "span[id|class|style|name|reference_type|reference_id|min_value]",//important for certificates

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
                        editor.insertContent('<span id="lastname" style="background-color: lightgray;">'+ window.trans.global.user.fields.lastname + '</span>&nbsp;');
                    }
                });
                editor.ui.registry.addButton('organizationTitle', {
                    text: window.trans.global.organization.title_singular,
                    onAction: function (_) {
                        editor.insertContent('<span id="organization_title" style="background-color: lightgray;">'+ window.trans.global.organization.title_singular + '</span>&nbsp;');
                    }
                });
                editor.ui.registry.addButton('organizationStreet', {
                    text: window.trans.global.organization.fields.street,
                    onAction: function (_) {
                        editor.insertContent('<span id="organization_street" style="background-color: lightgray;">'+ window.trans.global.organization.fields.street + '</span>&nbsp;');
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
                        editor.insertContent('<span id="organization_city" style="background-color: lightgray;">'+ window.trans.global.organization.fields.city + '</span>&nbsp;');
                    }
                });
                editor.ui.registry.addButton('certificateDate', {
                    text: window.trans.global.date,
                    onAction: function (_) {
                        editor.insertContent('<span id="date" style="background-color: lightgray;">'+ window.trans.global.date + '</span>&nbsp;');
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
        };

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

        tinymce.init(editor_config);

    </script>
@endsection
