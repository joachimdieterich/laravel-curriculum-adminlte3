@csrf
@include ('forms.input.text',
            ["model" => "metadataset",
            "field" => "version",
            "placeholder" => "1",
            "required" => true,
            "value" => old('version', isset($metadataset->version) ? $metadataset->version : '')])


<div>
    <input
        id="metadataset-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}"
    >
</div>
