@csrf
<color-picker-input></color-picker-input>
@include ('forms.input.text',
                    ["model" => "kanban",
                    "field" => "title",
                    "placeholder" => trans('global.kanban.fields.title'),
                    "required" => true,
                    "value" => old('title', isset($kanban) ? $kanban->title : '')])

@include ('forms.input.textarea',
                    ["model" => "kanban",
                    "field" => "description",
                    "placeholder" => trans('global.kanban.fields.description'),
                    "rows" => 3,
                    "value" => old('description', isset($logbook) ? $kanban->description : '')])
@can('medium_create')
    @include ('forms.input.file',
                ["model" => "media",
                "field" => "medium_id",
                "label" => false,
                "accept" => "image/*",
                "value" => old('medium_id', isset($kanban->medium_id) ? $kanban->medium_id : '')])
@endcan
<div class="pt-3">
    <input
        id="kanban-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>

@section('scripts')
@parent
@endsection
