<div id="{{ $field }}_form_group"
     class="form-group {{ $errors->has( $field ) ? ' has-danger' : '' }} {{ isset($css) ? $css : '' }}"
>
    @if(isset($show_label))
        <label for="{{ $field }}" class="{{ isset($class_left) ? $class_left : 'p-0 col-sm-12' }}">

            {{ isset($label) ? $label :trans("global.{$model}.title_singular") }}

            @if(isset($multiple))
                <span class="btn btn-info btn-xs deselect-all pull-right">{{trans("global.deselect_all")}}</span>
                <span class="btn btn-info btn-xs select-all pull-right mr-1">{{trans("global.select_all")}}</span>

            @endif
        </label>
    @endif

    <select name="{{ $field }}[]"
            id="{{ $field }}"
            aria-label="{{isset($aria_label) ? $aria_label : 'Select' }}"
            class="form-control select2 {{ $errors->has($field) ? ' is-invalid' : '' }} {{ isset($class_right) ? $class_right : 'col-sm-12' }}"
            style="{{ isset($style) ? $style : 'width:100%;' }}"

            @if(isset($onchange))
            onchange="{{ $onchange }}"
            @endif
            @if(isset($multiple))
            multiple="multiple"
            @endif
            @if(isset($readonly))
            disabled="disabled"
        @endif
    >

        <?php $current_optgroup_id = 'false'; ?>
        @if(!isset($multiple))
            <option></option>
        @endif
        @if(isset($options))
                @foreach($options as $v)
                    <?php $o_id = isset($option_id) ? $option_id : 'id'; ?>

                    @if (isset($optgroup[0]) && ($current_optgroup_id != $v->$optgroup_reference_field ))
                        <?php $optgroup_label = ((isset($optgroup_label)) ? $optgroup_label : 'title');
                        $opt_label = $optgroup->where((isset($optgroup_id)) ? $optgroup_id : 'id', $v->$optgroup_reference_field)->first()->$optgroup_label
                        ?>
                        <optgroup
                            id="{{ $v->$optgroup_reference_field }}"
                            label="{{ $opt_label }}"
                            @if (isset($optgroup_class))
                            data-class="{{ $optgroup_class }}"
                            @endif
                            @if (isset($optgroup_icon))
                            data-icon="{{ $optgroup_icon }}"
                            @endif
                        >
                            @endif

                            <option
                                value="{{ $v->$o_id }}"
                                @if (isset($option_icon))
                                    data-icon="{{ $option_icon }}"
                                @endif
                                @if(isset($multiple))
                                    {{ in_array($v->$o_id, (array)$value) ? 'selected' : '' }}
                                @else
                                    {{ ( $v->$o_id == $value ) ? 'selected' : '' }}
                                @endif

                            >
                                {{ (isset($option_label)) ? $v->$option_label :$v->title }}
                                @if (isset($combine_labels) ? $combine_labels : false)
                                    | {{ $opt_label }}
                                @endif
                            </option>

                            @if (isset($optgroup[0]))
                                @if ($current_optgroup_id != $v->$optgroup_reference_field )
                        </optgroup>
                    @endif
                    <?php $current_optgroup_id = $v->$optgroup_reference_field ?>
                    @endif

                @endforeach
        @endif

    </select>
    @if ($errors->has( $field ))
        <span id="{{ $field }}-error" class="error text-danger"
              for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
    @endif
</div>
@if (isset($url))
@section('scripts')
    @parent
    <!--hack to get select2 working-->
    <script>
        $(document).ready(function () {
            function formatText(icon) {
                return $('<span class="' + $(icon.element).data('class') + '"><i class="fas ' + $(icon.element).data('icon') + '"></i> ' + icon.text + '</span>');
            }
            <!--hack to get select2 working z-index-->
             $("#{{ $field }}").select2({
                placeholder: "{{ $placeholder }}",
                dropdownParent: $("#{{ $field }}").parent(),
                allowClear: "{{ $allowClear ?? true }}",
                ajax: {
                    delay: 250,
                    url: "{{ $url }}",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    /*processResults: function(data) {
                        let results = data.results;
                        let options = {{json_encode($value)}}
                        for (var i = 0; i < results.length; i++) {
                            if (options.includes(results[i].id )) {
                                results[i]["selected"] = "true";
                            }
                        }

                        data.results = results;
                        console.log(data);
                        return { results: data.results  };
                    },*/
                    cache: true,
                },
                templateSelection: formatText,
                templateResult: formatText,
            });

        });
    </script>
@endsection
@endif


@if (isset($placeholder))
    @section('scripts')
    @parent
    <!--hack to get select2 working-->
    <script>
        $(document).ready(function () {
            function formatText(icon) {
                return $('<span class="' + $(icon.element).data('class') + '"><i class="fas ' + $(icon.element).data('icon') + '"></i> ' + icon.text + '</span>');
            }
        <!--hack to get select2 working z-index-->
        $("#{{ $field }}").select2({
            placeholder: "{{ $placeholder }}",
            dropdownParent: $("#{{ $field }}").parent(),
            allowClear: "{{ $allowClear ?? true }}",
            templateSelection: formatText,
            templateResult: formatText
        });
    });
    </script>
    @endsection
@endif


