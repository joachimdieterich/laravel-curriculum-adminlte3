@extends('layouts.master')
@section('title')
    {{ trans('global.mapMarkerType.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.mapMarkerType.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("mapMarkerTypes.store") }}"
              method="POST"
              enctype="multipart/form-data">
            @include ('mapmarkertypes.form', [
                'mapmarker' => new App\MapMarkerType(),
                'buttonText' => trans('global.mapMarkerType.create')
            ])
        </form>
    </div>
</div>
@endsection
