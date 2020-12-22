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
        <a id="openMediumCreateModal"
           class="btn btn-primary text-white"
           onclick="app.__vue__.$modal.show('medium-create-modal',  {'description': {{ json_encode('') }} });">
            <i class="fa fa-cloud-upload-alt pr-2"></i>
            {{ trans('global.'.$model.'.title_singular') }}
        </a>
    </span>
    <input id="{{ $field }}"
           name="{{ $field }}"
           class="form-control"
           type="hidden"
           value="{{ $value }}">
    @if($errors->has( $field ))
        <p class="help-block">
            {{ $errors->first( $field ) }}
        </p>
    @endif
</div>

<img id="holder"
     @if($value != '')
     src="/media/{{$value}}"
     @endif
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
