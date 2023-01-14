@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ $curriculum->title }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/"
               :aria-label="trans('global.home')"><i class="fa fa-home"></i></a>
        @endif
    </li>
    @if(isset($course))
        @can('achievement_access')
            <li class="breadcrumb-item "><a
                    href="/curricula/{{ $course->curriculum_id }}">{{ Str::limit($curriculum->title, 10) }}</a></li>
        @endcan
    @else
        <li class="breadcrumb-item "><a
                href="/curricula/{{$curriculum->id}}">{{ Str::limit($curriculum->title, 10) }}</a></li>
    @endif
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection

@section('content')

    <div id="content_top_placeholder"></div>
    @can('achievement_access')
        @if(isset(json_decode($settings)->achievements))
            <table id="users-datatable" class="table table-hover datatable">
                <thead>
                <tr>
                <th width="10">
                    <a onclick="togglePosition()" class="link-muted">
                        <i id="toggleIcon" class="fa fa-arrow-left"></i>
                    </a>
                </th>
                <th>{{ trans('global.user.fields.username') }}</th>
                <th>{{ trans('global.user.fields.lastname') }}</th>
                <th>{{ trans('global.user.fields.firstname') }}</th>
                <th>{{ trans('global.role.fields.title') }}</th>
                <th>{{ trans('global.progress.title_singular') }}</th>
            </tr>
        </thead>
    </table>
    @endif
@endcan


<div id="curriculum_view_content" class="row">
    <curriculum-view
        ref="curriculumView"
        @if(isset($course))
            :course="{{ $course }}"
        @endif

        @if(isset($logbook))
            :logbook="{{ $logbook }}"
        @endif
        :curriculum="{{ $curriculum }}"
        :objectivetypes="{{ $objectiveTypes }}"
        :settings="{{ $settings }}">
    </curriculum-view>
</div>

<move-terminal-objective-modal></move-terminal-objective-modal>
{{--<content-modal></content-modal>--}}
<!--<objective-medium-modal></objective-medium-modal>-->
    <medium-modal></medium-modal>
@can('medium_create')
<medium-create-modal></medium-create-modal>
@endcan
@if(isset($certificates))
    <certificate-generate-modal  :certificates="{{ $certificates }}" ></certificate-generate-modal>
@endif
@endsection
@section('scripts')
@parent

@if(isset(json_decode($settings)->achievements))
<script>

function getDatatablesIds(selector){
    return $(selector).DataTable().rows({ selected: true }).ids().toArray();
}

function triggerVueEvent(type){
    if ( type === 'row' ) {
        app.__vue__.$refs.curriculumView.$refs.terminalObjectives.externalEvent(getDatatablesIds('#users-datatable')); //pass Ids to terminalObjectives vue component
    }
}

function isElementInViewport (el) {
    //special bonus for those using jQuery
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function togglePosition(){
    if (localStorage.getItem('#users-datatable-position') === 'content')
    {
        localStorage.setItem('#users-datatable-position', 'menu');
        $("#users-datatable_wrapper").appendTo("#menu_top_placeholder");
        $("#menu_top_placeholder").css({'background-color': '#ecf0f5',  'webkit-transform':'translate3d(0,0,0)'});
        $("#toggleIcon").removeClass('fa-arrow-left');
        $("#toggleIcon").addClass('fa-arrow-right');
        $('.dataTables_length').hide();
        $('.dataTables_filter').hide();
        $('.dataTables_info').hide();
    } else {
        localStorage.setItem('#users-datatable-position', 'content');
        $("#users-datatable_wrapper").appendTo("#content_top_placeholder");
        $("#menu_top_placeholder").css({'background-color': 'transparent', 'webkit-transform':'translate3d(0,0,0)'});
        $("#toggleIcon").removeClass('fa-arrow-right');
        $("#toggleIcon").addClass('fa-arrow-left');
        $('.dataTables_length').show();
        $('.dataTables_filter').show();
        $('.dataTables_info').show();
    }
}

$(document).ready( function () {
    //let dtButtons = false;//$.extend(true, [], $.fn.dataTable.defaults.buttons)
    localStorage.setItem('#users-datatable-position', 'content');
    table = $('#users-datatable').DataTable({
        ajax: "/courses/list?course_id={{ $course->id }}",
        columns: [
                 { data: 'check'},
                 { data: 'username' },
                 { data: 'lastname' },
                 { data: 'firstname' },
                 { data: 'role' },
                 { data: 'progress' },
                ],
        buttons: [],

    });

    table.on( 'select', function ( e, dt, type, indexes ) { //on select event
        triggerVueEvent(type);
        app.__vue__.$refs.curriculumView.externalEvent(true);
    });
    table.on( 'deselect', function ( e, dt, type, indexes ) { //on deselect event
        triggerVueEvent(type);
        app.__vue__.$refs.curriculumView.externalEvent(false);
    });

 });

</script>
@endif

@endsection
