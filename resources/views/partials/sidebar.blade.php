<div class="control-sidebar-content" style="height: 262px;">
    @yield('sidebar')
    <sidebar
    :user="{{ auth()->user() }}"></sidebar>
</div>
