<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
     <a href="{{ route("home") }}" class="brand-link">
        <span class="brand-text font-weight-light">curriculum</span>
    </a>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2"> 
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">{{ strtoupper(trans('global.curriculum.title')) }}</li>
                <li class="nav-item">
                    @include ('forms.input.select', 
                        ["model" => "organization", 
                        "field" => "current_curriculum_group_id",  
                        "options"=> auth()->user()->curricula(), 
                        "option_label" => "title",  
                        "onchange"=> "loadCurriculum()",  
                        "value" =>  ''])
                </li>
                <li class="nav-header">{{ strtoupper(trans('global.organization.title_singular')) }}</li>
                <li class="nav-item">
                     @include ('forms.input.select', 
                        ["model" => "organization", 
                        "field" => "current_organization_id",  
                        "options"=> auth()->user()->organizations, 
                        "option_label" => "title",  
                        "onchange"=> "setCurrentOrganization()",  
                        "value" =>  old('current_organization_id', isset(auth()->user()->current_organization_id) ? auth()->user()->current_organization_id : '')])
                </li>
                
               
                @can('curriculum_manage')
                    <li class="nav-item">
                        <a href="{{ route("curricula.index") }}" class="nav-link {{ request()->is('curricula') || request()->is('curricula/*') ? 'active' : '' }}">
                            <i class="fas fa-th"></i>
                            <p>
                                <span>{{ trans('global.curriculum.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is('permissions*') ? 'menu-open' : '' }} {{ request()->is('admin/roles*') ? 'menu-open' : '' }} {{ request()->is('admin/users*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle">
                            <i class="fas fa-users"></i>
                            <p>
                                <span>{{ trans('global.userManagement.title') }}</span>
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_manage')
                                <li class="nav-item">
                                    <a href="{{ route("permissions.index") }}" class="nav-link {{ request()->is('permissions') || request()->is('permissions/*') ? 'active' : '' }}">
                                        <i class="fas fa-unlock-alt"></i>
                                        <p>
                                            <span>{{ trans('global.permission.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_manage')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                        <i class="fas fa-briefcase"></i>
                                        <p>
                                            <span>{{ trans('global.role.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_manage')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                        <i class="fas fa-user"></i>
                                        <p>
                                            <span>{{ trans('global.user.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('organization_manage')
                    <li class="nav-item">
                        <a href="{{ route("admin.organizations.index") }}" class="nav-link {{ request()->is('admin/organizations') || request()->is('admin/organizations/*') ? 'active' : '' }}">
                            <i class="fas fa-university"></i>
                            <p>
                                <span>{{ trans('global.organization.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('group_manage')
                    <li class="nav-item">
                        <a href="{{ route("admin.groups.index") }}" class="nav-link {{ request()->is('admin/groups') || request()->is('admin/groups/*') ? 'active' : '' }}">
                            <i class="fa fa-users"></i>
                            <p>
                                <span>{{ trans('global.group.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('grade_manage')
                    <li class="nav-item">
                        <a href="{{ route("admin.grades.index") }}" class="nav-link {{ request()->is('admin/grades') || request()->is('admin/grades/*') ? 'active' : '' }}">
                            <i class="fas fa-signal"></i>
                            <p>
                                <span>{{ trans('global.grade.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('organization_type_manage')
                    <li class="nav-item">
                        <a href="{{ route("admin.organizationtypes.index") }}" class="nav-link {{ request()->is('admin/organizationtypes') || request()->is('admin/organizationtypes/*') ? 'active' : '' }}">
                            <i class="fas fa-signal"></i>
                            <p>
                                <span>{{ trans('global.organizationtype.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-sign-out-alt"></i>
                            <span>{{ trans('global.logout') }}</span>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

@section('scripts')
@parent
<!--hack to get select2 working-->
<script>
    $(document).ready(function() {
  $("#current_organization_id").select2({
    dropdownParent: $("#sidebar")
  });
  $("#current_curriculum_group_id").select2({
    dropdownParent: $("#sidebar")
  });
});

 
</script>
@endsection