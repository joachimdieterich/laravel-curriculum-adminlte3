<div class="form-group{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    @if(isset($show_label))      
        <label for="{{ $field }}">{{ trans("global.{$model}.title_singular") }}
            @if(isset($multiple)) 
            <span class="btn btn-info btn-xs select-all">Select all</span>
            <span class="btn btn-info btn-xs deselect-all">Deselect all</span>
            @endif
        </label>
    @endif
    <select name="{{ $field }}[]" 
            id="input-{{ $field }}" 
            class="form-control select2{{ $errors->has($field) ? ' is-invalid' : '' }}"
            @if(isset($onchange)) 
             onchange="{{ $onchange }}"
           @endif
            onchange="form-control select2{{ $errors->has($field) ? ' is-invalid' : '' }}" 
            @if(isset($multiple)) 
             multiple="multiple"
           @endif
           >
        @foreach($options as $v)
            <option 
                value="{{ $v->status_id }}"
                {{ ( $v->status_id == $value ) ? 'selected' : '' }}
            >
                {{ $v->lang_de }}
            </option>
        @endforeach
    </select>
    @if ($errors->has( $field ))
        <span id="{{ $field }}-error" class="error text-danger" for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
    @endif
</div>
