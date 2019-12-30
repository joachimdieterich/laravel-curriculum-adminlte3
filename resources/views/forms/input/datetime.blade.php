<div id="{{ $field }}_form_group" class="form-group{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif 
    </label>

    <div class="input-append">
        <input  type="text" 
                class="form-control float-right {{ $errors->has($field) ? ' is-invalid' : '' }}" 
                name="{{ $field }}" 
                id="{{ $field }}"
                
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
         <span class="add-on"><i class="icon-remove"></i></span>
    <span class="add-on"><i class="icon-th"></i></span>
        @if ($errors->has( $field ))
            <span id="{{ $field }}-error" class="error text-danger" for="input-{{ $field }}">{{ $errors->first( $field ) }}</span>
        @endif
    </div>
</div>
    

@section('styles')
@parent
    
@endsection
@section('scripts')
@parent
    <script src="{{ asset('node_modules/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('node_modules/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.'. app() -> getLocale().'.js') }}"></script>
    <script>
        $('#{{ $field }}').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
        });
    </script>
@endsection
