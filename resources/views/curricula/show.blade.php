@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ $curriculum->title }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER'))
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/">{{ trans('global.home') }}</a>
        @endif
    </li>
    <li class="breadcrumb-item "><a href="/curricula/{{$curriculum->id}}">{{ trans('global.curriculum.title_singular') }}</a></li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection

@section('content')

<div id="content_top_placeholder" ></div>
@can('achievement_access')
    @if(isset(json_decode($settings)->achievements))
    <table id="users-datatable" class="table table-hover datatable">
        <thead>
            <tr>
                <th width="10"></th>
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

     <div class="col-12">
    @if(isset($course))
        @can('achievement_access')
            <a class="btn btn-default btn-flat"
              href="/curricula/{{ $course->id }}">
                <i class="fas fa-th"></i>
            </a>
        @endcan
        @can('logbook_create')
            <a class="btn btn-default btn-flat"
               href="/logbooks/{{isset($logbook) ? $logbook->id : 'create?subscribable_type=App\\Course&subscribable_id='. $course->id }}">
                <i class="fas fa-book pr-1"></i>
                {{ trans('global.logbook.title_singular') }}
            </a>
        @endcan
    @endif
    @if(!isset(json_decode($settings)->course))
<!--    <button type="button" class="btn btn-default" data-toggle="tooltip"  onclick="">
            <i class="fa fa-compress"></i>
        </button>-->

    <div class="btn-group">
        @if (isset($curriculum->contents[0]))
            <dropdown-button
                label="{{ trans('global.curricula_content_subscriptions') }}"
                model="{{ @class_basename($curriculum->contents[0]) }}"
                :entries="{{ $curriculum->contents }}"
                parent="{{ json_encode($curriculum) }}"
            ></dropdown-button>
        @endif

        @if ($curriculum->glossar != null)
            <dropdown-button
                label="{{ trans('global.glossar.title') }}"
                model="{{ class_basename($curriculum->glossar->contents[0]) }}"
                :entries="{{ $curriculum->glossar->contents }}"
                parent="{{ json_encode($curriculum) }}"
            ></dropdown-button>
        @endif

        @if (count($curriculum->media) > 0)
            <dropdown-button
                label="{{ trans('global.curricula_media_subscriptions') }}"
                model="{{ class_basename($curriculum->media[0]) }}"
                :entries="{{ $curriculum->media }}"
                parent="{{ json_encode($curriculum) }}"
                styles="border-left:0px"
            ></dropdown-button>
        @endif

        <div class="btn-group">
            <button type="button" class="btn btn-default" data-toggle="dropdown">
                <i class="fa fa-print"></i>
                <div class="dropdown-menu" >
                    <a class="dropdown-item"
                       onclick="location.href='{{ route("print.curriculum", $curriculum->id) }}'">
                        <i class="fa fa-th text-center" style="width:20px"></i>
                        {{ trans('global.curriculum.print') }}
                    </a>
                    @if(isset($curriculum->glossar))
                    <a class="dropdown-item"
                       onclick="location.href='{{ route("print.glossar", $curriculum->glossar->id) }}'">
                        <i class="fa fa-equals text-center" style="width:20px"></i>
                        {{ trans('global.glossar.print') }}
                    </a>
                    @endif
                    <a class="dropdown-item"
                       onclick="location.href='{{ route("print.references", $curriculum->id) }}'">
                        <i class="fa fa-link text-center" style="width:20px"></i>
                        {{ trans('global.curriculum.print_references') }}
                    </a>
                </div>
            </button>
        </div>
    @endif



     <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                {{ trans('global.details') }}
                <span class="sr-only">Toggle Settings Dropdown</span>
                <div class="dropdown-menu" >
                    @can('certificate_create')
                        <a class="dropdown-item"
                           onclick="location.href='{{ route("certificates.create", ['curriculum_id' => $curriculum->id]) }}'">
                            <i class="fa fa-certificate text-center" style="width:20px"></i>
                            {{ trans('global.certificate.create') }}
                        </a>
                    @endcan
                    <a class="dropdown-item"
                       onclick="app.__vue__.$modal.show('curriculum-description-modal',  {'description': {{ json_encode($curriculum->description) }} });">
                        <span class="">
                            <i class="fa fa-info text-center" style="width:20px"> </i>
                        {{ trans('global.description') }}
                        </span>
                    </a>
                    @can('curriculum_edit')
                    <hr>
                    <a class="dropdown-item"
                       onclick="app.__vue__.$modal.show('content-create-modal',  {'referenceable_type': 'App\\Curriculum', 'referenceable_id': {{ $curriculum->id }}});">
                        <i class="fa fa-file-alt text-center" style="width:20px"></i>
                        {{ trans('global.content.create') }}
                    </a>
                    <a class="dropdown-item"
                       onclick="location.href='/curricula/{{ $curriculum->id }}/edit'">
                        <i class="fa fa-edit text-center" style="width:20px"></i>
                        {{ trans('global.curriculum.edit') }}
                    </a>
                    @endcan
                </div>
            </button>
        </div>

        @if(isset($certificates))
            @can('certificate_access')
            <a class="btn btn-default btn-flat"
               onclick="app.__vue__.$modal.show('certificate-generate-modal',  {'curriculum_id': {{ $curriculum->id }} });">
                <i class="fas fa-certificate pr-1"></i>
                {{ trans('global.certificate.generate') }}
            </a>
            @endcan
        @endif
    @if(!isset(json_decode($settings)->course))
    </div>

        @include ('forms.input.select',
                   ["model" => "curriculum",
                   "field" => "id",
                   "css" => "pull-right m-0",
                   "style" => "float:left; width:200px",
                   "options"=> auth()->user()->curricula(),
                   "placeholder" => trans('global.curricula_cross_references'),
                   "option_label" => "title",
                   "onchange"=> "triggerSetCrossReferenceCurriculumId(this.value)",
                   "value" =>  old('current_curriculum_cross_reference_id', isset(auth()->user()->current_curriculum_cross_reference_id) ? auth()->user()->current_curriculum_cross_reference_id : '')])
    @endif
    </div>

    <curriculum-view
        ref="curriculumView"
        :curriculum="{{ $curriculum }}"
        :objectivetypes="{{ $objectiveTypes }}"
        :settings="{{ $settings }}">
    </curriculum-view>
