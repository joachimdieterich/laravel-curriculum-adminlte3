<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

    @include('layouts.partials.head')
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" >
        <div id="app" class="wrapper">
            <div class="ps-2 pe-2" style="padding-bottom:50px">
                @yield('content')
                <input id="medium_id" class="invisible"> <!-- DONT REMOVE - used by TINYMCE -->
            </div>
        </div>

        @yield('scripts')
    </body>
</html>
