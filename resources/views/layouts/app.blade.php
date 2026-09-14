<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <title>{{ trans('global.site_title') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    @include('layouts.partials.favicon')

    @yield('styles')
</head>

@yield('content')

</html>
