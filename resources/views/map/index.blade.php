<!DOCTYPE html>
    <html lang="{{ App::getLocale() }}">

    @include('layouts.partials.head')
    <head>
        <!-- inside the <head> element -->
        <style>
            html, body {
                height: 100% !important;
            }
        </style>


    </head>
    <body>
        <div id="app" class="h-100">
            <leaflet-map
                :map="{{ $map }}"
            ></leaflet-map>
        </div>
        <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>

    </body>
</html>
