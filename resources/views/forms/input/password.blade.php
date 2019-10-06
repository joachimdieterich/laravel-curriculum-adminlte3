<div id="{{ $field }}_form_group" class="form-group {{ $errors->has('$field') ? 'has-error' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif 
    </label>
    <input 
        type="password"   
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
@include ('forms.input.checkbox', ["model" => null, "field" => $field."_show", "value" => ""])

@section('scripts')
@parent
<script>
    $(document).ready( function () {
        $('#{{ $field }}_show').on('change', function(){
            $('#{{ $field }}').attr('type',$('#{{ $field }}_show').prop('checked')==true?"text":"password"); 
        });
    });
</script>
@endsection