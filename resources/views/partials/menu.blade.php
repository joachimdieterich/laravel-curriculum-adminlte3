<aside class="main-sidebar sidebar-light-primary">    
    <!-- Brand Logo/menu -->
    @if(  env('BRAND_MENU_TITLE_1') )
    <div id="topnav" class="bg-lime">
        <button 
            type="button" 
            data-toggle="dropdown" 
            class="btn dropdown-menu-lime p-0 m-0" >
            <div id="mainpage-0" 
                 class="logo">
                <svg  class="p-1" version="1.0" xmlns="http://www.w3.org/2000/svg"
                    height="32.000000pt" viewBox="0 0 400.000000 460.000000"
                    preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,460.000000) scale(0.100000,-0.100000)"
                       fill="#fff" stroke="none">
                        <path d="M2231 4531 c-8 -5 -48 -74 -90 -153 l-76 -143 -60 -13 c-153 -32
                              -325 -76 -380 -96 l-60 -21 -135 87 c-156 102 -173 105 -259 57 -201 -114
                              -482 -343 -498 -407 -3 -14 11 -78 36 -161 23 -75 43 -146 43 -157 1 -11 -28
                              -65 -64 -119 -36 -55 -94 -152 -128 -215 -34 -63 -65 -119 -70 -123 -4 -5 -70
                              -22 -146 -38 -177 -37 -189 -42 -207 -87 -23 -59 -55 -207 -71 -332 -19 -145
                              -30 -352 -19 -366 4 -6 71 -43 148 -83 77 -40 145 -78 151 -84 6 -7 22 -73 37
                              -147 14 -74 41 -182 60 -240 18 -59 30 -110 25 -115 -5 -6 -47 -68 -93 -140
                              -61 -93 -85 -139 -85 -161 0 -65 203 -365 350 -516 69 -72 78 -78 115 -78 22
                              0 90 15 150 34 177 54 158 55 262 -13 50 -34 140 -87 200 -120 157 -86 149
                              -76 178 -220 40 -196 40 -197 83 -214 52 -21 250 -64 351 -76 119 -15 354 -24
                              367 -14 7 4 46 71 88 148 42 77 83 145 92 151 8 6 38 15 67 18 66 9 228 49
                              334 83 l82 27 138 -92 c76 -51 149 -92 162 -92 77 0 530 330 576 421 23 43 18
                              91 -21 221 -19 66 -36 127 -36 136 -1 9 24 55 55 101 55 82 69 126 49 157 -9
                              15 -406 254 -421 254 -6 0 -50 23 -98 50 -104 60 -134 63 -171 18 -57 -67
                              -256 -255 -315 -296 -409 -287 -958 -268 -1349 47 -267 216 -405 461 -439 786
                              -37 344 94 685 360 938 346 329 831 410 1270 210 71 -32 123 -41 147 -25 7 4
                              59 84 116 177 247 402 264 435 240 471 -4 6 -55 37 -114 68 -59 32 -109 65
                              -112 74 -3 9 -19 78 -34 152 -16 75 -34 146 -41 157 -20 39 -182 79 -436 108
                              -145 17 -283 19 -304 6z"/>
                    </g>
                </svg>
                <span class="brand-txt pl-3">{{ env('APP_NAME') }} <i class="fa fa-chevron-down"></i></span>

            </div>
        </button> 
        <div class="dropdown-menu bg-lime dropdown-menu-lime elevation-2">
        @php ($brand_iterator = 1)
        @while ( env('BRAND_MENU_TITLE_'.$brand_iterator) )
            <a href="{{ env('BRAND_MENU_HREF_'.$brand_iterator) }}" class=" dropdown-item">
                <i class="brand-dropdown_icon {{ env('BRAND_MENU_ICON_'.$brand_iterator) }} text-white"></i> 
                <span  class="font-weight-light pl-1">{{ env('BRAND_MENU_TITLE_'.$brand_iterator) }}</span>
            </a> 
        @php ($brand_iterator++)
        @endwhile

        </div>
    </div>
    @else
        <a href="{{ route("home") }}" class="brand-link p-1">
            <svg class="ml-2 pl-1" version="1.0" xmlns="http://www.w3.org/2000/svg"
                 width="29.000000pt" viewBox="0 0 400.000000 460.000000"
                 preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,460.000000) scale(0.100000,-0.100000)"
                   fill="#fff" stroke="none">
                    <path d="M2231 4531 c-8 -5 -48 -74 -90 -153 l-76 -143 -60 -13 c-153 -32
                          -325 -76 -380 -96 l-60 -21 -135 87 c-156 102 -173 105 -259 57 -201 -114
                          -482 -343 -498 -407 -3 -14 11 -78 36 -161 23 -75 43 -146 43 -157 1 -11 -28
                          -65 -64 -119 -36 -55 -94 -152 -128 -215 -34 -63 -65 -119 -70 -123 -4 -5 -70
                          -22 -146 -38 -177 -37 -189 -42 -207 -87 -23 -59 -55 -207 -71 -332 -19 -145
                          -30 -352 -19 -366 4 -6 71 -43 148 -83 77 -40 145 -78 151 -84 6 -7 22 -73 37
                          -147 14 -74 41 -182 60 -240 18 -59 30 -110 25 -115 -5 -6 -47 -68 -93 -140
                          -61 -93 -85 -139 -85 -161 0 -65 203 -365 350 -516 69 -72 78 -78 115 -78 22
                          0 90 15 150 34 177 54 158 55 262 -13 50 -34 140 -87 200 -120 157 -86 149
                          -76 178 -220 40 -196 40 -197 83 -214 52 -21 250 -64 351 -76 119 -15 354 -24
                          367 -14 7 4 46 71 88 148 42 77 83 145 92 151 8 6 38 15 67 18 66 9 228 49
                          334 83 l82 27 138 -92 c76 -51 149 -92 162 -92 77 0 530 330 576 421 23 43 18
                          91 -21 221 -19 66 -36 127 -36 136 -1 9 24 55 55 101 55 82 69 126 49 157 -9
                          15 -406 254 -421 254 -6 0 -50 23 -98 50 -104 60 -134 63 -171 18 -57 -67
                          -256 -255 -315 -296 -409 -287 -958 -268 -1349 47 -267 216 -405 461 -439 786
                          -37 344 94 685 360 938 346 329 831 410 1270 210 71 -32 123 -41 147 -25 7 4
                          59 84 116 177 247 402 264 435 240 471 -4 6 -55 37 -114 68 -59 32 -109 65
                          -112 74 -3 9 -19 78 -34 152 -16 75 -34 146 -41 157 -20 39 -182 79 -436 108
                          -145 17 -283 19 -304 6z"/>
                </g>
            </svg>

            <span class="pl-1 brand-text ">{{ env('APP_NAME') }}</span>
       </a>
    @endif
    <div id="menu_top_placeholder" class="clearfix"></div>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar user (optional) -->
        
        
        <!-- Sidebar Menu -->
        <span class="clearfix"></span>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar " data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item" style="width:100%">
                    @include ('forms.input.select', 
                        ["model" => "group", 
                        "field" => "current_curriculum_group_id",  
                        "options"=> auth()->user()->curricula(), 
                        "option_id" => "course_id",
                        "onchange"=> "location = '/courses/'+this.value", 
                        "optgroup" => auth()->user()->currentGroupEnrolments()->get(),    
                        "optgroup_id" => "id",
                        "optgroup_reference_field" => "group_id",
                        "placeholder" => trans('global.course.title').'...',
                        "value" =>  old('course_id', isset($course->id) ? $course->id : '')])
                </li>
