@extends('layouts.master')
@section('title')
    {{ trans('global.contactdetail.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.contactdetail.edit') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("contactdetails.update", [$contactdetail->id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @include('contactdetails.form', [
                    'contactdetail' => $contactdetail,
                    'buttonText' => trans('global.contactdetail.edit')
                ])
            </form>
        </div>
    </div>

@endsection
