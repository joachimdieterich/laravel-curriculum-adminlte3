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
           onclick="app.__vue__.$modal.show('medium-create-modal',
                {
                    'description': {{ json_encode('') }} ,
                    'accept': '{{ $accept ?? '' }}',
                    'subscribable_type': '{{  $subscribable_type ?? '' }}',
                    'subscribable_id': '{{  $subscribable_id ?? '' }}',
                });">
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

<!--<media-renderer
    style="max-height: 150px; overflow: auto;"
    :medium="{{ App\Medium::find($value) }}"
    :downloadable=false
></media-renderer>-->
<img id="{{ $field }}_holder"
     @if($value != '')
     src="/media/{{$value}}"
     @endif
     style="margin-top:15px;max-height:100px;">

<medium-modal></medium-modal>
@section('scripts')
@parent
<script>
    $(document).ready( function () {
        $("#{{ $field }}").on('change', function() {
            //reload thumbs
            $("#{{ $field }}_holder").attr("src", '/media/'+ $("#{{ $field }}").val());
        });
    });
</script>
@endsection
