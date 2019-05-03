<div class="form-check{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <input 
        id="input-{{ $field }}"
        type="checkbox" 
        class="form-check-input{{ $errors->has($field) ? ' is-invalid' : '' }}" 
        @if($value == 1)
            checked="checked
        @endif
        />
        <label class="form-check-label" for="input-{{ $field }}">{{ trans('global.'.$model.'.fields.'.$field) }}</label>
    @if($errors->has( $field))
        <p class="help-block">
            {{ $errors->first( $field ) }}
        </p>
    @endif
    <p class="helper-block">
        {{ trans('global.'.$model.'.fields.'.$field.'_helper') }}
    </p>
</div>