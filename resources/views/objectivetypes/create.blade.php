@extends('layouts.master')
@section('title')
    {{ trans('global.objectiveType.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.objectiveType.create') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route("objectiveTypes.store") }}"
              method="POST"
              enctype="multipart/form-data">
            @include ('objectivetypes.form', [
                'objectivetype' => new App\ObjectiveType,
                'buttonText' => trans('global.objectiveType.create')
            ])
        </form>
    </div>
</div>
@endsection