</div>
<terminal-objective-modal></terminal-objective-modal>
<enabling-objective-modal></enabling-objective-modal>
<curriculum-description-modal></curriculum-description-modal>
<content-modal></content-modal>
<content-create-modal></content-create-modal>
<objective-medium-modal></objective-medium-modal>
<medium-modal></medium-modal>
@if(isset($certificates))
    <certificate-generate-modal  :certificates="{{ $certificates }}" ></certificate-generate-modal>
@endif
@endsection
@section('scripts')
@parent
<script>
function triggerSetCrossReferenceCurriculumId(curriculum_id){
    app.__vue__.$refs.curriculumView.setCrossReferenceCurriculumId(curriculum_id);
}
</script>
@if(isset(json_decode($settings)->achievements))
    <script>

function getDatatablesIds(selector){
    return $(selector).DataTable().rows({ selected: true }).ids().toArray();
}

function triggerVueEvent(type){
    if ( type === 'row' ) {
        app.__vue__.$refs.curriculumView.externalEvent(getDatatablesIds('#users-datatable')); //pass Ids to vue component
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
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}

$(document).ready( function () {
    //let dtButtons = false;//$.extend(true, [], $.fn.dataTable.defaults.buttons)

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
    });
    table.on( 'deselect', function ( e, dt, type, indexes ) { //on deselect event
        triggerVueEvent(type);
    });

    $(window).on("scroll", function(table) {
        if (!isElementInViewport($("#content_top_placeholder"))){
            $("#users-datatable_wrapper").appendTo("#menu_top_placeholder");
            $("#menu_top_placeholder").css({'background-color': '#ecf0f5',  'webkit-transform':'translate3d(0,0,0)'});
            $("#curriculum_view_content").css({'padding-top': '100px'});
            $('.dataTables_length').hide();
            $('.dataTables_filter').hide();
            $('.dataTables_info').hide();
        } else {
            if (isElementInViewport($("#content_top_placeholder"))){
                $("#users-datatable_wrapper").appendTo("#content_top_placeholder");
                $("#menu_top_placeholder").css({'background-color': 'transparent', 'webkit-transform':'translate3d(0,0,0)'});
                $("#curriculum_view_content").css({'padding-top': '0px'});
                $('.dataTables_length').show();
                $('.dataTables_filter').show();
                $('.dataTables_info').show();
            }
        }
    });

 });

    </script>
@endif

@endsection
