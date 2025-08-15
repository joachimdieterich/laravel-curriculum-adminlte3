<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

@include('layouts.partials.head')

    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
        <!-- Site wrapper -->
        <div id="app" class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-lime navbar-light user-select-none">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link"
                        data-widget="pushmenu"
                        href="#"
                        onclick="toggleMenu()">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav pl-2 mr-auto">
                    <li class="nav-item">
                        <a class="nav-link"
                        href="/">
                            <b>Startseite</b>
                        </a>
                    </li>
                    <searchbar></searchbar>
                </ul>
                @include('partials.navbar')
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            @include('partials.menu')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper d-flex flex-column">
                <!-- Content Header (Page header) -->
                <section class="content-header">
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

                <!-- Main content -->
                <section class="content d-flex flex-column flex-fill">
                    @yield('content')
                    <input id="medium_id" class="d-none"> <!-- DONT REMOVE - used by TINYMCE -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <!-- Footer -->
            @include('partials.footer')
            <!-- Logout Form -->
            <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-light">
                <!-- Control sidebar content goes here -->
                @include('partials.sidebar')
            </aside>
            <!-- /.control-sidebar -->
        </div>

        <!-- ./wrapper -->
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
                        searchPlaceholder: "{{ trans('global.search') }}",
                        paginate: 
                        {
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
                        }
                    ],
                    select: {
                        style:  'multi+shift',
                        selector: 'td:not(:last-child)'
                    },
                    order: [[ 1, 'asc' ]],
                    scrollX: true,
                    pageLength: 25,
                    pagingType: "full_numbers",
                    dom: '<"top"f>rt<"bottom"pl>i',
                    buttons: []
                });
                $.fn.dataTable.ext.classes.sPageButton = '';
            });

            function setCurrentOrganization(selectBox) {
                var op = selectBox.options[selectBox.selectedIndex];
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    method: 'POST',
                    url: "{{ route('users.setCurrentOrganization') }}",
                    data: {
                        current_organization_id: op.value,
                        _method: 'PATCH',
                    }
                })
                .done(function () { location.reload() })
            }

            function setCurrentOrganizationAndPeriod(selectBox) {
                var op = selectBox.options[selectBox.selectedIndex];
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    method: 'POST',
                    url: "{{ route('users.setCurrentPeriod') }}",
                    data: {
                        current_period_id: op.value,
                        _method: 'PATCH',
                    }
                })
                .done(function () { location.reload() })
            }

            function destroyDataTableEntry(route, id) {
                window.event.stopPropagation(); // stops redirection to source

                if (confirm("{{ trans('global.areYouSure') }}")) {
                    $.ajax({
                        headers: { 'x-csrf-token': _token },
                        method: 'POST',
                        url: '/'+route+'/'+id,
                        data: { _method: 'DELETE' }
                    })
                    .done(function () {
                        $("#"+id).hide();
                    });
                }
            }

            function toggleMenu() {
                if (localStorage.getItem('menu_toggle_class') == 'sidebar-collapse') {
                    localStorage.setItem('menu_toggle_class', '');
                } else {
                    localStorage.setItem('menu_toggle_class', 'sidebar-collapse');
                }
            }

            if (localStorage.getItem('menu_toggle_class') === 'sidebar-collapse') {
                $("body").addClass('sidebar-collapse');
            }
        </script>

        @yield('scripts')
    </body>
</html>
