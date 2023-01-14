@extends('layouts.master')
@section('title')
    {{ trans('global.metadataset.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.metadataset.create') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("metadatasets.store") }}"
                  method="POST"
                  enctype="multipart/form-data">
                @include ('metadatasets.form', [
                    'metadataset' => new App\Metadataset,
                    'buttonText' => trans('global.metadataset.create')
                ])
        </form>
    </div>
</div>
@endsection
