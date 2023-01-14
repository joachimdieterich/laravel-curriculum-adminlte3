@extends('layouts.master')
@section('title')
    {{ trans('global.navigator_view.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.navigator_view.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("navigatorViews.store") }}"
              method="POST"
              enctype="multipart/form-data">
            @include ('navigators.views.form', [
                'view' => new App\NavigatorView,
                'buttonText' => trans('global.navigator_view.create')
            ])
        </form>
    </div>
</div>
@endsection
