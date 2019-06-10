@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.group.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.groups.store") }}" method="POST" enctype="multipart/form-data">
            <form
                method="POST"
                action="/groups"
            >
            @include ('admin.groups.form', [
                'group' => new App\Group,
                'buttonText' => 'Create Group'
            ])
        </form>
        </form>
    </div>
</div>
@endsection