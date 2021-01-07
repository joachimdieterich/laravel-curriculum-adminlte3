@csrf
@include ('forms.input.text',
            ["model" => "objectiveType",
            "field" => "title",
            "placeholder" => "Title",
            "required" => true,
            "value" => old('title', isset($objectivetype) ? $objectivetype->title : '')])

<div>
    <input
        id="objectivetype-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>
