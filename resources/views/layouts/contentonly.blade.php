<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

@include('layouts.partials.head')

<body class="" >
    <div id="app">

    <!-- Content Header (Page header) -->
        <section class="content-header p-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-8 pl-0">
                        <h1> @yield('title')</h1>
                    </div>
                    <div class="col-sm-4 pr-0">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <div class="pl-2 pr-2" style="padding-bottom:50px">
            @yield('content')
        </div>
            <!-- Footer -->
            @include('partials.footer', ['contentonly' => true])

    </div>

    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>

    @yield('scripts')

</body>

</html>
