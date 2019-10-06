@extends('layouts.contentonly')
@section('content')


<div class="header">
    <div class="row p-3">
        <div class="col-12">
            <h5 class="pull-left">
                <i class="fa fa-map-signs mr-1"></i> 
                {{ $navigators->title }} <small>| {{ isset($views->title) ? $views->title : ''}}
                    <br>
                    {{ isset($views->description) ? $views->description : ''}}
                </small>    
            </h5>
            @can('navigator_create')
                <span class="pull-right">
                    <a
                    @if(isset($views))
                        href="{{route('navigatorItems.create', ['navigator_id' => $navigators->id, 'view_id' => $views->id])}}"
                    @else   
                        href="{{route('navigatorViews.create', ['navigator' => $navigators->id])}}"            
                    @endif
                    class=" btn btn-primary btn-xs mr-1">
                        <i class="fa fa-plus"></i> 
                        {{ trans('global.add') }}
                    </a> 
                </span>
            @endcan
            @if(isset($views))
                @can('navigator_create')
                    <span class="pull-right">
                        <a href="{{route('navigatorViews.edit', ['navigator_view' => $views->id])}}"  
                            class=" btn btn-primary btn-xs mr-1">
                            <i class="fa fa-edit"></i> 
                            {{ trans('global.edit') }}
                        </a> 
                    </span>
                    <span class="pull-right">
                        <form action="{{route('navigatorViews.destroy', ['navigator_view' => $views->id])}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')    
                        <button type="submit"
                            class=" btn btn-danger btn-xs mr-1">
                            <i class="fa fa-trash"></i> 
                            {{ trans('global.delete') }}
                        </button> 
                        </form>
                    </span>
                @endcan
            @endif
           
        </div>
    </div>
</div>
   
@if(isset($views))
    <!-- navigatorView -->
    @include ('navigators.views.show', [
                'view' => $views,
    ])
    <!-- /navigatorView -->
@endif
 
@endsection

