@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.navigator_item.create') }}
    </div>

    <div class="card-body">
        <form action="{{route('navigatorItems.store', ['navigator_id' => $navigator_id, 'view_id' => $view_id])}}"
              method="POST"
              enctype="multipart/form-data">
            @include ('navigators.views.items.form', [
                'item' => new App\NavigatorItem,
                'buttonText' => trans('global.navigator_item.create')
            ])
        </form>
    </div>
</div>
@endsection
