<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

@include('layouts.partials.head')

    <body>
        <!-- Site wrapper -->
        <div
            id="app"
            class="d-flex flex-column min-vh-100"
        >
            <main-header
                :menu="{{ json_encode(config('app.brand_menu')) }}"
                env="{{ config('app.env') }}"
                :user="{{ json_encode(auth()->user()) }}"
                :role="{{ json_encode(auth()->user()->role()->only('id', 'title')) }}"
                :guest-id="{{ config('app.guest_user_id') }}"
            ></main-header>
            
            <!-- Content Wrapper. Contains page content -->
            <div
                id="content"
                class="d-flex flex-fill position-relative bg-gray-light"
            >
                @can('is_admin')
                    <div class="d-print-none">
                        <navigationbar></navigationbar>
                        <div
                            id="background-mask"
                            class="nav-collapse"
                            data-toggle="collapse"
                            data-target=".nav-collapse"
                            aria-expanded="false"
                            aria-controls="navigationbar background-mask"
                        ></div>
                    </div>
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
        </script>
        @yield('scripts')
    </body>
</html>