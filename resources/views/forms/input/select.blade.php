<div class="form-group {{ $errors->has( $field ) ? ' has-danger' : '' }}">
    @if(isset($show_label))      
        <label for="{{ $field }}" class="{{ isset($class_left) ? $class_left : 'col-sm-3' }}">{{ trans("global.{$model}.title_singular") }}
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

            <?php $optgroup_id = 0 ?>

            @foreach($options as $v)
                <?php $o_id = 'id'; ?>
                @if(isset($option_id)) 
                    <?php $o_id = $option_id ?>
                @endif

                @if (isset($optgroup) && ($optgroup_id != $v->$optgroup_field ))

                    @if(isset($optgroup_label))
                        <?php $opt_label = $optgroup->where('id', $v->$optgroup_field)->first()->$optgroup_label ?>
                    @else
                        <?php $opt_label = $optgroup->where('id', $v->$optgroup_field)->first()->title ?>
                    @endif
                    <optgroup label="{{ $opt_label }}">
                @endif
                        <option value="{{ $v->$o_id }}" {{ ( $v->$o_id == $value ) ? 'selected' : '' }} >

                            @if(isset($option_label))
                                {{ $v->$option_label }}
                            @else
                                {{ $v->title }}
                            @endif

                        </option>

                @if (isset($optgroup))
                    @if ($optgroup_id != $v->$optgroup_id )
                        </optgroup>
                    @endif
                    <?php $optgroup_id = $v->$optgroup_field ?>
                @endif
            @endforeach
        </select>
        @if ($errors->has( $field ))
            <span id="{{ $field }}-error" class="error text-danger" for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
        @endif
    
</div>