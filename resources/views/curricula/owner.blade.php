@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.edit_owner') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.edit_owner') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('curricula.storeOwner', $curriculum->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @csrf

                @include ('forms.input.select',
                        ["model" => "user",
                        "show_label" => true,
                        "class_left" => "col-12 px-0",
                        "placeholder" => trans('global.owner') ,
                        "field" => "owner_id",
                        "options"=> $users,
                        "option_id" => "id",
                        "option_label"=> "username",
                        "value" => old('owner_id', isset($curriculum->owner_id) ? $curriculum->owner_id : '') ])
                <div>
                <input class="btn btn-info" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection
