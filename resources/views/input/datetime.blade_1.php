<div class="form-group{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <input 
        class="form-control datetimepicker{{ $errors->has($field) ? ' is-invalid' : '' }}"
        name="{{ $field }}" 
        id="input-{{ $field }}"
        type="text" 
         
        placeholder="{{ __($placeholder) }}"
        value="{{ $value }}" 
        @if(isset($required)) 
            'required' 
        @endif
        />                        
     @if ($errors->has( $field ))
        <span id="{{ $field }}-error" class="error text-danger" for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
     @endif
</div>