<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

    @include('layouts.partials.head')
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" >
        <div id="app" class="wrapper">
            <div class="pl-2 pr-2" style="padding-bottom:50px">
                @yield('content')
                <input id="medium_id" class="invisible"> <!-- DONT REMOVE - used by TINYMCE -->
            </div>
        </div>

        <script src="{{ mix('js/app.js') }}"></script>

        @yield('scripts')
    </body>
</html>
