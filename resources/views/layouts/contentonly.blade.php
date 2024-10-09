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
        <section class="content-header p-2"
            style="padding-top:60px !important">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6 pl-0">
                        <h1> @yield('title')</h1>
                    </div>
                    <div class="col-sm-6 pr-0">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <div class="d-flex flex-column flex-fill px-3">
            @yield('content')
            <input id="medium_id" class="invisible"> <!-- DONT REMOVE - used by TINYMCE -->
        </div>
        <!-- Footer -->
        @include('partials.footer', ['contentonly' => true])
    </div>

    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ asset('node_modules/mathjax/es5/tex-svg.js') }}"></script>
    <script src="{{ asset('node_modules/moment/js/moment.min.js') }}"></script>
    <script src="{{ asset('node_modules/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    @yield('scripts')

</body>

</html>
