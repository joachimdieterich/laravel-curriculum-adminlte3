<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

@include('layouts.partials.head')

    <body>
        <!-- Site wrapper -->
        <div
            id="app"
            class="d-flex flex-column min-vh-100"
        >
            <!-- Header -->
            <nav class="main-header navbar navbar-expand navbar-lime navbar-light user-select-none" style="z-index: 1039;">
                <!-- Brand Logo/menu -->
                @include('partials.topmenu')
                <div class="d-flex align-items-center position-relative">
                    <a
                        href="#content"
                        class="skip bg-lime font-weight-bold position-absolute"
                        style="white-space: nowrap; z-index: 1040;"
                    >
                        {{ trans('global.skip_navigation') }}
                    </a>
                </div>
                <ul class="navbar-nav pl-2 mr-auto">
                    <li class="nav-item mr-2">
                        <button
                            type="button"
                            class="btn btn-icon"
                            data-toggle="collapse"
                            data-target=".nav-collapse"
                            aria-expanded="false"
                            aria-controls="navigationbar background-mask"
                        >
                            <i class="fa fa-bars px-1"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="/"
                        >
                            <b>{{ trans('global.home') }}</b>
                        </a>
                    </li>
                    <searchbar></searchbar>
                </ul>
                @include('partials.navbar')
            </nav>
            
            <!-- Content Wrapper. Contains page content -->
            <div
                id="content"
                class="d-flex flex-fill position-relative bg-gray-light"
            >
                @can('is_admin')
                    <navigationbar></navigationbar>
                    <div
                        id="background-mask"
                        class="nav-collapse"
                        data-toggle="collapse"
                        data-target=".nav-collapse"
                        aria-expanded="false"
                        aria-controls="navigationbar background-mask"
                    ></div>
                @endcan
                <div class="flex-fill">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-6 pl-0">
                                    <h1> @yield('title')</h1>
                                </div>
                                <div class="d-flex align-items-center col-sm-6 px-0">
                                    @yield('breadcrumb')
                                </div>
                            </div>
                            @yield('contributors')
                        </div><!-- /.container-fluid -->
                    </section>
    
                    <!-- Main content -->
                    <section class="content d-flex flex-column flex-fill">
                        @yield('content')
                        <input id="medium_id" class="d-none"> <!-- DONT REMOVE - used by TINYMCE -->
                    </section>
                    <!-- /.content -->
                </div>
            </div>

            <!-- Footer -->
            @include('partials.footer')
            <!-- Logout Form -->
            <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>
        <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
        <script src="{{ asset('node_modules/mathjax/es5/tex-svg.js') }}"></script>

        <script>
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
        </script>
        @yield('scripts')
    </body>
</html>
