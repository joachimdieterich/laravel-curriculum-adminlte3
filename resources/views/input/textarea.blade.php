<div class="form-group{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <textarea 
        class="form-control{{ $errors->has( $field ) ? ' is-invalid' : '' }}" 
        name="{{ $field }}" 
        id="input-{{ $field }}" 
        rows="{{ $rows }}"
        placeholder="{{ __( $placeholder ) }}" 
        @if(isset($required)) 
        'required' 
        @endif
        >
         {{ $value }}
    </textarea>
     @if ($errors->has( $field ))
        <span id="{{ $field }}-error" class="error text-danger" for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
     @endif
</div>