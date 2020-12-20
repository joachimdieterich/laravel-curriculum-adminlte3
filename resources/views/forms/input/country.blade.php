@include ('forms.input.select',
           ["model" => "country",
            "show_label" => true,
            "field" => "country_id",
            "options"=> $countries,
            "option_id" => "alpha2",
            "option_label"=> "lang_de",
            "value" => old('country_id', isset($country_id) ? $country_id : '') ])

@include ('forms.input.select',
           ["model" => "state",
            "show_label" => true,
            "field" => "state_id",
            "options"=> $states,
            "option_id" => "code",
            "option_label"=> "lang_de",
            "value" => old('state_id', isset($state_id) ? $state_id : '') ])

@section('scripts')
@parent
<script>
    $(function() {
        $('#country_id').change(function() {
            var url = "{{ url('countries') }}/" + $(this).val() + '/states/';

            $.get(url, function(data) {
                var select = $('#state_id');

                select.empty();

                $.each(data,function(key, value) {
                    select.append('<option value=' + value.code + '>' + value.lang_de + '</option>');
                });
            });
        });
    });
</script>
@endsection
