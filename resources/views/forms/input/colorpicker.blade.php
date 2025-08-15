<div id="{{ $field }}_form_group" class="form-group {{ $errors->has('$field') ? 'has-error' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required)) 
            * 
        @endif 
    </label>
    <div class="input-group blade-colorpicker colorpicker-element" data-colorpicker-id="2">
        <input 
            type="text" 
            id="{{ $field }}" 
            name="{{ $field }}"
            class="form-control" 
            data-original-title="" 
            value="{{ $value }}"
            @if(isset($readonly)) 
                readonly
            @endif 
            @if(isset($required)) 
                required
            @endif
            title="">
        @if($errors->has( $field ))
            <p class="help-block">
                {{ $errors->first( $field ) }}
            </p>
        @endif
        <p class="helper-block">
            {{ trans('global.'.$model.'.fields.'.$field.'_helper') }}
        </p>
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-square" style="color: rgb(176, 156, 71);"></i></span>
        </div>
    </div>
    
</div>
@section('styles')
@parent
<link rel="stylesheet" href="{{ asset('node_modules/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}"/>
@endsection
@section('scripts')
@parent
    <script src="{{ asset('node_modules/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
<script>
    $('.blade-colorpicker').colorpicker();
    $('.blade-colorpicker').on('colorpickerChange', 
        function(event){
            $('.blade-colorpicker .fa-square').css('color', event.color.toString());
        }
    );
</script>
@endsection