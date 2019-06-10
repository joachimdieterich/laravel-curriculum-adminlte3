<div class="form-group{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif 
    </label>

    <div class="input-group date">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fa fa-calendar"></i>
            </span>
        </div>
        <input  type="text" 
                class="form-control float-right {{ $errors->has($field) ? ' is-invalid' : '' }}" 
                accept=""name="{{ $field }}" 
                id="{{ $field }}"
                accesskey=""
                value="{{ $value }}" 
                @if(isset($placeholder)) 
                    placeholder="{{ __($placeholder) }}" 
                @endif 
                @if(isset($readonly)) 
                     readonly
                @endif 
                @if(isset($required)) 
                    'required' 
                @endif
        />
        @if ($errors->has( $field ))
            <span id="{{ $field }}-error" class="error text-danger" for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
        @endif
    </div>
</div>

