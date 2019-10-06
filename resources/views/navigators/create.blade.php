@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.navigator.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("navigators.store") }}" method="POST" enctype="multipart/form-data">
            @include ('navigators.form', [
                'navigator' => new App\Navigator,
                'buttonText' => trans('global.create').' '. trans('global.navigator.title_singular')
            ])
        </form>
    </div>
</div>
@endsection