@csrf
@include ('forms.input.text',
            ["model" => "organizationtype",
            "field" => "title",
            "placeholder" => "curriculum Headquarter",
            "required" => true,
            "value" => old('title', isset($organizationtype->title) ? $organizationtype->title : '')])

@include ('forms.input.text',
            ["model" => "organizationtype",
            "field" => "external_id",
            "value" => old('street', isset($organizationtype->external_id) ? $organizationtype->external_id : 0)])

@include ('forms.input.country', [
            "countries" => $countries,
            "country_id" => old('street', isset($organizationtype->country_id) ? $organizationtype->country_id : 'DE'),
            "states" => $states,
            "state_id" => old('street', isset($organizationtype->state_id) ? $organizationtype->state_id : 'DE-RP')
         ])

<div>
    <input
        id="organizationtype-save"
        class="btn btn-info"
        type="submit"
        value="{{ $buttonText }}"
    >
</div>
