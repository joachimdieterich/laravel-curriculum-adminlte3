<div id="{{ $field }}_form_group" class="input-group {{ $errors->has('$field') ? 'has-error' : '' }}">
    @if($label) 
    <label for="{{ $field }}">
    {{ trans('global.'.$model.'.fields.'.$field) }}
       @if(isset($required)) 
           * 
       @endif 
    </label>
    @endif
    <span class="input-group-btn">
        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-photo-video"></i> {{ trans('global.'.$model.'.title_singular') }}
        </a>
    </span>
    <input id="thumbnail" 
        name="filepath"
        class="form-control" 
        type="text" 
        value="{{ $value }}"
        >
    @if($errors->has( $field ))
        <p class="help-block">
            {{ $errors->first( $field ) }}
        </p>
    @endif
</div>
<img id="holder" 
    style="margin-top:15px;max-height:100px;">