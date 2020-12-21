@csrf
@include ('forms.input.text',
            ["model" => "subject",
            "field" => "title",
            "placeholder" => "Title",
            "required" => true,
            "value" => old('title', isset($subject) ? $subject->title : '')])

@include ('forms.input.text',
            ["model" => "subject",
            "field" => "title_short",
            "placeholder" => "Title_short",
            "value" => old('title_short', isset($subject) ? $subject->title_short : '')])

<div>
    <input
        id="subject-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}">
</div>
