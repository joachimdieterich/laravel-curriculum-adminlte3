<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('global.site_title') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @yield('styles')

    <script>
        window.trans = <?php
        // copy all translations from /resources/lang/CURRENT_LOCALE/* to global JS variable
        $lang_files = File::files(resource_path() . '/lang/' . App::getLocale());
        $trans = [];
        foreach ($lang_files as $f) {
            $filename = pathinfo($f)['filename'];
            $trans[$filename] = trans($filename);
        }
        echo json_encode($trans);
        ?>;
        window.Laravel = <?php
            echo json_encode([
                'csrfToken' => csrf_token(),
                'userId' => Auth::user()->id,
                'permissions' => Auth::user()->permissions()->pluck('title')->toArray()
            ]); ?>;
    </script>
</head>

<body class="" >
    <div id="app">
        @include('partials.topmenu')
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
        <div class="pl-2 pr-2">
            @yield('content')
        </div>
        <!-- Footer -->
        @include('partials.footer', ['contentonly' => true])

    </div>

    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
    @yield('scripts')

</body>

</html>
