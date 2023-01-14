@extends('layouts.master')
@section('title')
    {{ trans('global.curriculum.import') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="/curricula">{{ trans('global.curriculum.title') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.curriculum.import') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <form action="{{ route("curricula.import.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label for="imports" class="col-md-4 col-form-label text-md-right">
                        {{ trans('global.file') }}
                    </label>

                    <div class="col-md-6">
                        <input id="imports" type="file" name="imports[]" class="form-control" multiple>
                    </div>

                </div>
                <div>
                    <input class="btn btn-success" type="submit" value="importieren...">
                </div>
            </form>

        </div>
    </div>
@endsection
