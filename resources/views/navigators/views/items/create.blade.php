@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.navigator_item.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{route('navigatorItems.store', ['navigator_id' => $navigator_id, 'view_id' => $view_id])}}" method="POST" enctype="multipart/form-data">
            @include ('navigators.views.items.form', [
                'item' => new App\NavigatorItem,
                'buttonText' => trans('global.create').' '.trans('global.navigator_item.title_singular')
            ])
        </form>
        </form>
    </div>
</div>
@endsection