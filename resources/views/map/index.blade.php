<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

@include('layouts.partials.head')
<head>
    <style>
        html, body {
            height: 100% !important;
        }
    </style>
</head>
<body>
    <div id="app" class="h-100">
        <leaflet-map></leaflet-map>
    </div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>