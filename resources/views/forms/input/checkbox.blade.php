<div class="form-check{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <input 
        id="input-{{ $field }}"
        type="checkbox" 
        class="form-check-input{{ $errors->has($field) ? ' is-invalid' : '' }}" 
        @if($value == 1)
            checked="checked
        @endif
        />
        <label class="form-check-label" for="input-{{ $field }}">
            @if(isset($model)) 
                {{ trans('global.'.$model.'.fields.'.$field) }}
            @else
                {{ trans('global.'.$field) }}
            @endif
    </label>
    @if($errors->has( $field))
        <p class="help-block">
            {{ $errors->first( $field ) }}
        </p>
    @endif
    
    @if(isset($show_helper)) 
    <p class="helper-block">
        @if(isset($model)) 
            {{ trans('global.'.$model.'.fields.'.$field.'_helper') }}
        @else
            {{ trans('global.'.$field) }}
        @endif    
    </p>
    @endif
</div>