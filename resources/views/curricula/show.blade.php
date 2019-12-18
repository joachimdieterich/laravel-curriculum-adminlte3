@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ $curriculum->title }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if (Auth::user()->id == env('GUEST_USER')) 
            <a href="/navigators/{{Auth::user()->organizations()->where('organization_id', '=',  Auth::user()->current_organization_id)->first()->navigators()->first()->id}}">Home</a>
        @else
            <a href="/">Home</a>
        @endif
    </li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div id="content_top_placeholder" style="height:0px;"></div>
        @can('achievement_access')
            @if(isset(json_decode($settings)->achievements))
            <table id="users-datatable" class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('global.user.fields.username') }}</th>
                        <th>{{ trans('global.user.fields.firstname') }}</th>
                        <th>{{ trans('global.user.fields.lastname') }}</th>
                        <th>{{ trans('global.role.fields.title') }}</th>
                        <th>{{ trans('global.progress.title_singular') }}</th>
                    </tr>
                </thead>     
            </table>
            @endif
        @endcan
    </div>
</div>
<div class="row">  
     <div class="col-12">
<!--    <button type="button" class="btn btn-default" data-toggle="tooltip"  onclick="">
            <i class="fa fa-compress"></i>
        </button>-->
    <div class="btn-group">
        @if (isset($curriculum->contents[0]))
            <dropdown-button 
                label="{{ trans('global.curricula_content_subscriptions') }}" 
                model="{{ @class_basename($curriculum->contents[0]) }}"
                :entries="{{ $curriculum->contents }}"
            ></dropdown-button> 
        @endif   
        
        @if ($curriculum->glossar != null)
            <dropdown-button 
                label="{{ trans('global.glossar.title') }}" 
                model="{{ class_basename($curriculum->glossar->contents[0]) }}"
                :entries="{{ $curriculum->glossar->contents }}"
            ></dropdown-button> 
        @endif
       
        @if (count($curriculum->media) > 0)
            <dropdown-button 
                label="{{ trans('global.curricula_media_subscriptions') }}" 
                model="{{ class_basename($curriculum->media[0]) }}"
                :entries="{{ $curriculum->media }}"
                styles="border-left:0px"
            ></dropdown-button> 
        @endif
        
        @can(['curriculum_edit', 'certificate_show'])
        <div class="btn-group">        
            <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                {{ trans('global.settings') }}
                <span class="sr-only">Toggle Settings Dropdown</span>
                <div class="dropdown-menu" >
                    @can('certificate_show')
                        <a class="dropdown-item" 
                           onclick="location.href='{{ route("certificates.create") }}'">
                            <i class="fa fa-certificate pr-1"></i>
                            {{ trans('global.add') }} {{ trans('global.certificate.title_singular') }}
                        </a>
                     @endcan
                     <hr>
                    <a class="dropdown-item" 
                       onclick="location.href='/curricula/{{ $curriculum->id }}/edit'">
                        <i class="fa fa-edit pr-1"></i>
                        {{ trans('global.curriculum.edit') }} {{ Str::lower(trans('global.settings')) }}
                    </a>
                </div>
            </button>
        </div>
        @endcan
        @if(isset($certificates))
            <a class="btn btn-default btn-flat" 
               onclick="app.__vue__.$modal.show('certificate-generate-modal',  {'curriculum_id': {{ $curriculum->id }} });">
                <i class="fa fa-certificate pr-1"></i>
                {{ trans('global.generate') }} {{ trans('global.certificate.title_singular') }}
            </a>
        @endif
    </div>
        
        @include ('forms.input.select', 
                   ["model" => "curriculum", 
                   "field" => "id",  
                   "css" => "pull-right m-0",
                   "style" => "float:left; width:200px",
                   "options"=> auth()->user()->curricula(), 
                   "placeholder" => "Select cross references",
                   "option_label" => "title",  
                   "onchange"=> "triggerSetCrossReferenceCurriculumId(this.value)",  
                   "value" =>  old('current_curriculum_cross_reference_id', isset(auth()->user()->current_curriculum_cross_reference_id) ? auth()->user()->current_curriculum_cross_reference_id : '')])
        
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
<objective-description-modal></objective-description-modal>
<content-modal></content-modal>
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
        
        processing: true,
        serverSide: true,
        select: true,
        ajax: "/courses/list?course_id={{ $course->id }}",
        
        columns: [
                 { data: 'check'},
                 { data: 'username' },
                 { data: 'firstname' },
                 { data: 'lastname' },
                 { data: 'role' },
                 { data: 'progress' },
                ],
        buttons: [],
    });
    
    //align header/body
    $(".dataTables_scrollHeadInner").css({"width":"100%"});
    $(".table ").css({"width":"100%"});
    
    table.on( 'select', function ( e, dt, type, indexes ) { //on select event
        triggerVueEvent(type);
    });
    table.on( 'deselect', function ( e, dt, type, indexes ) { //on deselect event
        triggerVueEvent(type);
    });
    
    $(window).on("scroll", function(table) {
        if (!isElementInViewport($("#content_top_placeholder"))){
            $("#users-datatable_wrapper").appendTo("#menu_top_placeholder");
            $("#menu_top_placeholder").css({'background-color': '#ecf0f5', 'padding':'5px', 'webkit-transform':'translate3d(0,0,0)'});
        } else {
            if (isElementInViewport($("#content_top_placeholder"))){
                $("#users-datatable_wrapper").appendTo("#content_top_placeholder");
                $("#menu_top_placeholder").css({'background-color': 'transparent', 'padding':'0px', 'webkit-transform':'translate3d(0,0,0)'});
            }
        } 
    });
        
 });

    </script>
@endif

@endsection