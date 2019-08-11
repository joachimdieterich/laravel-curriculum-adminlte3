@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-12 mx-2">
        <h1>{{ $curriculum->title }}</h1>
    </div>
    
    <div class="col-12 mx-2">
        <button type="button" class="btn btn-default" data-toggle="tooltip"  onclick="">
            <i class="fa fa-compress"></i>
        </button>
       
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