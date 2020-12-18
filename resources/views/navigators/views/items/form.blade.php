@csrf
@if (isset($navigatorItem))
    @include ('forms.input.select',
           ["model" => "navigator_item",
           "label" => "Item Type",
           "show_label" => true,
           "field" => "referenceable_type",
           "options"=> $referenceable_types,
           "option_id" => "class",
           "option_label"=> "label",
           "readonly" => true,
           "value" => old('referenceable_type', isset($navigatorItem) ? $navigatorItem->referenceable_type : '') ])
@else
    @include ('forms.input.select',
               ["model" => "navigator_item",
               "label" => "Item Type",
               "show_label" => true,
               "field" => "referenceable_type",
               "options"=> $referenceable_types,
               "option_id" => "class",
               "option_label"=> "label",
               "value" => old('referenceable_type', isset($navigatorItem) ? $navigatorItem->referenceable_type : '') ])
@endif

@include ('forms.input.text',
            ["model" => "navigator_item",
            "field" => "title",
            "placeholder" => "Item Title",
            "value" => old('title', isset($navigatorItem) ? $navigatorItem->title : '')])
@include ('forms.input.textarea',
            ["model" => "navigator_item",
            "field" => "description",
            "placeholder" => "Description",
            "rows" => 3,
            "value" => old('description', isset($navigatorItem) ? $navigatorItem->description : '')])

@include ('forms.input.select',
            ["model" => "navigator_item",
             "label" => "Reference Id",
            "show_label" => true,
            "field" => "referenceable_id",
            "options"=> $curricula,
            "option_id" => "id",
            "option_label"=> "title",
            "value" => old('referenceable_id', isset($navigatorItem) ? $navigatorItem->referenceable_id : '') ])
@include ('forms.input.file',
            ["model" => "media",
            "field" => "medium_id",
            "label" => false,
            "value" => old('medium_id', isset($medium) ? $medium->id : '')])
@include ('forms.input.select',
            ["model" => "navigator_item",
             "label" => "Position",
            "show_label" => true,
            "field" => "position",
            "options"=> $position,
            "option_id" => "id",
            "option_label"=> "label",
            "value" => old('position', isset($navigatorItem) ? $navigatorItem->position : 'content') ])

@include ('forms.input.select',
            ["model" => "navigator_item",
             "label" => "css_class",
            "show_label" => true,
            "field" => "css_class",
            "options"=> $css_classes,
            "option_id" => "class",
            "option_label"=> "label",
            "value" => old('css_class', isset($navigatorItem) ? $navigatorItem->css_class : 'col-xs-12') ])
@include ('forms.input.select',
            ["model" => "navigator_item",
             "label" => "visibility",
            "show_label" => true,
            "field" => "visibility",
            "options"=> $visibility,
            "option_id" => "id",
            "option_label"=> "label",
            "value" => old('visibility', isset($navigatorItem) ? $navigatorItem->visibility : '1') ])
<div>
    <input
        id="navigator-item-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>

@section('scripts')
@parent
<script>
$(document).ready( function () {
    $('#referenceable_id_form_group').hide();
    $('#referenceable_type').change(function (){
        switch($(this).val()) {
            case "App\\Content":        $('#referenceable_id_form_group').hide();
                                        $('#title_form_group').show();
                                        $('#description_form_group').show();
                                        $('#medium_id_form_group').hide();
              break;
            case "App\\Curriculum":     $('#referenceable_id_form_group').show();
                                        $('#title_form_group').hide();
                                        $('#description_form_group').hide();
                                        $('#medium_id_form_group').hide();
              break;
            case "App\\NavigatorView":  $('#referenceable_id_form_group').hide();
                                        $('#title_form_group').show();
                                        $('#description_form_group').show();
                                        $('#medium_id_form_group').show();
              break;
            case "App\\Medium":         $('#referenceable_id_form_group').hide();
                                        $('#title_form_group').show();
                                        $('#description_form_group').show();
                                        $('#medium_id_form_group').show();
              break;
            default:
                break;
        }
    });

});
</script>
@endsection
