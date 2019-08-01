@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.user.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.users.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            @include('admin.users.form', [
                'buttonText' => 'Create User'
            ])
        </form>
    </div>
</div>

@endsection