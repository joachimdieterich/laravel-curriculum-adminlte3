@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.navigator_item.title_singular') }}
    </div>

    <div class="card-body">
        <form action="/navigators/{{$uri_segments[2]}}/{{$uri_segments[3]}}/store" method="POST" enctype="multipart/form-data">
            @include ('navigators.views.items.form', [
                'item' => new App\NavigatorItem,
                'buttonText' => trans('global.create').' '.trans('global.navigator_item.title_singular')
            ])
        </form>
        </form>
    </div>
</div>
@endsection