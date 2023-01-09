@csrf
@include ('forms.input.text',
                    ["model" => "meeting",
                    "field" => "uid",
                    "placeholder" => "uid",
                    "required" => true,
                    "value" => old('uid', isset($meeting) ? $meeting->uid : '')])
@include ('forms.input.file',
            ["model" => "media",
            "field" => "medium_id",
            "label" => false,
            "accept" => "image/*",
            "value" => old('medium_id', isset($meeting->medium_id) ? $meeting->medium_id : '')])
@include ('forms.input.textarea',
                    ["model" => "meeting",
                    "field" => "description",
                    "placeholder" => "Description",
                    "rows" => 3,
                    "value" => old('description', isset($meeting) ? $meeting->description : '')])
@include ('forms.input.textarea',
                    ["model" => "meeting",
                    "field" => "info",
                    "placeholder" => "info",
                    "rows" => 3,
                    "value" => old('info', isset($meeting) ? $meeting->info : '')])
@include ('forms.input.textarea',
                    ["model" => "meeting",
                    "field" => "speakers",
                    "placeholder" => "speakers",
                    "rows" => 3,
                    "value" => old('speakers', isset($meeting) ? $meeting->speakers : '')])
@include ('forms.input.textarea',
                    ["model" => "meeting",
                    "field" => "livestream",
                    "placeholder" => "livestream",
                    "rows" => 3,
                    "value" => old('livestream', isset($meeting) ? $meeting->livestream : '')])

<div>
    <input
        id="logbook-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>
