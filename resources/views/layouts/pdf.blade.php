<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
    <head>
        <meta charset="UTF-8">
        @vite(['resources/sass/app.scss'])
    </head>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
        <div id="app" class="wrapper">
            <section class="content">
                @yield('content')
            </section>
        </div>
    </body>
</html>
