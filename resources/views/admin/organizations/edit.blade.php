@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.organization.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.organizations.update", [$organization->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('admin.organizations.form', [
                'organization' => $organization,
                'statusses' => $statuses,
                'buttonText' => 'Update Project'
            ])
        </form>
    </div>
</div>

@endsection