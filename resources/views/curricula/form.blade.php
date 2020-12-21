@csrf
@include ('forms.input.text', ["model" => "curriculum", "field" => "title", "placeholder" => "Lehrplantitel",  "required" => true, "value" => old('title', isset($curriculum) ? $curriculum->title : '')])
@include ('forms.input.textarea', ["model" => "curriculum", "field" => "description", "placeholder" => "Description",  "rows" => 3, "value" => old('title', isset($curriculum) ? $curriculum->description : '')])
@include ('forms.input.text', ["model" => "curriculum", "field" => "author", "placeholder" => "Author",  "value" => old('author', isset($curriculum) ? $curriculum->author : '')])
@include ('forms.input.text', ["model" => "curriculum", "field" => "publisher", "placeholder" => "Publisher",  "value" => old('publisher', isset($curriculum) ? $curriculum->publisher : '')])
@include ('forms.input.text', ["model" => "curriculum", "field" => "city", "placeholder" => "Ilbesheim",  "value" => old('city', isset($curriculum) ? $curriculum->city : '')])

@include ('forms.input.datetime', ["model" => "curriculum", "field" => "date", "placeholder" => "2019-06-03 21:15:00",  "value" => old('date', isset($curriculum) ? $curriculum->date : '')])

@include ('forms.input.colorpicker',
            ["model" => "curriculum",
            "field" => "color",
            "placeholder" => "#231423",
            "value" => old('color', isset($curriculum) ? $curriculum->color : '')])

@include ('forms.input.file',
            ["model" => "media",
            "field" => "medium_id",
            "label" => false,
            "value" => old('medium_id', isset($media->id) ? $media->id : '')])

@include ('forms.input.select',
            ["model" => "grade",
            "show_label" => true,
            "field" => "grade_id",
            "options"=> $grades,
            "value" => old('grade_id', isset($curriculum->grade_id) ? $curriculum->grade_id : '') ])

@include ('forms.input.select',
            ["model" => "subject",
            "show_label" => true,
            "field" => "subject_id",
            "options"=> $subjects,
            "option_id" => "id",
            "value" => old('subject_id', isset($curriculum->subject_id) ? $curriculum->subject_id : '') ])

@include ('forms.input.select',
            ["model" => "organizationtype",
            "show_label" => true,
            "field" => "organization_type_id",
            "options"=> $organization_types,
            "value" => old('organization_type_id', isset($curriculum->organization_type_id) ? $curriculum->organization_type_id : '') ])

@include ('forms.input.select',
            ["model" => "curriculumtype",
            "show_label" => true,
            "field" => "type_id",
            "options"=> $curriculum_types,
            "value" => old('type_id', isset($curriculum->type_id) ? $curriculum->type_id : '') ])

@include ('forms.input.country', [
            "countries" => $countries,
            "country_id" => $curriculum->country_id,
            "states" => $states,
            "state_id" => $curriculum->state_id
         ])
<div>
    <input class="btn btn-info" type="submit" value="{{ $buttonText }}">
</div>
