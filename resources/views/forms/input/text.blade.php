<div id="{{ $field }}_form_group" class="form-group {{ $errors->has('$field') ? 'has-error' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif 
    </label>
    <input 
        type="{{ $type ?? 'text' }}"   
        id="{{ $field }}" 
        name="{{ $field }}" 
        class="form-control" 
        value="{{ $value }}"
        @if(isset($placeholder)) 
            placeholder="{{ __($placeholder) }}" 
        @endif 
        @if(isset($readonly)) 
             readonly
        @endif 
        @if(isset($required)) 
             required
        @endif 
        />
    @if($errors->has( $field ))
        <p class="help-block">
            {{ $errors->first( $field ) }}
        </p>
    @endif
    <p class="helper-block">
        {{ trans('global.'.$model.'.fields.'.$field.'_helper') }}
    </p>
</div>