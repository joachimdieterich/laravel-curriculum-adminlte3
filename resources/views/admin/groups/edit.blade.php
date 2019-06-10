@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.group.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.groups.update", [$group->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
           <form method="POST" 
              action="{{ $group->path() }}" >
            @method('PATCH')
            @include('admin.groups.form', [
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