<div id="{{ $field }}_form_group" class="form-group{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif 
    </label>

    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="far fa-calendar-alt"></i>
            </span>
        </div>
        <input 
            name="{{ $field }}" 
            id="{{ $field }}"
            type="text" 
            class="form-control {{ $errors->has($field) ? ' is-invalid' : '' }}" 
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
                <span 
                    id="{{ $field }}-error" 
                    class="error text-danger" 
                    for="input-{{ $field }}">{{ $errors->first( $field ) }}
                </span>
            @endif
    </div> 
</div>

@section('scripts')
@parent
    <script src="{{ asset('node_modules/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.'. app() -> getLocale().'.js') }}"></script>
    <script>
        $('#{{ $field }}').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
        });
    </script>
@endsection
