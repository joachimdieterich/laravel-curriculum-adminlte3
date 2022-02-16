<ul class="navbar-nav ml-auto">
       <!--Messages Dropdown Menu-->
<!--      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
             Message Start
            <div class="media">
              <img src="/media/1" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
             Message End
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
             Message Start
            <div class="media">
              <img src="/media/1" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
             Message End
          </a>

          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>-->
    @can('message_access')
        <li class="nav-item pr-4">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"
               onclick="triggerVueMessageLoaderEvent()"
               aria-label="{{ trans('global.messages') }}">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge"
                      style="right:0px">{{--{{ auth()->user()->newThreadsCount() }}--}}</span>
            </a>
        </li>
@endcan
<!--Notifications Dropdown Menu-->
    <li class="dropdown">
        <span class="user-menu dropdown-toggle" data-toggle="dropdown">
          <img class="img-circle color-white"
               src="{{ (auth()->user()->medium_id !== null) ? '/media/'.auth()->user()->medium_id  : Avatar::create(auth()->user()->fullName())->toBase64() }}"
               alt="User profile picture"
               style="height: 40px;width: 40px;">
          <b>{{ auth()->user()->fullName() }}</b>
        </span>
        <div class="user-menu dropdown-menu bg-lime dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header text-black">{{ optional(Auth::user()->role())->title}}</span>
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

    </ul>
@section('scripts')
@parent
<script>
function triggerVueMessageLoaderEvent(){
        app.__vue__.$refs.messageSidebar.externalLoadMessagesEvent()
}
</script>
@endsection
