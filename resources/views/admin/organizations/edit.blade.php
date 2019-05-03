@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.organization.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.organizations.update", [$organization->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
           <form method="POST" 
              action="{{ $organization->path() }}" >
            @method('PATCH')
            @include('admin.organizations.form', [
                'organization' => $organization,
                'buttonText' => 'Update Project'
            ])
        </form>
    </div>
</div>

@endsection