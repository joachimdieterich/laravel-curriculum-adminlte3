@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.navigator_item.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{route('navigatorItems.update', ['navigatorItem' => $navigatorItem, 'navigator_id' => $navigator->id, 'view_id' => $navigatorView->id])}}" method="POST" enctype="multipart/form-data">
            @method('PATCH')
            @include ('navigators.views.items.form', [
                'item' => $navigatorItem,
                'buttonText' => trans('global.edit').' '.trans('global.navigator_item.title_singular')
            ])
        </form>
        </form>
    </div>
</div>
@endsection