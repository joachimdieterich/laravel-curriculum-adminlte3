@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.user.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("users.update", [$user->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('users.form', [
                'user' => $user,
                'buttonText' => 'Update User'
            ])
        </form>
    </div>
</div>

@endsection
