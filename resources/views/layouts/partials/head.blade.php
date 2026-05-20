<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=7.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::user()->id }}">

    <title>{{ trans('global.site_title') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @include('layouts.partials.favicon')

    @yield('styles')

    <script>
        window.trans = <?php
            $translations = Cache::rememberForever('translations.' . App::getLocale(), function () {
                $lang_files = File::files(resource_path() . '/lang/' . App::getLocale());
                $trans = [];
                foreach ($lang_files as $f) {
                    $filename = pathinfo($f)['filename'];
                    $trans[$filename] = trans($filename);
                }
                return json_encode($trans);
            });
            echo $translations;
        ?>;

        window.Laravel = <?php
        echo json_encode([
            'csrfToken' => csrf_token(),
            'userId' => Auth::user()->id,
            'permissions' => Auth::user()->permissions()->pluck('title')->toArray()
        ]); ?>;
    </script>
</head>
