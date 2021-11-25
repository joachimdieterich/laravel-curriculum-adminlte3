@extends('layouts.contentonly')
@section('content')
<div class="header" style="margin-top:-25px;">
    <div class="row p-3">
        <div class="col-12">

            <h1 class="h5 ml-2 pull-left">
                <i class="fa fa-map-signs mr-1"></i>
                {{ $navigators->title }} <small>| {{ isset($views->title) ? $views->title : ''}}
                    <br>
                    {{ isset($views->description) ? strip_tags($views->description) : ''}}
                </small>
            </h1>

            @can('navigator_create')
                <span class="pull-right">
                    <a
                        @if(isset($views))
                        id="add-navigator-items"
                        href="{{route('navigatorItems.create', ['navigator_id' => $navigators->id, 'view_id' => $views->id])}}"
                        @else
                        id="add-navigator-view"
                        href="{{route('navigatorViews.create', ['navigator' => $navigators->id])}}"
                        @endif
                        class=" btn btn-primary btn-xs mr-1"
                        aria-label=" {{ trans('global.add') }}">
                        <i class="fa fa-plus"></i>
                        {{ trans('global.add') }}
                    </a>
                </span>
            @else
            <!--                <span class="pull-right">
                    <a
                    @if(isset($views))
                id="login"
                href="#"
                onclick="event.preventDefault(); document.getElementById('logoutform').submit();"
@endif
                    class=" btn btn-primary btn-xs">
                        <i class="fa fa-sign-in-alt"></i>
                        {{ trans('global.login') }}
                </a>
            </span>-->
            @endcan
            @if(isset($views))
                @can('navigator_create')
                    <span class="pull-right">
                        <a href="{{route('navigatorViews.edit', $views->id)}}"
                           id="edit-navigator-view"
                           class="btn btn-primary btn-xs mr-1"
                           aria-label="{{ trans('global.edit') }}">
                            <i class="fa fa-edit"></i>
                            {{ trans('global.edit') }}
                        </a>
                    </span>
                    <span class="pull-right">
                        <form action="{{route('navigatorViews.destroy', $views->id)}}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('DELETE')
                        <button type="submit"
                                id="delete-navigator-view"
                                class=" btn btn-danger btn-xs mr-1"
                                aria-label="{{ trans('global.delete') }}">
                            <i class="fa fa-trash"></i>
                            {{ trans('global.delete') }}
                        </button>
                        </form>
                    </span>
                @endcan
            @endif

        </div>
        <div class="col-12">
           <ol class="breadcrumb m-0">
                @foreach($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item "><a href="{{ $breadcrumb['href'] }}">{{ $breadcrumb['title'] }}</a></li>
                @endforeach
<!--                <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>-->
           </ol>
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

