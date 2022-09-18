<aside class="main-sidebar sidebar-light-primary">
    <!-- Brand Logo/menu -->
    @include('partials.topmenu')
    <div id="menu_top_placeholder" class="clearfix" style="overflow:auto; max-height:500px;"></div>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Menu -->
        <span class="clearfix"></span>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar"
                role="menu">
                <li class="nav-item"
                    style="width:100%"
                    role="menuitem">
                    @include ('forms.input.select',
                        ["model" => "group",
                        "field" => "current_curriculum_group_id",
                        "options"=> auth()->user()->currentCurriculaEnrolments(),
                        "option_id" => "course_id",
                        "onchange"=> "location = '/courses/'+this.value",
                        "optgroup" => auth()->user()->currentGroupEnrolments()->get(),
                        "optgroup_id" => "id",
                        "optgroup_icon" => "fa fa-users",
                        "optgroup_class" => "small",
                        "optgroup_reference_field" => "group_id",
                        "placeholder" => trans('global.course.title').'...',
                        "allowClear" => false,
                        "aria_label" => trans('global.course.title'),
                        "value" =>  old('course_id', isset($course->id) ? $course->id : '')
                        ])

                </li>
                <li class="nav-item"
                    style="width:100%"
                    role="menuitem">
                    @if (auth()->user()->organizations->count() > 1)
                        @include ('forms.input.select',
                             ["model" => "organization",
                             "field" => "current_organization_id",
                             "options"=> auth()->user()->organizations,
                             "option_id" => "id",
                             "option_icon" => "fa fa-university",
                             "placeholder" => trans('global.organization.title').'...',
                             "onchange"=> "setCurrentOrganization(this)",
                             "allowClear" => false,
                             "value" => auth()->user()->current_organization_id
                             ])
                    @else
                        <span class="nav-item px-3 py-2 text-bold" style="width:100%">
                        {{ auth()->user()->organizations->first()->title }}
                    </span>
                    @endif
                    @if ((auth()->user()->currentPeriods()->count() > 1) OR (auth()->user()->currentGroupEnrolments()->count() == 0))
                        @include ('forms.input.select',
                           ["model" => "period",
                           "field" => "current_period_id",
                           "options"=> auth()->user()->currentPeriods(),
                           "option_id" => "id",
                           "placeholder" => trans('global.period.title').'...',
                           "onchange"=> "setCurrentOrganizationAndPeriod(this)",
                           "allowClear" => false,
                           "value" => auth()->user()->current_period_id
                           ])
                    @endif
                </li>
            </ul>
        </nav>
        <nav class="mt-0">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">
                @can('curriculum_access')
                    <li class="nav-item"
                        role="menuitem">
                        <a href="{{ route("curricula.index") }}"
                           class="nav-link {{ request()->is('curricula') || request()->is('curricula/*') ? 'active' : '' }}">
                            <i class="fas fa-th"></i>
                            <p>
                                <span>{{ trans('global.curriculum.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('logbook_access')
                    <li class="nav-item"
                        role="menuitem">
                        <a href="{{ route("logbooks.index") }}"
                           class="nav-link {{ request()->is('logbooks') || request()->is('logbooks/*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <p>
                                <span>{{ trans('global.logbook.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('plan_access')
                    <li class="nav-item"
                        role="menuitem">
                        <a href="{{ route("plans.index") }}"
                           class="nav-link {{ request()->is('plans') || request()->is('plans/*') ? 'active' : '' }}">
                            <i class="fa fa-clipboard-list"></i>
                            <p>
                                <span>{{ trans('global.plan.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('kanban_access')
                    <li class="nav-item"
                        role="menuitem">
                        <a href="{{ route("kanbans.index") }}"
                           class="nav-link {{ request()->is('kanbans') || request()->is('kanbans/*') ? 'active' : '' }}">
                            <i class="fa fa-columns"></i>
                            <p>
                                <span>{{ trans('global.kanban.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('task_access')
                    <li class="nav-item"
                        role="menuitem">
                        <a href="{{ route("tasks.index") }}"
                           class="nav-link {{ request()->is('tasks') || request()->is('tasks/*') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <p>
                                <span>{{ trans('global.task.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan

{{--                @can('assignment_show')--}}
                    <li class="nav-item"
                        role="menuitem">
                            <a href="{{ route("exams.index") }}"
                               class="nav-link {{ request()->is('exams') || request()->is('exams/*') ? 'active' : '' }}">
                            <i class="fa-solid fa-ranking-star"></i>
                            <p>
                                <span>{{ trans('global.exam.title') }}</span>
                            </p>
                        </a>
                    </li>
{{--                @endcan--}}

                @can('user_access')
                    <li class="nav-header"
                        role="menuitem">
                        {{ trans('global.user_management') }}
                    </li>
                    @can('permission_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("permissions.index") }}"
                               class="nav-link {{ request()->is('permissions') || request()->is('permissions/*') ? 'active' : '' }}">
                                <i class="fas fa-unlock-alt"></i>
                                <p>
                                    <span>{{ trans('global.permission.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("roles.index") }}"
                               class="nav-link {{ request()->is('roles') || request()->is('roles/*') ? 'active' : '' }}">
                                <i class="fas fa-user-tag"></i>
                                <p>
                                    <span>{{ trans('global.role.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("users.index") }}"
                               class="nav-link {{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                                <i class="fas fa-user"></i>
                                <p>
                                    <span>{{ trans('global.user.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                @endcan


                @can('organization_access')
                    <li class="nav-header"
                        role="menuitem">
                        {{ trans('global.organization_management') }}
                    </li>

                    @can('navigator_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("navigators.index") }}"
                               class="nav-link {{ request()->is('navigators') || request()->is('navigators/*') ? 'active' : '' }}">
                                <i class="fa fa-map-signs"></i>
                                <p>
                                    <span>{{ trans('global.navigator.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('group_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("groups.index") }}"
                               class="nav-link {{ request()->is('groups') || request()->is('groups/*') ? 'active' : '' }}">
                                <i class="fa fa-users"></i>
                                <p>
                                    <span>{{ trans('global.group.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('grade_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("grades.index") }}"
                               class="nav-link {{ request()->is('grades') || request()->is('grades/*') ? 'active' : '' }}">
                                <i class="fas fa-layer-group"></i>
                                <p>
                                    <span>{{ trans('global.grade.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('period_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("periods.index") }}"
                               class="nav-link {{ request()->is('periods') || request()->is('periods/*') ? 'active' : '' }}">
                                <i class="fa fa-history"></i>
                                <p>
                                    <span>{{ trans('global.period.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('organization_type_access')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("organizationtypes.index") }}"
                               class="nav-link {{ request()->is('organizationtypes') || request()->is('organizationtypes/*') ? 'active' : '' }}">
                                <i class="fas fa-city"
                                   aria-hidden="true"></i>
                                <p>
                                    <span>{{ trans('global.organizationtype.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('certificate_edit')
                        <li class="nav-item"
                            role="menuitem">
                            <a href="{{ route("certificates.index") }}"
                               class="nav-link {{ request()->is('certificates') || request()->is('certificates/*') ? 'active' : '' }}">
                                <i class="fa fa-certificate"
                                   aria-hidden="true"></i>
                                <p>
                                    <span>{{ trans('global.certificate.title') }}</span>
                                </p>
                            </a>
                        </li>
                    @endcan
                    <li class="nav-item"
                        role="menuitem">
                        <a href="{{ route("organizations.index") }}"
                           class="nav-link {{ request()->is('organizations') || request()->is('organizations/*') ? 'active' : '' }}">
                            <i class="fas fa-university"
                               aria-hidden="true"></i>
                            <p>
                                <span>{{ trans('global.organization.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
