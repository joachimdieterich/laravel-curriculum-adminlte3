@csrf
@include ('forms.input.text',
            ["model" => "variantDefinitions",
            "field" => "title",
            "placeholder" => "Title",
            "required" => true,
            "value" => old('title', isset($variantdefinitions) ? $variantdefinitions->title : '')])
@include ('forms.input.textarea',
            ["model" => "variantDefinitions",
            "field" => "description",
            "placeholder" => "Description",
            "rows" => 3,
            "value" => old('description', isset($variantdefinitions) ? $variantdefinitions->description : '')])
@include ('forms.input.colorpicker',
            ["model" => "variantDefinitions",
            "field" => "color",
            "placeholder" => "#231423",
            "value" => old('color', isset($variantdefinitions) ? $variantdefinitions->color : '')])
@include ('forms.input.text',
            ["model" => "variantDefinitions",
            "field" => "css_icon",
            "placeholder" => "fa fa-tag",
            "value" => old('title', isset($variantdefinition) ? $variantdefinition->css_icon : '')])
<div>
    <input
        id="variantdefinition-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>
