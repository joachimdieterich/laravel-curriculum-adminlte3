<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

<link href="{{ mix('css/app.css') }}" rel="stylesheet"/>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
        <div id="app" class="wrapper">
            <section class="content">
                @yield('content')
            </section>
        </div>
    </body>
</html>
