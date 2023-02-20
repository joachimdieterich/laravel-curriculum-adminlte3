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
                    "editor_config_plugins" => [],
                    "editor_config_toolbar" => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
                    "value" => old('description', isset($kanban->description) ? $kanban->description : '')])
@can('medium_create')
    @include ('forms.input.file',
                ["model" => "media",
                "field" => "medium_id",
                "label" => false,
                "accept" => "image/*",
                "value" => old('medium_id', isset($kanban->medium_id) ? $kanban->medium_id : '')])
@endcan

<div id="kanban_comments_form_group" class="form-group pt-3">
    <span class="custom-control custom-switch custom-switch-on-green">
        <input
            type="checkbox"
            class="custom-control-input pt-1 "
            id="commentable"
            name="commentable"
            {{ ($kanban->commentable) ? "checked" : "" }}>
        <label class="custom-control-label " for="commentable" > Kommentare aktivieren</label>
    </span>
</div>

<div id="kanban_auto_refresh_form_group" class="form-group pt-3">
    <span class="custom-control custom-switch custom-switch-on-green">
        <input
            type="checkbox"
            class="custom-control-input pt-1 "
            id="auto_refresh"
            name="auto_refresh"
            {{ ($kanban->auto_refresh) ? "checked" : "" }}>
        <label class="custom-control-label " for="auto_refresh" > Automatisches aktualisieren</label>
    </span>
</div>

<div id="kanban_only_edit_owned_items_form_group" class="form-group pt-3">
    <span class="custom-control custom-switch custom-switch-on-green">
        <input
            type="checkbox"
            class="custom-control-input pt-1 "
            id="only_edit_owned_items"
            name="only_edit_owned_items"
            {{ ($kanban->only_edit_owned_items) ? "checked" : "" }}>
        <label class="custom-control-label " for="only_edit_owned_items" > Nutzer können nur selbst erstellte Status/Karten bearbeiten. </label>
    </span>
</div>

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
