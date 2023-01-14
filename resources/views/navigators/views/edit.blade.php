@extends('layouts.master')
@section('title')
    {{ trans('global.navigator_view.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.navigator_view.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("navigatorViews.update", [$navigatorView->id]) }}"
              method="POST"
              enctype="multipart/form-data">
            @method('PATCH')
            @include('navigators.views.form', [
                'view' => $navigatorView,
                'buttonText' => trans('global.navigator_view.edit')
            ])
        </form>
    </div>
</div>

@endsection
