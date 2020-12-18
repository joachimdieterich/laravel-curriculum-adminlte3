@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.navigator_item.edit') }}
    </div>

    <div class="card-body">
        <form action="{{route('navigatorItems.update', ['navigatorItem' => $navigatorItem, 'navigator_id' => $navigator->id, 'view_id' => $navigatorView->id])}}"
              method="POST"
              enctype="multipart/form-data">
            @method('PATCH')
            @include ('navigators.views.items.form', [
                'item' => $navigatorItem,
                'medium' => isset($medium) ? $medium : null,
                'buttonText' => trans('global.navigator_item.edit')
            ])
        </form>
        </form>
    </div>
</div>
@endsection
