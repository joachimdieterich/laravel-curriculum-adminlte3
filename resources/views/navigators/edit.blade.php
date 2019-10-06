@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.navigator.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("navigators.update", [$navigator->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('navigators.form', [
                'navigator' => $navigator,
                'organizations' => $organizations,
                'buttonText' => trans('global.edit').' '.trans('global.navigator.title_singular')
            ])
        </form>
    </div>
</div>

@endsection