<!--                <li class="nav-header pt-0">{{ strtoupper(trans('global.organization.title_singular')) }}</li>-->
                @if (auth()->user()->organizations->count() > 1)
                    <li class="nav-item" style="width:100%">
                         @include ('forms.input.select', 
                            ["model" => "organization", 
                            "field" => "current_organization_id",  
                            "options"=> auth()->user()->organizations, 
                            "option_label" => "title",  
                            "onchange"=> "setCurrentOrganization()",  
                            "value" =>  old('current_organization_id', isset(auth()->user()->current_organization_id) ? auth()->user()->current_organization_id : '')])
                    </li>
                @else
                <li class="nav-item px-3 py-2 text-bold" style="width:100%">
                   {{ auth()->user()->organizations->first()->title }}
                </li>
                @endif 
            </ul>
        </nav>
        <nav class="mt-2"> 
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @can('curriculum_access')
                    <li class="nav-item">
                        <a href="{{ route("curricula.index") }}" class="nav-link {{ request()->is('curricula') || request()->is('curricula/*') ? 'active' : '' }}">
                            <i class="fas fa-th"></i>
                            <p>
                                <span>{{ trans('global.curriculum.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('logbook_access')
                    <li class="nav-item">
                        <a href="{{ route("logbooks.index") }}" class="nav-link {{ request()->is('logbooks') || request()->is('logbooks/*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <p>
                                <span>{{ trans('global.logbook.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('task_access')
                    <li class="nav-item">
                        <a href="{{ route("tasks.index") }}" class="nav-link {{ request()->is('tasks') || request()->is('tasks/*') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <p>
                                <span>{{ trans('global.task.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('user_access')
                    <li class="nav-item has-treeview menu-open {{ request()->is('permissions*') ? 'menu-open' : '' }} {{ request()->is('roles*') ? 'menu-open' : '' }} {{ request()->is('users*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle">
                            <i class="fas fa-users"></i>
                            <p>
                                <span>{{ trans('global.user_management') }}</span>
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("permissions.index") }}" class="nav-link {{ request()->is('permissions') || request()->is('permissions/*') ? 'active' : '' }}">
                                        <i class="fas fa-unlock-alt"></i>
                                        <p>
                                            <span>{{ trans('global.permission.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("roles.index") }}" class="nav-link {{ request()->is('roles') || request()->is('roles/*') ? 'active' : '' }}">
                                        <i class="fas fa-user-tag"></i>
                                        <p>
                                            <span>{{ trans('global.role.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("users.index") }}" class="nav-link {{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
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
                
                @can('organization_access')
                    <li class="nav-item has-treeview menu-open {{ request()->is('permissions*') ? 'menu-open' : '' }} {{ request()->is('roles*') ? 'menu-open' : '' }} {{ request()->is('users*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle">
                            <i class="fas fa-university"></i>
                            <p>
                                <span>{{ trans('global.organization_management') }}</span>
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            
                            @can('navigator_access')
                                <li class="nav-item">
                                    <a href="{{ route("navigators.index") }}" class="nav-link {{ request()->is('navigators') || request()->is('navigators/*') ? 'active' : '' }}">
                                        <i class="fa fa-map-signs"></i>
                                        <p>
                                            <span>{{ trans('global.navigator.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('group_access')
                                <li class="nav-item">
                                    <a href="{{ route("groups.index") }}" class="nav-link {{ request()->is('groups') || request()->is('groups/*') ? 'active' : '' }}">
                                        <i class="fa fa-users"></i>
                                        <p>
                                            <span>{{ trans('global.group.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('grade_access')
                                <li class="nav-item">
                                    <a href="{{ route("grades.index") }}" class="nav-link {{ request()->is('grades') || request()->is('grades/*') ? 'active' : '' }}">
                                        <i class="fas fa-layer-group"></i>
                                        <p>
                                            <span>{{ trans('global.grade.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('period_access')
                                <li class="nav-item">
                                    <a href="{{ route("periods.index") }}" class="nav-link {{ request()->is('periods') || request()->is('periods/*') ? 'active' : '' }}">
                                        <i class="fa fa-history"></i>
                                        <p>
                                            <span>{{ trans('global.period.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('organization_type_access')
                                <li class="nav-item">
                                    <a href="{{ route("organizationtypes.index") }}" class="nav-link {{ request()->is('organizationtypes') || request()->is('organizationtypes/*') ? 'active' : '' }}">
                                        <i class="fas fa-city"></i>
                                        <p>
                                            <span>{{ trans('global.organizationtype.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route("organizations.index") }}" class="nav-link {{ request()->is('organizations') || request()->is('organizations/*') ? 'active' : '' }}">
                                    <i class="fas fa-university"></i>
                                    <p>
                                        <span>{{ trans('global.organization.title') }}</span>
                                    </p>
                                </a>
                            </li>
                            
                        </ul>
                    </li>
                @endcan
                @if(auth()->user()->role()->id == 1)
                <li class="nav-item">
                    <a href="{{ route("configs.index") }}" class="nav-link {{ request()->is('organizations') || request()->is('organizations/*') ? 'active' : '' }}">
                        <i class="fa fa-cogs"></i>
                        <p>
                            <span>{{ trans('global.config.title') }}</span>
                        </p>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
