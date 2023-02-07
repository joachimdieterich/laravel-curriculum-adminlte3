@csrf
@include ('forms.input.text',
            ["model" => "config",
            "field" => "key",
            "placeholder" => "key",
            "required" => true,
            "value" => old('key', isset($config) ? $config->key : '')])
@include ('forms.input.textareaWithoutEditor',
            ["model" => "config",
            "rows" => 10,
            "field" => "value",
            "placeholder" => "value",
            "required" => true,
            "value" => old('value', isset($config) ? $config->value : '')])
@include ('forms.input.text',
            ["model" => "config",
            "field" => "referenceable_type",
            "placeholder" => "referenceable_type",
            "value" => old('referenceable_type', isset($config) ? $config->referenceable_type : '')])
@include ('forms.input.text',
            ["model" => "config",
            "field" => "referenceable_id",
            "placeholder" => "referenceable_id",
            "value" => old('referenceable_id', isset($config) ? $config->referenceable_id : '')])
@include ('forms.input.text',
            ["model" => "config",
            "field" => "data_type",
            "placeholder" => "data_type",
            "required" => true,
            "value" => old('data_type', isset($config) ? $config->data_type : '')])


    <input
        id="setting-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}"
    >
</div>
