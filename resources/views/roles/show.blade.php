@extends('layouts.master')
@section('title')
    {{ trans('global.show') }} {{ trans('global.role.title') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.show') }} {{ trans('global.role.title') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')

<div class="card">
   <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        {{ trans('global.role.fields.title') }}
                    </th>
                    <td>
                        {{ $role->title }}
                    </td>
                </tr>
                <tr>
                    <th>
                        Permissions
                    </th>
                    <td>
                        @foreach($role->permissions as $id => $permissions)
                            <span class="label label-info label-many">{{ $permissions->title }}</span>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection