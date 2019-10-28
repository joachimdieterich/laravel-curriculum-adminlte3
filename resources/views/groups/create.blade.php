@extends('layouts.master')
@section('title')
     {{ trans('global.create') }} {{ trans('global.group.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.create') }} {{ trans('global.group.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("groups.store") }}" method="POST" enctype="multipart/form-data">
            <form
                method="POST"
                action="/groups"
            >
            @include ('groups.form', [
                'group' => new App\Group,
                'buttonText' => trans('global.create').' '. trans('global.group.title_singular')
            ])
        </form>
        </form>
    </div>
</div>
@endsection