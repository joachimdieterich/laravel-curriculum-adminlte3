@extends('layouts.master')
@section('title')
    {{ trans('global.contactdetail.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('global.contactdetail.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"
                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @include('partials.users.contactdetails', [
        'contactdetail' => $contactdetail,
        'organization'  => $organization
        ])

@endsection
