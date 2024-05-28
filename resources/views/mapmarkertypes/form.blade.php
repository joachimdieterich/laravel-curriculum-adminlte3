@csrf
@include ('forms.input.text',
            ["model" => "mapMarkerType",
            "field" => "title",
            "placeholder" => "Title",
            "required" => true,
            "value" => old('title', isset($mapmarkertype) ? $mapmarkertype->title : '')])

@include ('forms.input.text',
            ["model" => "mapMarkerType",
            "field" => "description",
            "placeholder" => "description",
            "value" => old('street', isset($mapmarkertype) ? $mapmarkertype->description : '')])

@include ('forms.input.colorpicker',
            ["model" => "mapMarkerType",
            "field" => "color",
            "placeholder" => "#2471A3",
            "value" => old('color', isset($mapmarkertype) ? $mapmarkertype->color : '#2471A3')])
@include ('forms.input.text',
            ["model" => "mapMarkerType",
            "field" => "css_icon",
            "placeholder" => "fa-circle",
            "required" => true,
            "value" => old('title', isset($mapmarkertype) ? $mapmarkertype->css_icon : 'fa-circle')])
<div>
    <input
        id="mapmarkertype-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>
