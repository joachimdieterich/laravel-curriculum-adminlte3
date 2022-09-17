@csrf
@include ('forms.input.text',
                    ["model" => "navigator",
                    "field" => "title",
                    "placeholder" => trans('global.navigator.fields.title'),
                    "required" => true,
                    "value" => old('title', isset($navigator) ? $navigator->title : '')])

@include ('forms.input.select',
                      ["model" => "organization",
                      "url" => "/organizations",
                      "placeholder" => trans('global.pleaseSelect'),
                      "show_label" => true,
                      "field" => "organization_id",
                      "value" => old('organization_id', isset($navigator->organization_id) ? $navigator->organization_id : '') ])

<div>
    <input
        id="navigator-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>
