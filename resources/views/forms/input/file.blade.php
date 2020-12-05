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
        <a id="lfm"
           class="btn btn-primary text-white"
           onclick="app.__vue__.$modal.show('medium-create-modal',  {'description': {{ json_encode('') }} });">
            <i class="fa fa-photo-video"></i>
            {{ trans('global.'.$model.'.title_singular') }}
        </a>
    </span>
    <input id="medium_id"
        class="form-control"
        type="hidden"
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

<medium-create-modal></medium-create-modal>

@section('scripts')
@parent
<script>
    $(document).ready( function () {
        $('#medium_id').on('change', function() {
            //reload thumbs
            $('#holder').attr("src", '/media/'+ $('#medium_id').val());

        });
    });
</script>

@endsection
