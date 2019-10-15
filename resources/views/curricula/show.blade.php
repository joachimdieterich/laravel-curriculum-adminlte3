@extends('layouts.admin')
@section('content')
<div class="row">
    
    <div class="col-12 mx-2">
        <h1>{{ $curriculum->title }}</h1>
        @can('achievement_manage')
            @if(isset(json_decode($settings)->achievements))
            <table id="users-datatable" class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('global.user.fields.username') }}</th>
                        <th>{{ trans('global.user.fields.firstname') }}</th>
                        <th>{{ trans('global.user.fields.lastname') }}</th>
                        <th>{{ trans('global.user.fields.email') }}</th>
                        <th>{{ trans('global.user.fields.email_verified_at') }}</th>
                        <th>{{ trans('global.status.title_singular') }}</th>
                        <th>Action</th>
                    </tr>
                </thead>     
            </table>
            @endif
        @endcan
    
<!--    <button type="button" class="btn btn-default" data-toggle="tooltip"  onclick="">
            <i class="fa fa-compress"></i>
        </button>-->
       
        @if ($curriculum->contents != null)
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
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    table = $('#users-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/courses/list?group_id={{ $course->group_id }}",
        
        columns: [
                 { data: 'check'},
                 { data: 'username' },
                 { data: 'firstname' },
                 { data: 'lastname' },
                 { data: 'email' },
                 { data: 'email_verified_at' },
                 { data: 'status' },
                 { data: 'action' }
                ],
        buttons: dtButtons
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