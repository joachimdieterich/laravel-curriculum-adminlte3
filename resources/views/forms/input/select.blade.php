<div id="{{ $field }}_form_group" 
     class="form-group {{ $errors->has( $field ) ? ' has-danger' : '' }}" 
>
    @if(isset($show_label))      
        <label for="{{ $field }}" class="{{ isset($class_left) ? $class_left : 'col-sm-3' }}">
            
            {{ isset($label) ? $label :trans("global.{$model}.title_singular") }}
          
            @if(isset($multiple)) 
            <span class="btn btn-info btn-xs select-all">Select all</span>
            <span class="btn btn-info btn-xs deselect-all">Deselect all</span>
            @endif
        </label>
    @endif
        <select name="{{ $field }}[]" 
                id="{{ $field }}" 
                class="form-control select2 {{ $errors->has($field) ? ' is-invalid' : '' }} {{ isset($class_right) ? $class_right : 'col-sm-9' }}"
                style="width:100%"
                @if(isset($onchange)) 
                    onchange="{{ $onchange }}"
                @endif
                onchange="form-control select2{{ $errors->has($field) ? ' is-invalid' : '' }}" 
                @if(isset($multiple)) 
                    multiple="multiple"
                @endif
               >

            <?php $current_optgroup_id = 0; ?>
<!--            <option></option>-->
            @foreach($options as $v)
                <?php $o_id = isset($option_id) ? $option_id : 'id'; ?>
            
                
                    @if (isset($optgroup[0]) && ($current_optgroup_id != $v->$optgroup_reference_field ))
                         <?php $optgroup_label = ((isset($optgroup_label)) ? $optgroup_label : 'title');
                               $opt_label = $optgroup->where((isset($optgroup_id)) ? $optgroup_id : 'id', $v->$optgroup_reference_field)->first()->$optgroup_label ?>
                        <optgroup label="{{ $opt_label }}">
                    @endif
                
                            <option value="{{ $v->$o_id }}" {{ ( $v->$o_id == $value ) ? 'selected' : '' }}>
                               {{ (isset($option_label)) ? $v->$option_label :$v->title }}
                            </option>
                
                    @if (isset($optgroup[0]))
                        @if ($current_optgroup_id != $v->$optgroup_reference_field )
                         </optgroup>
                        @endif
                        <?php $current_optgroup_id = $v->$optgroup_reference_field ?>
                    @endif
                
            @endforeach
        </select>
        @if ($errors->has( $field ))
            <span id="{{ $field }}-error" class="error text-danger" for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
        @endif
    
</div>
@if (isset($placeholder))
    @section('scripts')
    @parent
    <!--hack to get select2 working-->
    <script>
    $(document).ready(function() {
        <!--hack to get select2 working z-index-->
        $("#{{ $field }}").select2({
            placeholder: "{{ $placeholder }}",
            dropdownParent: $("#{{ $field }}").parent()
        });



    });


    </script>
    @endsection
 @endif