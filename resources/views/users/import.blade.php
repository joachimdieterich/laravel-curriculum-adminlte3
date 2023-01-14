@extends('layouts.master')
@section('title')
    {{ trans('global.user.import') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item "><a href="/users">{{ trans('global.user.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.user.import') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')


<div class="row ">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                {!!  trans('global.user.import_helper') !!}
                <div class="row ">
                    <div class="col-sm-6">
                        <table class="table table-striped"  >
                            <tbody>
                            <tr>
                                <th style="">group_id</th>
                                <th style="">{{ trans('global.group.title') }} (group)</th>
                            </tr>
                            @foreach(App\Group::where('organization_id', auth()->user()->current_organization_id)->get() as $grp_enr)
                                <tr>
                                    <td style="">{{ $grp_enr->id }}</td>
                                    <td style="">{{ $grp_enr->title }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-striped"  >
                            <tbody>
                            <tr>
                                <th style="">role_id</th>
                                <th style="">{{ trans('global.role.title') }} </th>
                            </tr>
                            @foreach(App\Role::all() as $role)
                                <tr>
                                    <td style="">{{ $role->id }}</td>
                                    <td style="">{{ $role->title }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @can('user_create')
                <form action="{{ route("users.storeImport") }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @include ('forms.input.file',
                             ["model" => "user",
                             "field" => "medium_id",
                             "label" => false,
                             "accept" => "text/csv,application/csv",
                             "value" => old('medium_id', isset($curriculum->medium_id) ? $curriculum->medium_id : '')])
                    <div>
                        <input class="btn btn-info" type="submit" value="{{ trans('global.user.create') }}">
                    </div>
                </form>
                @endcan
            </div>
        </div>
    </div><!-- ./col-xs-12 -->
</div>

@endsection
