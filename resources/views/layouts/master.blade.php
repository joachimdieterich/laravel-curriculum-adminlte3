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
                        <navigationbar
                            :organizations="{{ json_encode(auth()->user()->organizations()->select('organizations.id', 'title')->get()) }}"
                        ></navigationbar>
                        <div
                            id="background-mask"
                            class="nav-collapse w-100"
                            data-toggle="collapse"
                            data-target=".nav-collapse"
                            aria-expanded="false"
                            aria-controls="navigationbar background-mask"
                        ></div>
                    </div>
                @endcan
                <div class="d-flex flex-column flex-fill">
                    <!-- Content Header (Page header) -->
                    <section class="p-3">
                        <div class="d-flex">
                            <div class="col-12 col-sm-6 px-0">
                                <h1 class="h3 m-0">@yield('title')</h1>
                            </div>
                            <div class="d-none d-sm-flex align-items-center col-sm-6 px-0">
                                @yield('breadcrumb')
                            </div>
                        </div>
                        @yield('contributors')
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
        @yield('scripts')
    </body>
</html>