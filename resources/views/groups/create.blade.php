@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.group.title_singular') }}
    </div>

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