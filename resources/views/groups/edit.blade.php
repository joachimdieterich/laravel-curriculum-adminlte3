@extends('layouts.master')
@section('title')
    {{ trans('global.group.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.group.edit') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ $group->path() }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @include('groups.form', [
                    'group'         => $group,
                    'grades'        => $grades,
                    'periods'       => $periods,
                    'organizations' => $organizations,
                    'buttonText'    => trans('global.group.edit')
                ])
            </form>
    </div>
</div>

@endsection
