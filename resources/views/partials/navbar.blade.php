<ul class="navbar-nav ml-auto">
    @if (auth()->user()->id == config('app.guest_user_id'))
        <div class="user-menu">
            <button
                class="dropdown-item btn d-flex align-items-center t-18"
                onclick="document.getElementById('logoutform').submit();"
            >
                <i class="fa fa-right-to-bracket"></i>
                <span class="font-weight-bold py-1 ml-2">{{ trans('global.login') }}</span>
            </button>
        </div>
    @else
        <li class="dropdown mr-2">
            <span class="user-menu dropdown-toggle" data-toggle="dropdown" role="button">
                <img
                    src="{{ (auth()->user()->medium_id !== null) ? '/media/'.auth()->user()->medium_id  : Avatar::create(auth()->user()->fullName())->toBase64() }}"
                    alt="User profile picture"
                    class="img-circle color-white"
                    style="height: 40px; width: 40px;"
                />
                <b class="ml-1">{{ auth()->user()->fullName() }}</b>
            </span>
            <div class="user-menu dropdown-menu bg-lime dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header text-black">{{ optional(Auth::user()->role())->title }}</span>
                <div class="dropdown-divider"></div>
                @can('user_show')
                    <a href="{{route('users.show', auth()->user()->id)}}" class="dropdown-item">
                        <i class="fas fa-id-card mr-2 fa-fw text-white"></i>{{ trans('global.myProfile') }}
                    </a>
                @endcan
                @can('note_access')
                    <a href="{{ route("notes.index") }}" class="dropdown-item">
                        <i class="fa fa-sticky-note fa-fw mr-2 text-white"></i>{{ trans('global.note.title') }}
                    </a>
                @endcan
                @if(auth()->user()->role()->id == 1)
                    <a href="{{ route("admin.index") }}" class="dropdown-item">
                        <i class="fa fa-cogs fa-fw mr-2 text-white"></i>{{ trans('global.config.title') }}
                    </a>
                @endif
                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="fas fa-power-off fa-fw mr-2 text-white"></i>{{ trans('global.logout') }}
                </a>
            </div>
        </li>
    @endif
</ul>