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
               {{-- <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu"
                       onclick="toggleMenu()"><i class="fas fa-bars"></i></a>
                </li>--}}
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
            </div><!-- /.container-fluid -->
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
    <script src="{{ asset('node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('node_modules/moment/js/moment.min.js') }}"></script>
    <script src="{{ asset('node_modules/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(function () {
            let languages = {
                'de': '{{ asset("datatables/i18n/German.json") }}',
                'en': '{{ asset("datatables/i18n/English.json") }}',
                /* 'fr': '{{ asset("datatables/i18n/French.json") }}',*/
            };
            $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {className: 'btn-primary py-2 ml-1'})

            $.extend(true, $.fn.dataTable.defaults, {
                processing: true,
                serverSide: true,
                language: {
                    url: languages.{{ app() -> getLocale() }},
                    searchPlaceholder: '{{ trans('global.search') }}',
                    paginate: {
                        "first":      '<i class="fa fa-angle-double-left"></id>',
                        "last":       '<i class="fa fa-angle-double-right"></id>',
                        "next":       '<i class="fa fa-angle-right"></id>',
                        "previous":   '<i class="fa fa-angle-left"></id>',
                    },
                },
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets:   0
                    },
                    {
                        orderable: false,
                        searchable: false,
                        targets: - 1
                    }],
                select: {
                    style:  'multi+shift',
                    selector: 'td:not(:last-child)'
                },
                order: [[ 1, 'asc' ]],
                scrollX: true,
                pageLength: 50,
                pagingType: "full_numbers",
                dom: '<"top"f>rt<"bottom"pl>i',
                buttons: []
            });
            $.fn.dataTable.ext.classes.sPageButton = '';
        });
    </script>
    @yield('scripts')

</body>

</html>
