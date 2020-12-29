<div class="control-sidebar-content" style="height: 262px;">
    @yield('sidebar')
    <sidebar
        ref="messageSidebar"
    :user="{{ auth()->user() }}"></sidebar>
</div>
