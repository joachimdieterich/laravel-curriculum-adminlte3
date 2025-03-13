<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

@include('layouts.partials.head')

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed content-only" >

    <div id="app" class="d-flex flex-column flex-fill wrapper" style="height: 100vh">
        <!-- Navbar -->

        <nav class="main-header navbar navbar-expand navbar-lime navbar-light user-select-none py-0"
        style="margin-left:0">

            <!-- Left navbar links -->
            <ul class="navbar-nav">
                @include('partials.topmenu')
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav pl-2 mr-auto">
                <searchbar></searchbar>
            </ul>
            @include('partials.navbar')

        </nav>
        <!-- /.navbar -->

        <!-- Content Header (Page header) -->
        <section
            class="content-header px-3 py-2"
            style="margin-top: 3.5rem !important"
        >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6 pl-0">
                        <h1> @yield('title')</h1>
                    </div>
                    <div class="d-flex align-items-center col-sm-6 px-0">
                        @yield('breadcrumb')
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content d-flex flex-column flex-fill px-3">
            @yield('content')
            <input id="medium_id" class="d-none"> <!-- DONT REMOVE - used by TINYMCE -->
        </section>

        <!-- Footer -->
        @include('partials.footer', ['contentonly' => true])
    </div>

    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
    <script src="{{ asset('node_modules/mathjax/es5/tex-svg.js') }}"></script>
    {{--
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ asset('node_modules/moment/js/moment.min.js') }}"></script>
    <script src="{{ asset('node_modules/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>--}}
    @yield('scripts')

</body>

</html>
