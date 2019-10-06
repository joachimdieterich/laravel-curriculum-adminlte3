<div id="{{ $field }}_form_group" class="form-group {{ $errors->has( $field ) ? 'has-error' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif
    </label>
    <textarea 
        id="{{ $field }}" 
        name="{{ $field }}" 
        class="form-control"
        rows="{{ $rows }}"
        placeholder="{{ __( $placeholder ) }}" 
        @if(isset($required)) 
         required 
        @endif
        >
        {{ $value }}
    </textarea>
    @if($errors->has($field))
        <p class="help-block">
            {{ $errors->first($field) }}
        </p>
    @endif
    <p class="helper-block">
        {{ trans('global.'.$model.'.fields.'.$field.'_helper') }}
    </p>
</div>