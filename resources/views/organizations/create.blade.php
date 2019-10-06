@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.organization.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("organizations.store") }}" method="POST" enctype="multipart/form-data">
            @include ('organizations.form', [
                'organization' => new App\Organization,
                'buttonText' => 'Create Institution'
            ])
        </form>
    </div>
</div>
@endsection