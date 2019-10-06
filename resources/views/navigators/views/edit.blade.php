@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.navigator_view.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("navigatorViews.update", [$navigatorView->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('navigators.views.form', [
                'view' => $navigatorView,
                'buttonText' => trans('global.edit').' '.trans('global.navigator_view.title_singular')
            ])
        </form>
    </div>
</div>

@endsection