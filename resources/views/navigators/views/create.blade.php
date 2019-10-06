@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.navigator_view.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("navigatorViews.store") }}" method="POST" enctype="multipart/form-data">
            @include ('navigators.views.form', [
                'view' => new App\NavigatorView,
                'buttonText' => trans('global.create').' '. trans('global.navigator_view.title_singular')
            ])
        </form>
    </div>
</div>
@endsection