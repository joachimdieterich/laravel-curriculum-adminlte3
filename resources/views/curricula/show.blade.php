@extends((Auth::user()->role()->title == 'Guest') ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ $curriculum->title }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        @if ((Auth::user()->role()->title == 'Guest')) 
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
    @can('certificate_show')
        <div class="col-12">
            <a class="pull-right btn btn-success" href="{{ route("certificates.create") }}" >
                {{ trans('global.add') }} {{ trans('global.certificate.title_singular') }}
            </a>
            @if(isset($certificates))
            <a class="pull-right btn text-white btn-success mr-1" onclick="app.__vue__.$modal.show('certificate-generate-modal',  {'curriculum_id': {{$curriculum->id}}});" >
                {{ trans('global.generate') }} {{ trans('global.certificate.title_singular') }}
            </a>
            @endif
        </div>    
    @endcan
    <div class="col-12">
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
    
<!--    <button type="button" class="btn btn-default" data-toggle="tooltip"  onclick="">
            <i class="fa fa-compress"></i>
        </button>-->
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
                ></dropdown-button> 
        @endif
        
    </div>
    
    
    <curriculum-view
        ref="curriculumView"
        :curriculum="{{ $curriculum }}" 
        :terminalobjectives="{{ $terminalObjectives }}"
        :enablingobjectives="{{ $enablingObjectives }}"
        :objectivetypes="{{ $objectiveTypes }}"
        :settings="{{ $settings }}">
    </curriculum-view>
</div>
<terminal-objective-modal></terminal-objective-modal>
<enabling-objective-modal></enabling-objective-modal>
<objective-description-modal></objective-description-modal>
<content-modal></content-modal>
<medium-modal></medium-modal>
<objective-medium-modal></objective-medium-modal>
<medium-modal></medium-modal>
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
        app.__vue__.$refs.curriculumView.externalEvent(getDatatablesIds('#users-datatable')); //pass Ids to vue component
    }
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
 });
 
    </script>
@endif

@endsection