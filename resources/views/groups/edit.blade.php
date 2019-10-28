@extends('layouts.master')
@section('title')
     {{ trans('global.edit') }} {{ trans('global.group.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.edit') }} {{ trans('global.group.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.group.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("groups.update", [$group->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
           <form method="POST" 
              action="{{ $group->path() }}" >
            @method('PATCH')
            @include('groups.form', [
                'group' => $group,
                'grades' => $grades,
                'periods' => $periods,
                'organizations' => $organizations,
                'buttonText' => trans('global.edit').' '.trans('global.group.title_singular')
            ])
        </form>
    </div>
</div>

@endsection